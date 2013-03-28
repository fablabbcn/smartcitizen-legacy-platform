<?php
/**
 * CakePHP Component for checking current status of your app as to whether it is
 * authorized to interact with a user's oauth account via the API. And
 * provides methods for the "OAuth Dance" you need to have with oauth apis in order
 * to get authorized, that you can call from your controller actions.
 *
 * Authorization status is determined by whether you have an OAuth access token
 * and secret. These can be present in the datasource config or in the session.
 *
 * The checks for authorization status happen automatically in the startup()
 * method, and if the credentials are in the session, but not in the datasource
 * config (which is where they are needed for making authenticated requests),
 * they are automatically copied to the datasource config.
 *
 * In order to get authorization you need to:
 * 1. Get an OAuth Request Token
 * 2. Authorize the request token (and the app to interact with a users account)
 * 3. Get an OAuth Access Token
 *
 * @author Neil Crookes <neil@neilcrookes.com>, Dean Sofer <ProLoser>
 * @link http://www.neilcrookes.com, http://www.deansofer.com
 * @copyright (c) 2010 Neil Crookes
 * @license MIT License - http://www.opensource.org/licenses/mit-license.php
 */
App::uses('Component', 'Controller');
class OauthComponent extends Component {

	/**
	 * The other components used by this component
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('Session');

	/**
	 * The default/common elements of the requests for the oauth request and
	 * access tokens made through HttpSocketOauth.
	 *
	 * @var array
	 * @access protected
	 */
	protected $_oAuthRequestDefaults = array(
		'uri' => array(
			'scheme' => 'https',
		),
		'method' => 'GET',
		'auth' => array(
			'method' => 'OAuth'
		),
	);

	protected $_config = array();
	protected $_map = array();

	/**
	 * Default datasource config. Only used if a string is passed
	 *
	 * @var string
	 */
	public $useDbConfig = null;

	var $controller;

	/**
	 * Called before Controller::beforeFilter(), stores reference to Controller
	 * object
	 *
	 * @param AppController $controller
	 * @return void
	 * @access public
	 */
	public function initialize(Controller $controller) {
		$this->controller = $controller;

		$settings = (array)$this->settings;
		if (count($settings) === 1) {
			$this->useDbConfig = $settings[0];
		}

		foreach ($settings as $key => $setting) {
			if (is_int($key)) {
				$key = $setting;
			}
			$this->_config[$key] = array();
		}
	}

	/**
	 * Checks if we are authorized to access a users Api account via the API
	 * by first checking if the OAuth credentials are in the datasource config,
	 * else if they are in the session. If they are not in the config but are in
	 * the session, copy them into the datasource config. Finally, set the
	 * isAuthorized session variable so the authorization state can be easily
	 * determined in the view.
	 *
	 * @return void
	 */
	public function startup(Controller $controller) {
		foreach ($this->_config as $name => $options) {
			$isAuthorized = false;
			if ($this->accessTokenConfig($name)) {
				$isAuthorized = true;
			} elseif ($this->accessTokenSession($name)) {
				$isAuthorized = true;
				if ($this->Session->check('OAuth.'.$name.'.access_token')) {
					$this->_config[$name]['access_token'] = $this->Session->read('OAuth.'.$name.'.access_token');
				} else {
					$this->_config[$name]['oauth_token'] = $this->Session->read('OAuth.'.$name.'.oauth_token');
					$this->_config[$name]['oauth_token_secret'] = $this->Session->read('OAuth.'.$name.'.oauth_token_secret');
				}
			}
			App::uses('ConnectionManager', 'Model');
			$ds = ConnectionManager::getDataSource($name);
			$this->_config[$name] = $ds->config;

			if ($this->Session->check('OAuth.'.$name.'.access_token')) {
				$ds->config['access_token'] = $this->Session->read('OAuth.'.$name.'.access_token');
			} else {
				$ds->config['oauth_token'] = $this->Session->read('OAuth.'.$name.'.oauth_token');
				$ds->config['oauth_token_secret'] = $this->Session->read('OAuth.'.$name.'.oauth_token_secret');
			}
			$this->_config[$name]['isAuthorized'] = $isAuthorized;
			$this->Session->write('OAuth.'.$name.'.oauth_consumer_key', $this->_config[$name]['login']);
			$this->Session->write('OAuth.'.$name.'.oauth_consumer_secret', $this->_config[$name]['password']);
			$this->Session->write('OAuth.'.$name.'.isAuthorized', $isAuthorized);
		}
	}

	/**
	 * Returns true if OAuth credentials are in the config of the datasource
	 *
	 * @return boolean
	 */
	public function accessTokenConfig($dbConfig = null) {
		if (!$dbConfig) {
			$dbConfig = $this->useDbConfig;
		}
		return !empty($this->_config[$dbConfig]['access_token']) || (!empty($this->_config[$dbConfig]['oauth_token']) && !empty($this->_config[$dbConfig]['oauth_token_secret']));
	}

	/**
	 * Returns true if OAuth credentials are in the session
	 *
	 * @param string $dbConfig
	 * @return boolean
	 */
	public function accessTokenSession($dbConfig = null) {
		if (!$dbConfig) {
			$dbConfig = $this->useDbConfig;
		}
		return $this->Session->check('OAuth.'.$dbConfig.'.access_token') || ($this->Session->check('OAuth.'.$dbConfig.'.oauth_token') && $this->Session->check('OAuth.'.$dbConfig.'.oauth_token_secret'));
	}

	/**
	 * Returns a configuration map from the specific datasource plugin
	 *
	 * @param string $config
	 * @return array $this->_map
	 * @author Dean Sofer
	 */
	private function _getMap($dbConfig = null) {
		if (!empty($this->_map)) {
			return;
		}
		if (!$dbConfig) {
			$dbConfig = $this->useDbConfig;
		}
		$datasource = $this->_config[$dbConfig]['datasource'];
		$name = pluginSplit($datasource);
		if (!$this->_map = Configure::read('Apis.' . $name[1])) {
			Configure::load($name[0] . '.' . $name[1]);
			$this->_map = Configure::read('Apis.' . $name[1]);
		}
		if (isset($this->_map['oauth']['scheme'])) {
			$this->_oAuthRequestDefaults['uri']['scheme'] = $this->_map['oauth']['scheme'];
		}
	}

	/**
	 * The first stage of the OAuth Dance. Gets OAuth Request Token
	 * and OAuth Request Token Secret from the API.
	 *
	 * @param string $oAuthConsumerKey
	 * @param string $oAuthConsumerSecret
	 * @param string $oAuthCallback The url in your app that gets the Access Token
	 * @return array Array containing keys oauth_token and oauth__token_secret
	 */
	public function getOAuthRequestToken($oAuthConsumerKey, $oAuthConsumerSecret, $oAuthCallback) {
		$this->_getMap();

		$request = Set::merge($this->_oAuthRequestDefaults, array(
			'uri' => array(
				'host' => $this->_map['hosts']['oauth'],
				'path' => $this->_map['oauth']['request'],
			),
			'auth' => array(
				'oauth_consumer_key' => $oAuthConsumerKey,
				'oauth_consumer_secret' => $oAuthConsumerSecret,
				'oauth_callback' => $oAuthCallback,
			),
		));

		if (!empty($this->_config[$this->useDbConfig]['scope'])) {
			$request['uri']['query'] = array('scope' => $this->_config[$this->useDbConfig]['scope']);
		}

		App::uses('HttpSocketOauth', 'HttpSocketOauth.Lib');
		$Http = new HttpSocketOauth();
		$response = $Http->request($request);
		if ($Http->response['status']['code'] != 200) {
			return false;
		}

		parse_str($response, $requestToken);

		return $requestToken;

	}

	/**
	 * The second stage of the OAuth Dance. Redirects the user to
	 * the API website so they can authorize your application.
	 *
	 * @param string $oAuthRequestToken
	 * @return void
	 */
	public function authorize($oAuthRequestToken) {
		$this->_getMap();
		$redirect = $this->_oAuthRequestDefaults['uri']['scheme'] . '://' . $this->_map['hosts']['oauth'] . '/' . $this->_map['oauth']['authorize'] . '?oauth_token=' . $oAuthRequestToken;
		$this->controller->redirect($redirect);
	}

	/**
	 * Same as above for OAuth v2.0
	 *
	 * @param string $oAuthRequestToken
	 * @return void
	 */
	public function authorizeV2($oAuthConsumerKey, $oAuthCallback) {
		$this->_getMap();
		$redirect = $this->_oAuthRequestDefaults['uri']['scheme'] . '://' . $this->_map['hosts']['oauth'] . '/' . $this->_map['oauth']['authorize'] . '?client_id=' . $oAuthConsumerKey . '&redirect_uri=' . $oAuthCallback;
		if (!empty($this->_config[$this->useDbConfig]['scope'])) {
			$redirect .= '&scope=' . $this->_config[$this->useDbConfig]['scope'];
		}
		$this->controller->redirect($redirect);
	}

	/**
	 * The third stage of the OAuth Dance. Gets OAuth Access Token
	 * and OAuth Access Token Secret.
	 *
	 * @param string $oAuthConsumerKey
	 * @param string $oAuthConsumerSecret
	 * @param string $oAuthRequestToken
	 * @param string $oAuthRequestTokenSecret
	 * @param string $oAuthVerifier
	 * @return array Array containing keys token and token_secret
	 */
	public function getOAuthAccessToken($oAuthConsumerKey, $oAuthConsumerSecret, $oAuthRequestToken, $oAuthRequestTokenSecret, $oAuthVerifier) {
		$this->_getMap();
		$request = Set::merge($this->_oAuthRequestDefaults, array(
			'uri' => array(
				'host' => $this->_map['hosts']['oauth'],
				'path' => $this->_map['oauth']['access'],
			),
			'auth' => array(
				'oauth_consumer_key' => $oAuthConsumerKey,
				'oauth_consumer_secret' => $oAuthConsumerSecret,
				'oauth_token' => $oAuthRequestToken,
				'oauth_token_secret' => $oAuthRequestTokenSecret,
				'oauth_verifier' => $oAuthVerifier,
			),
		));

		App::uses('HttpSocketOauth', 'HttpSocketOauth.Lib');
		$Http = new HttpSocketOauth();

		$response = $Http->request($request);

		if ($Http->response['status']['code'] != 200) {
			return false;
		}

		parse_str($response, $accessToken);

		return $accessToken;

	}

	/**
	 * Same as above for OAuth v2.0
	 *
	 * @param string $oAuthConsumerKey
	 * @param string $oAuthConsumerSecret
	 * @param string $oAuthCode
	 * @param string $oAuthCallBack
	 * @return array Array containing keys token and token_secret
	 * @author Dean Sofer, adrelanex
	 */
	public function getOAuthAccessTokenV2($oAuthConsumerKey, $oAuthConsumerSecret, $oAuthCode, $oAuthCallBack) {
		$this->_getMap();
		$request = Set::merge($this->_oAuthRequestDefaults, array(
			'uri' => array(
				'host' => $this->_map['hosts']['oauth'],
				'path' => $this->_map['oauth']['access'],
			),
			'method' => 'POST',
			'body' => array(
				'grant_type' => 'authorization_code',
				'code' => $oAuthCode,
				'redirect_uri' => $oAuthCallBack,
				'client_id' => $oAuthConsumerKey,
				'client_secret' => $oAuthConsumerSecret,
			)
		));

		$Http = new HttpSocket(); 

		$response = $Http->request($request);

		if ($Http->response['status']['code'] != 200) {
			return false;
		}
		
		if($Http->response['Content-Type']='application/json')
			$accessToken = (array) json_decode($response);
		else
			parse_str($response, $accessToken);
		return $accessToken;
	}

	/**
	 * This is a convenience method that you can call from your controller action
	 * that you link to from your views to sign in with twitter, if you don't need
	 * to do anything special that deviates from the default approach.
	 *
	 * In your controller action you simply do:
	 *
	 *		 public function twitter_connect($redirect = null) {
	 *			 $this->Oauth->connect(urldecode($redirect));
	 *		 }
	 *
	 * The method first tries to obtain any required data that is not supplied in
	 * the parameters. See below for the parameters you can specify and what
	 * happens if they are not specified.
	 *
	 * It then tries to get an OAuth Request Token and OAuth Request Token Secret
	 * then redirects the user to the Api to authorize the OAuth Request Token.
	 *
	 * If for some reason we couldn't get a request token, an error message is set
	 * in the session flash and the user is redirected to the redirect param. If
	 * that is not set, the error message is dumped out.
	 *
	 * @param string $redirect The URL the user will be redirected back to after
	 * successfully connecting with twitter or an error occured. If not specified
	 * errors or results are just displayed on screen.
	 * @param string $oAuthCallback The URL twitter will redirect the user to
	 * after they authorize your application. If not specified it will be to a
	 * twitter_callback action in the current controller.
	 */
	public function connect($redirect = null, $oAuthCallback = null) {
		$this->_getMap();

		if (!$oAuthCallback) {
			$oAuthCallback = Router::url(array('action' => $this->useDbConfig.'_callback'), true);
		} elseif (is_array($oAuthCallback)) {
			$oAuthCallback = Router::url($oAuthCallback, true);
		}
		if (!isset($this->_config[$this->useDbConfig]['login'])) {
			$this->_error(__d('oauth', 'Could not get OAuth Consumer Key'), $redirect);
		}
		$oAuthConsumerKey = $this->_config[$this->useDbConfig]['login'];

		if (!isset($this->_config[$this->useDbConfig]['password'])) {
			$this->_error(__d('oauth', 'Could not get OAuth Consumer Secret'), $redirect);
		}
		$oAuthConsumerSecret = $this->_config[$this->useDbConfig]['password'];

		if (isset($this->_map['oauth']['version']) && $this->_map['oauth']['version'] == '2.0') {

			$this->authorizeV2($oAuthConsumerKey, $oAuthCallback);

		} else {

			$requestToken = $this->getOAuthRequestToken($oAuthConsumerKey, $oAuthConsumerSecret, $oAuthCallback);

			if ($requestToken) {
				$this->Session->write('OAuth.'.$this->useDbConfig.'.oauth_request_token', $requestToken['oauth_token']);
				$this->Session->write('OAuth.'.$this->useDbConfig.'.oauth_request_token_secret', $requestToken['oauth_token_secret']);
				$this->authorize($requestToken['oauth_token']);
			} else {
				$this->_error(__d('oauth', 'Could not get OAuth Request Token from '.$this->useDbConfig), $redirect);
			}
		}
	}

	/**
	 * This is a convenience method that you can call from your controller action
	 * that twitter redirects the user back to after they authorized your
	 * application, if you don't need to do anything special that deviates from
	 * the default approach.
	 *
	 * In your controller action you simply do:
	 *
	 *		 public function twitter_callback() {
	 *			 $this->Oauth->callback();
	 *		 }
	 *
	 * This method exchanges the authorised request token for the OAuth Access
	 * Token and OAuth Access Token Secret and stores them in the session before
	 * redirecting the user back to the URL passed in in the redirect parameter to
	 * the connect action above, or if that is not set, the details are dumped
	 * out.
	 */
	public function callback($redirect = null, $oAuthCallback = null) {
		$this->_getMap();
		
		if (!isset($this->_config[$this->useDbConfig]['login'])) {
			$this->_error(__d('oauth', 'Could not get OAuth Consumer Key'), $redirect);
		}
		$oAuthConsumerKey = $this->_config[$this->useDbConfig]['login'];

		if (!isset($this->_config[$this->useDbConfig]['password'])) {
			$this->_error(__d('oauth', 'Could not get OAuth Consumer Secret'), $redirect);
		}
		$oAuthConsumerSecret = $this->_config[$this->useDbConfig]['password'];

		if (isset($this->_map['oauth']['version']) && $this->_map['oauth']['version'] == '2.0') {

			if (!$oAuthCallback) {
				$oAuthCallback = Router::url(array('action' => $this->useDbConfig.'_callback'), true);
			} elseif (is_array($oAuthCallback)) {
				$oAuthCallback = Router::url($oAuthCallback, true);
			}
			
			if (empty($this->controller->params['url']['code'])) {
				$this->_error(__d('oauth', 'Could not get OAuth Access Code from ' . $this->useDbConfig), $redirect);
			}
			$oAuthCode = $this->controller->params['url']['code'];

			$accessToken = $this->getOAuthAccessTokenV2($oAuthConsumerKey, $oAuthConsumerSecret, $oAuthCode, $oAuthCallback);

		} else {

			if (!$this->Session->check('OAuth.'.$this->useDbConfig.'.oauth_request_token')) {
				$this->_error(__d('oauth', 'Could not get OAuth Request Token from session'), $redirect);
			}
			$oAuthRequestToken = $this->Session->read('OAuth.'.$this->useDbConfig.'.oauth_request_token');

			if (!$this->Session->check('OAuth.'.$this->useDbConfig.'.oauth_request_token_secret')) {
				$this->_error(__d('oauth', 'Could not get OAuth Request Token Secret from session'), $redirect);
			}
			$oAuthRequestTokenSecret = $this->Session->read('OAuth.'.$this->useDbConfig.'.oauth_request_token_secret');

			if (empty($this->controller->params['url']['oauth_verifier'])) {
				$this->_error(__d('oauth', 'Could not get OAuth Verifier from querystring'), $redirect);
			}
			$oAuthVerifier = $this->controller->params['url']['oauth_verifier'];

			$accessToken = $this->getOAuthAccessToken($oAuthConsumerKey, $oAuthConsumerSecret, $oAuthRequestToken, $oAuthRequestTokenSecret, $oAuthVerifier);
		}

		if ($accessToken) {
			$sessionData = $this->Session->read('OAuth.'.$this->useDbConfig);
			$sessionData = array_merge($sessionData, $accessToken);
			$this->Session->write('OAuth.'.$this->useDbConfig, $sessionData);

			if ($redirect) {
				$this->_error(__d('oauth', 'Successfully signed into '.$this->useDbConfig), $redirect);
			} else {
				return $accessToken;
			}

		} else {
			$this->_error(__d('oauth', 'Could not get OAuth Access Token from '.$this->useDbConfig), $redirect);
		}

	}

	/**
	 * Sets message in session flash and redirects to redirect URL if not empty,
	 * else just dump the message out on the screen.
	 *
	 * @param string $message
	 * @param string $redirect
	 */
	protected function _error($message, $redirect) {

		if ($redirect) {
			$this->Session->setFlash($message);
			$this->controller->redirect($redirect);
		}

		die($message);

	}
}
