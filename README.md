The Library Management System is a comprehensive software solution designed to streamline and automate various library operations. It provides an efficient platform for managing books, members, borrowings, and returns in a library setting. With this system, librarians can easily track and organize books, handle member registrations, facilitate book borrowing and returning processes, and generate reports.

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
    FOREIGN KEY (genre) REFERENCES genres(id) ON DELETE CASCADE
);

CREATE TABLE members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  member_id VARCHAR(6) UNIQUE NOT NULL,
  name VARCHAR(255) NOT NULL,
  dob DATE NOT NULL,
  phone BIGINT(10) UNSIGNED UNIQUE NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  gender BOOLEAN NOT NULL,
  address VARCHAR(255) NOT NULL,
  registration DATE DEFAULT CURRENT_DATE NOT NULL,
  renewal DATE DEFAULT CURRENT_DATE NOT NULL
);

CREATE TABLE borrows (
  id INT AUTO_INCREMENT PRIMARY KEY,
  book INT NOT NULL,
  member INT NOT NULL,
  issue DATE DEFAULT CURRENT_DATE NOT NULL,
  due DATE NOT NULL,
  FOREIGN KEY (book) REFERENCES books(id) ON DELETE CASCADE,
  FOREIGN KEY (member) REFERENCES members(id) ON DELETE CASCADE,
  returned DATE
);