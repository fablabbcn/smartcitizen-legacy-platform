<!-- File: /app/View/Posts/view.ctp -->
<?php
//debug($data['Feed']);
?>

<section style='width:550px'>
	<header>
<?php if (isset($actions)){ ?>
		<div class='action'>
			<?php 

				if (in_array("configure", $actions)){
					echo $this->Html->link('> '.__('Configure'), array('action' => 'configure', $data['Feed']['id']), array('class'=>'button')); 
				}
				echo " ";
				if (in_array("edit", $actions)){
					echo $this->Html->link('> '.__('Edit'), array('action' => 'edit', $data['Feed']['id']), array('class'=>'button')); 
				}
				echo " ";
				if (in_array("delete", $actions)){
					echo $this->Form->postLink(
						'X '.__('Delete'),
						array('action' => 'delete', $data['Feed']['id']),
						array('confirm' => __('Are you sure?'), 'class'=>'button'));
					
				}
			?>
		</div>
<?php } ?>
		<h1>Sensor <?php echo $data['Feed']['title']; ?></h1>

	</header>
	<article>
		<div class='col2'>
			<p><?php echo $data['Feed']['description']?></p>
		</div>
		<div class='col2 tright'>
			<?php if(isset($data['Hardware']['thumbf']))
				echo $this->Html->image(sprintf($data['Hardware']['thumbf'],100,100),array('class'=>'right')) ;
			else
				echo $this->Html->image(sprintf('/img/Post/21-smart_citizen_board_big_%dx%d.png',100,100),array('class'=>'right')) ;
			?>
			<p><i>location</i> : <?php echo $data['Feed']['location']['exposure'] ?></p>
			<p><i>status</i> : <?php echo $data['Feed']['status']?></p>
			<p><i>Hardware</i> : <br/><a href='http://beta.smartcitizen.me/posts/view/21'>Smart Citizen Kit</a></p>
		</div>
		<footer></footer>
	</article>
<section>
<section>
	<header>
		<h2>Datastreams</h2>
	</header>
<?php
	foreach ($data['Feed']['datastreams'] as $key=>$datastream):
//		debug($datastream);
		if($datastream['current_value']==null)
			$datastream['current_value']='--';
		
		
	?>
	<article>
			
		<!--
		<div style="float:left; line-height:1.2em; width:120px; height:70px; background:#069dbc; border-radius: 60px; margin: 45px 0 0 20px; text-align: center; padding:25px 0">
			<span style="line-height:2.5em; "><?php echo __('Curently') ?></span><br/>
			<span style="font-size:2em; font-weight:bold; font-weight:bold; font-family: UbuntuTitlingBold;"><?php echo  $datastream['current_value'].' '. $datastream['unit']['symbol']?></span>
			<span style=""><br/><?php echo  $data['Feed']['location']['exposure']?></span>
		</div>
		-->
		<div style="float:left; width:120px;">
			<?php echo  $this->element('graph_icon', array("datastream" => $datastream,"width" => "120", "height" => "120"));?>
			<div style="float:left; line-height:1.2em; width:120px;text-align: center;">
			<span style="line-height:2.5em; "><?php echo __('Curently') ?></span><br/>
			<span style="color:#dd7711;font-size:2em; font-weight:bold; font-weight:bold; font-family: UbuntuTitlingBold;"><?php echo  $datastream['current_value'].' '. $datastream['unit']['symbol']?></span>
			<span style=""><br/>
			<?php if(isset($datastream['tags'])){
				echo implode(' ' , $datastream['tags']);
			} ?></span>
		</div>
		</div>
		<?php echo  $this->element('graph_line', array("datastream" => $datastream,"width" => "370", "height" => "190", "class" => 'right'));?>
		<footer>
			</footer>
	</article>
	<br/>
	<?php endforeach; ?>
</section>

<section>
	<header>
		<h2>About the owner</h2>
	</header>
	<article>
	<?php if(isset($data['User'])): ?>
		<?php if(isset($data['User']['thumbf']))
				echo $this->Html->image(sprintf($data['User']['thumbf'],100,100),array('class'=>'avatar')) ;
			else
				echo $this->Html->image(sprintf('/img/User/default_%dx%d.png',100,100),array('class'=>'avatar')) ;
		?>
		<h3><?php echo h($data['User']['username'])?></h3>
		<p>role : <?php echo h($data['User']['role'])?></p>
		<p>location : <?php echo h($data['User']['city']).', '. h($data['User']['country']); ?></p>
		<p><?php echo h($data['User']['website']) ?>.</p>
	<?php else: ?>
		<p>cosm username : <?php echo h($data['Feed']['creator']) ?>.</p>
	<?php endif; ?>
		<footer></footer>
	</article>
	<br/>
	<footer>
		Id : <?php echo $data['Feed']['id']?> / Cosm Id : <?php echo $data['Feed']['cosm_id']?> / Location : <?php echo $data['Feed']['location']['exposure']?> /  Created: <?php echo $data['Feed']['created']; ?>
	</footer>
</section>