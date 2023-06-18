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
  FOREIGN KEY (parent_genre) REFERENCES genres(id)
);

INSERT INTO transactions(isbn, type, amount, provider, date, receipt_number) VALUES ("978-3-16-148410-0", 1, "1112", "Hailee Steinfeld", "1998-12-11", "INV-21-12-009"), ("873-3-16-720912-5", 0, "50", "Sabrina Carpenter", "2023-06-14", "INV-12-09-315");