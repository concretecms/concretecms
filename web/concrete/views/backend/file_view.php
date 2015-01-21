<?php defined('C5_EXECUTE') or die('Access Denied.');

$form = \Core::make('helper/form');

?>
<div style="text-align: center">
<?php

if ($to->getPackageHandle() != '') {
	Loader::packageElement('files/view/' . $to->getView(), $to->getPackageHandle(), array('fv' => $fv));
} else {
	Loader::element('files/view/' . $to->getView(), array('fv' => $fv));
}

?>
</div>

<div class="dialog-buttons">
<form method="post" action="<?=\URL::to('/ccm/system/file/download')?>?fID=<?=$f->getFileID()?>&fvID=<?=$f->getFileVersionID()?>" style="margin: 0px">
<label class="ccm-resize-related"><input id="ccm-resize-control" type="checkbox" /><?=t('View actual size')?></label>
<?=$form->submit('submit', t('Download'), array('class' => 'btn btn-primary pull-right'))?>
</form>
</div>
<style type="text/css">
	.ccm-resize-target.responsive {
		max-width:100%;
		width: auto;
		height:auto;
	}
</style>
<script type="text/javascript">
$(function(){
	if ($('.ccm-resize-target').length < 1) {
		$('ccm-resize-related').hide();
	} else { 
		function updateResizeTarget() {
			var mustResize = $('#ccm-resize-control').is(':checked');
			if (mustResize) {
				$('.ccm-resize-target').removeClass('responsive');
			} else {
				$('.ccm-resize-target').addClass('responsive');
			}
		}
		$('#ccm-resize-control').change(updateResizeTarget);
		updateResizeTarget();
	}
});
</script>
