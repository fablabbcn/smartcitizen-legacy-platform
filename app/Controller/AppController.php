<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $components = array(
		'RequestHandler',
		'Session',
		'Auth' => array(
			'loginRedirect' => array('controller' => 'users', 'action' => 'dashboard'),
			'logoutRedirect' => array('controller' => 'pages', 'action' => 'display', 'home'),
			'authorize' => array('Controller') // Added this line
		),
		'DebugKit.Toolbar' => array(/* array of settings */)
	);
	public $helpers = array('Session');
	
	public function isAuthorized($user) {
		// Admin can access every action
		if (isset($user['role']) && $user['role'] === 'admin') {
			return true;
		}
		
		// Default deny
		//$this->Auth->authError = "This error shows up with the user tries to access a part of the website that is protected.";
		if(isset($user['role']))
			$this->Session->setFlash('You are not allowed to acces this page.');
		else
			$this->Session->setFlash('You need to be authentified to participate. please login');
		return false;
	}
	
    public function beforeFilter() {
        $this->Auth->allow('index', 'view', 'display');
		 if($this->RequestHandler->isAjax()){
			Configure::write('debug', 0);// and forget debug messages
			$this->layout = 'ajax'; //or try with $this->layout = '';
		}
		
		//send loged in user information to the layout.

    }
	
	public function beforeRender() {
	 //  $this->set('currentUser', $this->Auth->user('id'));
			$authUser=array(
				'id'=>$this->Auth->user('id'),
				'username'=>$this->Auth->user('username'),
				'role'=>$this->Auth->user('role')
			);
			$this->set('authUser',$authUser);
	}
	
	
}
