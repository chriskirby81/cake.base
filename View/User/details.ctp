
<?php echo $this->Form->create('User'); ?>
<?php echo $this->Form->input('User.username'); ?>
<?php echo $this->Form->input('User.email'); ?>
<?php echo $this->Form->input('UserDetail.first_name'); ?>
<?php echo $this->Form->input('UserDetail.last_name'); ?>
<?php echo $this->Form->datePicker('UserDetail.birthdate'); ?>
<?php echo $this->Form->input('UserDetail.gender', array( 'type' => 'select', 'options' => array(
	'' => 'Gender',
	'M' => 'Male',
	'F' => 'Female'
))); ?>

<?php echo $this->Form->end('Update Profile'); ?>