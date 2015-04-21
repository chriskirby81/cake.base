<div id="login-form" class="users form">
<div class="login">
<h2>User Login</h2>
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User', array( 'url' => array( 'controller' => 'user', 'action' => 'login' )) ); ?>
    <fieldset>
        <legend><?php echo __('Please enter your email and password'); ?></legend>
    <?php
        echo $this->Form->input('User.email_or_username', array(
			'label' => 'Email or Username',
			'placeholder' => 'Email or Username'
		));
        echo $this->Form->input('User.password');
		echo $this->Form->input('Auth.remember', array(
			'type' => 'checkbox',
			'checked' => false,
			'label' => 'Don\'t Remember Me'
		));
		
    ?>

    </fieldset>
<?php echo $this->Form->end(__('Login')); ?>
</div>
<?php if(!isset($create) || isset($create) && $create ): ?>
<div class="create-account">
<div class="inner">
<h2>Not a user?</h2>
<p>Creating a account is free and easy. Click on the link below to get started.</p>
<p>
<?=$this->Html->link('Create Account', array( 'controller' => 'user', 'action' => 'add' ), array( 'class' => 'a-create-account' ))?>
</p>
</div>
</div>
 <?php endif; ?>
 
</div>