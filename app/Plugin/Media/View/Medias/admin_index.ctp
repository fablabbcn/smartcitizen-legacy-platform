<div class="bloc">
    <div class="content">
		<?php if(isset($_GET['src'])): ?>
			<div class="expand item">
				<table>
					<tr>
						<td style="width:140px"><img src="<?php echo $_GET['src']; ?>"></td>
						<td>
							<p><strong><?php echo __('File name') ?> : </strong> <?php echo basename($_GET['src']); ?></p>
						</td>
					</tr>
				</table>
				<table>
					<tr>
						<td style="width:140px"><label><?php echo __('Title') ?></label></td>
						<td><input class="title" name="title" type="text"></td>
					</tr>
					<tr>
						<td style="width:140px"><label><?php echo __('Alternative Text') ?></label></td>
						<td><input class="alt" name="alt" type="text" value="<?php echo $_GET['alt']; ?>"></td>
					</tr>
					<tr>
						<td style="width:140px"><label><?php echo __('Link target') ?></label></td>
						<td><input class="href" name="href" type="text"></td>
					</tr>
					<tr>
						<td style="width:140px"><label><?php echo __('Alignement') ?></label></td>
						<td>
							<input type="radio" name="align" class="align" id="align-none-up" value="none" <?php if($_GET['class'] == '') echo 'checked'; ?>>
							<?php echo $this->Html->image('/media/img/align-none.png'); ?><label for="align-none-up">Aucun</label>

							<input type="radio" name="align" class="align" id="align-left-up" value="left" <?php if($_GET['class'] == 'alignleft') echo 'checked'; ?>>
							<?php echo $this->Html->image('/media/img/align-left.png'); ?><label for="align-left-up">Gauche</label>

							<input type="radio" name="align" class="align" id="align-center-up" value="center" <?php if($_GET['class'] == 'aligncenter') echo 'checked'; ?>>
							<?php echo $this->Html->image('/media/img/align-center.png'); ?><label for="align-center-up">Centre</label>

							<input type="radio" name="align" class="align" id="align-right-up" value="right" <?php if($_GET['class'] == 'alignright') echo 'checked'; ?>>
							<?php echo $this->Html->image('/media/img/align-right.png'); ?><label for="align-right-up">Droite</label>
						</td>
					</tr>
					<tr>
						<td style="width:140px"> &nbsp; </td>
						<td>
							<p><a href="" class="submit"><?php echo __('Insert') ?></a>
						</td>
					</tr>
					<input type="hidden" name="file" value="<?php echo $_GET['src']; ?>" class="file">
				</table>
			</div>
		<?php endif; ?>
		<div id="plupload">
		    <div id="droparea" href="#">
		    	<p><?php echo __('Drag your file here') ?></p>
		    	<?php echo __('or') ?><br/>
		    	<a id="browse" href="#"><?php echo __('Browse')?></a> 
		    </div>
		</div>
		<table class="head" cellspacing="0">
			<thead>
				<tr>
					<th style="width:55%"><?php echo __('Files') ?></th>
					<th style="width:20%"><?php echo __('Order') ?></th>
					<th style="width:25%"><?php echo __('Action') ?></th>
				</tr>
			</thead>
		</table>
		<div id="filelist">
			<?php echo $this->Form->create('Media',array('url'=>array('controller'=>'medias','action'=>'order'))); ?>
			<?php foreach($medias as $v): $v = current($v);  ?>
				<?php require('admin_media.ctp'); ?>
			<?php endforeach; ?>
			<?php echo $this->Form->end(); ?>
		</div>

    </div>
</div>

<?php $this->Html->script('/media/js/jquery.form.js',array('inline'=>false)); ?>
<?php $this->Html->script('/media/js/plupload.js',array('inline'=>false)); ?>
<?php $this->Html->script('/media/js/plupload.html5.js',array('inline'=>false)); ?>
<?php $this->Html->script('/media/js/plupload.flash.js',array('inline'=>false)); ?>
<?php if($tinymce): ?>
	<?php $this->Html->script('/media/js/tiny_mce_popup.js',array('inline'=>false)); ?>
<?php endif; ?>
<?php $this->Html->scriptStart(array('inline'=>false)); ?>

jQuery(function(){

	$( "#filelist>form" ).sortable({
		update:function(){
			i = 0; 
			$('#filelist>form>div').each(function(){
				i++;
				$(this).find('input').val(i); 
			});
			$('#MediaAdminIndexForm').ajaxSubmit(); 
		}
	});
	
	var theFrame = $("#medias<?php echo $ref; ?>", parent.document.body);
	var uploader = new plupload.Uploader({
		runtimes : 'html5,flash',
		container: 'plupload',		
		browse_button : 'browse',
		max_file_size : '10mb',
		flash_swf_url : '<?php echo Router::url('/media/js/plupload/plupload.flash.swf'); ?>',
		url : '<?php echo Router::url(array('controller'=>'medias','action'=>'upload',$ref,$ref_id,'tinymce'=>$tinymce)); ?>',
		filters : [
			{title : "Image files", extensions : "jpg,gif,png"},
		],
		drop_element : 'droparea',
		multipart:true,
		urlstream_upload:true
	});

	uploader.init();

	uploader.bind('FilesAdded', function(up, files) {
		for (var i in files) {
			$('#filelist>form').prepend('<div class="item" id="' + files[i].id + '">' + files[i].name + ' (' + plupload.formatSize(files[i].size) + ') <div class="progressbar"><div class="progress"></div></div></div>');
		}
		uploader.start();
		$('#droparea').removeClass('dropping'); 
		theFrame.css({ height:$('body').height() + 40 }); 

	});

	uploader.bind('UploadProgress', function(up, file) {
		$('#'+file.id).find('.progress').css('width',file.percent+'%')
	});

	uploader.bind('FileUploaded', function(up, file, response){
		$('#'+file.id).after(response.response);
		$('#'+file.id).remove();
	});

	$('#droparea').bind({
       dragover : function(e){
           $(this).addClass('dropping'); 
       },
       dragleave : function(e){
           $(this).removeClass('dropping'); 
       }
	});

	$('a.del').live('click',function(e){
		e.preventDefault(); 
		elem = $(this); 
		if(confirm('<?php echo __('Do you really want to delete it?') ?> ?')){
			$.post(elem.attr('href'),{},function(data){
				elem.parents('.item').slideUp();
			});
		}
		theFrame.animate({ height:theFrame.height() - 40 }); 
	});

	$('a.toggle').live('click',function(e){
		e.preventDefault();
		var a = $(this);
		var height = a.parent().parent().find('.expand').outerHeight();
		if(a.text() == 'Afficher'){
			a.text('Cacher');
			a.parent().parent().animate({
				height : 40 + height
			});
			theFrame.animate({
				height : theFrame.height() + height
			});
		}else{
			a.text('Afficher');
			a.parent().parent().animate({
				height : 40
			});
			theFrame.animate({
				height : theFrame.height() - height
			});
		}
	});

	theFrame.height($(document.body).height() + 50);

	<?php if($tinymce): ?>
	$('a.submit').live('click',insertContent);

	function insertContent(){
		var win = window.dialogArugments || opener || parent || top;
		var item = $(this).parents('.item');
		var html = '<img src="'+item.find('.file').val()+'"';
		if( item.find('.alt').val() != '' ){
			html += ' alt="'+item.find('.alt').val()+'"';
		}
		if( item.find('.align:checked').val() != 'none' ){
			html += ' class="align'+item.find('.align:checked').val()+'"';
		}
		html += '>';
		if( item.find('.href').val() != '' ){
			html = '<a href="'+item.find('.href').val()+'" title="'+item.find('.title').val()+'">'+html+'</a>';
		}
    	win.send_to_editor(html);
    	tinyMCEPopup.close();
		return false; 
	}
	<?php endif; ?>


});

<?php $this->Html->scriptEnd(); ?>
