<!-- File: /app/View/Users/view.ctp -->

<div style='width:450px'>
	<section>
		<header>
<?php if (isset($actions)){ ?>
		<div class='action'>
			<?php 
				if (in_array("delete", $actions)){
					echo $this->Form->postLink(
						'X '.__('Delete'),
						array('action' => 'delete', $data['User']['id']),
						array('confirm' => __('Are you sure?'), 'class'=>'button'));
					echo " ";
				}
				if (in_array("dashboard", $actions)){
					echo $this->Html->link('! '.__('Dashboard'), array('action' => 'dashboard', $data['User']['id']), array('class'=>'button')); 
					echo " ";
				}
				if (in_array("edit", $actions)){
					echo $this->Html->link('> '.__('Edit'), array('action' => 'edit', $data['User']['id']), array('class'=>'button')); 
				}
			?>
		</div>
<?php } ?>
		<h1><?php echo h(ucfirst ($data['User']['username'])); ?></h1>		
		</header>
		<article>
			<?php if(isset($data['User']['thumbf']))
					echo $this->Html->image(sprintf($data['User']['thumbf'],100,100),array('class'=>'avatar')) ;
				else
					echo $this->Html->image(sprintf('/img/User/default_%dx%d.png',100,100),array('class'=>'avatar')) ;
			?>
			<p>role : <?php echo h($data['User']['role'])?></p>
			<p>location : <?php echo h($data['User']['city']).', '. h($data['User']['country']); ?></p>
			<p><?php echo h($data['User']['website']) ?>.</p>
			<footer></footer>
		</article>
	</section>
	<section <?php if(!empty($data['Feed'])) echo "class='feeds list'"; ?>>	
		<header>
			<h2><?php echo h(ucfirst ($data['User']['username'])); ?>'s <?php echo __('sensors') ?></h2>
		</header>
<?php if(empty($data['Feed'])): ?>
		<article>
				<?php echo __('There is no sensor yet'); ?>
				<br/>
		</article>
<?php else :?>
<?php 
				foreach($data['Feed'] as $k=>$v){
					$data['Feed'][$k]['Feed']=$v;
				}
				echo $this->element('feeds_list', array("feeds" => $data['Feed']));
?>
<?php endif ?>
		<footer></footer>
	</section>
	<section <?php if(!empty($data['Post'])) echo "class='posts list'"; ?>>
		<header>
			<h2><?php echo h(ucfirst ($data['User']['username'])); ?>'s <?php echo __("articles") ?></h2>
		</header>
<?php if(empty($data['Post'])): ?>
		<article>
			<?php echo __('There is no post yet') ; ?>
								<br/>
		</article>
<?php else :?>
<?php 
				foreach($data['Post'] as $k=>$v){
					$data['Post'][$k]['Post']=$v;
				}
				 echo $this->element('posts_list', array("posts" => $data['Post']));
?>
<?php endif ?>
		<footer></footer>
	</section>
	<section <?php if(!empty($data['Media'])) echo "class='medias list'"; ?>>
		<header>
			<h2><?php echo h(ucfirst ($data['User']['username'])); ?>'s <?php echo __("images") ?></h2>
		</header>
<?php if(empty($data['Media'])): ?>
		<article>
				<?php echo __('There is no images yet') ; ?>
					<br/>
		</article>
<?php else :?>

<?php
				foreach($data['Media'] as $k=>$v){
					$data['Media'][$k]['Media']=$v;
				}
				echo $this->element('medias_list', array("medias" => $data['Media']));
?>

<?php endif ?>
		<footer></footer>
	</section>

</div>