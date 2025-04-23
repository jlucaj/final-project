CREATE DATABASE final; 
USE final; 

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(18) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(18) DEFAULT 'anonymous', 
    content TEXT NOT NULL,
    mood ENUM('smiley', 'laughing', 'love', 'suprised', 'sad','angry') DEFAULT 'smiley',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
