<div id="user_files">

<?
if(isset($user_files)){
	foreach($user_files as $file){
	$types = explode('/',$file['Files']['type']);
	$type_major = $types[0];
	$type_minor = $types[1];
?>
		<div class="file" data-name="<?=$file['Files']['name']?>" data-type="<?=$file['Files']['type']?>" data-size="<?=$file['Files']['size']?>" data-path="<?=$file['Files']['path']?>" data-id="<?=$file['Files']['id']?>" >
            <div class="icon" data-type-major="<?=$type_major?>" data-type-minor="<?=$type_minor?>" ></div>
            <div class="name"><?=$file['Files']['name'] ?></div>

            <div class="type"><?=$file['Files']['type'] ?></div>
            <div class="size"><?=$file['Files']['size'] ?></div>
		</div>
		
<?
	}
}
?>

</div>