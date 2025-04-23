<?php

namespace app\core;

use app\controllers\MainController;
use app\controllers\PostController;
use app\controllers\UserController;

class Router {
    public $uriArray;
    private $matched = false; 

    function __construct() {
        $this->uriArray = $this->routeSplit();
        $this->handleMainRoutes();
        $this->handlePostRoutes();
        $this->handleUserRoutes();

        if (!$this->matched) { 
            $mainController = new MainController(); 
            $mainController->notFound(); 
        }
    }

    protected function routeSplit() {
        $removeQueryParams = strtok($_SERVER["REQUEST_URI"], '?');
        return explode("/", $removeQueryParams);
    }

    protected function handleMainRoutes() {
        if ($this->uriArray[1] === '' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $mainController = new MainController();
            $mainController->homepage();
            $this->matched = true;
        }
        if ($this->uriArray[1] === 'about' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $mainController = new MainController();
            $mainController->about();
            $this->matched = true;
        }
        
        if ($this->uriArray[1] === 'contact' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $mainController = new MainController();
            $mainController->contact();
            $this->matched = true; 
        }    

        if ($this->uriArray[1] === 'login' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $mainController = new MainController();
            $mainController->login();
            $this->matched = true;
        }
        
        if ($this->uriArray[1] === 'your-notes' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $mainController = new MainController();
            $mainController->userNotes();
            $this->matched = true;
        }
    }

    protected function handlePostRoutes() {
        if ($this->uriArray[1] === 'api' && $this->uriArray[2] === 'posts') {
            $postController = new PostController();
    
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                // Check if this is a request for user-specific posts
                if (isset($this->uriArray[3]) && $this->uriArray[3] === 'user') {
                    $postController->getUserPosts();
                    $this->matched = true;
                } else {
                    // Regular get all posts
                    $postController->getPosts();
                    $this->matched = true;
                }
            }
    
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $postController->createPost();
                $this->matched = true;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($this->uriArray[3])) {
                $postController->updatePost($this->uriArray[3]); 
                $this->matched = true;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($this->uriArray[3])) {
                $postController->deletePost($this->uriArray[3]);
                $this->matched = true;
            }
        }
    }
    
    protected function handleUserRoutes() {
        $userController = new UserController();
        
        if ($this->uriArray[1] === 'register' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $userController->register();
            $this->matched = true;
        }
        
        if ($this->uriArray[1] === 'api' && $this->uriArray[2] === 'auth') {

            if ($this->uriArray[3] === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $userController->register();
                $this->matched = true;
            }
            
            if ($this->uriArray[3] === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $userController->login();
                $this->matched = true;
            }
            
            if ($this->uriArray[3] === 'logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $userController->logout();
                $this->matched = true;
            }
            
            if ($this->uriArray[3] === 'check' && $_SERVER['REQUEST_METHOD'] === 'GET') {
                $userController->checkAuth();
                $this->matched = true;
            }
        }
    }
}