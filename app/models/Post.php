<?php

namespace app\models;

class Post extends Model {
    protected $table = 'posts';

    public function getAllPosts() {
        return $this->findAll();
    }

    public function getUserPosts($username) {
        $query = "SELECT * FROM {$this->table} WHERE username = :username";
        return $this->query($query, ['username' => $username]);
    }

    public function getPostById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $result = $this->query($query, ['id' => $id]);
        
        if (is_array($result) && isset($result[0])) {
            return $result[0];
        }
        
        return false;
    }

    public function createPost($username, $content, $mood) {
        $query = "INSERT INTO {$this->table} (username, content, mood) VALUES (:username, :content, :mood)";
        return $this->query($query, [
            'username' => htmlspecialchars($username),
            'content' => htmlspecialchars($content),
            'mood' => $mood
        ]);
    }

    public function updatePost($id, $content, $mood, $username) {
        // only update if the post belongs to user
        $query = "UPDATE {$this->table} SET content = :content, mood = :mood 
                 WHERE id = :id AND username = :username";
        
        return $this->query($query, [
            'content' => htmlspecialchars($content),
            'mood' => $mood,
            'id' => $id,
            'username' => $username
        ]);
    }

    public function deletePost($id, $username) {
        // only delete if the post belongs to user
        $query = "DELETE FROM {$this->table} WHERE id = :id AND username = :username";
        return $this->query($query, [
            'id' => $id, 
            'username' => $username
        ]);
    }
}
