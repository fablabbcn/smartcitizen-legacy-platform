
		<?php foreach ($feeds as $feed) : ?>
		<a href="<?php echo Router::url(array('controller' => 'feeds','action' => 'view', $feed['Feed']['id'])); ?>">
			<article class='list-item'>
				<div class="icon-list">
					<span class="small-circle"><?php echo  count($feed['Feed']['datastreams']) ?></span>
				</div>
				<h3><?php echo $feed['Feed']['title']; ?></h3><p>
				Curently <?php echo $feed['Feed']['location']['exposure']?> : 
				<?php $flatData='';
					foreach ($feed['Feed']['datastreams'] as $datastream) {
							$flatData .= $datastream['current_value'].' '.  $datastream['unit']['symbol']. ' / ';
					}
					echo substr($flatData, 0, -2)
				?></p>
				<p><small>Created on <?php echo date("m.d.y",strtotime($feed['Feed']['created'])); ?> /
				Last update on <?php echo date("m.d.y",strtotime($feed['Feed']['updated'])); ?></small></p>
			</article>
		</a>
		<?php endforeach ?>