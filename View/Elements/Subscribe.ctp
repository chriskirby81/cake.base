<h2><?=isset($title)?$title:'Subscribe'?></h2>
<div class="content">
<p>
Stay up to date on new casinos, bonuses and alerts that you need to know about.
</p>
<?= $this->Form->create('Subcribe', array('url' => '/subscribe/add')); ?>
<?= $this->Form->input('Subscribe.email', array('label' => 'Enter Email')); ?>
<?= $this->Form->end('Subcribe'); ?>
</div>