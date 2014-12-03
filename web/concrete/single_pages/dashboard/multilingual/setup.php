<?php defined('C5_EXECUTE') or die("Access Denied.");?>
<?
use Concrete\Core\Multilingual\Page\Section as MultilingualSection;
?>
<fieldset>
    <legend><?php echo t('Content Sections')?></legend>
    <?php
    $nav = Loader::helper('navigation');
    if (count($pages) > 0) { ?>
        <table class="table table-striped">
        <tr>
            <th>&nbsp;</th>
            <th style="width: 45%"><?php echo t("Name")?></th>
            <th style="width: auto"><?php echo t('Language')?></th>
            <th style="width: 30%"><?php echo t('Path')?></th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach($pages as $pc) {
            $pcl = MultilingualSection::getByID($pc->getCollectionID()); ?>
            <tr>
                <td><?php echo $ch->getSectionFlagIcon($pc)?></td>
                <td><a href="<?php echo $nav->getLinkToCollection($pc)?>"><?php echo $pc->getCollectionName()?></a></td>
                <td><?php echo $pcl->getLanguageText()?> (<?php echo $pcl->getLocale();?>)</td>
                <td><?php echo $pc->getCollectionPath()?></td>
                <td><a href="<?php echo $this->action('remove_language_section', $pc->getCollectionID(), Loader::helper('validation/token')->generate())?>" class="icon-link"><i class="fa fa-trash"></i>copy_tree</a></td>
            </tr>
        <?php } ?>
        </table>

    <?php } else { ?>
        <p><?php echo t('You have not created any multilingual content sections yet.')?></p>
    <?php } ?>
    <form method="post" action="<?php echo $this->action('add_content_section')?>">
        <h4><?php echo t('Add a Language')?></h4>
        <div class="form-group">
            <label class="control-label"><?php echo t('Choose a Parent Page')?></label>
            <?php echo Loader::helper('form/page_selector')->selectPage('pageID', '')?>
        </div>
        <div class="form-group">
            <?php echo $form->label('msLanguage', t('Choose Language'))?>
            <?php echo $form->select('msLanguage', $languages);?>
        </div>
        <div class="form-group">
            <label class="control-label"><?php echo t('Language Icon')?></label>
            <div id="ccm-multilingual-language-icon"><?php echo t('None')?></div>
        </div>
        <div class="form-group">
            <?php echo Loader::helper('validation/token')->output('add_content_section')?>
            <button class="btn btn-default pull-left" type="submit" name="add"><?=t('Add Content Section')?></button>
        </div>
    </form>
</fieldset>

<div class="spacer-row-6"></div>

<script type="text/javascript">
$(function() {
	$("select[name=msLanguage]").change(function() {
		ccm_multilingualPopulateIcons($(this).val(), '');
	});
	ccm_multilingualPopulateIcons($("select[name=msLanguage]").val(), '<?php echo $_POST["msIcon"]?>');
});

ccm_multilingualPopulateIcons = function(lang, icon) {
	if (lang && lang != '') {
	    $("#ccm-multilingual-language-icon").load('<?php echo $view->action("load_icons")?>', {'msLanguage': lang, 'selectedLanguageIcon': icon});
	}
};

</script>


<fieldset>
    <legend><?php echo t('Copy Language Tree')?></legend>
<?
$u = new User();
$copyLanguages = array();
$includesHome = false;
foreach($pages as $pc) {
	$pcl = MultilingualSection::getByID($pc->getCollectionID());
	if ($pc->getCollectionID() == HOME_CID) {
		$includesHome = true;
	}
	$copyLanguages[$pc->getCollectionID()] = $pc->getCollectionName() . ' - ' . $pcl->getLanguageText();
}

if ($u->isSuperUser() && !$includesHome) { ?>
<form method="post" id="ccm-internationalization-copy-tree" action="#">
	<?php if (count($pages) > 1) {
		$copyLanguageSelect1 = $form->select('copyTreeFrom', $copyLanguages);
		$copyLanguageSelect2 = $form->select('copyTreeTo', $copyLanguages);
		
		?>
		<p><?php echo t('Copy all pages from a language to another section. This will only copy pages that have not been associated. It will not replace or remove any pages from the destination section.')?></p>
		<div class="clearfix">
		<label><?php echo t('Copy From')?></label>
		<div class="input"><?php echo $copyLanguageSelect1?></div>
		</div>
		
		<div class="clearfix">
		<label><?php echo tc('Destination', 'To')?></label>
		<div class="input"><?php echo $copyLanguageSelect2?></div>
		</div>
	
		<div class="clearfix">
		<label></label>
		<div class="input">
			<?php echo Loader::helper('validation/token')->output('copy_tree')?>
            <button class="btn btn-default pull-left" type="submit" name="copy"><?=t('Copy Tree')?></button>
		</div>
		</div>

	<?php } else if (count($pages) == 1) { ?>
		<p><?php echo t("You must have more than one multilingual section to use this tool.")?></p>
	<?php } else { ?>
		<p><?php echo t('You have not created any multilingual content sections yet.')?></p>
	<?php } ?>

	<? if(version_compare(APP_VERSION, '5.6.0.3', '>')) { 
			// 5.6.1 OR GREATER
		?>
		<script type="text/javascript">
		$(function() {
			$("#ccm-internationalization-copy-tree").on('submit', function() {
				var ctf = $('select[name=copyTreeFrom]').val();
				var ctt = $('select[name=copyTreeTo]').val();
				if (ctt > 0 && ctf > 0 && ctt != ctf) {
					ccm_triggerProgressiveOperation(
						CCM_TOOLS_PATH + '/dashboard/sitemap_copy_all', 
						[{'name': 'origCID', 'value': ctf}, {'name': 'destCID', 'value': ctt}, {'name': 'copyChildrenOnly', 'value': true}],
						"<?=t('Copy Language Tree')?>", function() {
							window.location.href= "<?=$this->action('tree_copied')?>";
						}
					);
				} else {
					alert("<?=t('You must choose two separate multilingual sections to copy from/to')?>");
				}
				return false;
			});
		});
		</script>

	<? } ?>

</form>
<? } else if (!$u->isSuperUser()) { ?>
	<p><?=t('Only the super user may copy language trees.')?></p>
<? } else if ($includesHome) { ?>
	<p><?=t('Since one of your multilingual sections is the home page, you may not duplicate your site tree using this tool. You must manually assign pages using the page report.')?></p>
<? } ?>
</fieldset>


<?php if (count($pages) > 0) {
	$defaultLanguages = array('' => t('** None Set'));
	foreach($pages as $pc) {
		$pcl = MultilingualSection::getByID($pc->getCollectionID());
		$defaultLanguages[$pcl->getLocale()] = $pcl->getLanguageText();
	}
	$defaultLanguagesSelect = $form->select('defaultLanguage', $defaultLanguages, $defaultLanguage);
	

	?>

    <br/>

    <h3><?php echo t('Multilingual Settings')?></h3>

	<form method="post" action="<?php echo $this->action('set_default')?>">
        <div class="form-group">
            <label class="control-label"><?php echo t('Default Language');?></label>
            <?php print $defaultLanguagesSelect; ?>
        </div>

        <div class="form-group">
            <div class="checkbox">
            <label>
                <?php echo $form->checkbox('useBrowserDetectedLanguage', 1, $useBrowserDetectedLanguage)?>
                <span><?php echo t('Attempt to use visitor\'s language based on their browser information.') ?></span>
            </label>
            </div>
            <div class="checkbox">
            <label>
                <?php echo $form->checkbox('redirectHomeToDefaultLanguage', 1, $redirectHomeToDefaultLanguage)?>
                <span><?php echo t('Redirect home page to default language section.') ?></span>
            </label>
            </div>
        </div>

        <div class="form-group">
            <?php echo Loader::helper('validation/token')->output('set_default')?>
            <button class="btn btn-default pull-left" type="submit" name="save"><?=t('Save Settings')?></button>
        </div>
	</form>
	<?php } ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
