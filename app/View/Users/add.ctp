<!-- app/View/Users/add.ctp -->
<section style="width:300px">
     <header>
		<h1><?php echo __('Create a new User'); ?></h1>
	</header>
	<article>
	<?php 
		echo $this->Form->create('User', array('type' => 'file')); 
		echo $this->Form->input('username');
		echo $this->Form->input('city');
		echo $this->Form->input('country');
		echo $this->Form->input('website');
        echo $this->Form->input('email');
        echo $this->Form->input('password');
		echo $this->Form->input('Media.file', array('type' => 'file'));
    ?>
	<?php echo $this->Form->end(__('Submit')); ?>
    </article>
</section>