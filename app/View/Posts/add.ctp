<!-- File: /app/View/Posts/add.ctp -->
<section>
	<header>
		<h1>Create Post</h1>
	</header>
	<article>
	<?php
	echo $this->Form->create('Post');
	echo $this->Form->input('title');
	echo $this->Form->end('Create Post');
	?>
	</article>
</section>