<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
$form = Loader::helper('form');
if (!$cp->canRead()) {
	die(_("Access Denied."));
}
Loader::model('file_set');
?>

<ul class="ccm-dialog-tabs" id="ccm-add-to-tabs">
<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-file-add-to-set"><?=t('Add to Set')?></a></li>
<li><a href="javascript:void(0)" id="ccm-file-add-to-archive"><?=t('Add to Archive')?></a></li>
</ul>

<script type="text/javascript">
var ccm_alatTab = "ccm-file-add-to-set";
$("#ccm-add-to-tabs a").click(function() {
	$("li.ccm-nav-active").removeClass('ccm-nav-active');
	$("#" + ccm_alatTab + "-tab").hide();
	ccm_alatTab = $(this).attr('id');
	$(this).parent().addClass("ccm-nav-active");
	$("#" + ccm_alatTab + "-tab").show();
});
</script>

<div id="ccm-file-add-to-set-tab">
<form method="post" id="ccm-file-add-to-set-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/add_to/">
<?=$form->hidden('task', 'add_to_sets')?>

<h1><?=t('Add to Set')?></h1>

<h2><?=t('Add to Existing Set(s)')?></h2>
<? $s1 = FileSet::getMySets(); ?>
<? foreach($s1 as $s) { ?>

	<div class="ccm-file-set-add-cb"><?=$form->checkbox('fsID[]', $s->getFileSetID())?> <?=$s->getFileSetName()?></div>

<? } ?>

<hr />

<h2><?=t('Add to New Set')?></h2>

<?=$form->checkbox('fsNew', 1)?> <?=$form->text('fsNewText', array('style' => 'width: 250px'))?> <?=$form->checkbox('fsNewShare', 1, true)?> <?=t('Make set public')?>

<br/><br/>
<?
$h = Loader::helper('concrete/interface');
$b1 = $h->submit(t('Add to Selected Sets'), 'ccm-file-add-to-set-form', 'left');
print $b1;
?>
</form>

</div>

<div id="ccm-file-add-to-archive-tab" style="display: none">

<h1><?=t('Add to Archive')?></h1>

</div>