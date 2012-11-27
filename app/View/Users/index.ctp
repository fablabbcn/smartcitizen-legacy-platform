<!-- File: /app/View/Users/index.ctp -->
<section class='posts list' style='width:400px';>
	<header>
		<h1><?php echo __('Lastest Citizens') ?></h1>
		<div class='action'>
			<?php echo $this->Html->link('+ '.__('Register'), array('controller' => 'users', 'action' => 'add'),array('class'=>'button')); ?>
		</div>
	</header>
<?php
			if(empty($users))
				echo __('There is no user yet') ;
			else
				echo $this->element('users_list', array("users" => $users));
?>
		<footer>
		</footer>
</section>	