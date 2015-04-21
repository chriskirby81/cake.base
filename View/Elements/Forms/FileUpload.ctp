<? 
if(!isset($this->file_uploaders)){
	$this->file_uploaders = 1;
}else{
	$this->file_uploaders++;
}
$this->uploader_id = 'file_upload_'.$this->file_uploaders;


?>
<div class="filedrop_wrapper">

    <div id="<?=$this->uploader_id?>" class="empty filedrop" >
       
    </div>

<div id="<?=$this->uploader_id?>_loader" class="file_loader" >
   <div class="files"></div>
   <div class="progress"></div>
</div>
                  

<input id="start_upload" type="button" value="Start Upload" />
</div>


<script type="text/javascript">

GLOBAL.Loader.loadScript(['/js/chartjumper.dropbox.js'], function(){
	var dropbox = new DropBox('#<?=$this->uploader_id?>');
	<?=$js_var?>(dropbox);
});
</script>