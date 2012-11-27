<!-- File: /app/View/Posts/view.ctp -->
<?php
//debug($feed['Feed']);
?>

<section style='width:650px'>
	<header>
		<div class='action'>
		    <?php echo $this->Form->postLink(
                'X '.__('Delete'),
                array('action' => 'delete', $feed['Feed']['id']),
                array('confirm' => __('Are you sure?'), 'class'=>'button'));
            ?>
            <?php echo $this->Html->link('> '.__('Edit'), array('action' => 'edit', $feed['Feed']['id']), array('class'=>'button')); ?>
		</div>
		<h1>Sensor <?php echo $feed['Feed']['title']; ?></h1>

	</header>
	<article>
		<p><?php echo $feed['Feed']['description']?></p>
		<p>by : <?php echo substr($feed['Feed']['creator'],23)?></p>
		<p>status : <?php echo $feed['Feed']['status']?></p>
		<hr/>
	</article>
<?php
	foreach ($feed['Feed']['datastreams'] as $key=>$datastream):
//		debug($datastream);
		if($datastream['current_value']==null)
			$datastream['current_value']='--';
		
		$datatypes=array(
			"temperature",
			"humidity",
			"light",
			"sound",
			"co2"
		);
		$datastream['datatype']="other"; //default value.
		foreach($datatypes as $datatype){
			//if id or tag contains the datatype string
			if (strpos($datastream['id'],$datatype) !== false || strpos(implode(' ' , $datastream['tags']),$datatype) !== false) { 
				$datastream['datatype']=$datatype;
			}
		}
	?>
	<article>
		<header>
			<h2><?php
			echo $datastream['id'];
			?></h2>
			<?php if(isset($datastream['tags'])){
				echo '<h3>'.implode(' ' , $datastream['tags']).'</h3>';
			} ?>
		</header>
		<?php echo  $this->element('graph_line', array("datastream" => $datastream,"width" => "450", "height" => "200", "class" => 'left'));?>
		<div style="float:right; line-height:1.2em; width:120px; height:70px; background:#069dbc; border-radius: 60px; margin: 45px 0 0 20px; text-align: center; padding:25px 0">
			<span style="line-height:2.5em; "><?php echo __('Curently') ?></span><br/>
			<span style="font-size:2em; font-weight:bold; font-weight:bold; font-family: UbuntuTitlingBold;"><?php echo  $datastream['current_value'].' '. $datastream['unit']['symbol']?></span>
			<span style=""><br/><?php echo  $feed['Feed']['location']['exposure']?></span>
		</div>
		<footer></footer>
	</article>
	<?php endforeach; ?>
	<footer>
		Id : <?php echo $feed['Feed']['id']?> / Cosm Id : <?php echo $feed['Feed']['cosm_id']?> / Location : <?php echo $feed['Feed']['location']['exposure']?> /  Created: <?php echo $feed['Feed']['created']; ?>
	</footer>
</section>