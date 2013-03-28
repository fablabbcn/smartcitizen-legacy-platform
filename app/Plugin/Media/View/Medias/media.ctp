<?php $sizes = getimagesize(IMAGES.$v['file']);  ?>
<div class="item <?php if($thumbID && $v['id'] === $thumbID): ?>thumbnail<?php endif; ?>">
	<input type="hidden" value="<?php echo $v['position']; ?>" name="data[Media][<?php echo $v['id']; ?>]">
	<div class="visu"><?php echo $this->Html->image($v['file']) ?></div>
	<?php echo basename($v['file']); ?>
	<div class="actions">
		<?php if($thumbID !== false && $v['id'] !== $thumbID): ?>
			<?php echo $this->Html->link(__("Select as cover image"),array('action'=>'thumb',$v['id'])); ?> -
		<?php endif; ?>
		<?php echo $this->Html->link(__('Delete'),array('action'=>'delete',$v['id']),array('class'=>'del')); ?> 
		<?php if ($tinymce): ?>
			- <a href="#" class="toggle"><?php echo __('More')?></a>
		<?php endif ?>
	</div>
	<div class="expand">
		<table>
			<tr>
				<td style="width:140px"><?php echo $this->Html->image($v['file']) ?></td>
				<td>
					<p><strong><?php echo __('File name') ?> :</strong> <?php echo basename($v['file']); ?></p>
					<p><strong><?php echo __('Image size') ?> :</strong> <?php echo $sizes[0].'x'.$sizes[1]; ?></p>
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td style="width:140px"><label><?php echo __('Title') ?></label></td>
				<td><input class="title" name="title" type="text"></td>
			</tr>
			<tr>
				<td style="width:140px"><label><?php echo __('Alternative text') ?></label></td>
				<td><input class="alt" name="alt" type="text"></td>
			</tr>
			<tr>
				<td style="width:140px"><label><?php echo __('Link target') ?></label></td>
				<td><input class="href" name="href" type="text"></td>
			</tr>
			<tr>
				<td style="width:140px"><label><?php echo __('Alignement') ?></label></td>
				<td>
					<input type="radio" name="align-<?php echo $v['id']; ?>" class="align" id="align-none-<?php echo $v['id']; ?>" value="none" checked>
					<?php echo $this->Html->image('/media/img/align-none.png'); ?><label for="align-none-<?php echo $v['id']; ?>">Aucun</label>

					<input type="radio" name="align-<?php echo $v['id']; ?>" class="align" id="align-left-<?php echo $v['id']; ?>" value="left">
					<?php echo $this->Html->image('/media/img/align-left.png'); ?><label for="align-left-<?php echo $v['id']; ?>">Gauche</label>

					<input type="radio" name="align-<?php echo $v['id']; ?>" class="align" id="align-center-<?php echo $v['id']; ?>" value="center">
					<?php echo $this->Html->image('/media/img/align-center.png'); ?><label for="align-center-<?php echo $v['id']; ?>">Centre</label>

					<input type="radio" name="align-<?php echo $v['id']; ?>" class="align" id="align-right-<?php echo $v['id']; ?>" value="right">
					<?php echo $this->Html->image('/media/img/align-right.png'); ?><label for="align-right-<?php echo $v['id']; ?>">Droite</label>
				</td>
			</tr>
			<tr>
				<td style="width:140px"> &nbsp; </td>
				<td>
					<p><a href="" class="submit"><?php echo __('Insert') ?></a> <?php echo $this->Html->link(__('Delete'),array('action'=>'delete',$v['id']),array('class'=>'del')); ?></p>
				</td>
			</tr>
			<input type="hidden" name="file" value="<?php echo $this->Html->url('/img/'.$v['file']); ?>" class="file">
		</table>
	</div>
</div>