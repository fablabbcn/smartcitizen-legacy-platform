<?php
/*
CREATE TABLE feeds (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11),
    cosm_id INT(11),
    cosm_key INT(11),
    created DATETIME DEFAULT NULL,
    modified DATETIME DEFAULT NULL
);
*/

class Feed extends AppModel {
    public $name = 'Feed';

    public $belongsTo = 'User';
	
	protected $_schema = array(
		'user_id' => array(
			'type' => 'int',
			'length' => 11
		),
		'cosm_id' => array(
			'type' => 'int',
			'length' => 11
		),
		'cosm_key' => array(
			'type' => 'string',
			'length' => 30
		)
	);
	
    public $validate = array(
        'user_id' => array(
            'rule' => 'notEmpty'
        )
    );
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct();
		$this->Cosm = ClassRegistry::init("CosmFeed");
	}
	
	public function find($type = 'first', $params = array()) {
		$return_cosm = $this->Cosm->find($type,$params);
		if(!$return_cosm)
			return;
			
		
		
		foreach($return_cosm as $k=>$v){
			$return_db = parent::find('first',  array('conditions' => array('Feed.cosm_id' => $v['id'])));
//			debug($return_db);
			if($return_db['Feed']){
				//small hack for loading media image sprintf (can slow the request )
				$user=ClassRegistry::init('User');
				$return_db2=$user->read(null, $return_db['Feed']['user_id']);
				$return_db['User']=$return_db2['User'];
				//end hack
				
				$return[$k]=$return_db;
				$return[$k]['Feed']=array_merge($return_db['Feed'],$v);
			}else //let feed that are not stored in our database....
				$return[$k]['Feed']=$v;
		}
		return $return;
	}
	
	public function read($fields = null , $id= null) {
		$return_cosm = $this->Cosm->read($fields, $id);
		$return_db = parent::find('first',  array('conditions' => array('Feed.cosm_id' => $return_cosm ['id'])));
		if($return_db['Feed']){
			
			//small hack for loading media image sprintf
			$user=ClassRegistry::init('User');
			$return_db2=$user->read(null, $return_db['Feed']['user_id']);
			$return_db['User']=$return_db2['User'];
			//end hack
			
			$return=$return_db;
			$return['Feed']=array_merge($return_db['Feed'],$return_cosm);
		}else
			$return['Feed']=$return_cosm;
//		debug($return_db);
		
		return $return;
	}
	
	public function search($keyword){
		return $this->Cosm->search($keyword);
	}
	
	
	
	public function save($data = null, $validate = true, $fieldList = array()) {
		
		//Pass datas to CosmFeed class
		$data['CosmFeed']=$data['Feed'];
		if(!empty($data['Feed']['id'])){ //It's not a new entry but an Update
			$this->read(null, $data['Feed']['id']);
			$data['CosmFeed']['id']=$this->cosm_id;
		}
		
		//Store in Cosm
		if(!$this->Cosm->save($data, $validate, $fieldList)){
			throw new InternalErrorException(__("The feed couldn't be saved on Cosm Server"),502);
			return false;	
		}
		
		//If new, 
		if(empty($data['Feed']['id'])){ 
			//generate an api Key for this feed
			if(!$this->Cosm->generateKey()){
				throw new InternalErrorException(__("The feed couldn't be saved on Cosm Server"),502);
				return false;
			}
			
			//and save it in the db along with the cosm feed id and the data provided in $data.
			$data["Feed"]["cosm_id"]=$this->Cosm->id;
			$data["Feed"]["cosm_key"]=$this->Cosm->key;
			return parent::save($data, $validate, $fieldList);
		}else{
			return true;
		}
	}
	public function generateKey($id){
		if($id===false || $id===null) //It's not a new entry but an Update
			return false;
		$this->read(null, $id);
		if($this->cosm_id==0)
			return false;
		$this->Cosm->id=$this->cosm_id;
		if(!$this->Cosm->generateKey()){
			throw new InternalErrorException(__("The key couldn't be generated on Cosm Server"),502);
			return false;
		}
		
		//and save it in the db along with the cosm feed id and the data provided in $data.
		$data["Feed"]["cosm_id"]=$this->Cosm->id;
		$data["Feed"]["cosm_key"]=$this->Cosm->key;
		return parent::save($data);
	}
	
	public function delete($id = null, $cascade = true) {
		return  $this->Cosm->delete($id);
	}
	
	public function isOwnedBy($feed, $user) {
//		user.cosm_id == feed.user Login
		return $this->field('id', array('id' => $feed, 'user_id' => $user)) === $feed;
	}
}