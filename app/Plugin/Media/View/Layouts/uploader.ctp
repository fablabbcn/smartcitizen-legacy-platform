<!DOCTYPE html>
<html>
    <head>
        <title>Uploader</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php echo $this->Html->css('/media/css/style.css'); ?>
        <?php echo $this->fetch('css'); ?>
    </head>
    <body>
        
	   <?php echo $this->Session->flash('Auth'); ?>
	   <?php echo $this->Session->flash(); ?>
        
       <?php echo $this->fetch('content'); ?>

        <!-- jQuery AND jQueryUI -->
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
        <?php echo $this->fetch('script'); ?>
		<script type="text/javascript">
		</script>
	
    </body>
</html>