-- Database: todo_board
CREATE DATABASE IF NOT EXISTS todo_board;
USE todo_board;

-- Tabel boards (papan kerja)
CREATE TABLE IF NOT EXISTS boards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#FFB5E8',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel lists (daftar dalam board)
CREATE TABLE IF NOT EXISTS lists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    board_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    position INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (board_id) REFERENCES boards(id) ON DELETE CASCADE
);

-- Tabel cards (kartu dalam list)
CREATE TABLE IF NOT EXISTS cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    list_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_path VARCHAR(255),
    position INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (list_id) REFERENCES lists(id) ON DELETE CASCADE
);

-- Data sample
INSERT INTO boards (title, description, color) VALUES 
('Project Planning', 'Perencanaan proyek untuk aplikasi to-do list', '#FFB5E8'),
('Personal Tasks', 'Tugas-tugas pribadi', '#B5F8FF');

INSERT INTO lists (board_id, title, position) VALUES 
(1, 'To Do', 0),
(1, 'In Progress', 1),
(1, 'Done', 2);

INSERT INTO cards (list_id, title, description, position) VALUES 
(1, 'Desain Database', 'Membuat struktur database untuk aplikasi', 0),
(1, 'Setup Project', 'Inisialisasi project dengan PHP dan MySQL', 1),
(2, 'Coding Backend', 'Implementasi CRUD operations', 0);