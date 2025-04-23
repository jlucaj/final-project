// github api integration
document.addEventListener('DOMContentLoaded', function () {
    const username = 'jlucaj';
    fetchUserProfile(username);
    fetchUserRepositories(username);
});

function fetchUserProfile(username) {
    fetch(`https://api.github.com/users/${username}`)
        .then(response => response.json())
        .then(data => {

            // update profile info
            document.getElementById('github-name').textContent = data.name || data.login;
            document.getElementById('github-username').textContent = `@${data.login}`;
            document.getElementById('github-repos').textContent = data.public_repos;
            document.getElementById('github-followers').textContent = data.followers;
            document.getElementById('github-following').textContent = data.following;
            document.getElementById('github-bio').innerHTML = `<p class="mb-0">${data.bio || 'No bio available'}</p>`;
            document.getElementById('github-avatar').innerHTML = `<img src="${data.avatar_url}" alt="${data.login}" class="rounded-circle" width="80" height="80">`;
        })
        .catch(() => {
            document.getElementById('github-profile').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Failed to load GitHub profile. Please try again later.
                </div>
            `;
        });
}

function fetchUserRepositories(username) {
    fetch(`https://api.github.com/users/${username}/repos?sort=updated&per_page=5`)
        .then(response => response.json())
        .then(repos => {
            const reposContainer = document.getElementById('github-repositories');

            // handle empty repos
            if (repos.length === 0) {
                reposContainer.innerHTML = `<p class="text-light">No public repositories found</p>`;
                return;
            }

            // build repos html
            let reposHTML = '';
            repos.forEach(repo => {
                reposHTML += `
                    <div class="repo-item p-3 mb-3 bg-dark rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <a href="${repo.html_url}" target="_blank" class="link-info">${repo.name}</a>
                            </h5>
                            <span class="badge bg-secondary">${repo.language || 'N/A'}</span>
                        </div>
                        <p class="text-light small mb-2 mt-2">${repo.description || 'No description available'}</p>
                        <div class="d-flex align-items-center small text-light">
                            <span class="me-3"><i class="fas fa-star me-1"></i>${repo.stargazers_count}</span>
                            <span><i class="fas fa-code-branch me-1"></i>${repo.forks_count}</span>
                        </div>
                    </div>
                `;
            });

            reposContainer.innerHTML = reposHTML;
        })
        .catch(() => {
            document.getElementById('github-repositories').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Failed to load repositories.
                </div>
            `;
        });
}