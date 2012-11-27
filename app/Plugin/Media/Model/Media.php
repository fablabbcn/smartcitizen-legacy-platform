<?php
class Media extends AppModel{
	var $name = 'Media';
	public $useTable = 'medias';
	public $order    = 'position ASC';

	function beforeDelete($cascade = true){
		$file = $this->field('file');
		$info = pathinfo($file);
		foreach(glob(IMAGES.$info['dirname'].'/'.$info['filename'].'_*x*.jpg') as $v){
			unlink($v);
		}
		unlink(IMAGES.$file);
		return true;
	}

	function afterFind($results, $primary = false){
		foreach($results as $k=>$result){
			if(isset($result[$this->alias]['file'])){
				$results[$k][$this->alias]['filef'] = substr($result[$this->alias]['file'],0,-4).'_%dx%d.jpg';
			}
		}
		return $results;
	}

	function beforeSave($options = array()){
		if( isset($this->data['Media']['file']) && is_array($this->data['Media']['file']) && isset($this->data['Media']['ref']) ){
			$model 		= ClassRegistry::init($this->data['Media']['ref']);
			$dir 		= $model->medias['path'];
			$ref_id 	= $this->data['Media']['ref_id'];
			$pathinfo 	= pathinfo($this->data['Media']['file']['name']);
			$filename 	= Inflector::slug($pathinfo['filename'],'-');
			$search 	= array('%id','%mid','%cid','%y','%m','%f');
			$replace 	= array($ref_id,ceil($ref_id/1000),ceil($ref_id/100),date('Y'),date('m'),Inflector::slug($filename));
			$dir  		= str_replace($search,$replace,$dir).'.'.$pathinfo['extension'];
			$this->testDuplicate($dir);
			if(!file_exists(dirname(IMAGES.$dir))){
				mkdir(dirname(IMAGES.$dir),0777,true);
			}
			move_uploaded_file($this->data['Media']['file']['tmp_name'], IMAGES.$dir);
			chmod(IMAGES.$dir,0777);
			$this->data['Media']['file'] = $dir;
		}
		return true;
	}

	/**
	* If the file $dir already exists we add a {n} before the extension
	**/
	function testDuplicate(&$dir,$count = 0){
		$file = $dir;
		if($count > 0){
			$pathinfo = pathinfo($dir);
			$file = $pathinfo['dirname'].'/'.$pathinfo['filename'].'-'.$count.'.'.$pathinfo['extension'];
		}
		if(!file_exists(IMAGES.$file)){
			$dir = $file;
		}else{
			$count++;
			$this->testDuplicate($dir,$count);
		}
	}

}