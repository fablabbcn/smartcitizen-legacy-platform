<?php
/**
CREATE TABLE posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(50),
    body TEXT,
    user_id INT(11),
    topic_id INT(11),
	media_id INT(11),
	approved TINYINT(2),
    created DATETIME DEFAULT NULL,
    modified DATETIME DEFAULT NULL
);
**/
class Post extends AppModel {

	public $belongsTo = array('User','Topic');
	
	public $actsAs = array(
		'Containable',
		'Media.Media' => array(
		  'path' => 'Post/%id-%f'
		 )
	);

    public $validate = array(
        'title' => array(
            'rule' => 'notEmpty'
        ),
        'body' => array(
            'rule' => 'notEmpty'
        )
    );
	
	public function isOwnedBy($post, $user) {
		return $this->field('id', array('id' => $post, 'user_id' => $user)) === $post;
	}
	
	public function find($type = 'first', $params = array()) {
		$return_db = parent::find($type, $params);
		if(isset($return_db['User'])){
			//small hack for loading media image sprintf (can slow the request )
			$user=ClassRegistry::init('User');
			$return_db2=$user->read(null, $return_db['User']['id']);
			$return_db['User']=$return_db2['User'];
			//end hack
		}
		return $return_db;
	}
}	