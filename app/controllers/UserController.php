<?php

namespace app\controllers;
use app\models\User;

class UserController extends Controller {
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->returnView('./assets/views/user/register.html');
            return;
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        
        // validate input
        $errors = [];
        if (empty($username)) {
            $errors['username'] = 'Username is required';
        } elseif (strlen($username) < 3 || strlen($username) > 18) {
            $errors['username'] = 'Username must be between 3 and 18 characters';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }
        
        if (!empty($errors)) {
            $this->returnJSON([
                'success' => false,
                'errors' => $errors
            ]);
            return; 
        }
        
        $userModel = new User();
        $result = $userModel->register($username, $password);
        
        if (isset($result['error'])) {
            $this->returnJSON([
                'success' => false,
                'errors' => ['username' => $result['error']]
            ]);
            return; 
        }
        
        $this->returnJSON(['success' => true]);
        return; 
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->returnView('./assets/views/user/login.html');
            return;
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        
        // validate input
        $errors = [];
        if (empty($username)) {
            $errors['username'] = 'Username is required';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        }
        
        if (!empty($errors)) {
            $this->returnJSON([
                'success' => false,
                'errors' => $errors
            ]);
        }
        
        $userModel = new User();
        $user = $userModel->authenticate($username, $password);
        
        if (!$user) {
            $this->returnJSON([
                'success' => false,
                'errors' => ['general' => 'Invalid username or password']
            ]);
        }
        
        // store user data in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;
        
        $this->returnJSON([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username']
            ]
        ]);
    }
    
    public function logout() {
        // clear session data
        $_SESSION = [];
        
        // destroy the session
        session_destroy();
        
        $this->returnJSON(['success' => true]);
    }
    
    public function checkAuth() {
        
        $isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
        
        $this->returnJSON([
            'authenticated' => $isLoggedIn,
            'user' => $isLoggedIn ? [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username']
            ] : null
        ]);
    }
}