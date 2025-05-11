<?php

namespace app\controllers;
use app\models\Post;

class PostController extends Controller {
    public function getPosts() {
        $postModel = new Post();
        $posts = $postModel->getAllPosts();
        $this->returnJSON($posts);
    }

    public function createPost() {        
        // only logged in users can create posts
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $this->returnJSON([
                'success' => false,
                'error' => 'You must be logged in to create notes'
            ]);
            return;
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        $username = $_SESSION['username'];
        $content = $data['content'] ?? '';
        $mood = $data['mood'] ?? 'smiley';
    
        $postModel = new Post();
        $result = $postModel->createPost($username, $content, $mood);
    
        if (isset($result['error']) || isset($result['exception'])) {
            $this->returnJSON([
                'success' => false,
                'debug' => $result
            ]);
        }
    
        $this->returnJSON(['success' => true]);
    }

    public function updatePost($id) {
        // only logged in users can update posts
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $this->returnJSON([
                'success' => false,
                'error' => 'You must be logged in to update notes'
            ]);
            return;
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        $content = $data['content'] ?? null;
        $mood = $data['mood'] ?? null;
    
        if ($content === null || $mood === null) {
            $this->returnJSON(['success' => false, 'error' => 'Missing required fields']);
        }
        
        $postModel = new Post();
        $username = $_SESSION['username'];
        
        // check if post exists
        $post = $postModel->getPostById($id);
        if (!$post) {
            $this->returnJSON(['success' => false, 'error' => 'Note not found']);
            return;
        }

        if ($post['username'] !== $username) {
            $this->returnJSON(['success' => false, 'error' => 'You can only edit your own notes']);
            return;
        }

        $result = $postModel->updatePost($id, $content, $mood, $username);
        if ($result === true) {
            $this->returnJSON(['success' => true]);
        }

        $this->returnJSON(['success' => false, 'error' => 'Failed to update note']);
    }


    public function deletePost($id) {
        // only logged in users can delete posts
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $this->returnJSON([
                'success' => false,
                'error' => 'You must be logged in to delete notes'
            ]);
            return;
        }
    
        $username = $_SESSION['username'];
        $postModel = new Post();
    
        // check if post exists and belongs to the user
        $post = $postModel->getPostById($id);
        if (!$post) {
            $this->returnJSON(['success' => false, 'error' => 'Note not found']);
            return;
        }
    
        if ($post['username'] !== $username) {
            $this->returnJSON(['success' => false, 'error' => 'You can only delete your own notes']);
            return;
        }
    
        $result = $postModel->deletePost($id, $username);
    
        $this->returnJSON(['success' => $result !== false]);
    }
    
    public function getUserPosts() {
        // only allow logged in users to view their posts
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $this->returnJSON([
                'success' => false,
                'error' => 'You must be logged in to view your notes'
            ]);
            return;
        }
        
        $username = $_SESSION['username'];
        $postModel = new Post();
        $posts = $postModel->getUserPosts($username);
        
        $this->returnJSON($posts);
    }
}
