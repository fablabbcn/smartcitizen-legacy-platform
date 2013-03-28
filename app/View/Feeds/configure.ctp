<!-- File: /app/View/Posts/view.ctp -->
<?php
//debug($data['Feed']);
/*
$config=array();
$config['feed_id']=$data['Feed']['cosm_id'];
$config['api_key']=$data['Feed']['cosm_key'];
$config['ssid']=$data['Feed']['wifi_ssid'];
$config['password']=$data['Feed']['wifi_pwd'];
$config['rate']=15;
*/
?>

<section style='width:410px'>
	<header>
		<div class='action'>
			<?php 
					echo $this->Html->link('< '.__('Back to sensor'), array('action' => 'view', $data['Feed']['id']), array('class'=>'button light')); 
			?>
		</div>
		<h1>Configuration of the Sensor</h1>

	</header>
	<article>
			<p>The Smart Citizen Kit need this configuration file to connect to your wifi and send the information to Cosm</p>
			<p>You can either :</p>
			
			<p>- Use the automatic system using java to upload the config file to you kit trough usb.
			<?php echo $this->Html->link('> '.__('Automatic configuration'), array('action' => 'view', $data['Feed']['id']), array('class'=>'button right')); ?>
			
			<footer></footer>
			<br/>
			<p>- Download the 'config.txt' file and place it in the root folder of the SD card</p>
			<?php echo $this->Html->link('> '.__('download config.txt'),"#", array('class'=>'button right')); ?>    
			<footer></footer>
			<br/>
			<p>- Edit the arduino script and change the initial variables :</p>
			<textarea rows='7'>
char mySSID[] = "<?php echo $data['Feed']["wifi_ssid"] ?>";
char myPassword[] = "<?php echo $data['Feed']["wifi_pwd"] ?>"
char wifiEncript[] = WEP64;
char antenna[] = EXT_ANT; // ANTENNA EXTERNA
#define PACHUBE_FEED "<?php echo $data['Feed']["cosm_id"] ?>"
#define APIKEY "<?php echo $data['Feed']["cosm_key"] ?>"
			</textarea>
			<?php /**	 echo $this->Html->link('> '.__('download file'), array('controller' => 'users','action' => 'download_config'), array('class'=>'button right')); */?>
			
		<footer></footer>
	</article>
<section>

