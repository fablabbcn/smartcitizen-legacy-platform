<?php
App::uses('AppModel', 'Model');

/**
 * Contact Model
 *
 * @property Contact $Contact
 */
class Contactform extends AppModel {

    public $_schema = array(
        'Name' => array('type' => 'string' , 'null' => false, 'default' => '', 'length' => '50'),
        'Mail' => array('type' => 'string' , 'null' => false, 'default' => '', 'length' => '80'),
        'Message' => array('type' => 'text' , 'null' => false, 'default' => ''),
    );

    public $useTable = false;

    public $validate = array(
	'Name' => array(
	    'notempty' => array(
		'rule' => array('notempty'),
		'required' => true
	    )
	),
	'Mail' => array(
	    'email' => array(
		'rule' => array('email'),
		'required' => true
	    )
	),
	'Message' => array(
	    'notempty' => array(
		'rule' => array('notempty'),
		'required' => true
	    )
	),
    );

    public function beforeValidate($options = array()) {
	parent::beforeValidate($options);

	$this->validate['Name']['notempty']['message'] = __d('contactform', 'please insert your name');
	$this->validate['Mail']['email']['message'] = __d('contactform', 'please insert your email address');
	$this->validate['Message']['notempty']['message'] = __d('contactform', 'please enter your message');
    }


}