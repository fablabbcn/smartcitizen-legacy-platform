<!-- File: /app/View/Feeds/add.ctp -->
<section style='width:330px'>
	<header>
		<h1>Register a new Kit</h1>
	</header>
	<article>
		<p> Please describe precisely where is your kit. The more accurate is it, the more it's meaningfull for the comunity</p>
		<p> You don't have a kit ? Please <?php echo $this->Html->link('contact', array('controller' => 'pages', 'action' => 'display', 'share')); ?> us to get one ! </p>
<?php
		echo $this->Form->create('Feed');
		echo $this->Form->input('title');
		echo $this->Form->input('description', array('rows' => '3'));
		echo $this->Form->input('location');
		echo $this->Form->input('elevation');
?>
<br/>
<legend>(<b>tip</b> : Click on the map to fill this 2 fields.)</legend>
<?php
		echo $this->Form->input('longitude');
		echo $this->Form->input('latitude');
?>
<hr/>
<legend>These informations are for the configuration of the kit, and will not be displaed publicly</legend>
<?php
		echo $this->Form->input('wifi_ssid');
		echo $this->Form->input('wifi_pwd');
		echo $this->Form->input('Kit_number');
		echo $this->Form->end('Save Sensor');
?>
	</article>
</section>