CREATE DATABASE example1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

create user 'organizer'@'localhost';
GRANT ALL PRIVILEGES ON example1.* To 'organizer'@'localhost' IDENTIFIED BY 'your secret password';

USE example1;

source contests.sql

source participants.sql

