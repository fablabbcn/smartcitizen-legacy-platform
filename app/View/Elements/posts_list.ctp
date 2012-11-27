	
		<?php foreach ($posts as $post) :?>
		<a href='<?php echo Router::url(array('controller' => 'posts','action' => 'view', $post['Post']['id'])); ?>' >
			<article class='list-item post'>
				<div class="icon-list">
<?php			if(isset($post['Post']['thumbf']))
					echo $this->Html->image(sprintf($post['Post']['thumbf'],100,100));
				else
					echo $this->Html->image(sprintf('/img/Post/default_%dx%d.png',100,100));?>
				</div>
				<h3><?php echo h($post['Post']['title']) ?></h3>
				<p><?php echo h(substr(strip_tags($post['Post']['body']),0,150)) ?></p>
				
			</article>
		</a>
		<?php endforeach;?>