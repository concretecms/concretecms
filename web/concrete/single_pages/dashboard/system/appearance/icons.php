<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Bookmark Icons'), false, 'span12 offset2', false)?>

<form method="post" id="favicon-form" action="<?=$this->action('update_favicon')?>" enctype="multipart/form-data" >
<div class="ccm-pane-body">
	<?=$this->controller->token->output('update_favicon')?>
	
	<input id="remove-existing-favicon" name="remove_favicon" type="hidden" value="0" />
	<?
	$favIconFID=intval(Config::get('FAVICON_FID'));
	if($favIconFID){
		$f = File::getByID($favIconFID);
		?>
		<div>
		<img src="<?=$f->getRelativePath() ?>" />
		<a onclick="removeFavIcon()"><?=t('Remove')?></a>
		</div>
		<script>
		function removeFavIcon(){
			document.getElementById('remove-existing-favicon').value=1;
			$('#favicon-form').get(0).submit();
		}
		</script>
	<? }else{ ?>
		<input id="favicon_upload" type="file" name="favicon_file"/>		
		<div class="ccm-dashboard-description" style="margin-top:4px"><?=t('Your image should be 16x16 pixels, and should be an gif or a png with a .ico file extension.')?></div>
	<? } ?>
</div>
<div class="ccm-pane-footer">
	<?
	print $interface->submit(t('Save'), 'favicon-form', 'left','primary');
	?>
</div>
</form>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
