<? 
if(isset($items)){
	foreach($items as $item){
		echo $this->element('SideBar/'.$item);
	}
}
?>