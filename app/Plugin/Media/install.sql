CREATE TABLE medias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ref VARCHAR(60),
    ref_id INT(11),
    file VARCHAR(255),
    position INT(11) DEFAULT 0 
);