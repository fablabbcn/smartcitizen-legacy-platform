<?php

App::uses('CakeEmail', 'Network/Email');
App::uses('Sanitize', 'Utility');

class ContactformController extends AppController {

    public $helpers = array('Form');
    public $components = array('Email', 'Auth');

    public $uses = array('Contactform.Contactform');

    public function beforeFilter() {
	parent::beforeFilter();
	$this->Auth->allow('show');
	$this->cacheAction = false;
    }

    public function show() {
	if($this->request->is('post')) {
	    $email = new CakeEmail();
	    try {
    	    $email->config('contactform');
	    } catch(Exception $e) {
    	    echo 'Config in email.php not found';
    	    exit;
	    }

	    $this->Contactform->set($this->request->data['Contactform']);

	    if($this->Contactform->validates()) {
		$data = $this->request->data['Contactform'];

		$email->to(Sanitize::clean($data['Mail']))
		  ->subject(__d('contactform', 'contact form request'))
		  ->send(
			  __d('contactform', 'name').': '.Sanitize::clean($data['Name'])."\n".
			  __d('contactform', 'email').': '.Sanitize::clean($data['Mail'])."\n\n".
			  __d('contactform', 'message').":\n".
			  Sanitize::html($data['Message'])."\n\n".
			  "----------------------------\n".
			  __d('contactform', 'sent from').' '.Router::url('/', true)
		    );

		$this->Session->setFlash(__d('contactform', 'contact form was submitted successfully'), '', array('status' => 'success'));
		$this->redirect('/');
	    } else {
		// throw errors from model
	    }

	}

	$this->set('title_for_layout', __d('contactform', 'contact form'));
    }

}