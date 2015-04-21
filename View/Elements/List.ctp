<?php
$listIndex = $this->Utils->counter('list');
$listId = isset($id) ? $id : 'list-'.$listIndex;
$listType = isset($type) ? $type : 'rows';
?>

<div id="<?=$listId?>" class="list" >

<? foreach( $list as $litem ): ?>
	<div id="<?=$listId?>-item-<?=$this->Utils->counter('list-'.$listIndex)?>" class="item <?=$listType?>" >
		<div class="inner clearfix">
		<? if(isset($element)): 
			echo $this->element($element, array(
				'item' => $litem
			));
		endif; ?>
		</div>
	</div>
<? endforeach; ?>

</div>