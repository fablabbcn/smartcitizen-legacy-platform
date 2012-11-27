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
		<p>Location : <?php echo $feed['Feed']['location']['exposure']?><p>
	</article>

	<footer>
		Id : <?php echo $feed['Feed']['id']?> / Cosm Id : <?php echo $feed['Feed']['cosm_id']?> /  Created: <?php echo $feed['Feed']['created']; ?>
	</footer>
</section>


<section>
	<header>
		<h2>DataStream</h2>
	</header>
	<article class='datastream' style='height:150px; width:600px;overflow-x: auto;overflow-y: hidden;'>
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
			<div id="indicator_<?php echo $datastream['id'] ?>" class='indicator' style="float:left; line-height:1.2em; width:120px; height:70px; background:#069dbc; border-radius: 60px; margin: 45px 0 0 20px; text-align: center; padding:25px 0">
				<span style="line-height:2.5em; "><?php echo __('Curently') ?></span><br/>
				<span style="font-size:2em; font-weight:bold; font-weight:bold; font-family: UbuntuTitlingBold;"><?php echo  $datastream['current_value'].' '. $datastream['unit']['symbol']?></span>
				<span style=""><br/><b><?php echo $datastream['id']; ?></b>
				<?php if(isset($datastream['tags'])){
					echo implode(' ' , $datastream['tags']);
				} ?></span>
			</div>
			
<?php endforeach; ?>

			<footer></footer>
	</article>
	<article class='graph'>
		<div id='graph' style='width: 600px; height: 200px;'> </div>
		<script>
		<?php 
			//Preparing data for graph.
			$chart=array('x'=>array(),'y'=>array(),'label'=>array(),'symbol'=>array());
			foreach ($feed['Feed']['datastreams'] as $i=>$datastream){
				$chart['label'][$i]=$datastream['id'];
				$chart['symbol'][$i]=$datastream['unit']['symbol'];
				foreach($datastream['datapoints'] as $j=>$datapoint){
				
					//Cleaning timestamp.
				//	$datapoint['at']=preg_replace('/T/'," ",$datapoint['at']);
				//	$datapoint['at']=preg_replace('/\..*$/', "",$datapoint['at']);
					//Recompiling the value with previous one.
					$chart['x'][$i][$j]=strtotime($datapoint['at']); //'at' value for index.
					$chart['y'][$i][$j]=$datapoint['value']; //'at' value for index.
				}
			}
		?>
		
		
		
		
		
		<?php $this->start('script');?>
				
				
				$(function() {
					
					var dataX = <?php echo json_encode($chart['x']) ?>;
					var dataY = <?php echo json_encode($chart['y']) ?>;
					
					var labels = <?php echo json_encode($chart['label']) ?>;
					
					// CREATE GRAPH
					var r = Raphael("graph");
					var chart = r.linechart(
						10, 10,      // top left anchor
						490, 180,    // bottom right anchor
						dataX,
						dataY,
						{	
							axis: "0 0 1 0",   // draw axes on the left and bottom
							symbol: "",
							colors: [
							   "#995555",
							   "#995555",
							   "#995555",
							   "#995555",
							   "#995555",       
							   "#995555",       
							   "#995555",       // the first line is red 
							   "#555599"       // the third line is invisible
							]
						  });
					  chart.hoverColumn(function () {
							this.tags = r.set();

							for (var i = 0, ii = this.y.length; i < ii; i++) {
								this.tags.push(r.tag(this.x, this.y[i], this.values[i], 160, 10).insertBefore(this));
							}
						}, function () {
							this.tags && this.tags.remove();
						}
					);
					for( var i = 0, l = chart.axis.length; i < l; i++ ) {
					  // change the axis and tick-marks
					  chart.axis[i].attr("stroke", "#999999");
								   
					  // change the axis labels
					  var axisItems = chart.axis[i].text.items
					  for( var ii = 0, ll = axisItems.length; ii < ll; ii++ ) {
						 axisItems[ii].attr("fill", "#999999");
					  }
					}
				});
					
		<?php $this->end();?>
		</script>
	</article>
