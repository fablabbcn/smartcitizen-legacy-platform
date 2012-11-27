<!-- File: /app/View/Posts/index.ctp -->

<section class='posts list' style='width:400px';>
	<header>
		<h1><?php echo __('Lastest Post') ?></h1>
		<div class='action'>
			<?php echo $this->Html->link('+ '.__('Add your post'), array('controller' => 'posts', 'action' => 'add'),array('class'=>'button')); ?>
		</div>
	</header>
<?php
			if(empty($posts))
				echo __('There is no post yet') ;
			else
				echo $this->element('posts_list', array("posts" => $posts));
?>
		<footer>
		</footer>
</section>	
