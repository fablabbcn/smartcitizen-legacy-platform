<?php
	if(!isset($width))
		$width='500';
	if(!isset($height))
		$height='150';
	if(!isset($class))
		$class='';
	if(!isset($style))
		$style='';
		
	$id=md5($datastream['id']);
?>			
	<div id="graph_<?php echo $id ?>" style='width:<?php echo $width?>px; height:<?php echo $height?>px; <?php echo $style?>' class="<?php echo $class?>">

<?php if(isset($datastream['datapoints'])){ ?>

<script><?php $this->start('inline-script');?>
	$(document).ready( function() {
		var datas_<?php echo $id ?> = <?php echo json_encode($datastream['datapoints']) ?>;
		var i = datas_<?php echo $id ?>.length;
		while ( i-- ) {
		  datas_<?php echo $id ?>[i].at = datas_<?php echo $id ?>[i].at.replace(/T/," ").replace(/\..*$/, "");
		}

		// CREATE GRAPH

		graph = new Morris.Line({
			element           : 'graph_<?php echo $id ?>',
			data              : datas_<?php echo $id ?>,
			xkey              : 'at',
			ykeys             : [ 'value' ],
			ymax              : 'auto',
			ymin              : 'auto',
			postUnits         : '<?php echo $datastream['unit']['symbol']?>',
			labels            : [ '<?php echo $datastream['id']?>' ],
			lineColors        : [ '#dd7711' ],
			pointGrowColor    : "#FF9d22",
			pointSize         : 1,
			gridStrokeWidth   : 0.3,
			gridLineColor     : '#888',
			gridTextColor     : '#888',
			gridTextSize      : 11,
//			marginTop         : 25,
//			marginRight       : 15,
//			marginBottom      : 30,
//			marginLeft        : 15,
			hoverPaddingX     : 13,
			hoverPaddingY     : 8,
			hoverBorderWidth  : 3,
			hoverBorderColor  : '#000',
			hoverOpacity      : .8,
			hoverMargin       : 10,
			hoverFillColor    : '#000',
			hoverFontSize     : 11,
			hoverXFontSize    : 20,
			hoverLabelColor   : '#fff',
			hoverValueColor   : '#dd7711',
			smooth            : true,
			hideHover         : true,
			xLabels           : "hour"
		});
	});
<?php $this->end();?></script>
<?php
	}else{ 
		 echo '<span style="line-height:'.$height.'px">'.__("No data received yet").'</span>'; 
	} ?>
	
	
	</div>
	
	
	
	
	
	
