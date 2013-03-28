<?php
/**
 * Apis DataSource
 *
 * [Short Description]
 *
 * @package default
 * @author Dean Sofer
 **/
App::uses('DataSource', 'Model/Datasource');
class ApisSource extends DataSource {

/**
 * The description of this data source
 *
 * @var string
 */
	public $description = 'Apis DataSource';

/**
 * Holds the datasource configuration
 *
 * @var array
 */
	public $config = array();

	// TODO: Relocate to a dedicated schema file
	public $_schema = array();

/**
 * Instance of CakePHP core HttpSocket class
 *
 * @var HttpSocket
 */
	public $Http = null;

/**
 * Request Logs
 *
 * @var array
 * @access private
 */
	private $__requestLog = array();

/**
 * Request Log limit per entry in bytes
 *
 * @var integer
 * @access protected
 */
	protected $_logLimitBytes = 5000;

/**
 * Holds a configuration map
 *
 * @var array
 */
	public $map = array();

/**
 * API options
 * @var array
 */
	public $options = array(
		'format'    => 'json',
		'ps'		=> '&', // param separator
		'kvs'		=> '=', // key-value separator
	);

/**
 * Loads HttpSocket class
 *
 * @param array $config
 * @param HttpSocket $Http
 */
	public function __construct($config, $Http = null) {
		if (!isset($this->config['database']))
			$this->config['database'] = '';
		// Store the API configuration map
		$name = pluginSplit($config['datasource']);
		if (!$this->map = Configure::read('Apis.' . $name[1])) {
			Configure::load($name[0] . '.' . $name[1]);
			$this->map = Configure::read('Apis.' . $name[1]);
		}

		// Store the HttpSocket reference
		if (!$Http) {
			if (!empty($this->map['oauth']['version'])) {
				if ($this->map['oauth']['version'][0] == 2) {
					$config['method'] = 'OAuthV2';
				} else {
					$config['method'] = 'OAuth';
				}
				App::uses('HttpSocketOauth', 'HttpSocketOauth.Lib');
				$Http = new HttpSocketOauth();
			} else {
				App::uses('HttpSocket', 'Network/Http');
				$Http = new HttpSocket();
			}
		}
		$this->Http = $Http;
		parent::__construct($config);
	}

	public function describe($model) {
		return array();
	}

/**
 * Sends HttpSocket requests. Builds your uri and formats the response too.
 *
 * @param string $params
 * @param array $options
 *		method: get, post, delete, put
 *		data: either in string form: "option1=foo&option2=bar" or as a keyed array: array('option1' => 'foo', 'option2' => 'bar')
 * @return array $response
 * @author Dean Sofer
 */
	public function request(&$model) {
		if (is_object($model)) {
			$request = $model->request;
		} elseif (is_array($model)) {
			$request = $model;
		} elseif (is_string($model)) {
			$request = array('uri' => $model);
		}

		if (isset($this->config['method']) && $this->config['method'] == 'OAuth') {
			$request = $this->addOauth($model, $request);
		} elseif (isset($this->config['method']) && $this->config['method'] == 'OAuthV2') {
			$request = $this->addOauthV2($model, $request);
		}

		if (empty($request['uri']['host'])) {
			$request['uri']['host'] = $this->map['hosts']['rest'];
		}

		if (empty($request['uri']['scheme']) && !empty($this->map['oauth']['scheme'])) {
			$request['uri']['scheme'] = $this->map['oauth']['scheme'];
		}

		// Remove unwanted elements from request array
		$request = array_intersect_key($request, $this->Http->request);

		if (!empty($this->tokens)) {
			$request['uri']['path'] = $this->swapTokens($model, $request['uri']['path'], $this->tokens);
		}

		if (method_exists($this, 'beforeRequest')) {
			$request = $this->beforeRequest($model, $request);
		}

		$model->request = $request;

		$timerStart = microtime(true);

	    // Issues request
	    $response = $this->Http->request($request);

		$timerEnd = microtime(true);

		// Log the request in the query log
		if(Configure::read('debug')) {
			$logText = '';
			foreach(array('request','response') as $logPart) {
				$logTextForThisPart = $this->Http->{$logPart}['raw'];
				if($logPart == 'response') {
					$logTextForThisPart = $logTextForThisPart['response'];
				}
				if(strlen($logTextForThisPart) > $this->_logLimitBytes) {
					$logTextForThisPart = substr($logTextForThisPart, 0, $this->_logLimitBytes).' [ ... truncated ...]';
				}
				$logText .= '---'.strtoupper($logPart)."---\n".$logTextForThisPart."\n\n";
			}
			$took = round(($timerEnd - $timerStart)/1000);
			$newLog = array(
				'query' => $logText,
				'error' => '',
				'affected' => '',
				'numRows' => '',
				'took' => $took,
			);
			$this->__requestLog[] = $newLog;
		}

		$response = $this->decode($response);

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
	 * Supplements a request array with oauth credentials
	 *
	 * @param object $model
	 * @param array $request
	 * @return array $request
	 */
	public function addOauth(&$model, $request) {
		if (!empty($this->config['oauth_token']) && !empty($this->config['oauth_token_secret'])) {
			$request['auth']['method'] = 'OAuth';
			$request['auth']['oauth_consumer_key'] = $this->config['login'];
			$request['auth']['oauth_consumer_secret'] = $this->config['password'];
			if (isset($this->config['oauth_token'])) {
				$request['auth']['oauth_token'] = $this->config['oauth_token'];
			}
			if (isset($this->config['oauth_token_secret'])) {
				$request['auth']['oauth_token_secret'] = $this->config['oauth_token_secret'];
			}
		}
		return $request;
	}

	/**
	 * Supplements a request array with oauth credentials
	 *
	 * @param object $model
	 * @param array $request
	 * @return array $request
	 */
	public function addOauthV2(&$model, $request) {
		if (!empty($this->config['access_token'])) {
			$request['auth']['method'] = 'OAuth';
			$request['auth']['oauth_version'] = '2.0';
			$request['auth']['client_id'] = $this->config['login'];
			$request['auth']['client_secret'] = $this->config['password'];
			if (isset($this->config['access_token'])) {
				$request['auth']['access_token'] = $this->config['access_token'];
			}
		}
		return $request;
	}

	/**
	 * Decodes the response based on the content type
	 *
	 * @param string $response
	 * @return void
	 * @author Dean Sofer
	 */
	public function decode($response) {
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
				App::uses('Xml', 'Utility');
				$Xml = new Xml($response);
				$response = $Xml->toArray(false); // Send false to get separate elements
				$Xml->__destruct();
				$Xml = null;
				unset($Xml);
				break;
			case 'application/json':
			case 'application/javascript':
			case 'text/javascript':
				$response = json_decode($response, true);
				break;
		}
		return $response;
	}

	/*public function listSources() {
		return array_keys($this->_schema);
	}*/

	/**
	 * Iterates through the tokens (passed or request items) and replaces them into the url
	 *
	 * @param string $url
	 * @param array $tokens optional
	 * @return string $url
	 * @author Dean Sofer
	 */
	public function swapTokens(&$model, $url, $tokens = array()) {
		$formattedTokens = array();
		foreach ($tokens as $token => $value) {
			$formattedTokens[':'.$token] = $value;
		}
		$url = strtr($url, $formattedTokens);
		return $url;
	}

/**
 * Generates a conditions section of the url
 *
 * @param array $params permitted conditions
 * @param array $queryData passed conditions in key => value form
 * @return string
 * @author Dean Sofer
 */
	public function buildQuery($params = array(), $data = array()) {
		$query = array();
		foreach ($params as $param) {
			if (!empty($data[$param]) && $this->options['kvs']) {
				$query[] = $param . $this->options['kvs'] . $data[$param];
			} elseif (!empty($data[$param])) {
				$query[] = $data[$param];
			}
		}
		return implode($this->options['ps'], $query);
	}

/**
 * Tries iterating through the config map of REST commmands to decide which command to use
 *
 * @param object $model
 * @param string $action
 * @param string $section
 * @param array $fields
 * @return boolean $found
 * @author Dean Sofer
 */
	public function scanMap(&$model, $action, $section, $fields = array()) {
		if (!isset($this->map[$action][$section])) {
			throw new Exception('Section ' . $section . ' not found in Apis Driver Configuration Map - ' . get_class($this));
		}
		$map = $this->map[$action][$section];
		foreach ($map as $path => $conditions) {
			$optional = (isset($conditions['optional'])) ? $conditions['optional'] : array();
			unset($conditions['optional']);
			if (array_intersect($fields, $conditions) == $conditions) {
				return array($path, $conditions, $optional);
			}
		}
		throw new Exception('[ApiSource] Could not find a match for passed conditions');
	}

/**
 * Play nice with the DebugKit
 *
 * @param boolean sorted ignored
 * @param boolean clear will clear the log if set to true (default)
 * @return array of log requested
 */
	public function getLog($sorted = false, $clear = true){
		$log = $this->__requestLog;
		if($clear){
			$this->__requestLog = array();
		}
		return array('log' => $log, 'count' => count($log), 'time' => 'Unknown');
	}


/**
 * Just-In-Time callback for any last-minute request modifications
 *
 * @param object $model
 * @param array $request
 * @return array $request
 * @author Dean Sofer
 */
	public function beforeRequest($model, $request) {
		return $request;
	}


/**
 * Uses standard find conditions. Use find('all', $params). Since you cannot pull specific fields,
 * we will instead use 'fields' to specify what table to pull from.
 *
 * @param string $model The model being read.
 * @param string $queryData An array of query data used to find the data you want
 * @return mixed
 * @access public
 */
	public function read(Model $model, $queryData = array(), $recursive = null) {
		if (!isset($model->request)) {
			$model->request = array();
		}
		$model->request = array_merge(array('method' => 'GET'), $model->request);
		if (!isset($queryData['conditions'])) {
			$queryData['conditions'] = array();
		}
		if (empty($model->request['uri']['path']) && !empty($queryData['path'])) {
			$model->request['uri']['path'] = $queryData['path'];
			$model->request['uri']['query'] = $queryData['conditions'];
		} elseif (!empty($this->map['read']) && (is_string($queryData['fields']) || !empty($queryData['section']))) {
			if (!empty($queryData['section'])) {
				$section = $queryData['section'];
			} else {
				$section = $queryData['fields'];
			}
			$scan = $this->scanMap($model, 'read', $section, array_keys($queryData['conditions']));
			$model->request['uri']['path'] = $scan[0];
			$model->request['uri']['query'] = array();
			$usedConditions = array_intersect(array_keys($queryData['conditions']), array_merge($scan[1], $scan[2]));
			foreach ($usedConditions as $condition) {
				$model->request['uri']['query'][$condition] = $queryData['conditions'][$condition];
			}
		}
		return $this->request($model);
	}

/**
 * Sets method = POST in request if not already set
 *
 * @param AppModel $model
 * @param array $fields Unused
 * @param array $values Unused
 */
	public function create(Model $model, $fields = null, $values = null) {
		if (!isset($model->request)) {
			$model->request = array();
		}
		if (empty($model->request['body']) && !empty($fields) && !empty($values)) {
			$model->request['body'] = array_combine($fields, $values);
		}
		$model->request = array_merge(array('method' => 'POST'), $model->request);
		$scan = $this->scanMap($model, 'create', $model->useTable, $fields);
		if ($scan) {
			$model->request['uri']['path'] = $scan[0];
		} else {
			return false;
		}
		return $this->request($model);
	}

/**
 * Sets method = PUT in request if not already set
 *
 * @param AppModel $model
 * @param array $fields Unused
 * @param array $values Unused
 */
	public function update(Model $model, $fields = null, $values = null, $conditions = null) {
		if (!isset($model->request)) {
			$model->request = array();
		}
		if (empty($model->request['body']) && !empty($fields) && !empty($values)) {
			$model->request['body'] = array_combine($fields, $values);
		}
		$model->request = array_merge(array('method' => 'PUT'), $model->request);
		if (!empty($this->map['update']) && in_array('section', $fields)) {
			$scan = $this->scanMap($model, 'write', $fields['section'], $fields);
			if ($scan) {
				$model->request['uri']['path'] = $scan[0];
			} else {
				return false;
			}
		}
		return $this->request($model);
	}

/**
 * Sets method = DELETE in request if not already set
 *
 * @param AppModel $model
 * @param mixed $id Unused
 */
	public function delete(Model $model, $id = null) {
		if (!isset($model->request)) {
			$model->request = array();
		}
		$model->request = array_merge(array('method' => 'DELETE'), $model->request);
		return $this->request($model);
	}

	public function calculate($model, $func, $params = array()) {

	}

	public function getColumnType() {
		return true;
	}
}
