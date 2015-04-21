<!-- app/View/Users/add.ctp -->
<div class="users form">
<?php echo $this->Form->create('User'); ?>
   
    <?php
	 	echo $this->Form->input('User.email', array( 
		'required' => true 
		));
        echo $this->Form->input('User.password');
        echo $this->Form->input('role', array(
			'type' => 'hidden',
            'value' => 'user'
        ));
		echo $this->Form->input('Auth.remember', array(
			'type' => 'checkbox',
			'checked' => false,
			'label' => 'Don\'t Remember Me'
		));
    ?>
<?php echo $this->Form->end(__('Create User')); ?>
</div>