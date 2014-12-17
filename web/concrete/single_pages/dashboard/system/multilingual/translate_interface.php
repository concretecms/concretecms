<?defined('C5_EXECUTE') or die("Access Denied.")?>

<?$valt = Loader::helper('validation/token')?>

<?php
if ($this->controller->getTask() == 'translate_po') {

//	Loader::packageElement('simplepo/edit', 'multilingual_plus', array('catalogID' => $catalogID,'lang'=>$lang));

}  else {

	if (!is_dir(DIR_LANGUAGES_SITE_INTERFACE) || !is_writable(DIR_LANGUAGES_SITE_INTERFACE)) { ?>
		<div class="alert alert-warning"><?=t('You must create the directory %s and make it writable before you may run this tool. Additionally, all files within this directory must be writable.', DIR_LANGUAGES_SITE_INTERFACE)?></div>
	<? } ?>

	<?php
	$nav = Loader::helper('navigation');
	Loader::model('section', 'multilingual');
	$pages = \Concrete\Core\Multilingual\Page\Section::getList();
	$defaultLanguage = Config::get('concrete.multilingual.default_locale');

	$ch = Core::make('multilingual/interface/flag');
	if (count($pages) > 0) { ?>

<div class="ccm-dashboard-content-full">
    <div class="table-responsive">
        <table class="ccm-search-results-table">
            <thead>
            <tr>
                <th>&nbsp;</th>
                <th><span><?=t("Name")?></span></th>
                <th><span><?=t('Locale')?></span></th>
                <th colspan="2"><span><?=t('Completion')?></span></th>
                <th><span><?=t('Last Updated')?></span></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <? foreach($pages as $pc) {
                $pcl = \Concrete\Core\Multilingual\Page\Section::getByID($pc->getCollectionID());?>
                <tr>
                    <td><?=$ch->getSectionFlagIcon($pc)?></td>
                    <td>
                        <a href="<?=$nav->getLinkToCollection($pc)?>">
                            <?=$pc->getCollectionName()?>
                        </a>
                    </td>
                    <td style="white-space: nowrap">
                        <?php echo $pc->getLocale(); ?>
                        <? if ($pc->getLocale() != $defaultLanguage) { ?>
                            <a href="#" class="icon-link launch-tooltip" title="<?=REL_DIR_LANGUAGES_SITE_INTERFACE?>/<?=$pc->getLocale()?>.mo"><i class="fa fa-question-circle"></i></a>
                        <? } ?>
                    </td>
                    <td style="width: 40%">
                        <? if ($pc->getLocale() != $defaultLanguage) { ?>
                            <?
                            $data = $extractor->getSectionSiteInterfaceCompletionData($pc);
                            ?>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?=$data['completionPercentage']?>%">&nbsp;</div>
                            </div>
                        <? } ?>
                    </td>
                    <td style="white-space: nowrap">
                        <span class="percent"><?=$data['completionPercentage']?>%</span> - <span class="translated"><?=$data['translatedCount']?></span> <?=t('of')?> <span class="total"><?=$data['messageCount']?></span>
                    </td>
                    <td>
                        <? if ($pc->getLocale() != $defaultLanguage) {
                            if (file_exists(DIR_LANGUAGES_SITE_INTERFACE . '/' . $pc->getLocale() . '.mo'))
                                print date('F d, Y g:i:s A', filemtime(DIR_LANGUAGES_SITE_INTERFACE . '/' . $pc->getLocale() . '.mo'));
                            else
                                print t('File not found.');
                        }
                        else
                            echo t('N/A'); ?>
                    </td>
                    <? if ($pc->getLocale() == $defaultLanguage) { ?>
                        <td></td>
                    <? } else { ?>
                        <td><a href="<?=$this->action('translate_po', $pc->getCollectionID())?>" class="icon-link"><i class="fa fa-pencil"></i></a></td>
                    <? } ?>
                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>
</div>

        <?
        if (is_dir(DIR_LANGUAGES_SITE_INTERFACE) && is_writable(DIR_LANGUAGES_SITE_INTERFACE)) { ?>

        <form method="post" action="<?=$controller->action('reload')?>">
        <div class="ccm-dashboard-header-buttons btn-group">
            <button class="btn btn-default" type="submit"><?=t('Reload Strings')?></button>
            <?=$valt->output('reload')?>
            <button class="btn btn-danger" type="button" data-dialog="reset" value="reset"><?=t('Reset All')?></button>
        </div>
        </form>


            <div style="display: none">
                <div id="ccm-dialog-reset-languages" class="ccm-ui">
                    <?
                    $u = new User();
                    if ($u->isSuperUser()) { ?>
                    <form method="post" class="form-stacked" style="padding-left: 0px" action="<?=$view->action('reset_languages')?>">
                        <?=Loader::helper("validation/token")->output('reset_languages')?>
                        <p><?=t('Are you sure? This will remove all translations from all languages, in the database and in your site PO files. This cannot be undone.')?></p>
                    </form>
                    <div class="dialog-buttons">
                        <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                        <button class="btn btn-danger pull-right" onclick="$('#ccm-dialog-reset-languages form').submit()"><?=t('Confirm Reset')?></button>
                    </div>
                    <? } else { ?>
                        <p><?=t("Only the admin user may reset all languages.")?></p>
                    <? } ?>
                </div>
            </div>

            <script type="text/javascript">
                $(function() {
                    $('button[data-dialog=reset]').on('click', function() {
                        jQuery.fn.dialog.open({
                            element: '#ccm-dialog-reset-languages',
                            modal: true,
                            width: 320,
                            title: '<?=t("Reset Languages")?>',
                            height: 'auto'
                        });
                    });
                });
            </script>

        <? } ?>

        <style type="text/css">
            table.ccm-search-results-table div.progress {
                margin-bottom: 0px;
            }
        </style>


	<? } else { ?>
		<p><?=t('You have not created any multilingual content sections yet.')?></p>
	<? } ?>
<? } ?>
</div>