<? 
if(!isset($this->file_uploaders)){
	$this->file_uploaders = 1;
}else{
	$this->file_uploaders++;
}

if(!isset($id)){
	$this->uploader_id = 'videodrop_'.$this->file_uploaders;
}else{
	$this->uploader_id = 'videodrop_'.$id;
}

$upload_type = isset($upload_type) ? $upload_type : '';

?>
<link href="/css/videodrop.css" rel="stylesheet" />

<div id="<?=$this->uploader_id?>" class="video_drop_wrapper initializing">
	<div id="videodrop_bg">
    	<div class="graphic"><h1 id="videodrop_bg_text" class="tk-bree">Initializing Please Wait...</h1><canvas id="videodrop_bg_canvas" width="600" ></canvas></div>
        <div class="shadow"><canvas id="videodrop_bg_over" width="250" height="300" ></canvas></div>
    </div>
    <div id="videodrop_file_btn">
    	<input id="_videodrop_file_btn" type="file" multiple="true" >
    </div>
    <div id="videodrop_files" >
       
    </div>
	<!-- <input id="start_upload" type="button" value="Start Upload" /> -->
</div>


