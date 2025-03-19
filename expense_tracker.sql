create database expense_tracker;
USE expense_tracker;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(99) NOT NULL UNIQUE,
    password VARCHAR(200) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
desc users;
select * from users;

CREATE TABLE income (
    id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    category VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
desc income;
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    category VARCHAR(50) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
DESC expenses;
drop table expenses;
CREATE TABLE budget(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    monthly_budget DECIMAL(10, 2) NOT NULL,
    yearly_budget DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

desc budget;

CREATE TABLE admin(
    username VARCHAR(99) NOT NULL,
    password VARCHAR(233) NOT NULL,
    email VARCHAR(99) NOT NULL,
    last_login TIMESTAMP default current_timestamp
);
INSERT INTO admin (username, password, email) VALUES ('admin', 'vedant2212', 'admin@expensetracker.com');
select * from admin;
desc admin;

CREATE TABLE user_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    is_blocked BOOLEAN DEFAULT FALSE,
    blocked_at TIMESTAMP default current_timestamp NOT NULL,
    reason TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
drop table user_status;
create table feedback(
	user_id int not null,
    description text,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
drop table feedback;
truncate table expenses;
truncate table income;
desc user_status;
select * from user_status;