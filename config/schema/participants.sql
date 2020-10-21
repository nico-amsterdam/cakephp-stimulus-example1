CREATE TABLE participants (
 id INT AUTO_INCREMENT PRIMARY KEY,
 contest_id INT NOT NULL,
 name VARCHAR(50) NOT NULL,
 email VARCHAR(255),
 date_of_birth DATE,
 created DATETIME,  -- or: created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 modified DATETIME, -- or: modified TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY contest_name_uniq (contest_id, name),
 UNIQUE KEY contest_email_uniq (contest_id, email), 
 FOREIGN KEY contest_fk (contest_id) REFERENCES contests(id)
) CHARSET=utf8mb4;

