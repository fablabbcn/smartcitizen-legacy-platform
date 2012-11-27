<!-- File: /app/View/Posts/view.ctp -->
<?php //print_r($data); ?>
<section style="width:500px;">
	<header>
		<div class='action'>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $data['Post']['id']),array('class'=>'button')); ?>
			<?php echo $this->Form->postLink(
                __('Delete'),
                array('action' => 'delete', $data['Post']['id']),
                array('confirm' => __('Are you sure?'),'class'=>"button"));
            ?>
        </div>
		<h1><?php echo h($data['Post']['title']); ?></h1>
		
	</header>
	<article>
		<p><?php echo $data['Post']['body'] ?></p>
	</article>
</section>
<section class='medias list'>
	<header>
		<h2>Related medias</h2>
	</header>
<?php 
	if(!empty($data['Media'])){
		foreach($data['Media'] as $k=>$v){
			$data['Media'][$k]['Media']=$v;
		}
		echo $this->element('medias_list', array("medias" => $data['Media']));
	}
?>
	<footer>
		Written by: <?php echo $data['User']['username']; ?> on: <?php echo $data['Post']['created']; ?>
	</footer>
</section>