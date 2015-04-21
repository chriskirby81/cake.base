<? 
if(!isset($this->file_uploaders)){
	$this->file_uploaders = 1;
}else{
	$this->file_uploaders++;
}

if(!isset($id)){
	$this->uploader_id = 'dropbox_'.$this->file_uploaders;
}else{
	$this->uploader_id = 'dropbox_'.$id;
}

$upload_type = isset($upload_type) ? $upload_type : '';

?>
<div class="filedrop_wrapper">

    <div id="<?=$this->uploader_id?>" class="empty filedrop" data-text="Initializing..." data-upload-type="<?= $upload_type ?>" >
       
    </div>

	<!-- <input id="start_upload" type="button" value="Start Upload" /> -->
</div>


<script type="text/javascript">
APP.onReady(function(){
	APP.require(['files/dropbox', 'files/file', '/css/filedrop.css'], function(){
		var dropbox = new DropBox('<?=$this->uploader_id?>');
		<?=$callback?>(dropbox);
	});
});
</script>