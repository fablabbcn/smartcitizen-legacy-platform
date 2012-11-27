<?php

App::uses('DataSource', 'Model/Datasource');
App::uses('Xml', 'Utility');
App::uses('View', 'View');

/**
 * DataSource for interacting with REST APIs
 *
 * @author Neil Crookes <neil@neilcrookes.com>
 * @link http://www.neilcrookes.com
 * @copyright (c) 2010 Neil Crookes
 * @license MIT License - http://www.opensource.org/licenses/mit-license.php
 */
class RestSource extends DataSource {

/**
 * The description of this data source
 *
 * @var string
 */
	public $description = 'Rest DataSource';

/**
 * Instance of CakePHP core HttpSocket class
 *
 * @var HttpSocket
 */
	public $Http = null;

/**
 * Time the last request took
 *
 * @var integer
 */
	public $took = null;

/**
 * Request count.
 *
 * @var integer
 */
	protected $_requestCnt = 0;

/**
 * Total duration of all request.
 *
 * @var integer
 */
	protected $_requestTime = null;

/**
 * Log of request executed by this DataSource
 *
 * @var array
 */
	protected $_requestLog = array();

/**
 * Maximum number of items in request log
 *
 * This is to prevent query log taking over too much memory.
 *
 * @var integer Maximum number of request in the request log.
 */
	protected $_requestLogMax = 200;

/**
 * Request log limit per entry in bytes
 *
 * @var integer Request log limit per entry in bytes
 */
	protected $_requestLogLimitBytes = 256;

/**
 * Loads HttpSocket class
 *
 * @param array $config
 * @param HttpSocket $Http
 */
	public function __construct($config, $Http = null) {
		parent::__construct($config);
		if (!$Http) {
			App::uses('HttpSocket', 'Network/Http');
			$Http = new HttpSocket();
		}
		$this->Http = $Http;
	}

/**
 * Sets method = POST in request if not already set
 *
 * @param AppModel $model
 * @param array $fields Unused
 * @param array $values Unused
 */
	public function create($model, $fields = null, $values = null) {
		$model->request = array_merge(array('method' => 'POST'), $model->request);
		return $this->request($model);
	}

/**
 * Sets method = GET in request if not already set
 *
 * @param AppModel $model
 * @param array $queryData Unused
 */
	public function read($model, $queryData = array()) {
		$model->request = array_merge(array('method' => 'GET'), $model->request);
		return $this->request($model);
	}

/**
 * Sets method = PUT in request if not already set
 *
 * @param AppModel $model
 * @param array $fields Unused
 * @param array $values Unused
 */
	public function update($model, $fields = null, $values = null) {
		$model->request = array_merge(array('method' => 'PUT'), $model->request);
		return $this->request($model);
	}

/**
 * Sets method = DELETE in request if not already set
 *
 * @param AppModel $model
 * @param mixed $id Unused
 */
	public function delete($model, $id = null) {
		$model->request = array_merge(array('method' => 'DELETE'), $model->request);
		return $this->request($model);
	}

/**
 * Issues request and returns response as an array decoded according to the
 * response's content type if the response code is 200, else triggers the
 * $model->onError() method (if it exists) and finally returns false.
 *
 * @param mixed $model Either a CakePHP model with a request property, or an
 * array in the format expected by HttpSocket::request or a string which is a
 * URI.
 * @return mixed The response or false
 */
	public function request($model) {
		if (is_object($model)) {
			$request = $model->request;
		} elseif (is_array($model)) {
			$request = $model;
		} elseif (is_string($model)) {
			$request = array('uri' => $model);
		}
		$log = isset($request['log']) ? $request['log'] : Configure::read('debug') > 1;

		// Remove unwanted elements from request array
		$request = array_intersect_key($request, $this->Http->request);

		$timerStart = microtime(true);
		// Issues request
		$response = $this->Http->request($request);
		/* @var $response HttpResponse */

		if ($log) {
			// Record log
			$this->took = round(microtime(true) - $timerStart, 3) * 1000;
			$this->logRequest($request, $response);
		}

		// Get content type header
		$contentType = $this->Http->response['header']['Content-Type'];

		// Extract content type from content type header
		if (preg_match('/^([a-z0-9\/\+]+);\s*charset=([a-z0-9\-]+)/i', $contentType, $matches)) {
			$contentType = $matches[1];
			$charset = $matches[2];
		}

		// Decode response according to content type
		switch ($contentType) {
			case 'application/xml':
			case 'application/atom+xml':
			case 'application/rss+xml':
				// If making multiple requests that return xml, I found that using the
				// same Xml object with Xml::load() to load new responses did not work,
				// consequently it is necessary to create a whole new instance of the
				// Xml class. This can use a lot of memory so we have to manually
				// garbage collect the Xml object when we've finished with it, i.e. got
				// it to transform the xml string response into a php array.
				$Xml = Xml::build($response->body);
				$response = Xml::toArray($Xml); // Send false to get separate elements
				unset($Xml);
				break;
			case 'application/json':
			case 'text/javascript':
				$response = json_decode($response->body, true);
				break;
		}

		if (is_object($model)) {
			$model->response = $response;
		}

		// Check response status code for success or failure
		if (substr($this->Http->response['status']['code'], 0, 1) != 2) {
			if (is_object($model) && method_exists($model, 'onError')) {
				$model->onError();
			}
			return false;
		}

		return $response;
	}

/**
 * Log given HTTP request.
 *
 * @param array $request request data
 * @param array $response response data
 * @return void
 */
	public function logRequest($request, $response) {
		$this->_requestCnt++;
		$this->_requestTime += $this->took;
		$separator = '[**snip**]';
		$maxlength = $this->_requestLogLimitBytes - strlen($separator);

		$requestUri = $this->Http->url($request['uri']);
		$requestBody = $this->Http->request['body'];
		$responseBody = $this->Http->response['body'];
		if (strlen($requestBody) > $this->_requestLogLimitBytes) {
			$requestBody = substr_replace($requestBody, $separator, $maxlength / 2, strlen($requestBody) - $maxlength);
		}
		if (strlen($responseBody) > $this->_requestLogLimitBytes) {
			$responseBody = substr_replace($responseBody, $separator, $maxlength / 2, strlen($responseBody) - $maxlength);
		}
		$this->_requestLog[] = array(
			'request_method' => $this->Http->request['method'],
			'request_uri' => $requestUri,
			'request_body' => h($requestBody),
			'response_code' => $this->Http->response['status']['code'],
			'response_type' => $this->Http->response['header']['Content-Type'],
			'response_size' => strlen($this->Http->response['body']),
			'response_body' => h($responseBody),
			'query' => $this->Http->request['method'] . ' ' . $requestUri,
			'params' => '',
			'error' => '',
			'affected' => '',
			'numRows' => strlen($this->Http->response['body']),
			'took' => $this->took
		);
		if (count($this->_requestLog) > $this->_requestLogMax) {
			array_pop($this->_requestLog);
		}
	}

/**
 * Get the request log as an array.
 *
 * @param boolean $sorted Get the request sorted by time taken, defaults to false.
 * @param boolean $clear If True the existing log will cleared.
 * @return array Array of queries run as an array
 */
	public function getLog($sorted = false, $clear = true) {
		if ($sorted) {
			$log = sortByKey($this->_requestLog, 'took', 'desc', SORT_NUMERIC);
		} else {
			$log = $this->_requestLog;
		}
		if ($clear) {
			$this->_requestLog = array();
		}
		return array('log' => $log, 'count' => $this->_requestCnt, 'time' => $this->_requestTime);
	}

/**
 * Outputs the contents of the queries log. If in a non-CLI environment the sql_log element
 * will be rendered and output.  If in a CLI environment, a plain text log is generated.
 *
 * @param boolean $sorted Get the queries sorted by time taken, defaults to false.
 * @return void
 */
	public function showLog($sorted = false) {
		$log = $this->getLog($sorted, false);
		if (empty($log['log'])) {
			return;
		}
		if (PHP_SAPI != 'cli') {
			$controller = null;
			$View = new View($controller, false);
			$View->set('logs', array($this->configKeyName => $log));
			echo $View->element('request_dump', array('_forced_from_dbo_' => true), array('plugin' => 'Rest'));
		} else {
			foreach ($log['log'] as $k => $i) {
				print (($k + 1) . ". {$i['request_uri']}\n");
			}
		}
	}

}
