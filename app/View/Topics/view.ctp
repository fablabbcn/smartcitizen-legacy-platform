<!-- File: /app/View/Topics/view.ctp -->
<?php // Debug($data) ?>
<section class='posts list' style='width:400px';>
	<header>
		<h1><?php echo $data['Topic']['name'] ?></h1>
		<div class='action'>
			<?php 
				if (isset($actions) && in_array("edit", $actions)){
					echo $this->Html->link('+ '.__('Edit'), array('controller' => 'topics', 'action' => 'edit',$data['Topic']['id']),array('class'=>'button'));
				}
				if (isset($actions) && in_array("delete", $actions)){
					echo $this->Form->postLink(
						__('Delete'),
						array('action' => 'delete', $data['Topic']['id']),
						array('confirm' => __('Are you sure?'),'class'=>"button"));
				}
				if (isset($actions) && in_array("addPost", $actions)){
					echo $this->Html->link('+ '.__('Add your post'), array('controller' => 'posts', 'action' => 'add'),array('class'=>'button'));
				}
				?>
		</div>
	</header>
<?php
	
			if(empty($data['Post']))
				echo "<p>".__('There is no post yet')."</p>" ;
			else{
				foreach($data['Post'] as $k=>$v){
						$data['Post'][$k]['Post']=$v;
					}
				echo $this->element('posts_list', array("posts" => $data['Post']));
			}
?>
		<footer>
		</footer>
</section>	
