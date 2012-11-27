<?php
$this->start('log');
//echo $this->element('sql_dump');
//	var_dump($feeds);
$this->end();
?>

<section class='feeds list' style="width:400px;">	
	<header>
		<div class='action'>
			<?php echo $this->Html->link('+ '.__('Add your sensor'), array('controller' => 'feeds', 'action' => 'add'),array('class'=>'button')); ?>
		</div>
		<h1><?php echo __('Lastest Sensors') ?></h1>
	</header>
<?php
		if(empty($feeds))
			echo __('There is no sensors yet');
		else
			echo $this->element('feeds_list', array("feeds" => $feeds));
?>

	<footer></footer>
</section>
	
<!-- File: /app/View/Feeds/index.ctp -->

