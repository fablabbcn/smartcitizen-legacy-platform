<?php
class UploaderHelper extends AppHelper{

	var $helpers = array('Html','Form');
	var $javascript = false; 

	public function tinymce($field){
		$model = $this->Form->_models; $model = key($model); 
		$this->javascript(); 
        $html = $this->Form->input($field,array('label'=>false,'class'=>'wysiwyg','style'=>'width:100%;height:500px','row' => 160));
    	if(isset($this->request->data[$model]['id'])){
			$html .= '<input type="hidden" id="explorer" value="'.$this->Html->url('/media/medias/index/'.$model.'/'.$this->request->data[$model]['id']).'/tinymce:1">';
    	}
		return $html; 
	}

	private function javascript(){
		if($this->javascript){ return false; }
		$this->javascript = true; 
		$this->Html->script('/media/js/tinymce/tiny_mce.js',array('inline'=>false));
	}

	public function iframe($ref,$ref_id){
		return '<iframe src="'.Router::url('/').'media/medias/index/'.$ref.'/'.$ref_id.'" style="width:100%;" id="medias'.$ref.'"></iframe>';
	}
}