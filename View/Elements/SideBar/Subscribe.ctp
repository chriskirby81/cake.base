<?php 
$label = isset($label)?$label:'Subscribe';
?>
<h2><?=isset($title)?$title:'Subscribe'?></h2>
<div class="content">
<p>
<?=isset($descr)?$descr:'Subscribe Now! its easy'?>
</p>
<?= $this->Form->create('Subcribe', array('url' => '/subscribe/add')); ?>
<?= $this->Form->input('Subscribe.email', array('label' => 'Enter Email')); ?>
<?= $this->Form->end($label); ?>
</div>
