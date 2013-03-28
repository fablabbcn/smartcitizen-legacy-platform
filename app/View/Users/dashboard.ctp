<!-- File: /app/View/Users/view.ctp -->

<div style='width:450px'>
	<section>
		<header>
			<div class='action'>
				<?php echo $this->Html->link('> '.__('My Profile'), array('action' => 'view',$data['User']['id']),array('class'=>'button')); ?>
				<?php echo $this->Html->link('x '.__('logout'), array('action' => 'logout'),array('class'=>'button light')); ?>
				
			</div>
			<h1>My Dashboard</h1>		
		</header>
		<article>
			<?php if(isset($data['User']['thumbf']))
					echo $this->Html->image(sprintf($data['User']['thumbf'],100,100),array('class'=>'right')) ;
				else
					echo $this->Html->image(sprintf('/img/User/default_%dx%d.png',100,100),array('class'=>'right')) ;
			?>
			<p>Welcome <?php echo h(ucfirst ($data['User']['username'])); ?></p>
			<p>You are actual participation level is : 5xp</p>
			<p>In order to participate further more in your city you can :</p>
		</article>
<?php if(in_array('photo',$todo)){ ?>
		<article>
			<h2>Update your photo profile</h2>
			and show a more representative image of you
			<?php echo $this->Html->link('> '.__('Upload a photo'), array('controller' => 'users','action' => 'edit',$data['User']['id']), array('class'=>'right button')); ?>
			<p>10xp<p>
		</article>
<?php } ?>
<?php if(in_array('email',$todo)){?>
		<br/>
		<article>
			<h2>Verify your email</h2>
			<?php echo $this->Html->link('> '.__('Send a new email'), array('controller' => 'users','action' => 'email_confirm'), array('class'=>'button right')); ?>
			<p>And be able to add content to the website (Ressources)<p>
			<p>20xp<p>
		</article>
<?php } ?>
<?php if(in_array('cosm',$todo)){?>
		<br/>
		<article>
			<h2>Connect to your cosm account</h2>
			<?php echo $this->Html->link('> '.__('Connect to cosm'), array('controller' => 'users','action' => 'cosm_connect'), array('class'=>'button right')); ?>		
			<p>And be able to add sensors<p>
			<p>20xp<p>
		</article>
<?php } ?>
<?php if(in_array('feed',$todo)){?>
		<br/>
		<article>
			<h2>Register a new sensor</h2>
			<?php echo $this->Html->link('> '.__('New Sensor'), array('controller' => 'feeds','action' => 'add'), array('class'=>'button right')); ?>		
			<p>And share the data your collecting from the city <p>
			<p>100xp<p>
		</article>
<?php } ?>
<?php if(in_array('post',$todo)){?>
		<br/>
		<article>
			<h2>Write an article</h2>
			<?php echo $this->Html->link('> '.__('New Article'), array('controller' => 'posts','action' => 'add'), array('class'=>'button right')); ?>		
			<p>And share your knowledge of smart citizen<p>
			<p>50xp<p>
		</article>
<?php } ?>
			<footer></footer>
		</article>
	</section>
<?php /*
	<section>	
		<header>
			<h2>My Profile</h2>
			<div class='action'>
				<?php echo $this->Html->link('> '.__('Edit'), array('controller' => 'users','action' => 'edit',$data['User']['id']), array('class'=>'button')); ?>
			</div>
		</header>
		<article>
		<?php if(isset($data['User']['thumbf']))
				echo $this->Html->image(sprintf($data['User']['thumbf'],100,100),array('class'=>'avatar')) ;
			else
				echo $this->Html->image(sprintf('/img/User/default_%dx%d.png',100,100),array('class'=>'avatar')) ;
		?>
		<p>username : <?php echo h($data['User']['username'])?></p>
		<p>role : <?php echo h($data['User']['role'])?></p>
		<p>city : <?php echo h($data['User']['city'].','.$data['User']['country']);?></p>
		<p>website : <?php echo h($data['User']['website'])?></p>
		<p>email : <?php echo h($data['User']['email'])?></p>
		<p>Cosm User : <?php echo h($data['User']['cosm_user'])?></p>
		<footer></footer>
		</article>
		<footer></footer>
	</section>
	<section class='feeds list'>	
		<header>
			<h2><?php echo __('My sensors') ?></h2>
			<div class='action'>
				<?php echo $this->Html->link('+ '.__('Add a new sensor'), array('controller' => 'feeds','action' => 'add'), array('class'=>'button')); ?>
			</div>
		</header>
<?php if(empty($data['Feed'])): ?>
		<a href="<?php echo Router::url(array('controller' => 'feeds','action' => 'add')); ?>">
			<article>
				<?php echo __('There is no sensor yet'); ?>
				<br/><br/><br/>
				<h4><?php echo __('Click to add one'); ?></h4>
			</article>
		</a>
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
	<section class='posts list'>
		<header>
			<div class='action'>
				<?php echo $this->Html->link('+ '.__('Add a new post'), array('controller' => 'posts','action' => 'add'), array('class'=>'button')); ?>
			</div>
			<h2><?php echo __("My posts") ?></h2>
		</header>
<?php if(empty($data['Post'])): ?>
		<a href="<?php echo Router::url(array('controller' => 'posts','action' => 'add')); ?>">
			<article>
				<?php echo __('There is no post yet') ; ?>
					<br/><br/><br/>
				<h4><?php echo __('Click to add one'); ?></h4>
			</article>
		</a>
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
	<section class='medias list'>
		<header>
			<h2><?php echo __("My images") ?></h2>
			<div class='action'>
				<?php echo $this->Html->link('+ '.__('Upload new photos'), array('controller' => 'users','action' => 'edit',$data['User']['id']), array('class'=>'button')); ?>
			</div>
		</header>
<?php if(empty($data['Media'])): ?>
		<a href="<?php echo Router::url(array('controller' => 'users','action' => 'edit',$data['User']['id'])); ?>">
			<article>
				<?php echo __('There is no images yet') ; ?>
					<br/><br/><br/>
				<h4><?php echo __('Click to edit your profile and add somes'); ?></h4>
			</article>
		</a>
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
	<?php // echo $this->element('medias_list', array("medias" => $data['Media']));?>
*/ ?>
</div>