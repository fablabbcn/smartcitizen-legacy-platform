<?php
/**
*/
	$response=array();

	$response['content']= $this->fetch('content'); 
	$response['aside']= $this->fetch('aside'); 
	$response['map']= $this->fetch('map');
	$response['css']= $this->fetch('css');
	$response['script']= $this->fetch('script');
	$response['inlinescript']= $this->fetch('inline-script');
	

	$response['flash']=$this->Session->flash();


	 echo json_encode ($response); 
 ?>