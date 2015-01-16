<?php

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
<?=$form->submit('submit', t('Download'), array('class' => 'btn btn-primary pull-right'))?>
</form>
</div>
