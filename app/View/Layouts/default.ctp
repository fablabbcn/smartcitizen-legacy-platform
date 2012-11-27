<?php
/**
 *
 * Main Layout for classic html request (xhr disable)
 *
 * @copyright     Copyright 2012, Collectif277. (http://collectif277.fr)
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

?>
<?php echo $this->Html->docType('html5');?>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<?php echo $this->Html->charset(); ?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>
		Smart Citizen :
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

//		echo $this->Html->css('cake.generic');
		echo $this->Html->css('h5bp');
		
		echo $this->Html->css('leaflet');
		echo '<!--[if lte IE 8]>'.$this->Html->css('leaflet.ie').'<![endif]-->'; 
		
		echo $this->Html->css('default');
		
		echo $this->fetch('meta');
		echo $this->fetch('css');
	?>
</head>
<body>
	<div id="wrapper"> 
		<header id='banner' role="banner">
			<a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>">
				<img id='logo' src='/img/logo_50x50.png' alt='smartcitizen'>
				<h1 id="title">Smart Citizen </h1>
				<h2 id="subtitle">beta</h2>
			</a>
		</header>
		<nav id='nav' role="navigation">
			<ul>
				<li><?php echo $this->Html->link('Login', array('controller' => 'users', 'action' => 'me'), array('id'=>'login')); ?></li>
				<li><?php echo $this->Html->link('Updates', array('controller' => 'search', 'action' => 'updates')); ?></li>
				<li><?php echo $this->Html->link('Sensors', array('controller' => 'feeds', 'action' => 'index')); ?></li>
<!--				<li><?php echo $this->Html->link('Projects', array('controller' => 'users', 'action' => 'index')); ?></li> -->
				<li><?php echo $this->Html->link('Users', array('controller' => 'users', 'action' => 'index')); ?></li>
				<li><?php echo $this->Html->link('Resources', array('controller' => 'topics', 'action' => 'index')); ?></li>
				<li><?php echo $this->Html->link('About', array('controller' => 'pages', 'action' => 'display', 'about')); ?></li>
			</ul>
		</nav>
		<!--  RIGHT SIDE PANEL  -->
		<div id='content' role="main">
			<div id='principal'>
				<div class='inside_content'>
					<?php echo $this->fetch('content'); ?>
					<footer></footer><br/><br/><br/>
				</div>
			</div>
		</div>
		<!-- LEFT SIDE PANEL  -->
		<aside id='aside' role="complementary">
			<div id='complementary'>
			<?php 
			if ($this->fetch('aside')!='')
				echo $this->fetch('aside'); 
			else
				echo $this->element('search_input');
			?>
			</div>
		</aside>
		<!--  MAP DISPLAY -->
		<div id="map"></div>
		<script src="http://cdn.leafletjs.com/leaflet-0.4/leaflet.js"></script>
		<!--  MAP DATAS -->
<!--
		<script>
			<?php echo $this->fetch('map'); ?>
		</script>
!-->
		<footer id='footer'>
			<div id='loginInfo' class='left'>
			<?php if ($authUser){ ?>
				loged as <?php echo $this->Html->link($authUser['username'], array('controller' => 'users', 'action' => 'view',$authUser['id'])); ?> | <?php echo $this->Html->link('logout', array('controller' => 'users', 'action' => 'logout')); ?>
			<?php }else{ ?>
				<?php echo $this->Html->link('login', array('controller' => 'users', 'action' => 'login')); ?>
			<?php } ?>
			</div>
			<div class='right'>
				<a href='htpp://smartcitizen.me/'>Smart Citizen</a>
				is a project by
				<a href='htpp://fablabbcn.org/'>FabLab Barcelona</a>,
				<a href='htpp://iaac.net/'>Iaac</a> and
				<a href='htpp://hangar.org/'>Hangar</a>
				| developped thanks to
				<a href='htpp://goteo.org/'>Goteo</a>,
				<a href='htpp://cosm.com/'>Cosm</a>,
				<a href='htpp://openstreetmap.org/'>OpenStreetMap</a>,
				<a href='http://leaflet.cloudmade.com/'>Leaflet</a>,
				<a href='http://raphaeljs.com/'>Raphaël</a>,
				<a href='http://jquery.com/'>jQuery</a> and
				<a href='http://cakephp.org/'>CakePHP</a>
				| more 
				<?php echo $this->Html->link('about', array('controller' => 'pages', 'action' => 'display','about')); ?>.
			</div>
		</footer>
		<div id='flash'>
			<?php echo $this->Session->flash(); ?>
			<?php //echo $this->Session->flash('auth'); ?>
		</div>
		<div id='log'>
			<div>
				<?php echo $this->fetch('log'); ?>
				<?php //echo $this->element('request_dump'); ?>
			</div>
		</div>
	</div>

<?php
		echo $this->Html->script( array(
										'h5bp',
//										'modernizr-2.5.3.min',
		
//										'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',
										'jquery-1.8.1.min',
//										'jquery.history',
										'jquery.form',
										'leaflet',
										'raphael',
										'morris',		
//										'morris-min',		
//										'cosmjs',
										'g.raphael-min',
										'g.line-min',
										'default',
										));
?>
<div id='scripts'>
	<?php	echo $this->fetch('script'); ?>
	<script type="text/javascript">
	<?php	echo $this->fetch('inline-script'); ?>
	</script>
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-28740999-2']);
	  _gaq.push(['_trackPageview']);

	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
</div>
</body>
</html>
