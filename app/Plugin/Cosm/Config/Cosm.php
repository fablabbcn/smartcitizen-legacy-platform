<?php
/**
 * A Cosm API Method Map
 *
 * Refer to the apis plugin for how to build a method map
 * https://github.com/ProLoser/CakePHP-Api-Datasources
 * Makes usage of the Apis plugin by Proloser
 *
 * @package Cosm Datasource
 * @author Adrelanex
 * @version 0.0.1
 **/
 
$config['Apis']['Cosm']['hosts'] = array(
	'oauth' => 'cosm.com/oauth',
	'rest' => 'api.cosm.com/v2',
);
// https://cosm.com/docs/beta/oauth/
$config['Apis']['Cosm']['oauth'] = array(
	'version' => '2.0', // not transmitted to HttpSocketOauth ?!!
	'authorize' => 'authenticate', // should be : https://cosm.com/oauth/authenticate?client_id=your_client_id
	'request' => 'token', // should be : https://cosm.com/oauth/token
	'access' => 'token',
);
$config['Apis']['Cosm']['read'] = array(
	'feeds' => array(
		'feeds/:id' => array(
			'id',
			'optional' => array(
				'datastreams',
				'show_user',
			),
		),
	),
);

$config['Apis']['Cosm']['create'] = array(
	// field
	'feeds' => array(
		'feeds' => array(
			'title',
			'optional' => array(
				'datastream',
			),
		),
	),
	'cosm_feeds' => array(
		'feeds/:id' => array(
			'id',
			'optional' => array(
				'datastreams',
				'show_user',
			),
		),
	),
);

$config['Apis']['Cosm']['update'] = array(
);

$config['Apis']['Cosm']['delete'] = array(
);