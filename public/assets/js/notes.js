const createBtn = document.getElementById("createBtn");
const noteBoard = document.getElementById("noteBoard");
const userInfoElement = document.getElementById("userInfo");
const logoutBtn = document.getElementById("logoutBtn");
const loginPrompt = document.getElementById("loginPrompt");

// mood emojis mapping
const moodEmojis = {
    'smiley': '😊',
    'laughing': '😂',
    'love': '❤️',
    'surprised': '😮',
    'sad': '😢',
    'angry': '😡'
};

// current user info
let currentUser = null;

// check authentication status
const checkAuth = async () => {
    try {
        const res = await fetch("/api/auth/check");
        const result = await res.json();

        if (result.authenticated) {
            currentUser = result.user;
            updateUserInterface(true);
        } else {
            currentUser = null;
            updateUserInterface(false);
        }

        return result.authenticated;
    } catch (err) {
        console.error("Auth check failed");
        return false;
    }
};

// update UI based on authentication status
const updateUserInterface = (isLoggedIn) => {
    if (isLoggedIn) {
        userInfoElement.textContent = `Welcome, ${currentUser.username}`;
        userInfoElement.classList.remove("d-none");
        logoutBtn.classList.remove("d-none");
        createBtn.classList.remove("d-none");
        loginPrompt.classList.add("d-none");

        const yourNotesBtn = document.querySelector(".view-your-notes-btn");
        if (yourNotesBtn) yourNotesBtn.classList.remove("d-none");

        document.querySelector(".navbar-brand").innerHTML = '<i class="fas fa-comment-dots"></i> SocialNotes';
        document.querySelector(".navbar-brand").href = "/";
    } else {
        userInfoElement.classList.add("d-none");
        logoutBtn.classList.add("d-none");
        createBtn.classList.add("d-none");
        loginPrompt.classList.remove("d-none");

        const yourNotesBtn = document.querySelector(".view-your-notes-btn");
        if (yourNotesBtn) yourNotesBtn.classList.add("d-none");

        document.querySelector(".navbar-brand").innerHTML = '<i class="fas fa-comment-dots"></i> SocialNotes';
        document.querySelector(".navbar-brand").href = "/";
    }
};

// show toast notification for errors only
const showToast = (message, isError = false) => {
    if (!isError) return;

    let toastContainer = document.getElementById("toastContainer");
    if (!toastContainer) {
        toastContainer = document.createElement("div");
        toastContainer.id = "toastContainer";
        toastContainer.className = "toast-container position-fixed bottom-0 end-0 p-3";
        document.body.appendChild(toastContainer);
    }

    const toastId = `toast-${Date.now()}`;
    const toastEl = document.createElement("div");
    toastEl.className = `toast bg-danger text-white`;
    toastEl.id = toastId;
    toastEl.setAttribute("role", "alert");
    toastEl.setAttribute("aria-live", "assertive");
    toastEl.setAttribute("aria-atomic", "true");

    toastEl.innerHTML = `
        <div class="toast-header bg-danger text-white">
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">${message}</div>
    `;

    toastContainer.appendChild(toastEl);
    const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 5000 });
    toast.show();

    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
};

// handle logout
if (logoutBtn) {
    logoutBtn.addEventListener("click", async () => {
        try {
            const res = await fetch("/api/auth/logout", {
                method: "POST"
            });

            const result = await res.json();

            if (result.success) {
                currentUser = null;
                updateUserInterface(false);
                window.location.href = "/login";
            }
        } catch (err) {
            showToast("Failed to log out. Please try again.", true);
        }
    });
}

// load all notes from the backend
const loadNotes = async () => {
    try {
        const res = await fetch("/api/posts");
        const notes = await res.json();
        return notes || [];
    } catch (err) {
        showToast("Failed to load notes. Please refresh the page.", true);
        return [];
    }
};

// update a note via PUT
const modifyNote = async (noteID, newText, mood = "smiley") => {
    if (!currentUser) {
        showToast("You must be logged in to edit notes", true);
        return;
    }

    try {
        const res = await fetch(`/api/posts/${noteID}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                content: newText,
                mood: mood
            })
        });

        const result = await res.json();
        if (!result.success) {
            showToast(result.error || "Failed to update note", true);
        }
    } catch (err) {
        showToast("Error updating note", true);
    }
};

// create note card in DOM
const makeNote = (note) => {
    const wrapper = document.createElement("div");
    wrapper.className = "note-card";

    // check if this note belongs to the current user
    const isOwnNote = currentUser && note.username === currentUser.username;

    if (isOwnNote) {
        wrapper.classList.add("own-note");
    } else {
        wrapper.classList.add("other-note");
    }

    // add mood indicator emoji
    const moodIndicator = document.createElement("div");
    moodIndicator.className = "mood-indicator";
    moodIndicator.textContent = moodEmojis[note.mood] || moodEmojis.smiley;
    wrapper.appendChild(moodIndicator);

    const textField = document.createElement("textarea");
    textField.className = "note-text";
    textField.value = note.content;

    if (isOwnNote) {
        textField.placeholder = "Write something...";
        textField.addEventListener("input", () => {
            modifyNote(note.id, textField.value, note.mood);
        });
    } else {
        textField.readOnly = true;
        textField.placeholder = "";
    }

    const remove = document.createElement("button");
    remove.className = "remove-btn";
    remove.innerHTML = '<i class="fas fa-times"></i>';

    if (!isOwnNote) {
        remove.style.display = "none";
    }

    remove.addEventListener("click", async () => {
        if (!currentUser) {
            showToast("You must be logged in to delete notes", true);
            return;
        }

        try {
            const res = await fetch(`/api/posts/${note.id}`, {
                method: "DELETE"
            });

            const result = await res.json();

            if (result.success) {
                wrapper.remove();
            } else {
                showToast(result.error || "Failed to delete note", true);
            }
        } catch (err) {
            showToast("Error deleting note", true);
        }
    });

    // add mood selector (only for user's own notes)
    if (isOwnNote) {
        const moodSelector = document.createElement("div");
        moodSelector.className = "mood-selector";

        Object.entries(moodEmojis).forEach(([moodType, emoji]) => {
            const emojiElement = document.createElement("span");
            emojiElement.className = `mood-emoji ${note.mood === moodType ? 'selected' : ''}`;
            emojiElement.textContent = emoji;
            emojiElement.title = moodType.charAt(0).toUpperCase() + moodType.slice(1);

            emojiElement.addEventListener("click", () => {
                moodSelector.querySelectorAll('.mood-emoji').forEach(el => {
                    el.classList.remove('selected');
                });
                emojiElement.classList.add('selected');
                moodIndicator.textContent = emoji;
                modifyNote(note.id, textField.value, moodType);
            });

            moodSelector.appendChild(emojiElement);
        });

        wrapper.appendChild(moodSelector);
    }

    // add username display
    const usernameElement = document.createElement("div");
    usernameElement.className = "note-username";
    usernameElement.textContent = note.username;

    wrapper.appendChild(remove);
    wrapper.appendChild(usernameElement);
    wrapper.appendChild(textField);

    return wrapper;
};

// create new note via POST
const createNote = async () => {
    if (!currentUser) {
        showToast("You must be logged in to create notes", true);
        return;
    }

    try {
        const res = await fetch("/api/posts", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                content: "New note...",
                mood: "smiley"
            })
        });

        const result = await res.json();
        if (result.success) {
            loadAndDisplayNotes();
        } else {
            showToast(result.error || "Failed to create note", true);
        }
    } catch (err) {
        showToast("Error creating note", true);
    }
};

// initialize and display notes
const loadAndDisplayNotes = async () => {
    noteBoard.querySelectorAll(".note-card").forEach(n => n.remove());
    const notes = await loadNotes();
    notes.forEach(note => {
        const noteEl = makeNote(note);
        noteBoard.insertBefore(noteEl, createBtn);
    });
};

// on load
(async () => {
    await checkAuth();
    await loadAndDisplayNotes();
})();

createBtn.addEventListener("click", createNote);