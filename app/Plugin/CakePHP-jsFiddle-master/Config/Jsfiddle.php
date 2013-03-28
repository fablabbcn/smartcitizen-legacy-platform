<?php
/**
 * A JsFiddle API Method Map
 *
 * Refer to the apis plugin for how to build a method map
 * https://github.com/ProLoser/CakePHP-Api-Datasources
 *
 * @link http://doc.jsfiddle.net/api/index.html
 */
$config['Apis']['Jsfiddle']['hosts'] = array(
	'rest' => 'jsfiddle.net',
);
$config['Apis']['Jsfiddle']['read'] = array(
	// field
	'fiddles' => array(
		// http://doc.jsfiddle.net/api/fiddles.html
		'api/user/:user/demo/list.json' => array(
			'user',
			// optional conditions the api call can take
			'optional' => array(
				'callback', // function name for the Xdomain (default: None). if no callback provided standard JSON will be returned
				'start', // offset element (default: 0)
				'limit', // number of elements to return (default: 10)
				'sort', // sorting type - date, alphabetical or framework (default: ‘date’)
				'order', // desc or asc (default: ‘asc’)
				'framework', // filter framework (default: None)
			),
		),
	),
	// http://doc.jsfiddle.net/api/get_username.html
	'user' => array(
		// Replies the username of the logged in user. Returns empty string if not logged in.
		'user/get_username/' => array(),
	),
);

$config['Apis']['Jsfiddle']['create'] = array(
	// http://doc.jsfiddle.net/api/post.html
	'fiddles' => array(
		'api/post/:framework/:version/dependencies/:dependencies' => array(
			'framework', // the desired framework name. Which framework should be loaded with the fiddle (vanilla for plain JavaScript)
			'version', // substring of the framework version - the last passing will be used. If 1.3 will be given, jsFiddle will use the latest search result. it will favorize 1.3.2 over 1.3.1 and 1.3
			'dependencies', // comma separated list of dependency substrings. It would mark any dependency containing the substring.
			'optional' => array(
				'html', 'js', 'css', // code for specific panels
				'resources', // a comma separated list of external resources
				'title', // title of the fiddle
				'description', // description of the fiddle
				'normalize_css', // yes or no - should normalize.css be loaded before any CSS declarations?
				'dtd', // substring of the chosen DTD (i.e. “html 4”)
			),
		),
		'api/post/:framework/:version' => array(
			'framework',
			'version',
			'optional' => array(
				'html', 'js', 'css',
				'resources',
				'title',
				'description',
				'normalize_css',
				'dtd',
			),
		),
	),
);