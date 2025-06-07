CREATE TABLE user (
    user_ID INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(150) NOT NULL,
    user_phone_num VARCHAR(10) NOT NULL,
    user_profile_photo VARCHAR(350),
    user_password VARCHAR(150) NOT NULL,
    user_detail VARCHAR(350),
    coin_amount INT(11) NOT NULL
);

CREATE TABLE song (
    song_ID INT AUTO_INCREMENT PRIMARY KEY,
    artist_ID INT,
    comment_ID INT,
    donate_ID INT,
    user_ID INT,
    song_name VARCHAR(100) NOT NULL,
    youtube_link VARCHAR(350),
    song_photo VARCHAR(350),
    song_created VARCHAR(350),
    FOREIGN KEY (artist_ID) REFERENCES artist(artist_ID),
    FOREIGN KEY (comment_ID) REFERENCES comment(comment_ID),
    FOREIGN KEY (donate_ID) REFERENCES donate(donate_ID),
    FOREIGN KEY (user_ID) REFERENCES user(user_ID)
);

CREATE TABLE artist (
    artist_ID INT AUTO_INCREMENT PRIMARY KEY,
    artist_name VARCHAR(100) NOT NULL,
    artist_email VARCHAR(150) NOT NULL,
    artist_facebook VARCHAR(150),
    artist_instagram VARCHAR(150),
    artist_youtube VARCHAR(150),
    artist_photo VARCHAR(150),
    total_donate_amount INT(11)
);

CREATE TABLE plays (
    plays_ID INT AUTO_INCREMENT PRIMARY KEY,
    song_ID INT,
    play_created VARCHAR(150) NOT NULL,
    FOREIGN KEY (song_ID) REFERENCES song(song_ID)
);

CREATE TABLE comment (
    comment_ID INT AUTO_INCREMENT PRIMARY KEY,
    song_ID INT,
    user_ID INT,
    comment_detail VARCHAR(350) NOT NULL,
    FOREIGN KEY (song_ID) REFERENCES song(song_ID),
    FOREIGN KEY (user_ID) REFERENCES user(user_ID)
);

CREATE TABLE payment (
    payment_ID INT AUTO_INCREMENT PRIMARY KEY,
    user_ID INT,
    payment_package VARCHAR(150) NOT NULL,
    payment_date DATE NOT NULL,
    FOREIGN KEY (user_ID) REFERENCES user(user_ID)
);

CREATE TABLE donate (
    donate_ID INT AUTO_INCREMENT PRIMARY KEY,
    user_ID INT,
    song_ID INT,
    wallet_ID INT,
    donate_amount INT(11) NOT NULL,
    donate_date DATE NOT NULL,
    FOREIGN KEY (user_ID) REFERENCES user(user_ID),
    FOREIGN KEY (song_ID) REFERENCES song(song_ID)
);

CREATE TABLE admin (
    admin_ID INT AUTO_INCREMENT PRIMARY KEY,
    admin_name VARCHAR(100) NOT NULL,
    admin_password VARCHAR(200) NOT NULL
);
