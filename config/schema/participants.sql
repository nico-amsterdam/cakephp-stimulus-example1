CREATE TABLE participants (
 id INT AUTO_INCREMENT PRIMARY KEY,
 contest_id INT NOT NULL,
 name VARCHAR(50) NOT NULL,
 email VARCHAR(255),
 date_of_birth DATETIME,
 created DATETIME,
 modified DATETIME,
 UNIQUE KEY contest_name_uniq (contest_id, name),
 FOREIGN KEY contest_fk (contest_id) REFERENCES contests(id)
) CHARSET=utf8mb4;

