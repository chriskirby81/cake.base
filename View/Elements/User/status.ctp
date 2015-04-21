<aside>
	<? if(!empty($authUser)): ?>
 		Welcome, <?=isset($authUser['username']) ? ucwords($authUser['username']) : 'User'.$authUser['id']?><br>
		<?= $this->Html->link('Logout', array( 'controller' => 'user', 'action' => 'logout' )) ?>
 	<? else: ?>
		<?= $this->Html->link('Login', array( 'controller' => 'user', 'action' => 'login' )) ?>
		<?= $this->Html->link('Create Account', array( 'controller' => 'user', 'action' => 'add' )) ?>
	<? endif; ?>
</aside>