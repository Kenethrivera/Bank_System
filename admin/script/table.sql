create database bank_db;

CREATE TABLE loans (
	loan_id INT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    loan_type VARCHAR(100) NOT NULL,
    amount DECIMAL(5,2) NOT NULL,
    Status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    application_date DATE
);

INSERT INTO `loans`(`loan_id`, `customer_name`, `loan_type`, `amount`, `Status`, `application_date`) VALUES (6,'james','loan','2000','Pending','2026-01-01'), (7,'escueta','loan','2000','Pending','2026-01-01'), (8,'rivera','loan','2000','Pending','2026-01-01');

CREATE TABLE user_accounts (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(150) NOT NULL,
    MiddleName VARCHAR(150),
    LastName VARCHAR(150) NOT NULL,
    Email VARCHAR(150) UNIQUE,
    Phone VARCHAR(15) UNIQUE,
    Address VARCHAR(150) NOT NULL,
    Birthdate DATE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    Img VARCHAR(150) NOT NULL,
    Role VARCHAR(10) DEFAULT 'User',
    Status VARCHAR(10) DEFAULT 'Pending',
    Balance DECIMAL(10,2) DEFAULT 0.00
);


INSERT INTO user_accounts  (FirstName, MiddleName, LastName, Email, Phone, Address, Birthday, Password) VALUES
('Abdul', 'Pacalundo', 'Disomimba', 'malik@test.com', '0912345678', 'Quezon City, Philippines', '1998-05-12', 'malik12345');

