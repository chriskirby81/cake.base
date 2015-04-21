<? if(!empty($authUser)): ?>
	<?= $this->Html->link('Logout', array( 'controller' => 'user', 'action' => 'logout' )) ?>
<? else: ?>
	<?= $this->Html->link('Login', array( 'controller' => 'user', 'action' => 'login' )) ?>
<? endif; ?>
