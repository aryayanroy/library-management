# library-management
This web app will manage all the book records, library members, fines, etc.


Run this MySQL query:

CREATE TABLE admins(
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(13) NOT NULL UNIQUE,
    type TINYINT(1) NOT NULL,
    amount INT NOT NULL,
    provider VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    receipt_number VARCHAR(20) NOT NULL
);