<!-- File: /app/View/Users/dashboard.ctp -->
<?php // echo $this->html->image($user['User']['avatar'], array('class'=>'left')); ?>
<p>Hi <b><?php echo h($user['User']['username']); ?></b><p>
<p><small>Role : <?php echo $user['User']['role']; ?></small></p>
<?= $this->image->resize($user['User']['photo'],100,100) ?>
