<?php
/**
CREATE TABLE topics (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    created DATETIME DEFAULT NULL,
    modified DATETIME DEFAULT NULL
);
**/
class Topic extends AppModel {

    public $hasMany = 'Post';

    public $validate = array(
        'title' => array(
            'rule' => 'notEmpty'
        ),
        'body' => array(
            'rule' => 'notEmpty'
        )
    );
	
}