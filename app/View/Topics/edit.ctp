<!-- File: /app/View/Topics/add.ctp -->
<section>
	<header>
		<h1>Edit Topic</h1>
	</header>
	<article>
	<?php
	echo $this->Form->create('Topic');
	echo $this->Form->input('name');
	echo $this->Form->end('Save Topic');
	?>
	</article>
</section>