# MyFriendSystem
My Friend System - PHP / MySQL


-- Create the database if it does not exist
CREATE DATABASE IF NOT EXISTS friendappdb;

-- Use the specified database
USE friendappdb;

-- Create the 'friends' table
CREATE TABLE IF NOT EXISTS friends (
    friend_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    friend_email VARCHAR(50) NOT NULL,
    password VARCHAR(20) NOT NULL,
    profile_name VARCHAR(30) NOT NULL,
    date_started DATE NOT NULL,
    num_of_friends INT UNSIGNED
);

-- Create the 'myfriends' table
CREATE TABLE IF NOT EXISTS myfriends (
    friend_id1 INT NOT NULL,
    friend_id2 INT NOT NULL,
    PRIMARY KEY (friend_id1, friend_id2),
    CONSTRAINT fk_friend_id1 FOREIGN KEY (friend_id1) REFERENCES friends(friend_id),
    CONSTRAINT fk_friend_id2 FOREIGN KEY (friend_id2) REFERENCES friends(friend_id),
    CHECK (friend_id1 <> friend_id2)
);



