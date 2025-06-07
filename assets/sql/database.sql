CREATE DATABASE ikun_music;
USE ikun_music;

DROP TABLE IF EXISTS Comments;
DROP TABLE IF EXISTS Songs;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
------------------------------------
CREATE TABLE Songs (
    id INT(11) NOT NULL AUTO_INCREMENT,
    song_title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    language VARCHAR(255),
    categories VARCHAR(255),
    release_date DATE,
    mp3_upload VARCHAR(255),
    profile_picture_upload VARCHAR(255),
    background_picture_upload VARCHAR(255),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-------------------------------------
CREATE TABLE Comments (
    id INT(11) NOT NULL AUTO_INCREMENT,
    song_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,  -- Add the user_id column
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_song_id (song_id),
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)  -- Define the foreign key constraint
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
---------------------------------------
CREATE TABLE artist (
    artist_id INT(11) NOT NULL AUTO_INCREMENT,
    artist_name VARCHAR(255) NOT NULL,
    artist_email VARCHAR(255) NOT NULL,
    artist_youtube VARCHAR(255),
    artist_photo VARCHAR(255),
    PRIMARY KEY (artist_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-------------------------------------
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_email VARCHAR(255) NOT NULL,
    admin_password VARCHAR(255) NOT NULL
);
-------------------------------------
ALTER TABLE Comments
ADD CONSTRAINT fk_song_id
FOREIGN KEY (song_id) REFERENCES Songs(id) ON DELETE CASCADE ON UPDATE CASCADE;
ADD COLUMN user_id INT(11),
ADD CONSTRAINT fk_user_id
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE users
ADD otp VARCHAR(10) DEFAULT NULL;
