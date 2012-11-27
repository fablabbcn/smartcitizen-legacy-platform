<!-- File: /app/View/Posts/view.ctp -->
<?
$this->start('log');
echo $this->element('sql_dump');
var_dump($feed);
$this->end();
?>
<section style='width:600px'>
<h1><?=$feed['id']?> : <?php echo $feed['title']; ?></h1>

<p><small>Created: <?php echo $feed['created']; ?></small></p>
<?php foreach ($feed['datastreams'] as $key=>$datastream):?>

	<h2><?=implode(' ' , $datastream['tags'])?></h2>
	<script src="/js/cosm-graph-1.0.0.js" data-resource="/feeds/<?= $feed['id'] ?>/datastreams/<?= $datastream['id'] ?>" data-width="500px" data-height="200px" data-key="QiKJDoIHZt-7sxtavM1jRhS39B-SAKxFWHZIM0ZMSUV2ND0g" defer></script>
<?php endforeach; ?>