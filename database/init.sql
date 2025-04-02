-- Active: 1743623855189@@127.0.0.1@3406@news_db
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Przykładowe dane
INSERT INTO articles (title, description) VALUES
('Welcome to CRDG', 'This is your first article. You can edit or delete it from the admin panel.'),
('Getting Started', 'Login to the admin panel using admin/test to manage your articles.'); 
