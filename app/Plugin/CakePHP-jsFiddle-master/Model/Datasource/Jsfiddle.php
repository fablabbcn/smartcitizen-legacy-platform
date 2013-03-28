<?php
/**
 * Jsfiddle Driver for Apis Source
 *
 * Makes usage of the Apis plugin by Proloser
 *
 * @package Jsfiddle Datasource
 * @author Dean Sofer
 * @version 0.0.1
 **/
App::uses('ApisSource', 'Apis.Model/Datasource');
class Jsfiddle extends ApisSource {

    /**
     * The description of this data source
     *
     * @var string
     */
    public $description = 'JsFiddle DataSource Driver';

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

    public function beforeRequest($model, $request) {
        return $request;
    }
}