<!-- File: /app/View/Posts/edit.ctp -->
<div style="width:500px;">
	<section>
		<header>
			<h1>Edit the Post</h1>
		</header>
		<article>
	<?php
		echo $this->Form->create('Post', array('action' => 'edit'));
		echo $this->Form->input('title');
		echo $this->Form->input('topic_id');
	?>
	<br/><br/>
	<?php
		echo $this->Uploader->tinymce('body');
		echo $this->Form->input('id', array('type' => 'hidden'));
		echo $this->Form->end('Save Post');
	?>
		</article>
	</section>

	<section>
		<header>
			<h2><?php echo __("Manage post's images"); ?></h1>
		</header>
		<?php echo $this->Uploader->iframe('Post', $this->request->data['Post']['id']); ?>
		<footer></footer>
	</section>
</div>