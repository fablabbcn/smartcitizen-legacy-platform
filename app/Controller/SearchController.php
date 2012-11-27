<?php
// app/Controller/SearchController.php
class SearchController extends AppController {

	public $helpers = array('Html', 'Form', 'Session','Text');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('search'); // Letting users register themselves
		$this->Auth->allow('updates'); // Letting users register themselves
	}

	public function search() {
		
		if(isset($this->params->query['keyword'])){
			$keyword = $this->params->query['keyword'];
		}else{
			$keyword=false;
		}
		$this->set('keyword',$keyword);
		
		$cond = false;
		$data=array();
		$this->loadModel('Feed');
		if($keyword){
			$feeds = $this->Feed->search($keyword);
		}else
			$feeds = $this->Feed->find('all', array('limit' => 10));
		
		$this->loadModel('User');
		if($keyword){
			$cond = array('OR'=>array('User.username LIKE' => '%'.$keyword .'%'));
			$users = $this->User->find('all',array('conditions'=>$cond,'limit' => 10));
		}else
			$users = $this->User->find('all', array('limit' => 10));
		
		$this->loadModel('Post');
		if($keyword) {
			$cond = array('OR'=>array('Post.title LIKE' => '%'.$keyword .'%','Post.body LIKE' => '%'.$keyword .'%'));
			$posts = $this->Post->find('all',array('conditions'=>$cond,'limit' => 10));
		}else
			$posts = $this->Post->find('all', array('limit' => 10));
		
		$this->loadModel('Media');
		if($keyword){
			$cond = array('OR'=>array('Media.file LIKE' => '%'.$keyword .'%'));
			$medias = $this->Media->find('all',array('conditions'=>$cond,'limit' => 10));
		}else
			$medias = $this->Media->find('all', array('limit' => 10));
		
		
		$this->set(array('feeds'=>$feeds, 'users'=>$users, 'posts'=>$posts, 'medias'=>$medias));
	}
	
	public function updates() {
		
		$cond = false;
		$datass=array();
		$this->loadModel('Feed');
		$datass['Feed']= $this->Feed->find('all', array('limit' => 10,'order' => 'Feed.created'));
		
		$this->loadModel('User');
		$datass['User']= $this->User->find('all', array('limit' => 10,'order' => 'User.created'));
		
		$this->loadModel('Post');
		$datass['Post']= $this->Post->find('all', array('limit' => 10,'order' => 'Post.created'));
		
		$this->loadModel('Media');
		$datass['Media']= $this->Media->find('all', array('limit' => 10,'order' => 'Media.created'));
		
		
		foreach($datass as $model=>$datas){
			foreach($datas as $k=>$data){
				$updates[strtotime($data[$model]['created'])]=$data;
				$updates[strtotime($data[$model]['created'])]['model']=$model;
			}
		}
		krsort($updates);
		//debug($updates);
		if ($this->RequestHandler->isRss() ) {
			return $this->set(compact($updates));
		}
		
		$this->set(array('data'=>array('Update'=>$updates)));
	}
}