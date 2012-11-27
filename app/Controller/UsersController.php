<?php
// app/Controller/UsersController.php
class UsersController extends AppController {

	public $helpers = array('Html', 'Form', 'Session','Text');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('add'); // Letting users register themselves
	}
	
	public function isAuthorized($user) {
		// All registered users can see their profile and logout
		if (in_array($this->action, array('me', 'logout'))) {
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
		
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
			if(!isset($this->request->data['User']['role']))
				$this->request->data['User']['role']='citizen';				
            if ($this->User->save($this->request->data)) {
				if($this->request->data['Media']['file']['error']!=0 || $this->request->data['Media']['file']['size']==0){
						$this->Session->setFlash(__('User created with success (without image)'));
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
						$this->Auth->login($this->User);
						$this->redirect(array('action' => 'view',$this->User->id));
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
            throw new NotFoundException(__('Invalid user'));
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
	
	public function me() {
		$id=$this->Auth->user('id');
        $this->redirect(array('action' => 'view',$id));
	}
}