<? defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
if ($c->isEditMode()) { ?>
	<div class="ccm-edit-mode-disabled-item" style="height: 400px">
		<div style="padding: 80px 0px 0px 0px"><? echo t('Google Map disabled in edit mode.')?></div>
	</div>
<?  } else { ?>	
	<?  if( strlen($title)>0){ ?><h3><? echo $title?></h3><?  } ?>
	<div id="googleMapCanvas<? echo $bID?>" class="googleMapCanvas"></div>	
<?  } ?>