<?php
$this->start('aside');
//	echo $this->element('search_input', array("keyword" => $keyword));
$this->end();


function fromNow($debut){
	$nbr = time() - strtotime($debut);
	$u="s";
	$coef = array("minutes"=>60,'hours'=>60,'days'=>24,'years'=>365);
	foreach($coef as $key=>$div)
		if($nbr>$div){
			$nbr = ($nbr/$div);
			$u=$key;
		}else break;
	return round($nbr).$u;
}
?>



<div style="width:400px">
	<section>	
		<header>
			<div class='action'>
				<?php echo $this->Html->link('+ '.__('Rss feed'), array('controller' => 'mixed', 'action' => 'updates',"ext" => "rss"),array('class'=>'button light')); ?>
			</div>
			<h1><?php echo __('Updates') ?></h1>
			<span class='subh1'><?php echo __('most recent')?></span>
		</header>
<?php 
foreach($data['Update'] as $update):
?>
<?php 
		$model=$update['model'];
		$controler = strtolower($model).'s';
		if(isset($update['User'])){
//			$userImage = $update['User']['thumbf'];
			$user=ucfirst($update['User']['username']);
			$userlink=$this->Html->link($user, array('controller' => 'users', 'action' => 'view',$update['User']['id'])) ;
			$user_id=$update['User']['id'];
		}else{
			$userImage = 'User/default_%dx%d.png';
			$user='anonymous';
			$userlink=$user;
			$user_id=null;
		}
//		debug($update);
	
		if($update['model']=='Feed'){		?>
		
		<header>
			<h2>
				<?php echo $userlink ?>
				Pluged in a new 
				<?php echo $this->Html->link('sensor', array('controller' => 'feeds', 'action' => 'view',$update['Feed']['id']));?> :
			</h2>
		</header>
		<a class='update-link'href='<?php echo Router::url(array('controller' => 'feeds', 'action' => 'view',$update['Feed']['id'])); ?>' >
			<article class='item-update'>
				<div class="icon-update">
						<span class="small-circle"><?php echo  count($update['Feed']['datastreams']) ?></span>
					</div>
					<h4><?php echo $update['Feed']['title']; ?></h4>
					Curently <?php echo $update['Feed']['location']['exposure']?> : 
					<?php $flatData='';
						foreach ($update['Feed']['datastreams'] as $datastream) {
								$flatData .= $datastream['current_value'].' '.  $datastream['unit']['symbol']. ' / ';
						}
						echo substr($flatData, 0, -2)
					?><br/>
			<footer/>
			</article>
		</a>
<?php }elseif($update['model']=='User'){ ?>
		<header>
			<h2>
				<?php echo $userlink ?>
				joined our network
			</h2>
		</header>
		<a class='update-link'href='<?php echo Router::url(array('controller' => 'users', 'action' => 'view',$update['User']['id'])); ?>' >
			<article class='item-update'>
					<div class="icon-update">
	<?php			if(isset($update['User']['thumbf']))
						echo $this->Html->image(sprintf($update['User']['thumbf'],100,100));
					else
						echo $this->Html->image(sprintf('/img/User/default_%dx%d.png',100,100));?>
					</div>
					<h4><?php echo h($update['User']['username']) ?></h4>
					<?php echo count($update['Feed']) ?> sensors, <?php echo count($update['Post']) ?> posts, <?php echo count($update['Media']) ?> images<br/>
					<small><?php echo h($update['User']['role'])?> in <?php echo h($update['User']['city']).', '. h($update['User']['country']); ?></small>
			<footer/>
			</article>
		</a>
<?php }elseif($update['model']=='Post'){ ?>
		<header>
			<h2>
				<?php echo $user ?>
				posted a new 
				<?php echo $this->Html->link('ressource', array('controller' => 'posts', 'action' => 'view',$update['Post']['id']));?> 
			</h2>
		</header>
		<a class='update-link'href='<?php echo Router::url(array('controller' => 'posts', 'action' => 'view',$update['Post']['id'])); ?>' >
			<article class='item-update'>
				<div class="icon-update">
	<?php		if(isset($update['Post']['thumbf']))
					echo $this->Html->image(sprintf($update['Post']['thumbf'],100,100));
				else
					echo $this->Html->image(sprintf('/img/Post/default_%dx%d.png',100,100));?>
				</div>
				<h4><?php echo h($update['Post']['title']) ?></h4>
				<?php echo h(substr(strip_tags($update['Post']['body']),0,150)) ?>
			<footer/>
			</article>
		</a>

<?php } /*?>
		<div class="time-update">
				<p><?php echo date("m.d.y",strtotime($update[$model]['created'])); ?></p>
		</div>
		<div class="icon-update">
<?php			echo $this->Html->image(sprintf($userImage,100,100)); ?>
		</div>
*/?>		<footer>
			<?php echo fromNow($update[$model]['created']); ?> ago.
		</footer>

<?php endforeach; ?>
		<footer></footer>
	</section>
	
</div>