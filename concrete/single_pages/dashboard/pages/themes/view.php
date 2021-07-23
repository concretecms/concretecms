<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\Theme\Theme $activeTheme
 */
// HELPERS
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$ih = $app->make('helper/concrete/ui');

$alreadyActiveMessage = t('This theme is currently active on your site.');

if (isset($activate_confirm)) {
    // Confirmation Dialogue.
    // Separate inclusion of dashboard header and footer helpers to allow for more UI-consistant 'cancel' button in pane footer, rather than alongside activation confirm button in alert-box.?>
    <div class="alert alert-danger">
        <h5><strong><?=t('Apply this theme to every page on your site?'); ?></strong></h5>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?= $ih->button(t('Ok'), $activate_confirm, 'right', 'btn btn-primary'); ?>
            <?= $ih->button(t('Cancel'), URL::to('/dashboard/pages/themes/'), 'left'); ?>
        </div>
    </div>
    <?php
} else {
    // Themes listing / Themes landing page.
    // Separate inclusion of dashboard header and footer helpers - no pane footer.
    ?>
    <h3><?=t('Currently Installed'); ?></h3>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table"><?php
        if (count($tArray) == 0) {
            ?><tbody>
                <tr>
                    <td><p><?=t('No themes are installed.'); ?></p></td>
               </tr>
            </tbody><?php
        } else {
            ?><tbody>
            <?php
            foreach ($tArray as $t) {
                ?>
                <tr <?php if ($activeTheme->getThemeID() == $t->getThemeID()) {
                    ?> class="ccm-theme-active" <?php
                } ?>>
                    <td>
                        <div class="ccm-themes-thumbnail" style="padding:4px;background-color:#FFF;border-radius:3px;border:1px solid #DDD;">
                            <?=$t->getThemeThumbnail(); ?>
                        </div>
                    </td>
                    <td width="100%" style="vertical-align:middle;">
                        <div class="btn-group float-end">
                        <?php
                        if ($activeTheme->getThemeID() == $t->getThemeID()) { ?>
                            <button disabled class="btn btn-secondary btn-sm"><?=t('Activate')?></button>
                        <?php } else { ?>
                            <a href="<?=$view->action('activate', $t->getThemeID())?>" class="btn btn-secondary btn-sm"><?=t('Activate')?></a>
                        <?php } ?>
                        <?php if ($t->isThemeCustomizable()) { ?>
                            <a href="<?=$view->action('preview', $t->getThemeID())?>" class="btn btn-secondary btn-sm"><?=t('Preview &amp; Customize')?></a>
                        <?php } else { ?>
                            <a href="<?=$view->action('preview', $t->getThemeID())?>" class="btn btn-secondary btn-sm"><?=t('Preview')?></a>
                       <?php } ?>
                        <a href="<?=$view->action('inspect', $t->getThemeID())?>" class="btn btn-sm btn-secondary"><?=t('Page Templates')?></a>
                        <?php
                        if ($activeTheme->getThemeID() == $t->getThemeID()) { ?>
                            <button disabled class="btn btn-danger btn-sm"><?=t('Remove')?></button>
                        <?php } else { ?>
                            <a href="<?=$view->action('remove', $t->getThemeID(), $token->generate('remove'))?>" class="btn btn-secondary btn-sm"><?=t('Remove')?></a>
                        <?php } ?>
                    </div>
                        <p class="ccm-themes-name"><strong><?=$t->getThemeDisplayName(); ?></strong></p>
                        <p class="ccm-themes-description"><em><?=$t->getThemeDisplayDescription(); ?></em></p>

                        <?php if ($activeTheme->getThemeID() == $t->getThemeID() && $activeTheme->hasSkins()) {

                            $skins = $activeTheme->getSkins(); ?>
                            <form method="post" action="<?=$view->action('save_selected_skin'); ?>" >
                                <div><strong><?=t('Skins')?></strong></div>
                                <?php $token->output('save_selected_skin'); ?>
                                <?php
                                foreach($skins as $skin) { ?>
                                    <button type="submit" name="themeSkinIdentifier"
                                            value="<?=$skin->getIdentifier()?>"
                                            class="<?php if ($themeSkinIdentifier == $skin->getIdentifier()) { ?>active<?php } ?> btn btn-sm btn-secondary">
                                        <?=$skin->getName()?>
                                    </button>
                                <?php } ?>
                                </div>
                            </form>


                        <?php } ?>
                    </td>
                </tr>
                <?php
            } ?></tbody><?php
        } ?></table>

    <?php
    if (count($tArray2) > 0) {
        ?>
        <hr>
        <h3><?=t('Themes Available to Install'); ?></h3>
        <table class="table">
        
            <tbody>
            <?php foreach ($tArray2 as $t) {
            ?>
                <tr>
                    <td>
                        <div class="ccm-themes-thumbnail" style="padding:4px;background-color:#FFF;border-radius:3px;border:1px solid #DDD;">
                            <?=$t->getThemeThumbnail(); ?>
                        </div>
                    </td>
                    <td width="100%" style="vertical-align:middle;">
                        <p class="ccm-themes-name"><strong><?=$t->getThemeDisplayName(); ?></strong></p>
                        <p class="ccm-themes-description"><em><?=$t->getThemeDisplayDescription(); ?></em></p>
                        <div class="ccm-themes-button-row clearfix"><?php
                            if (strlen($t->error) > 0) {
                                ?><div class="alert alert-danger" role="alert"><?php echo nl2br(h($t->error)); ?></div><?php
                            } else {
                                echo $ih->button(t('Install'), $view->action('install', $t->getThemeHandle()), 'left', 'btn-secondary');
                            } ?></div>
                    </td>
                </tr>
            <?php
        } ?>
            </tbody>
        </table>
        <?php
    }
        if (Config::get('concrete.marketplace.enabled') == true) {
            ?>

            <div class="mt-5">
                <h3 class="mt-2"><?=t('Want more themes?'); ?></h3>
                <p><?=t('You can download themes and add-ons from the concrete5 marketplace.'); ?></p>
                <p><a class="btn btn-success" href="<?=URL::to('/dashboard/extend/themes'); ?>"><?=t('Get More Themes'); ?></a></p>
            </div>
        <?php
        }
    }
