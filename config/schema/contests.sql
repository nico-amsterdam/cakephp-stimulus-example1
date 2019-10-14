CREATE TABLE contests (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(50) NOT NULL,
 number_of_prizes INT NOT NULL,
 prize1 VARCHAR(255),
 prize2 VARCHAR(255),
 prize3 VARCHAR(255),
 created DATETIME,
 modified DATETIME,
 UNIQUE KEY (name)
) CHARSET=utf8mb4;

