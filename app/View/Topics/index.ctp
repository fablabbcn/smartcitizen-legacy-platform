<!-- File: /app/View/Topics/index.ctp -->
<?php //debug ($data); ?>
<section class='topics list' style='width:400px';>
	<header>
		<h1><?php echo __("Ressource's Topics") ?></h1>
		<div class='action'>
			<?php 
				if (isset($actions) && in_array("add", $actions)){
					echo $this->Html->link('+ '.__('Add a topic'), array('controller' => 'topics', 'action' => 'add'),array('class'=>'button'));
				}
			?>
		</div>
	</header>
<?php
	if(empty($data))
		echo __('There is no topic yet') ;
	else{
		foreach ($data as $topic){
			$count = count($topic['Post']);
?>
			<a href='<?php echo Router::url(array('controller' => 'topics','action' => 'view', $topic['Topic']['id'])); ?>' >
				<article class='list-item topic'>
					<h3><?php echo h($topic['Topic']['name']) ?></h3>
					<p><?php echo $count ?> post in this category. 
<?php if($count>0){?>
					last update on <?php echo $topic['Post'][0]['modified']; ?></p>
<?php }?>
				</article>
			</a>
<?php
		}
	}
?>
		<footer>
		</footer>
</section>	
