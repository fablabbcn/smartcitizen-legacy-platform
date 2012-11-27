<?php
class PostsController extends AppController {
    public $helpers = array('Html', 'Form', 'Session','Text');
//    public $components = array('Session');
public $components = array('RequestHandler');
			
	public function isAuthorized($user) {
		// All registered users can add posts
		if ($this->action === 'add') {
			return true;
		}

		// The owner of a post can edit and delete it
		if (in_array($this->action, array('edit', 'delete'))) {
			$postId = $this->request->params['pass'][0];
			if ($this->Post->isOwnedBy($postId, $user['id'])) {
				return true;
			}
		}

		return parent::isAuthorized($user);
	}	
	
    public function index() {
		if ($this->RequestHandler->isRss() ) {
			$posts = $this->Post->find('all', array('limit' => 20, 'order' => 'Post.created'));
			return $this->set(compact('posts'));
		}
		
        $this->set('posts', $this->Post->find('all'));
    }
	
    public function view($id = null) {
        $this->Post->id = $id;
        $this->set('data', $this->Post->read());
    }
	
	public function add() {
        if ($this->request->is('post')) {
			$this->request->data['Post']['user_id'] = $this->Auth->user('id');
            $this->Post->create();
            if ($this->Post->save($this->request->data)) {
                $this->Session->setFlash('Your post has been saved.');
                $this->redirect(array('action' => 'edit', $this->Post->id));
            } else {
                $this->Session->setFlash('Unable to add your post.');
            }
        }
    }
	
	public function edit($id = null) {
		$this->helpers[] ='Media.Uploader';
		$this->Post->id = $id;
		if ($this->request->is('get')) {
			$this->request->data = $this->Post->read();
			$this->set('topics', $this->Post->Topic->find('list'));
		} else {
			if ($this->Post->save($this->request->data)) {
				$this->Session->setFlash('Your post has been updated.');
				$this->redirect(array('action' => 'view',$this->Post->id));
			} else {
				$this->Session->setFlash('Unable to update your post.');
			}
		}
	}
	
	public function delete($id) {
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}
		if ($this->Post->delete($id)) {
			$this->Session->setFlash('The post with id: ' . $id . ' has been deleted.');
			$this->redirect(array('action' => 'index'));
		}
	}
	

}