<!-- app/View/Media/form.ctp -->
<div class="media form">
<?php echo $this->Form->create('Media'); ?>
	<?php 
		echo $this->Form->input('Media.name', array( 
	 	'label' => 'File Name',
		'required' => true 
		));
		
		echo $this->Form->fileDrop('Media.images', array(
			'script' => '/file/upload'
		));
	?>
<?php echo $this->Form->end(__(ucwords($action) . ' Media')); ?>
</div>