<?php defined('C5_EXECUTE') or die("Access Denied.");
$fh = Loader::helper('interface/flag', 'multilingual');
$nav = Loader::helper('navigation');
?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Page Report'),false, false, false); ?>
<div class="ccm-pane-body">
<?php if (count($sections) > 0) { ?>
	<form method="get" action="<?php echo $this->action('view')?>" id="ccm-multilingual-page-report-form" class="form-stacked">
		<div class='row'>
		<fieldset class="span5">
			<legend style='margin-bottom:0'><?php echo t('Choose Source')?></legend>
			<div class="clearfix">
				<div class="">
					<?php echo $form->select('sectionIDSelect', $sections, $sectionID)?>
				</div>
			</div>
		</fieldset>
		<fieldset class="span5">
			<legend style='margin-bottom:0'><?php echo t('Choose Targets')?></legend>
			<div class="clearfix">
			<?php foreach($sectionList as $sc) { ?>
				<?php $args = array('style' => 'vertical-align: middle');
				if ($sectionID == $sc->getCollectionID()) {
					$args['disabled'] = 'disabled';
				}
				?>
					<div class="">
						<ul class="inputs-list">
							<li>
								<label>
									<?php echo $form->checkbox('targets[' . $sc->getCollectionID() . ']', $sc->getCollectionID(), in_array($sc->getCollectionID(), $targets), $args)?>
									<span>
										<?php echo $fh->getSectionFlagIcon($sc)?>
										<?php echo $sc->getLanguageText(). " (".$sc->getLocale().")"; ?>
									</span>
								</label>
							</li>
						</ul>
					</div>
			<?php } ?>
			</div>
		</fieldset>
		</div>
		<div class="row">
		<fieldset class="span5">
			<legend style='margin-bottom:0'><?php echo t('Display')?></legend>
				<?php echo $form->hidden('sectionID', $sectionID); ?>
			<div class="clearfix">
				<div class="">
					<ul class="inputs-list">
						<li>
							<label>
								<?php echo $form->radio('showAllPages', 0, 0)?>
								<span><?php echo t('Only Missing Pages')?></span>
							</label>
						</li>
						<li>
							<label>
								<?php echo $form->radio('showAllPages', 1, false)?>
								<span><?php echo t('All Pages') ?></span>
							</label>
						</li>
					</ul>
				</div>
			</div>
		</fieldset>
		<fieldset class="span5">
			<legend><?=t('Filter Pages')?></legend>
			<label><?=t('By Name')?></label>
			<?=$form->text('keywords')?>
		</fieldset>
		</div>
		<div class="row">
			<div span="12"><?php echo $form->submit('submitForm', t('Go'), '',' ccm-button-right primary')?></div>
		</div>
	</form>
	<?php if (count($pages) > 0) { ?>
		<?php echo $pl->displaySummary()?>
	<?php } ?>


		<table class="ccm-results-list" cellspacing="0" cellpadding="0" border="0" id="ccm-multilingual-page-report-results">
		<thead>
		<tr>
			<th><?php 				$sourceMS = MultilingualSection::getByID($sectionID);
				print $sourceMS->getLanguageText(); echo " (".$sourceMS->getLocale().")";

			?></th>
			<?php foreach($targetList as $sc) { ?>
				<?php if ($section->getCollectionID() != $sc->getCollectionID()) { ?>
					<th><?php 						print $sc->getLanguageText();
						echo " (".$sc->getLocale().")";
					?></th>
				<?php } ?>
			<?php } ?>
		</tr>
		</thead>
		<tbody>
		<?php if (count($pages) > 0) { ?>
		<?php $class = 'ccm-list-record-no-hover ccm-list-record-alt'; ?>
		<?php foreach($pages as $pc) {
			if ($class == 'ccm-list-record-no-hover ccm-list-record-alt') {
				$class = 'ccm-list-record-no-hover';
			} else {
				$class = 'ccm-list-record-no-hover ccm-list-record-alt';
			}

			?>
		<tr class="<?php echo $class?>">
			<td>
				<a href="<?php echo $nav->getLinkToCollection($pc)?>"><?php echo $pc->getCollectionName()?></a>
				<div style="font-size: 10px;"><?=$pc->getCollectionPath()?></div>
			</td>
			<?php foreach($targetList as $sc) { ?>
				<?php if ($section->getCollectionID() != $sc->getCollectionID()) { ?>
					<td style='width:165px;text-align:right' id="node-<?php echo $pc->getCollectionID()?>-<?php echo $sc->getLocale()?>"><?php 						$cID = $sc->getTranslatedPageID($pc);
						if ($cID) {
							$p = Page::getByID($cID);
							print '<div style="margin-bottom: 8px"><a href="' . $nav->getLinkToCollection($p) . '">' . $p->getCollectionName() . '</a></div>';
						} else if ($cID === '0') {
							print '<div style="margin-bottom: 8px">' . t('Ignored') . '</div>';

						}

							$cParentID = $pc->getCollectionParentID();
							$cParent = Page::getByID($cParentID);
							$cParentRelatedID = $sc->getTranslatedPageID($cParent);
							if ($cParentRelatedID) {

								$assignLang = t('Re-Map');
								if (!$cID) {
									$assignLang = t('Map');
								}
						?>
						<form>
							<fieldset>
							<?php if (!$cID) { ?>
								<input class='btn success' style="font-size: 10px" type="button" value="<?php echo t('Create')?>" ccm-source-page-id="<?php echo $pc->getCollectionID()?>" ccm-destination-language="<?php echo $sc->getLocale()?>" name="ccm-multilingual-create-page" />
							<?php } ?>
							<input  class='btn info' style="font-size: 10px" type="button" value="<?php echo $assignLang?>" ccm-source-page-id="<?php echo $pc->getCollectionID()?>" ccm-destination-language="<?php echo $sc->getLocale()?>" name="ccm-multilingual-assign-page" />
							<?php if ($cID !== '0' && !$cID) { ?>
								<input class='btn warning' style="font-size: 10px" type="button" value="<?php echo t('Ignore')?>" ccm-source-page-id="<?php echo $pc->getCollectionID()?>" ccm-destination-language="<?php echo $sc->getLocale()?>" name="ccm-multilingual-ignore-page" />
							<?php } ?>
							</fieldset>
						</form>

						<?php } else { ?>
							<div class="ccm-note"><?php echo t("Create the parent page first.")?></div>
						<?php } ?>
					</td>
				<?php } ?>
			<?php } ?>
		</tr>
		<?php } ?>

		<?php } else { ?>
		<tr>
			<td colspan="4"><?php echo t('No pages found.')?></td>
		</tr>
		<?php } ?>
		</tbody>
		</table>
		<?php echo $pl->displayPagingV2()?>

<script type="text/javascript">

var activeAssignNode = false;

$(function() {
	$('input[name=ccm-multilingual-create-page]').click(function() {
		ccm_multilingualCreatePage($(this).attr('ccm-source-page-id'), $(this).attr('ccm-destination-language'));
	});

	$("select[name=sectionIDSelect]").change(function() {
		$(".ccm-multilingual-page-report-target input").attr('disabled', false);
		$(".ccm-multilingual-page-report-target input[value=" + $(this).val() + "]").attr('disabled', true).attr('checked', false);
		$("input[name=sectionID]").val($(this).val());
		$("#ccm-multilingual-page-report-form").submit();
	});
	$('input[name=ccm-multilingual-ignore-page]').click(function() {
		ccm_multilingualIgnorePage($(this).attr('ccm-source-page-id'), $(this).attr('ccm-destination-language'));
	});

	$("input[name=ccm-multilingual-assign-page]").click(function() {
		activeAssignNode = this;
		$.fn.dialog.open({
			title: '<?php echo t("Choose A Page") ?>',
			href: CCM_TOOLS_PATH + '/sitemap_overlay.php?sitemap_mode=select_page&callback=ccm_multilingualAssignPage',
			width: '550',
			modal: false,
			height: '400'
		});
	});
});

ccm_multilingualAssignPage = function(cID, cName) {
	var srcID = $(activeAssignNode).attr('ccm-source-page-id');
	var destLang = $(activeAssignNode).attr('ccm-destination-language');
	$("#node-" + srcID + "-" + destLang).load('<?php echo $this->action("assign_page")?>', {'token': '<?php echo Loader::helper("validation/token")->generate("assign_page")?>', 'sourceID': srcID, 'destID': cID});
}
ccm_multilingualCreatePage = function(srcID, destLang) {
	$("#node-" + srcID + "-" + destLang).load('<?php echo $this->action("create_page")?>', {'token': '<?php echo Loader::helper("validation/token")->generate("create_page")?>', 'sourceID': srcID, 'locale': destLang});
}
ccm_multilingualIgnorePage = function(srcID, destLang) {
	$("#node-" + srcID + "-" + destLang).load('<?php echo $this->action("ignore_page")?>', {'token': '<?php echo Loader::helper("validation/token")->generate("ignore_page")?>', 'sourceID': srcID, 'locale': destLang});
}

</script>
<?php } else { ?>
	<p><?php echo t('You have not defined any multilingual sections for your site yet.')?></p>
<?php } ?>

</div>
<div class="ccm-pane-footer"></div>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>