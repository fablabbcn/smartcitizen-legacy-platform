<?php
// app/Controller/UsersController.php


class FeedsController extends AppController {
	public $helpers = array('Html', 'Form', 'Session','Text');
	public $components = array(
		'Apis.Oauth' => array(
			'cosm'
		)
	);
	// Everybody can see feed
	public function beforeFilter() {
        $this->Auth->allow('index', 'view', 'test','configure');
    }
	public function isAuthorized($user) {
		// All registered users can add a feed
		if (in_array($this->action, array('add'))) {
			return true;
		}

		// The owner can edit and delete the feeds he own
		if (in_array($this->action, array('edit', 'configure', 'delete'))) {
			$feedId = $this->request->params['pass'][0];
			if ($this->Feed->isOwnedBy($feedId, $user['id'])) {
				return true;
			}
		}

		return parent::isAuthorized($user);
	}
	
    public function index() {
        $this->set('feeds', $this->Feed->find('all'));
    }
	
	public function view($id = null) {
		$this->Feed->id = $id;
/*        if (!$this->Feed->exists()) {
            throw new NotFoundException(__('Invalid sensor'));
        }
*/
		$data=$this->Feed->read(null, $id);
        $this->set('data', $data);
		if ($this->Auth->loggedIn() AND ($this->Auth->user('id') === $data['User']['id'] OR $this->Auth->user('role') === 'admin')) {
			$this->set('actions',array('edit','configure','delete'));
		}
    }
	
/* An alternative will be to use the php library from pachubeAPI :	
    public function view($id = null) {
        App::import('Vendor', 'PachubeAPI');
		$pachube = new PachubeAPI("v3anu3yj1aadeAP-S5c7lXJEiqCSAKxBQ0J3K2F1RFFkOD0g");

		$this->set('feed', json_decode($pachube->getFeed("json", $id), true));
    }
*/
	public function add() {
        if ($this->request->is('post')) {
			//add user id (for ownership)
			$this->request->data['Feed']['user_id'] = $this->Auth->user('id');
			$this->loadModel('User');
			$this->User->id = $this->Auth->user('id');
			$data = $this->User->read(null,$this->Auth->user('id'));
			$this->request->data['Feed']['cosm_token'] = $data['User']['cosm_token'];
//            debug($this->request->data['Feed']['cosm_token']);
			$this->Feed->create();
            if ($this->Feed->save($this->request->data)) {
				debug($this->Feed->Cosm->id);
                $this->Session->setFlash('Your feed has been created.');
                $this->redirect(array('action' => 'view',$this->Feed->Cosm->id));
            } else {
                $this->Session->setFlash('Unable to add your feed.');
				$this->Oauth->useDbConfig = 'cosm';
				$this->Oauth->connect();
            }
        }
    }
	
	public function edit($id = null) {
		$this->Feed->id = $id;
		if ($this->request->is('get')) {
			$this->request->data = $this->Feed->read(null,$id);
		} else {
			if ($this->Feed->save($this->request->data)) {
				$this->Session->setFlash('Your feed has been updated.');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Unable to update your feed.');
			}
		}
	}
	
	public function configure($id = null) {
		$this->Feed->id = $id;
		$data=$this->Feed->read(null, $id);
        $this->set('data', $data);
	}
	
	public function configureauto($id = null) {
		$this->Feed->id = $id;
		$data=$this->Feed->read(null, $id);
        $this->set('data', $data);
	}
	
	public function delete($id) {
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}
		if ($this->Feed->delete($id)) {
			$this->Session->setFlash('The feed with id: ' . $id . ' has been deleted.');
			$this->redirect(array('action' => 'index'));
		}else{
			$this->Session->setFlash('Function not yet available...');
			$this->redirect(array('action' => 'view',$id));
		}
	}
	
	public function test() {
//		$this->set('result' = ClassRegistry::init('Feed')->save(array('Feed' => array('text' => 'Hello World!')));
	}
	
}