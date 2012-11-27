<!-- app/View/Users/add.ctp -->
<div style="width:300px">
<section>
	<header>
		<h1><?php echo __('Edit your profile'); ?></h1>
	</header>
	<article>
	<?php 
		echo $this->Form->create('User'); 
        echo $this->Form->input('username');
		echo $this->Form->input('city');
		echo $this->Form->input('country');
		echo $this->Form->input('website');
        echo $this->Form->input('email');
        echo $this->Form->input('password');
		/*
		echo $this->Form->input('role', array(
            'options' => array('admin' => 'Admin', 'author' => 'Citizen')
        ));
		*/
		echo $this->Form->end(__('Submit')); ?>
    </article>

</section>
<section>
	<header>
		<h2><?php echo __('Manage your images'); ?></h1>
	</header>
<?php echo $this->Uploader->iframe('User', $this->request->data['User']['id']); ?>
	<footer></footer>
</section>
<br/><br/>
</div>