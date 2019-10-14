create database example1;
create user 'organizer'@'localhost';
GRANT ALL PRIVILEGES ON example1.* To 'organizer'@'localhost' IDENTIFIED BY 'your secret password';

USE example1;

source contests.sql

source participants.sql

