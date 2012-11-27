<?php
/**
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(50),
    role VARCHAR(20),
    cosm_id INT(11),
    cosm_token VARCHAR(20),
    created DATETIME DEFAULT NULL,
    modified DATETIME DEFAULT NULL
);
**/

class User extends AppModel {

	public $actsAs = array(
		'Containable',
		'Media.Media' => array(
		  'path' => 'User/%id-%f'
		)
	  );
	  
	public $hasMany = array(
//		'Media' //not needed, plugin do update automaitcly)
        'Feed' => array(
            'className'  => 'Feed',
            'order'      => 'Feed.created DESC'
        ),
        'Post' => array(
            'className'  => 'Post',
//            'conditions' => array('Post.approved' => '1'), //to develop....
            'order'      => 'Post.created DESC'
        )
    );
	
    public $validate = array(
		'username' => array(
			'Only alphabets and numbers allowed' => array(
				'rule'    => 'alphaNumeric',
			 ),
			'Minimum length of 4 characters' => array(
				'rule'    => array('minLength', 4),
			),
			'This username is already taken' => array(
				'rule'    => 'isUnique',
			),
		),
        'password' => array(
			'Minimum length of 6 characters' => array(
				'rule'    => array('minLength', 6),
			),
        ),
        'email' => 'email',
		'city' => array(
            'rule'    => 'notEmpty',
		),
		'country' => array(
            'rule'    => 'notEmpty',
		),
        'role' => array(
            'valid' => array(
                'rule' => array('inList', array('admin', 'citizen')),
                'message' => 'Please enter a valid role',
                'allowEmpty' => false
            )
        )
    );
	
	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		
//		debug($this->data);
		return true;
	}
}