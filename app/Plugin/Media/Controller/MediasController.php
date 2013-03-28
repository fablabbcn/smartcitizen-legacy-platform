<?php
class MediasController extends AppController{
    
    public $components = array('Media.Img');
    public $order = array('Media.position ASC'); 

    function beforeFilter(){
        parent::beforeFilter(); 
		
		$this->Auth->allow('crop'); // Public function (controled by Routes)
		
        if(in_array($this->request->action, array('admin_upload','admin_index','admin_delete')) && array_key_exists('Security', $this->components)){
            $this->Security->validatePost = false;
            $this->Security->csrfCheck = false;
        }
        $this->layout = 'uploader';
    }

    function blocked(){
        throw new NotFoundException(); 
    }

    /**
    * Permet de cropper les images
    **/
    function crop(){ 
        if(!isset( $this->request->params['file'])){
            die(); 
        }
        extract($this->request->params);
        $file = str_replace('.','',$file); 
        $size = explode('x',$format); 
        $images = glob(IMAGES.$file.'.*');
        $dest = IMAGES.$file.'_'.$format.'.jpg';
        if(empty($images)){
            die(); 
        }else{
            $image = current($images); 
        }
        if($this->Img->redim($image,$dest,$size[0],$size[1])){
            header("Content-type: image/jpg");
            echo file_get_contents($dest);
            exit();
        }
    }

    function grayscale(){
        if(!isset( $this->request->params['file'])){
            die(); 
        }
        extract($this->request->params);
        $file = str_replace('.','',$file); 
        $dest = IMAGES.$file.'_bw.jpg';
        $file = IMAGES.$file.'.jpg';
        if(file_exists($file)){
            $img = imagecreatefromjpeg($file);
            imagefilter($img,IMG_FILTER_GRAYSCALE);
            imagejpeg($img,$dest,90);
            header("Content-type: image/jpg");
            echo file_get_contents($dest);
            exit();
        }
        die(); 
    }

    /**
    * Liste les médias
    **/
    function index($ref,$ref_id){
        $this->loadModel($ref); 
        $d['ref'] = $ref;
        $d['ref_id'] = $ref_id;
        $medias = $this->Media->find('all',array(
            'conditions' => array('ref_id' => $ref_id,'ref' => $ref)
        )); 
        $d['medias'] = $medias;
        $d['tinymce']= isset($this->request->params['named']['tinymce']); 
        $d['thumbID'] = false;
        if($this->$ref->hasField('media_id')){
            $this->$ref->id = $ref_id; 
            $d['thumbID'] = $this->$ref->field('media_id');
        }
        $this->set($d);
    }

    /**
    * Upload (Ajax)
    **/
    function upload($ref,$ref_id){
        $this->Media->save(array(
            'ref'    => $ref,
            'ref_id' => $ref_id,
            'file'   => $_FILES['file']
        ));
        $this->loadModel($ref); 
        $d['v'] = current($this->Media->read());
        $d['tinymce']= isset($this->request->params['named']['tinymce']); 
        $d['thumbID'] = $this->$ref->hasField('media_id');
        $this->set($d);
        $this->layout = false; 
        $render = $this->render('admin_media'); 
        die($render);
    }

    /**
    * Suppression (Ajax)
    **/
    function delete($id){
        $this->Media->delete($id); 
        die(); 
    }

    /**
    * Met l'image à la une
    **/
    function thumb($id){
        $this->Media->id = $id; 
        $ref = $this->Media->field('ref');
        $ref_id = $this->Media->field('ref_id');
        $this->loadModel($ref);
        $this->$ref->id = $ref_id; 
        $this->$ref->saveField('media_id',$id);
        $this->redirect($this->referer());
    }

    function order(){
        if(!empty($this->request->data['Media'])){
            foreach($this->request->data['Media'] as $k=>$v){
                $this->Media->id = $k;
                $this->Media->saveField('position',$v); 
            }
        }
        die(); 
    }
    

}