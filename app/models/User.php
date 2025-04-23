<?php

namespace app\models;

class User extends Model {
    protected $table = 'users';

    public function register($username, $password) {
        // check if user name exists
        $existingUser = $this->getUserByUsername($username);
        if ($existingUser) {
            return ['error' => 'Username already exists'];
        }

        // hashing password for security 
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO {$this->table} (username, password_hash) VALUES (:username, :password_hash)";
        return $this->query($query, [
            'username' => htmlspecialchars($username),
            'password_hash' => $password_hash
        ]);
    }

    public function authenticate($username, $password) {
        $user = $this->getUserByUsername($username);
        
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['password_hash'])) {
            // removing hash before returning user data
            unset($user['password_hash']);
            return $user;
        }

        return false;
    }

    public function getUserByUsername($username) {
        $query = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        $result = $this->query($query, ['username' => $username]);
        
        if (is_array($result) && isset($result[0])) {
            return $result[0];
        }
        
        return false;
    }
}