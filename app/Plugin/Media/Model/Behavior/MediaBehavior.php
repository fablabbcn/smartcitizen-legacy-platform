<?php
class MediaBehavior extends ModelBehavior{

	private $options = array(
		'path'    => 'uploads/%y/%m/%f',
		'formats' => false
	);

	public function setup(Model $model,$options = array()){
		$model->medias = array_merge($this->options,$options);
		$model->hasMany['Media'] = array(
			'className'  => 'Media.Media',
			'foreignKey' => 'ref_id',
			'order'		 => 'Media.position ASC',
			'conditions' => 'ref = "'.$model->name.'"',
			'dependent'  => true
		);
		if($model->hasField('media_id')){
			$model->belongsTo['Thumb'] = array(
				'className'  => 'Media.Media',
				'foreignKey' => 'media_id',
				'conditions' => null,
				'counterCache'=> false
			);
		}
	}

	public function afterSave(Model $model, $created){
		if(!empty($model->data[$model->name]['thumb']['name'])){
			$file = $model->data[$model->name]['thumb'];

			// Current thumb
			$media_id = $model->field('media_id');
			if($media_id != 0){
				$model->Media->delete($media_id);
			}

			// Upadte thumb
			$model->Media->save(array(
				'ref_id' => $model->id,
				'ref'	 => $model->name,
				'file'   => $file
			));
			$model->saveField('media_id',$model->Media->id);
		}
	}

	public function afterFind(Model $model, $data, $primary = false){
		foreach($data as $k=>$v){
			// Thumbnail
			if(isset($v['Thumb']['file'])){
				$v[$model->name]['thumb'] = '/img/'.$v['Thumb']['file'];
				$v[$model->name]['thumbf'] = '/img/'.$v['Thumb']['filef'];
			}
			if(!empty($v['Media'])){
				$v['Media'] = Set::Combine($v['Media'],'{n}.id','{n}');
			}
			if( !empty($v[$model->name]['media_id']) && isset($v['Media'][$v[$model->name]['media_id']]) ){
				$media_id = $v[$model->name]['media_id'];
				$v[$model->name]['thumb'] = $v['Media'][$media_id]['file'];
				$v[$model->name]['thumbf'] = $v['Media'][$media_id]['filef'];
			}
			$data[$k] = $v;
		}
		return $data;
	}





}