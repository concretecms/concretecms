<?
defined('C5_EXECUTE') or die("Access Denied.");

// HELPERS
$bt = Loader::helper('concrete/ui');
$valt = Loader::helper('validation/token');
$form = Loader::helper("form");
$alreadyActiveMessage = t('This theme is currently active on your site.');

?>

	<? if (isset($activate_confirm)) { ?>

    <?

	// Confirmation Dialogue.
	// Separate inclusion of dashboard header and footer helpers to allow for more UI-consistant 'cancel' button in pane footer, rather than alongside activation confirm button in alert-box.

	?>



        <div class="alert alert-danger">

            <h5>
                <strong><?=t('Apply this theme to every page on your site?')?></strong>
            </h5>

        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
        <?=$bt->button(t("Ok"), $activate_confirm, 'right', 'btn btn-primary');?>
    	<?=$bt->button(t('Cancel'), $view->url('/dashboard/pages/themes/'), 'left');?>
        </div>
        </div>

	<? } else { ?>

    <?

	// Themes listing / Themes landing page.
	// Separate inclusion of dashboard header and footer helpers - no pane footer.

	?>

	<h3><?=t('Currently Installed')?></h3>

	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table">
	<?
	if (count($tArray) == 0) { ?>

        <tbody>
            <tr>
                <td><p><?=t('No themes are installed.')?></p></td>
            </tr>
		</tbody>

	<? } else { ?>

    	<tbody>

		<? foreach ($tArray as $t) { ?>

            <tr <? if ($siteThemeID == $t->getThemeID()) { ?> class="ccm-theme-active" <? } ?>>

                <td>
					<div class="ccm-themes-thumbnail" style="padding:4px;background-color:#FFF;border-radius:3px;border:1px solid #DDD;">
						<?=$t->getThemeThumbnail()?>
					</div>
				</td>

                <td width="100%" style="vertical-align:middle;">

                    <div class="btn-group" style="float: right">
                    <? if ($siteThemeID == $t->getThemeID()) { ?>
                        <?=$bt->button_js(t("Activate"), "alert('" . $alreadyActiveMessage . "')", 'left', 'primary ccm-button-inactive', array('disabled'=>'disabled'));?>
                    <? } else { ?>
                        <?=$bt->button(t("Activate"), $view->url('/dashboard/pages/themes','activate', $t->getThemeID()), 'left', 'primary');?>
                    <? } ?>
                        <?//$bt->button_js(t("Preview"), "ccm_previewInternalTheme(1, " . intval($t->getThemeID()) . ",'" . addslashes(str_replace(array("\r","\n",'\n'),'',$t->getThemeDisplayName())) . "')", 'left');?>
                        <?=$bt->button(t("Inspect"), $view->url('/dashboard/pages/themes/inspect', $t->getThemeID()), 'left');?>
                        <?//$bt->button(t("Customize"), $view->url('/dashboard/pages/themes/customize', $t->getThemeID()), 'left');?>
                    <? if ($siteThemeID == $t->getThemeID()) { ?>
                        <?=$bt->button(t("Remove"), $view->url('/dashboard/pages/themes', 'remove', $t->getThemeID(), $valt->generate('remove')), 'right', 'btn-danger', array('disabled'=>'disabled'));?>
                    <? } else { ?>
                        <?=$bt->button(t("Remove"), $view->url('/dashboard/pages/themes', 'remove', $t->getThemeID(), $valt->generate('remove')), 'right', 'btn-danger');?>
					<? } ?>
					</div>


                    <p class="ccm-themes-name"><strong><?=$t->getThemeDisplayName()?></strong></p>
                    <p class="ccm-themes-description"><em><?=$t->getThemeDisplayDescription()?></em></p>

                </td>
            </tr>

		<? } ?>

        </tbody>

	<? } ?>

    </table>


    <form method="post" action="<?=$view->action('save_mobile_theme')?>" class="form-inline">
    <h3><?=t("Mobile Theme")?></h3>
    <p><?=t("To use a separate theme for mobile browsers, specify it below.")?></p>

    <div class="control-group">
    <?=$form->label('MOBILE_THEME_ID', t('Mobile Theme'), array('style'=>'margin-right: 10px;'))?>
    	<? $themes[0] = t('** Same as website (default)'); ?>
    	<? foreach($tArray as $pt) {
    		$themes[$pt->getThemeID()] = $pt->getThemeDisplayName();
    	} ?>
    	<?=$form->select('MOBILE_THEME_ID', $themes, Config::get('concrete.misc.mobile_theme_id'))?>
        <button class="btn btn-default" type="submit"><?=t('Save')?></button>
    </div>
    </form>
    <br/><br/>


	<?
	if (count($tArray2) > 0) { ?>

	<h3><?=t('Themes Available to Install')?></h3>


	<table class="table">
		<tbody>
		<? foreach ($tArray2 as $t) { ?>
            <tr>

                <td>
					<div class="ccm-themes-thumbnail" style="padding:4px;background-color:#FFF;border-radius:3px;border:1px solid #DDD;">
						<?=$t->getThemeThumbnail()?>
					</div>
				</td>

                <td width="100%" style="vertical-align:middle;">
                <p class="ccm-themes-name"><strong><?=$t->getThemeDisplayName()?></strong></p>
                <p class="ccm-themes-description"><em><?=$t->getThemeDisplayDescription()?></em></p>

                <div class="ccm-themes-button-row clearfix">
                <?=$bt->button(t("Install"), $view->url('/dashboard/pages/themes','install',$t->getThemeHandle()),'left','primary');?>
                </div>
                </td>

            </tr>
        <? } // END FOREACH ?>

        </tbody>
	</table>

    <!-- END AVAILABLE TO INSTALL -->

	<? } // END 'IF AVAILABLE' CHECK ?>

    <? if (Config::get('concrete.marketplace.enabled') == true) { ?>

	<div class="well" style="padding:10px 20px;">
        <h3><?=t('Want more themes?')?></h3>
        <p><?=t('You can download themes and add-ons from the concrete5 marketplace.')?></p>
        <p><a class="btn btn-success" href="<?=$view->url('/dashboard/extend/themes')?>"><?=t("Get More Themes")?></a></p>
    </div>

    <? } ?>

	<? } // END 'ELSE' DEFAULT LISTING ?>
