<?defined('C5_EXECUTE') or die("Access Denied.")?>

<?$valt = Loader::helper('validation/token')?>

<?php
if ($this->controller->getTask() == 'translate_po') { ?>

    <style type="text/css">
        div.ccm-translate-site-interface-messages span.original {
            float: left;
            width: 50%;
            height: 20px;
            overflow: hidden;
        }
        div.ccm-translate-site-interface-messages span.translation {
            float: left;
            width: 50%;
            height: 20px;
            overflow: hidden;
        }

        div.ccm-translate-site-interface-messages li.list-group-item {
            transition: all 0.2s linear;
        }

        li.list-group-item:hover {
            background-color: #dedede;
            cursor:pointer;
        }

        div.ccm-translate-site-interface-messages ul.list-group {
            overflow: scroll;
        }

        div.ccm-translate-site-interface-translate form.translate-form {
            display:none;
        }
    </style>


    <div class="row">
        <div class="ccm-translate-site-interface-messages col-md-7">
            <div class="panel panel-primary">
                <div class="panel-heading clearfix">
                    <span class="original"><?=t('Original String')?></span>
                    <span class="translation"><?=t('Translation')?></span>
                </div>
                <ul class="list-group">
                    <? foreach($translations as $string) { ?>
                        <li class="list-group-item <? if ($string->hasTranslation()) { ?>list-group-item-success<? } ?> clearfix" data-translation="<?=$string->getRecordID()?>">
                            <span class="original">
                                <?=$string->getOriginal()?>
                            </span>
                            <span class="translation">
                                <?=$string->getTranslation()?>
                            </span>
                        </li>
                    <? } ?>
                </ul>
                <div class="panel-footer"></div>
            </div>
        </div>
        <div class="col-md-5 ccm-translate-site-interface-translate">
            <div class="panel panel-primary">
                <div class="panel-heading"><?=t('Translate')?></div>
                <div class="panel-body">
                    <? foreach($translations as $string) { ?>
                        <form method="post" class="translate-form" action="<?=$controller->action('save_translation')?>" data-form="<?=$string->getRecordID()?>">
                            <input type="hidden" name="mtID" value="<?=$string->getRecordID()?>">
                            <div class="form-group">
                                <label class="control-label" for="original-<?=$string->getRecordID()?>"><?=t('Original String')?></label>
                                <textarea class="form-control" disabled id="original-<?=$string->getRecordID()?>" rows="8"><?=h($string->getOriginal())?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="translation-<?=$string->getRecordID()?>"><?=t('Translation')?></label>
                                <textarea class="form-control" name="msgstr" id="translation-<?=$string->getRecordID()?>" rows="8"><?=h($string->getTranslation())?></textarea>
                            </div>
                            <button class="btn btn-primary" data-btn="save"><?=t('Save &amp; Continue')?></button>
                            <div class="spacer-row-3"></div>
                        </form>
                    <? } ?>
                </div>
                <div class="panel-footer"></div>
            </div>
        </div>
    </div>

    <div class="ccm-dashboard-header-buttons">
        <a href="<?=$controller->action('view')?>" class="btn btn-default"><?=t('Back to List')?></a>
    </div>

    <script type="text/javascript">
        var $translateBody;
        activateTranslation = function(id) {
            var $listItem = $('li[data-translation=' + id + ']'),
                $form = $('form[data-form=' + id + ']');

            $('div.ccm-translate-site-interface-messages li.list-group-item-info').removeClass('list-group-item-info');
            $('div.ccm-translate-site-interface-translate form').hide();
            $listItem.addClass('list-group-item-info');
            $form.concreteAjaxForm({
                success: function(r) {
                    $listItem.addClass('list-group-item-success');
                    return;
                }
            }).show();
        }

        saveAndContinue = function(id) {
            var $activeTranslation = $('li.list-group-item-info'),
                $next = $activeTranslation.next(),
                $form = $('form[data-form=' + id + ']');

            if ($next.length) {
                activateTranslation($next.attr('data-translation'));
                $form.submit();
            }
        }

        $(function() {
            $translateBody = $('div.ccm-translate-site-interface-translate div.panel-body');
            var windowHeight = $(window).height();
            var height = windowHeight - 350;
            var existingHeight = $translateBody.find('form').height();
            if (existingHeight > height) {
                height = existingHeight;
            }

            $('div.ccm-translate-site-interface-messages ul.list-group').css('height', height);
            $('div.ccm-translate-site-interface-translate div.panel-body').css('height', height);

            $('div.ccm-translate-site-interface-messages').on('click', 'li[data-translation]', function() {
                var translation = $(this).attr('data-translation');
                activateTranslation(translation);
            });

            $('div.ccm-translate-site-interface-translate').on('click', 'button[data-btn=save]', function() {
                var translation = $(this).attr('data-translation');
                saveAndContinue(translation);
            });

            $(window).on('keydown', function(e) {
                if (e.keyCode == 40) {
                    e.preventDefault();
                    var $activeTranslation = $('li.list-group-item-info');
                    saveAndContinue($activeTranslation.attr('data-translation'));
                }

                if (e.keyCode == 38) {
                    e.preventDefault();
                    var $activeTranslation = $('li.list-group-item-info'),
                        $previous = $activeTranslation.prev();
                    if ($previous.length) {
                        activateTranslation($previous.attr('data-translation'));
                    }
                }
            });

            activateTranslation($('div.ccm-translate-site-interface-messages li[data-translation]:first-child').attr('data-translation'));
        });



    </script>

    <?
}  else {

	if (!is_dir(DIR_LANGUAGES_SITE_INTERFACE) || !is_writable(DIR_LANGUAGES_SITE_INTERFACE)) { ?>
		<div class="alert alert-warning"><?=t('You must create the directory %s and make it writable before you may run this tool. Additionally, all files within this directory must be writable.', DIR_LANGUAGES_SITE_INTERFACE)?></div>
	<? } ?>

	<?php
	$nav = Loader::helper('navigation');
	Loader::model('section', 'multilingual');
	$pages = \Concrete\Core\Multilingual\Page\Section\Section::getList();
	$defaultSourceLocale = Config::get('concrete.multilingual.default_source_locale');

	$ch = Core::make('multilingual/interface/flag');
	$dh = Core::make('helper/date');
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
                $pcl = \Concrete\Core\Multilingual\Page\Section\Section::getByID($pc->getCollectionID());?>
                <tr>
                    <td><?=$ch->getSectionFlagIcon($pc)?></td>
                    <td>
                        <a href="<?=$nav->getLinkToCollection($pc)?>">
                            <?=$pc->getCollectionName()?>
                        </a>
                    </td>
                    <td style="white-space: nowrap">
                        <?php echo $pc->getLocale(); ?>
                        <? if ($pc->getLocale() != $defaultSourceLocale) { ?>
                            <a href="#" class="icon-link launch-tooltip" title="<?=REL_DIR_LANGUAGES_SITE_INTERFACE?>/<?=$pc->getLocale()?>.mo"><i class="fa fa-question-circle"></i></a>
                        <? } ?>
                    </td>
                    <td style="width: 40%">
                        <? if ($pc->getLocale() != $defaultSourceLocale) { ?>
                            <?
                            $data = $extractor->getSectionSiteInterfaceCompletionData($pc);
                            ?>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?=$data['completionPercentage']?>%">&nbsp;</div>
                            </div>
                        <? } ?>
                    </td>
                    <td style="white-space: nowrap">
                        <? if ($pc->getLocale() != $defaultSourceLocale) { ?>
                            <span class="percent"><?=$data['completionPercentage']?>%</span> - <span class="translated"><?=$data['translatedCount']?></span> <?=t('of')?> <span class="total"><?=$data['messageCount']?></span>
                        <? } ?>
                    </td>
                    <td>
                        <? if ($pc->getLocale() != $defaultSourceLocale) {
                            if (file_exists(DIR_LANGUAGES_SITE_INTERFACE . '/' . $pc->getLocale() . '.mo'))
                                print $dh->formatDateTime(filemtime(DIR_LANGUAGES_SITE_INTERFACE . '/' . $pc->getLocale() . '.mo'), true);
                            else
                                print t('File not found.');
                        }
                        else
                            echo t('N/A'); ?>
                    </td>
                    <? if ($pc->getLocale() == $defaultSourceLocale) { ?>
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

        <form method="post" action="<?=$controller->action('submit')?>">
        <div class="ccm-dashboard-header-buttons btn-group">
            <button class="btn btn-default" type="submit" name="action" value="reload"><?=t('Reload Strings')?></button>
            <button class="btn btn-default" type="submit" name="action" value="export"><?=t('Export to .PO')?></button>
            <?=$valt->output()?>
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