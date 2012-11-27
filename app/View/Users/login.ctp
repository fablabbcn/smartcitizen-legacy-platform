<section style="width:300px">
    <header>
		<h1><?php echo __('Login to Participate'); ?></h1>
	</header>
	<article>
	
	<?php echo $this->Form->create('User'); ?>
    <?php
        echo $this->Form->input('username');
        echo $this->Form->input('password');
    ?>
	<?php echo $this->Form->end(__('Login')); ?>
	</article>
</section>

<section style="width:300px">
	<header>
		<h1><?php echo __('New Here ?'); ?></h1>
	</header>
	<article>
		<p>The website aim to connect people looking for a better city. You're part of them ? feel free to join us !</p>
		<?php echo $this->Html->link(__('Register'), array('action' => 'add'),array('class'=>'right button')); ?>
	<footer></footer>
	</article>
</section>