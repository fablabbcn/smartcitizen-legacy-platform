<?php
/**
* Cosm Driver for Apis Source
* 
* Makes usage of the Apis plugin by Proloser
*
* @package Cosm Datasource
* @author Adrelanex
* @version 0.0.1
**/
App::uses('ApisSource', 'Apis.Model/Datasource');
class Cosm extends ApisSource {

	public $_schema = array(
		'feeds' => array(
			'id' => array(
				'type' => 'integer',
				'null' => true,
				'key' => 'primary',
				'length' => 11,
			),
			'title' => array(
				'type' => 'string',
				'null' => true,
				'key' => 'primary',
				'length' => 140,
			),
			'status' => array(
				'type' => 'string',
				'null' => true,
				'key' => 'primary',
				'length' => 140,
			),
		),
	);

	/**
	 * The description of this data source
	 *
	 * @var string
	 */
	public $description = 'Cosm DataSource';

	/**
	 * Set the datasource to use OAuth
	 *
	 * @param array $config
	 * @param HttpSocket $Http
	 */
	public function __construct($config) {
		$config['method'] = 'OAuth';
		parent::__construct($config);
	}

	public function describe($model) {
		return $this->_schema['feeds'];
	}

	/**
	 * Stores the queryData so that the tokens can be substituted just before requesting
	 *
	 * @param string $model 
	 * @param string $queryData 
	 * @return mixed $data
	 * @author Dean Sofer
	 */
	public function read(&$model, $queryData = array()) {
		$this->tokens = $queryData['conditions'];
		return parent::read($model, $queryData);
	}

	/**
	 * Supplement the request object with github-specific data
	 *
	 * @param array $request 
	 * @return array $response
	 */
	public function beforeRequest(&$model, $request) {
/*
		$request['uri']['scheme'] = 'https';
		// Attempted fix for 3.0
		if (strtoupper($request['method']) === 'GET' && !empty($this->config['access_token'])) {
			$request['uri']['query']['access_token'] = $this->config['access_token'];
		}
		$request['uri']['path'] .= '.' . $this->options['format'];
*/
		return $request;
		
	}
}