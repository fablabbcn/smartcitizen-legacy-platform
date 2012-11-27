<!-- File: /app/View/Posts/edit.ctp -->

<section style='width:300px'>
	<header>
		<h1>Edit a Feed</h1>
	</header>
	<article>
<?php
		echo $this->Form->create('Feed');
		echo $this->Form->input('title');
		echo $this->Form->input('description', array('rows' => '3'));
?>
<br/>
<legend>(<b>tip</b> : Click on the map to fill this 2 fields.)</legend>
<?php
		echo $this->Form->input('longitude');
		echo $this->Form->input('latitude');
		echo $this->Form->input('id', array('type' => 'hidden'));
		echo $this->Form->end('Save Feed');
?>
	</article>
</section>