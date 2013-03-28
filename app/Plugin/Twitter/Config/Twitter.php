<?php
/**
 * A Github API Method Map
 *
 * Refer to the apis plugin for how to build a method map
 * https://github.com/ProLoser/CakePHP-Api-Datasources
 *
 * @link https://dev.twitter.com/docs/api/1.1
 */
$config['Apis']['Twitter']['hosts'] = array(
	'oauth' => 'api.twitter.com/oauth',
	'rest' => 'api.twitter.com/1',
);
// http://developer.github.com/v3/oauth/
$config['Apis']['Twitter']['oauth'] = array(
	'authorize' => 'authorize', // Example URI: https://github.com/login/oauth/authorize
	'request' => 'request_token', //client_id={$this->config['login']}&redirect_uri
	'access' => 'access_token',
);
$config['Apis']['Twitter']['read'] = array(
	'tweets' => array(
		'statuses/show/:id' => array(
			'id',
			'optional' => array(
				'trim_user',
				'include_entities',
			),
		),
	),
	'followers' => array(
		'followers/ids' => array(
			'optional' => array(
				'user_id',
				'screen_name',
 				'cursor',
				'stringify_ids',
			),
		),
	),
	'friends' => array(
		'friends/ids' => array(
			'optional' => array(
				'user_id',
				'screen_name',
				'cursor',
				'stringify_ids',
			),
		),
	),
	'users' => array(
		'users/show' => array(
			'user_id',
			'screen_name',
			'optional' => array(
				'include_entities'
			),
		),
		'users/lookup' => array(
			'optional' => array(
				'screen_name',
				'user_id',
				'include_entities',
			),
		),
	),
);

$config['Apis']['Twitter']['create'] = array(
	// field
	'tweets' => array(
		'statuses/update' => array(
			'status',
			'optional' => array(
				'in_reply_to_status_id',
				'lat',
				'long',
				'place_id',
				'display_coordinates',
				'trim_user',
				'include_entities',
				'wrap_links'
			),
		),
	),
);

$config['Apis']['Twitter']['update'] = array(
);

$config['Apis']['Twitter']['delete'] = array(
);