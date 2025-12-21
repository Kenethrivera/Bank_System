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
ID Int AUTO_INCREMENT PRIMARY KEY,
    FirstName Varchar(150) not null,
    MiddleName Varchar(150),
    LastName Varchar(150) not null,
    Email Varchar(150) UNIQUE key,
    Phone Varchar(15) UNIQUE key,
    Address Varchar(150) not null,
	Birthday Date not null,
    Password Varchar(150) not null,
    Img Varchar(150) not null,
    Role Varchar(10) DEFAULT 'User',
    Status Varchar(10) DEFAULT 'Pending'
);

INSERT INTO user_accounts  (FirstName, MiddleName, LastName, Email, Phone, Address, Birthday, Password) VALUES
('Abdul', 'Pacalundo', 'Disomimba', 'malik@test.com', '0912345678', 'Quezon City, Philippines', '1998-05-12', 'malik12345');

