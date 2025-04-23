<?php

namespace app\controllers;

//this is an example controller class, feel free to delete
class MainController extends Controller {

    public function homepage() {
        $this->returnView('./assets/views/main/notesView.html');
    }
    
    public function about() {
        $this->returnView('./assets/views/main/about.html');
    }
    
    public function contact() {
        $this->returnView('./assets/views/main/contact.html');
    }

    public function notfound() { 
        $this->returnView('./assets/views/main/notFound.html');
    }

    public function login() {
        $this->returnView('./assets/views/user/login.html');
    }
    
    public function register() {
        $this->returnView('./assets/views/user/register.html');
    }
    
    public function userNotes() {
        $this->returnView('./assets/views/user/userNotes.html'); 
    }
}