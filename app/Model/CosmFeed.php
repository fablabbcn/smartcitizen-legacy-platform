<?php
class CosmFeed extends AppModel {
    public $name = 'CosmFeed';
	public $useDbConfig = 'cosm';
	protected $_schema = array(
		'title' => array(
			'type' => 'string',
			'length' => 30
		),
		'tags' => array(
			'type' => 'string',
			'length' => 30
		),
		'description' => array('type' => 'text')
	);
	
	public $defaultFeed = array(
		'title' => null,
		'tags' => array(),
		'description' => null,
		'location' => array(
			'exposure' => null,
		),
		'datastreams' => array(),
	);
	
	public $defaultDatastream = array(
			"id" => null,
			"at" => null,
			"current_value" => null,
			'unit' => array(
				'symbol'=>null,
			),
		);
	
	
	
	public function find($type = 'first', $params = array()) {
		$this->request = array(
			'uri' => array(
				'host' => 'api.cosm.com',
//				'path' => 'v2/feeds.json?per_page=10&user=smartcitizen' //&status=live
				'path' => '/v2/feeds.json?lat=41.38299120166604&lon=2.171001434326172&distance=29.296875&tag=temperature&status=live'
			),
			'header' => array(
				'X-ApiKey' => Configure::read('cosm.apikey')
			),
		);
		$return = parent::find($type, $params);
		if(!$return)
		return;
		$feeds=array();
		foreach ($return['results'] as $key=>$feed){
			$feeds[$key]=$this->_format($feed);
		}
		return $feeds;
	}
	
	public function read($fields = null , $id= null) {
		$this->request = array(
			'uri' => array(
				'host' => 'api.cosm.com',
				'path' => 'v2/feeds/'.$id.'.json?duration=15hours&interval=3600'
			),
			'header' => array(
				'X-ApiKey' => Configure::read('cosm.apikey')
			),
		);
		$feed= parent::find('all');
		$feed=$this->_format($feed);
//		debug($feed);
		return $feed;
	}
	
	public function search($keyword){
		$this->request = array(
			'uri' => array(
				'host' => 'api.cosm.com',
				'path' => 'v2/feeds.json?per_page=10&user=smartcitizen&q='.urlencode($keyword) //&status=live
			),
			'header' => array(
				'X-ApiKey' => Configure::read('cosm.apikey')
			),
		);
		$return= parent::find('all');
		if(!$return)
		return;
		$feeds=array();
		foreach ($return['results'] as $key=>$feed){
			$feeds[$key]['Feed']=$this->_format($feed);
		}
		return $feeds;
	}
	
	
	
	private function _format($feed) {
		if(!empty($feed['location']))
			$feed['location'] = array_merge($this->defaultFeed['location'],$feed['location']);
		$feed = array_merge($this->defaultFeed,$feed);
		foreach ($feed['datastreams'] as $k=>$v){
			$feed['datastreams'][$k] = array_merge($this->defaultDatastream,$v);
			if(!empty($v['unit']))
				$feed['datastreams'][$k]['unit'] = array_merge($this->defaultDatastream['unit'],$v['unit']);
		}
		$feed['cosm_id']=$feed['id'];
		return $feed;
	}
	
	public function save($data = null, $validate = true, $fieldList = array()) {
		if(!is_numeric($data['CosmFeed']['latitude']))
			$data['CosmFeed']['latitude']=0;
		if(!is_numeric($data['CosmFeed']['longitude']))
			$data['CosmFeed']['longitude']=0;
			
		$this->request = array(
			'uri' => array(
				'host' => 'api.cosm.com',
				'path' => 'v2/feeds.json'
			),
			'body' => 
			'{
			  "title":'.json_encode($data['CosmFeed']['title']).',
			  "description": '.json_encode($data['CosmFeed']['description']).',
			  "version":"1.0.0",
			  "tags":["smartcitizen","test"],
			  "location":{
				"disposition":"fixed",
				"name":"office",
				"lat":'.$data['CosmFeed']['latitude'].',
				"lon":'.$data['CosmFeed']['longitude'].',
				"exposure":"outdoor",
				"domain":"physical"
			  },
			  "datastreams":[
				{
				  "id":"0",
				  "tags":["light"],
				  "unit": {
					"label": "Relative light",
					"symbol": "%"
				  }
				},
				{
				  "id":"1",
				  "tags":["temperature"],
				  "unit": {
					"label": "Celsius",
					"symbol": "°C"
				  }
				},
				{
				  "id":"2",
				  "tags":["humidity"],
				  "unit": {
					"label": "Relative Humidity",
					"symbol": "%"
				  }
				},
				{
				  "id":"3",
				  "tags":["sound"],
				  "unit": {
					"label": "Decibels",
					"symbol": "dB"
				  }
				},
				{
				  "id":"4",
				  "tags":["co2"],
				  "unit": {
					"label": "Parts per million",
					"symbol": "ppm"
				  }
				}
			  ]
			}',
			'header' => array(
				'X-ApiKey' => Configure::read('cosm.apikey')
			),
		);
		if (isset($data['CosmFeed']['id'])){ //update !!!
			$this->request['uri']['path'].='v2/feeds/'.$data['CosmFeed']['id'].'.json';
		}
		$return = parent::save($data, $validate, $fieldList);
		$resultLocation = explode('/',$this->response->headers['Location']);
		$this->id = $resultLocation[5];
//		debug($this->id);
		return  $return;
	}
	
	public function generateKey($label = false) {
		if(empty($label))
			$label=$this->id;
		$this->request = array(
			'uri' => array(
				'host' => 'api.cosm.com',
				'path' => 'v2/keys.json'
			),
			'body' => 
			'{
			  "key":{
				"label":"'.$this->id.'",
				"private_access": true,
				"permissions":[
				  {
					"access_methods":["get", "put", "post", "delete"] ,
					"resources": [
					  {
						"feed_id": "'.$this->id.'"
					  }
					]
				  }
				]
			  }
			}',
			'header' => array(
				'X-ApiKey' => Configure::read('cosm.apikey')
			),
			'method' => 'POST',
		);
		
		$db = $this->getDataSource();
		if(!$db->request($this)){
			return false;
		}
		
		$resultLocation = explode('/',$this->response->headers['Location']);
		$this->key = $resultLocation[6];
//		debug($this->key);
		return true;
	}
	
	public function delete($id = null, $cascade = true) {
		$this->request = array(
			'uri' => array(
				'host' => 'api.cosm.com',
				'path' => 'v2/feeds/'.$id.'.json'
			),
			'header' => array(
				'X-ApiKey' => Configure::read('cosm.apikey')
			),
		);
		return  parent::delete($id,$cascade);
	}
	
	public function onError(){
//		throw new InternalErrorException($this->response['errors'],402);
	}
	
	
/*
To implement in a RestSource class
User::find('all')               == GET    http://api.example.com/users.json
User::read(null, $id)           == GET    http://api.example.com/users/$id.json
User::save()                    == POST   http://api.example.com/users.json
User::save(array('id' => $id))  == PUT    http://api.example.com/users/$id.json
User::delete($id)               == DELETE http://api.example.com/users/$id.json
*/






//SPECIAL HACK : overwrite setSource function that was causing _shema set to null.

	public function setSource($tableName) {
		$this->setDataSource($this->useDbConfig);
		$db = ConnectionManager::getDataSource($this->useDbConfig);
		$db->cacheSources = ($this->cacheSources && $db->cacheSources);

		if (method_exists($db, 'listSources')) {
			$sources = $db->listSources();
			if (is_array($sources) && !in_array(strtolower($this->tablePrefix . $tableName), array_map('strtolower', $sources))) {
				throw new MissingTableException(array(
					'table' => $this->tablePrefix . $tableName,
					'class' => $this->alias,
					'ds' => $this->useDbConfig,
				));
			}
//			$this->_schema = null;
		}
		$this->table = $this->useTable = $tableName;
		$this->tableToModel[$this->table] = $this->alias;
	}
}