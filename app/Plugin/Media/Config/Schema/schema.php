<?php 
/* generated on: 2012-02-26 23:30:54 : 1330299054 */
class MediaSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $medias = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary', 'collate' => NULL, 'comment' => ''),
		'ref' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 60, 'key' => 'index', 'collate' => 'latin1_swedish_ci', 'comment' => '', 'charset' => 'latin1'),
		'ref_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index', 'collate' => NULL, 'comment' => ''),
		'file' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'charset' => 'latin1'),
		'position' => array('type' => 'integer', 'null' => true, 'default' => '0', 'collate' => NULL, 'comment' => ''),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'ref' => array('column' => 'ref', 'unique' => 0), 'ref_id' => array('column' => 'ref_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);
}
