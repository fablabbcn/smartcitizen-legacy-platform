<?php
// app/Controller/UsersController.php
class UsersController extends AppController {

	public $helpers = array('Html', 'Form', 'Session','Text');
	public $components = array(
		'Apis.Oauth' => array(
			'cosm'
		)
	);
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('add'); // Letting users register themselves
	}
	
	public function isAuthorized($user) {
		// All registered users can see their profile and logout
		if (in_array($this->action, array('dashboard', 'logout', 'cosm_connect', 'cosm_callback', 'cosm_connect', 'email_confirm'))) {
			return true;
		}

		// The owner can edit and delete his profile
		if (in_array($this->action, array('edit', 'delete'))) {
			$requestedId = $this->request->params['pass'][0];
			if ($requestedId == $user['id']) {
				return true;
			}
		}

		return parent::isAuthorized($user);
	}	
	
	public function login() {
//		debug($id);
		$id=$this->Auth->user('id');
		if ($id) {
			$this->redirect(array('action' => 'view',$id));
		}elseif ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('Invalid username or password, try again'));
			}
		}
	}

	public function logout() {
		$this->redirect($this->Auth->logout());
	}

    public function index() {
//        $this->User->recursive = 0;
		$users = $this->User->find('all',array(
//			'fields' => array('Post.id','Post.title','Post.body','Thumb.file'),
//			'contain' => 'Thumb'
		));
        $this->set('users', $users);
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
		$data=$this->User->read(null, $id);
//Hack for loading each feed from cosm (!to clean !)
		$this->loadModel('Feed');
		foreach($data['Feed'] as $k=>$feed)
		{
			$feedData=$this->Feed->read(null,$feed['cosm_id']);
			$data['Feed'][$k]=$feedData['Feed'];
		}
//end of hack
        $this->set('data',$data);
		
		if ($this->Auth->loggedIn() AND $this->Auth->user('id') === $data['User']['id']) {
			$this->set('actions',array('dashboard','edit'));
		}
//For super Admin 
/*
		if ($this->Auth->loggedIn() AND $this->Auth->user('role') === 'admin') {
			$this->set('actions',array('dashboard','edit','delete'));
		}
*/
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
			if(!isset($this->request->data['User']['role']))
				$this->request->data['User']['role']='citizen';				
            if ($this->User->save($this->request->data)) {
				if($this->request->data['Media']['file']['error']!=0 || $this->request->data['Media']['file']['size']==0){
						$this->Session->setFlash(__('User created with success, Please login to continue'));
						$this->Auth->login($this->User->id);
						$this->redirect(array('action' => 'view',$this->User->id));
				}else{
					$this->loadModel('Media.Media');
					if($this->Media->save(array(
						'ref'    => 'User',
						'ref_id' => $this->User->id,
						'file'   => $this->request->data['Media']['file']
					))) { 
						$this->User->saveField('media_id',$this->Media->id);
						$this->Session->setFlash(__('User created with success'));
						if(!$this->Auth->login()){
							$this->Session->setFlash(__('login failed. please enter your username and password to login'));
							$this->redirect(array('action' => 'login'));
						}else{
							$this->redirect(array('action' => 'dashboard'));
						}
						
					}else { 
						$this->Session->setFlash(__('The user image could not be saved. Please, try again.'));
					}
				}
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
    }

    public function edit($id = null) {
		$this->helpers[] ='Media.Uploader';
        $this->User->id = $id;
        if (!$this->User->exists()) {
            $this->Session->setFlash(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                $this->redirect(array('action' => 'view',$this->User->id));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'));
//       $this->redirect(array('action' => 'index'));
    }
	
	public function cosm_connect() {
		$this->Oauth->useDbConfig = 'cosm';
        $this->Oauth->connect();
    }
	
	public function cosm_callback() {
		$this->User->set('id',$this->Auth->user('id'));
		$this->Oauth->useDbConfig = 'cosm';
		if ($this->Oauth->callback()){
            $this->Session->setFlash(__('Successfully connected to Cosm'));
			$this->User->set('id',$this->Auth->user('id'));
			$this->User->set('cosm_token',$this->Session->read('OAuth.cosm.access_token'));
			$this->User->set('cosm_user',$this->Session->read('OAuth.cosm.user'));
			$this->User->save();
		}else{
            $this->Session->setFlash(__('Impossible to connect to Cosm'));
		}
		$this->redirect(array('action' => 'dashboard'));
    }
	
	public function dashboard() {
		$id=$this->Auth->user('id');
		$data=$this->User->read(null, $id);
        $this->set('data',$data);
		
		$todo=array();
		if(!$data['User']['media_id']){
			$todo[]='photo';
		}
		if(!$data['User']['email_verified']){
			$todo[]='email';
		}else{
//			if(empty($data['Post'])){
				$todo[]='post';
//			}
		}
		if(!$data['User']['cosm_token']){
			$todo[]='cosm';
		}else{
			//if test connection to COSM api with token.
//			if(empty($data['Feed'])){
				$todo[]='feed';
//			}
		}
		
		$this->set('todo',$todo);
	}
	
	public function email_confirm() {
		$id=$this->Auth->user('id');
		$data=$this->User->read(null, $id);
		$code=($id+277)*277*277;
		
		App::uses('CakeEmail', 'Network/Email');
		
		$email = new CakeEmail('default');
		$email->viewVars(array('code' => $code));
		$email->template('welcome') //	View/Emails/text/welcome.ctp
		->emailFormat('both')
		->subject('Welcome to the smartcitizen network')
		->to($data['User']['email'])
		->send();

//		CakeEmail::deliver($data['User']['email'], 'welcome', $code, array('from' => 'server@smartcitizen.me'));

		$this->Session->setFlash(__('Mail sent. Check your inbox & spam folder'));
		$this->redirect(array('action' => 'dashboard'));
	}
	
	public function confirm_email($code = null) {
		$id=$code/277/277-277;
		$this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid code'));
        }
		$this->User->set('email_verified', true);
		$this->User->save();
		$this->Session->setFlash(__('Mail confirmed.'));
		$this->redirect(array('action' => 'dashboard'));
	}
	
}