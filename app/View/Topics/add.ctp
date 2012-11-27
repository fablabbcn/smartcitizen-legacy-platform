<!-- File: /app/View/Topics/add.ctp -->
<section>
	<header>
		<h1>Create Topic</h1>
	</header>
	<article>
	<?php
	echo $this->Form->create('Topic');
	echo $this->Form->input('name');
	echo $this->Form->end('Create Topic');
	?>
	</article>
</section>