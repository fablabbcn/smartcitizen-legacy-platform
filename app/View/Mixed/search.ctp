<?php
$this->start('aside');
	echo $this->element('search_input', array("keyword" => $keyword));
$this->end();
?>



<div style="width:400px">
	<section class='feeds list'>	
		<header>
			<div class='action'>
				<?php echo $this->Html->link('+ '.__('More results'), array('controller' => 'feeds', 'action' => 'index'),array('class'=>'button light')); ?>
			</div>
			<h1><?php echo __('Sensors') ?></h1>
			<span class='subh1'><?php echo __('10 most recent')?></span>
		</header>
<?php
			if(empty($feeds))
				echo '<p>'.__('There is no sensors matching your search').'</p>';
			else
				echo $this->element('feeds_list', array("feeds" => $feeds));?>
	
		<footer></footer>
	</section>
	
	<section class='users list'>
		<header>
			<div class='action'>
				<?php echo $this->Html->link('+ '.__('More results'), array('controller' => 'users', 'action' => 'index'),array('class'=>'button light')); ?>
			</div>
			<h1><?php echo __('Users') ?></h1>
			<span class='subh1'><?php echo __('10 most recent')?></span>
		</header>
<?php
			if(empty($users))
				echo '<p>'.__('There is no citizen matching your search').'</p>' ;
			else
				echo $this->element('users_list', array("users" => $users));
?>
		<footer></footer>
	</section>
	
	<section class='users list'>
		<header>
			<div class='action'>
				<?php echo $this->Html->link('+ '.__('More results'), array('controller' => 'posts', 'action' => 'index'),array('class'=>'button light')); ?>
			</div>
			<h1><?php echo __('Ressources') ?> </h1>
			<span class='subh1'><?php echo __('10 most recent')?></span>
		</header>
<?php
			if(empty($posts))
				echo '<p>'.__('There is no post matching your search').'</p>' ;
			else
				echo $this->element('posts_list', array("posts" => $posts));
?>
		<footer></footer>
	</section>
	
	<?php //echo $this->element('medias_list', array("medias" => $medias));?>
</div>