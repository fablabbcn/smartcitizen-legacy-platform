# Apis Plugin

Since I started going through several restful apis things started to become repetitive. I decided to layout my code in a more 'proper ' fashion then.

## Notes

`ApiDatasource` is an **abstract** class and must be extended by the Api you wish to support.
Open a bug ticket if you'd like some help making your own or just want me to do it.
It's _very_ easy to add new ones - [check out the list](#expanding-functionality)

## Installation

### Step 1: Clone or download to `Plugin/Apis`

### Step 2: Add your configuration to `database.php` and set it to the model

```
:: database.php ::
var $myapi = array(
	'datasource' => 'MyPlugin.MyPlugin', // Example: 'Github.Github'
	
	// These are only required for authenticated requests (write-access)
	'login' => '--Your API Key--',
	'password' => '--Your API Secret--',
);

:: MyModel.php ::
var $useDbConfig = 'myapi';
```

### Step 3: Authenticating (requires a configuration map)

```
MyController extends AppController {
	var $components = array(
		'Apis.Oauth' => 'linkedin', // name of the $dbConfig to use
	);
	
	function connect() {
		$this->Oauth->connect();
	}
	
	function linkedin_callback() {
		$this->Oauth->callback();
	}
}
```

You can also use multiple database configurations

```
var $components = array(
	'Apis.Oauth' => array(
		'linkedin',
		'github',
		'flickr',
	);
);
```

However this requires you to specify which config to use before calling authentication methods

```
function beforeFilter() {
	$this->Oauth->useDbConfig = 'github';
}
```

### Step 4: Querying the API

> **NOTE:** You _must_ load the OauthComponent on any action that makes a call to the Api

Best to just give an example. I switch the datasource on the fly because the model is actually a `projects` table in the
DB. I tend to query from my API and then switch to default and save the results.

```
Class Project extends AppModel {
	function findAuthedUserRepos() {
		$this->setDataSource('github');
		$projects = $this->find('all', array(
			'fields' => 'repos'
		));
		$this->setDataSource('default'); // if more queries are done later
		return $projects;
	}
}
```

## Expanding functionality

Use the [template branch](https://github.com/ProLoser/CakePHP-Api-Datasources/tree/template) as a starting point for creating your own datasource.

__Checkout other plugins for examples__

 * [Flickr](https://github.com/proloser/cakephp-flickr)
 * [Vimeo](https://github.com/proloser/cakephp-vimeo)
 * [Instagram](https://github.com/proloser/cakephp-instagram)
 * [Facebook](https://github.com/JavRok/http_socket_oauth)
 * [LinkedIn](https://github.com/ProLoser/CakePHP-LinkedIn)
 * [Github](https://github.com/ProLoser/CakePHP-Github) which does OAuth v2
 * [PhotoBucket](https://github.com/ProLoser/CakePHP-Photobucket)
 * [Dribbble](https://github.com/ProLoser/CakePHP-Dribbble)
 * [Instagram](https://github.com/ProLoser/CakePHP-Instagram)
 * [Asana](https://github.com/ProLoser/CakePHP-Asana)
 * [Delicio.us](https://github.com/ProLoser/CakePHP-Delicious)
 * [Forrst](https://github.com/ProLoser/CakePHP-Forrst)
 * [CloudPrint (Google)](https://github.com/tenebrousedge/CakePHP-Cloudprint)
 * [JsFiddle](https://github.com/ProLoser/CakePHP-JsFiddle) for the bare minimum needed to add a new API
 * A whole lot more

### Creating a configuration map

_[MyPlugin]/Config/[MyPlugin].php_

REST paths must be ordered from most specific conditions to least (or none). This is because the map is iterated through
until the first path which has all of its required conditions met is found. If a path has no required conditions, it will
be used. Optional conditions aren't checked, but are added when building the request.

```
$config['Apis']['MyPlugin']['hosts'] = array(
	'oauth' => 'api.myplugin.com/login/oauth',
	'rest' => 'api.myplugin.com/v1',
);
$config['Apis']['MyPlugin']['oauth'] = array(
	'version' => '1.0', // [Optional] OAuth version (defaults to 1.0): '1.0' or '2.0'
	'scheme' => 'https', // [Optional] Values: 'http' or 'https'
	'authorize' => 'authorize', // Example URI: api.linkedin.com/uas/oauth/authorize
	'request' => 'requestToken',
	'access' => 'accessToken',
	'login' => 'authenticate', // Like authorize, just auto-redirects
	'logout' => 'invalidateToken',
);
$config['Apis']['MyPlugin']['read'] = array(
	// field
	'people' => array(
		// api url
		'people/id=' => array(
			// required conditions
			'id',
		),
		'people/url=' => array(
			'url',
		),
		'people/~' => array(),
	),
	'people-search' => array(
		'people-search' => array(
		// optional conditions the api call can take
			'optional' => array(
				'keywords',
			),
		),
	),
);
$config['Apis']['MyPlugin']['write'] = array(
);
$config['Apis']['MyPlugin']['update'] = array(
);
$config['Apis']['MyPlugin']['delete'] = array(
);
```

### Creating a custom datasource 

Try browsing the apis datasource and seeing what automagic functionality you can hook into.

_[MyPlugin]/Model/Datasource/[MyPlugin].php_

```
App::uses('ApisSource', 'Apis.Model/Datasource'); 
Class MyPlugin extends ApisSource {
	// Examples of overriding methods & attributes:
	public $options = array(
		'format'    => 'json',
		'ps'		=> '&', // param separator
		'kvs'		=> '=', // key-value separator
	);
	// Key => Values substitutions in the uri-path right before the request is made. Scans uri-path for :keyname
	public $tokens = array();
	// Enable OAuth for the api
	public function __construct($config) {
		$config['method'] = 'OAuth'; // or 'OAuthV2'
		parent::__construct($config);
	}
	// Last minute tweaks
	public function beforeRequest(&$model, $request) {
		$request['header']['x-li-format'] = $this->options['format'];
		return $request;
	}
}
```

### Creating a custom oauth component (recommended approach)

_[MyPlugin]/Controller/Component/[MyPlugin].php_

```
App::uses('Oauth', 'Apis.Component/Component');
Class MyPluginComponent extends OauthComponent {
	// Override & supplement your methods & attributes
}
```

### On-the-fly customization
Lets say you don't feel like bothering to make a new plugin just to support your api, or the existing plugin doesn't cover
enough of the features. Good news! The plugin degrades gracefully and allows you to manually manipulate the request (thanks
to NeilCrookes' RESTful plugin).

Simply populate Model->request with any request params you wish and then fire off the related action. You can even continue
using the `$data` & `$this->data` for `save()` and `update()` or pass a `'path'` key to `find()` and it will automagically
be injected into your request object.

## Roadmap / Concerns

**I'm eager to hear any recommendations or possible solutions.**

* **More automagic**
* **Better map scanning:**
  I'm not sure of a good way to add map scanning to `save()`, `update()` and `delete()` methods yet since I have little control
  over the arguments passed to the datasource. It is easy to supplement `find()` with information and utilize it for processing.
* **Complex query-building versatility:**
  Some APIs have multiple different ways of passing query params. Sometimes within the same request! I still need to flesh
  out param-building functions and options in the driver so that people extending the datasource have less work.