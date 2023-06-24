# library-management
This web app will manage all the book records, library members, fines, etc.

Admin credentials:
username: admin
password: admin@123

Run this MySQL query:

CREATE DATABASE library_management;

USE library_management;

CREATE TABLE admins(
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

INSERT INTO admins(username, password) VALUES ("admin", "$2y$10$xOAcU5hweyzkZ6.X3aBK1OBc2AN23M.ztz4Blefe47JdERpqtYEs.");

CREATE TABLE genres(
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  parent_genre INT,
  FOREIGN KEY (parent_genre) REFERENCES genres(id) ON DELETE CASCADE
);

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    authors VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) UNIQUE NOT NULL,
    genre INT NOT NULL,
    FOREIGN KEY (genre) REFERENCES genres(id)
);