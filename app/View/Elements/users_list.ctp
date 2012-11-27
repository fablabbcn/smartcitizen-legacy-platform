	
		<?php foreach ($users as $user) :?>
		<a href='<?php echo Router::url(array('controller' => 'users','action' => 'view', $user['User']['id'])); ?>' >
			<article class='list-item user'>
				<div class="icon-list">
<?php			if(isset($user['User']['thumbf']))
					echo $this->Html->image(sprintf($user['User']['thumbf'],100,100));
				else
					echo $this->Html->image(sprintf('/img/User/default_%dx%d.png',100,100));?>
				</div>
				<h3><?php echo h($user['User']['username']) ?></h3>
				<p><?php echo count($user['Feed']) ?> sensors, <?php echo count($user['Post']) ?> posts, <?php echo count($user['Media']) ?> images<br/><p>
				<p><small><?php echo h($user['User']['role'])?> in <?php echo h($user['User']['city']).', '. h($user['User']['country']); ?></small></p>
				
			</article>
		</a>
		<?php endforeach;?>