<?php
class TopicsController extends AppController {
    public $helpers = array('Html', 'Form', 'Session','Text');
//    public $components = array('Session');
public $components = array('RequestHandler');
			
	public function isAuthorized($user) {
		//if action != index or view : Only accept admin user (see AppController.php)
		return parent::isAuthorized($user);
	}	
	
    public function index() {
        $this->set('data', $this->Topic->find('all'));
		if ($this->Auth->user('role') == 'admin') {
			$this->set('actions',array('add'));
		}
    }
	
    public function view($id = null) {
        $this->Topic->id = $id;
        $this->set('data', $this->Topic->read());
		if ($this->Auth->user('role')){
			$actions=array();
			if ($this->Auth->user('role') === 'admin') {
				$actions=array('edit','delete');
			}
			//for eveyone registered
			$actions[]=('addPost');
			$this->set('actions',$actions);
		}
	}
	
	public function add() {
        if ($this->request->is('post')) {
            $this->Topic->create();
            if ($this->Topic->save($this->request->data)) {
                $this->Session->setFlash('Your topic has been saved.');
                $this->redirect(array('action' => 'view', $this->Topic->id));
            } else {
                $this->Session->setFlash('Unable to add your topic.');
            }
        }
    }
	
	public function edit($id = null) {
		$this->helpers[] ='Media.Uploader';
		$this->Topic->id = $id;
		if ($this->request->is('get')) {
			$this->request->data = $this->Topic->read();
		} else {
			if ($this->Topic->save($this->request->data)) {
				$this->Session->setFlash('Your topic has been updated.');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Unable to update your topic.');
			}
		}
	}
	
	public function delete($id) {
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}
		if ($this->Topic->delete($id)) {
			$this->Session->setFlash('The topic with id: ' . $id . ' has been deleted.');
			$this->redirect(array('action' => 'index'));
		}
	}
	

}