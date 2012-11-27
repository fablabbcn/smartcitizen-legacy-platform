	
		<?php foreach ($medias as $media) :?>
<?php /*		<a href='<?php echo Router::url(array('controller' => 'medias','action' => 'view', $media['Media']['id'])); ?>' > */?>
			<article class='media list-item'>
				<div class="icon-list">
<?php			if(isset($media['Media']['filef']))
					echo $this->Html->image(sprintf($media['Media']['filef'],100,100));
				else
					echo $this->Html->image(sprintf('/Media/default_%dx%d.jpg',100,100));?>
				</div>
<?php $filename= substr(basename($media['Media']['file']),0,-4);
if (strlen($filename)>20) $filename = substr($filename,0,20)."..." ?>
				<h4><?php echo $filename ?></h4>
			</article>
<?php /*		</a> */?>
		<?php endforeach;?>