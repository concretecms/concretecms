<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<h3><?=t('Installed Themes')?></h3>

<?php if (count($tArray)) { ?>

    <div class="ps-0 container-fluid">
        <div class="row row-cols-3">

            <?php foreach($tArray as $t) {
                $thumbnail = $t->getThemeThumbnail();
                $thumbnail->class('card-img-top')->width(null)->height(null);
                ?>

                <div class="col mb-4">
                    <div class="card h-100 <?php if ($activeTheme->getThemeID() == $t->getThemeID()) { ?>border-primary border<?php } ?>">
                        <?=$thumbnail ?>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="card-title mb-0"><?=$t->getThemeDisplayName(); ?></h5>
                                <div class="dropdown ms-auto">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        <?php if ($activeTheme->getThemeID() == $t->getThemeID() && $activeTheme->hasSkins()) { ?>
                                            <li class="dropdown-header"><?=t('Active Skin')?></li>
                                            <?php
                                            $skins = $activeTheme->getSkins();
                                            foreach ($skins as $skin) { ?>
                                                <li><a href="<?=$view->action('save_selected_skin', $skin->getIdentifier(), $token->generate('save_selected_skin'))?>"
                                                       class="dropdown-item">
                                                        <?php if ($themeSkinIdentifier == $skin->getIdentifier()) { ?>
                                                            <span class="text-success"><i class="fas fa-check"></i> <?=$skin->getName()?></span>
                                                        <?php } else { ?>
                                                            <?=$skin->getName()?>
                                                        <?php } ?>
                                                    </a></li>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if ($activeTheme->getThemeID() != $t->getThemeID()) { ?>
                                            <li><a href="javascript:void(0)"
                                                   data-dialog="activate-theme-<?=$t->getThemeID()?>"
                                                   class="dropdown-item"><?=t('Activate')?></a></li>
                                        <?php } ?>
                                        <?php if ($t->supportsThemeDocumentation()) { ?>
                                            <li class="dropdown-divider"></li>
                                            <li class="dropdown-header"><?=t('Documentation')?></li>
                                            <?php /* No, this is not a typo. Theme documentation is used for documentation purposes AND for previewing when customizing */ ?>
                                            <?php if ($t->hasThemeDocumentation()) { ?>
                                                <li><a href="<?=$view->action('preview', $t->getThemeID())?>" class="dropdown-item"><?=t('View')?></a></li>
                                                <li><a href="javascript:void(0)" class="dropdown-item" data-dialog="uninstall-documentation-<?=$t->getThemeID()?>"><?=t('Uninstall')?></a></li>
                                            <?php } else { ?>
                                                <li><a href="javascript:void(0)" class="dropdown-item" data-dialog="install-documentation-<?=$t->getThemeID()?>"><?=t('Install')?></a></li>
                                            <?php } ?>
                                            <li class="dropdown-divider"></li>
                                        <?php } ?>
                                        <?php if ($activeTheme->getThemeID() == $t->getThemeID()) { ?>
                                            <?php if ($t->isThemeCustomizable()) { ?>
                                                <li><a href="<?=$view->action('preview', $t->getThemeID())?>" class="dropdown-item"><?=t('Customize')?></a></li>
                                            <?php }
                                        } else { ?>
                                            <li><a href="<?=$view->action('preview', $t->getThemeID())?>" class="dropdown-item"><?=t('Preview')?></a></li>
                                        <?php } ?>
                                        <li><a href="<?=$view->action('inspect', $t->getThemeID())?>" class="dropdown-item"><?=t('Page Templates')?></a></li>
                                        <li><a href="<?=$view->action('remove', $t->getThemeID(), $token->generate('remove'))?>"
                                               class="dropdown-item <?php if ($activeTheme->getThemeID() == $t->getThemeID()) { ?>disabled<?php } else { ?>text-danger<?php } ?>"><?=t('Remove Theme')?></a></li>
                                    </ul>
                                </div>
                            </div>

                            <p class="card-text text-secondary small"><?=$t->getThemeDisplayDescription(); ?></p>
                        </div>

                    </div>
                </div>

                <div class="d-none">
                    <div data-dialog-wrapper="activate-theme-<?=$t->getThemeID()?>">
                        <form method="post" data-form-activate-theme="<?=$t->getThemeID()?>" action="<?= $view->action('activate_confirm') ?>">
                            <?php $token->output("activate_confirm") ?>
                            <input type="hidden" name="pThemeID" value="<?=$t->getThemeID()?>">
                            <p><?= t('This will reset any page-level theme choices and apply the selected theme to all pages on your site.') ?></p>
                            <div class="dialog-buttons">
                                <button class="btn btn-secondary" data-dialog-action="cancel"><?=t('Cancel')?></button>
                                <button type="submit" onclick="$('form[data-form-activate-theme=<?=$t->getThemeID()?>]').trigger('submit')" class="btn btn-primary"><?=t('Activate')?></button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($t->supportsThemeDocumentation()) { ?>
                    <div class="d-none">
                        <?php if ($t->hasThemeDocumentation()) { ?>
                            <div data-dialog-wrapper="uninstall-documentation-<?=$t->getThemeID()?>">
                                <form method="post" data-form-uninstall-documentation="<?=$t->getThemeID()?>" action="<?= $view->action('uninstall_documentation', $t->getThemeID()) ?>">
                                    <?php $token->output("uninstall_documentation") ?>
                                    <p><?= t('This will uninstall the theme documentation added to this theme, and remove any files, images or demonstration data added with it.') ?></p>
                                    <div class="dialog-buttons">
                                        <button class="btn btn-secondary" data-dialog-action="cancel"><?=t('Cancel')?></button>
                                        <button type="submit" onclick="$('form[data-form-uninstall-documentation=<?=$t->getThemeID()?>]').trigger('submit')" class="btn btn-primary"><?=t('Uninstall')?></button>
                                    </div>
                                </form>
                            </div>
                        <?php } else { ?>
                            <div data-dialog-wrapper="install-documentation-<?=$t->getThemeID()?>">
                                <form method="post" data-form-install-documentation="<?=$t->getThemeID()?>" action="<?= $view->action('install_documentation', $t->getThemeID()) ?>">
                                    <?php $token->output("install_documentation") ?>
                                    <p><?= t('This will install documentation for this theme. It may include files, images or other CMS data for demonstration purposes.') ?></p>
                                    <div class="dialog-buttons">
                                        <button class="btn btn-secondary" data-dialog-action="cancel"><?=t('Cancel')?></button>
                                        <button type="submit" onclick="$('form[data-form-install-documentation=<?=$t->getThemeID()?>]').trigger('submit')" class="btn btn-primary"><?=t('Install')?></button>
                                    </div>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

            <?php } ?>

        </div>
    </div>

<?php } else { ?>

    <p><?=t('No themes are installed.'); ?></p>

<?php } ?>

<?php
if (count($tArray2) > 0) {
    ?>

    <h3 class="mt-5"><?=t('Themes Available to Install'); ?></h3>

        <div class="ps-0 container-fluid">
            <div class="row row-cols-3">

            <?php foreach($tArray2 as $t) {
                $thumbnail = $t->getThemeThumbnail();
                $thumbnail->class('card-img-top')->width(null)->height(null);
                ?>

                    <div class="col mb-4">
                        <div class="card h-100">
                            <?=$thumbnail ?>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <h5 class="card-title mb-0"><?=$t->getThemeDisplayName(); ?></h5>
                                </div>

                                <p class="card-text text-secondary small"><?=$t->getThemeDisplayDescription(); ?></p>
                            </div>
                            <form class="m-3" method="post" action="<?=$view->action('install')?>">
                                <?=$token->output('install_theme')?>
                                <input type="hidden" name="theme" value="<?=$t->getThemeHandle()?>">
                                <button type="submit" class="btn w-100 btn-block btn-secondary"><?=t('Install')?></button>
                            </form>
                        </div>
                    </div>
        <?php } ?>
            </div>
        </div>
<?php } ?>

<?php if ($hasThemeCustomizations) { ?>


    <div class="mt-5">
        <h3><?=t('Customizations Detected'); ?></h3>
        <div class="alert alert-warning">
            <p><?=t('Theme customizations have been detected on your site theme or on individual pages in your site. To begin the reset process for these customizations, click below.'); ?></p>
            <form method="post" action="<?=$view->action('reset_customizations')?>">
                <?=$token->output('reset_customizations')?>
                <button type="button" data-dialog="reset-customizations" data-dialog-width="500" class="btn btn-secondary"><?=t('Reset')?></button>
            </form>
        </div>
    </div>

    <div class="d-none">
        <div data-dialog-wrapper="reset-customizations">
            <form method="post" data-form="reset-customizations" action="<?= $view->action('reset_customizations') ?>">
                <?php $token->output("reset_customizations") ?>
                <?php if ($hasSiteThemeCustomizations) { ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="resetSiteThemeCustomizations" id="resetSiteThemeCustomizations" value="1" checked>
                        <label class="form-check-label" for="resetSiteThemeCustomizations">
                            <?=t('Revert site-wide theme customizations to the default preset.')?>
                        </label>
                    </div>
                <?php } ?>
                <?php if ($hasPageThemeCustomizations) { ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="resetPageThemeCustomizations" id="resetPageThemeCustomizations" value="1" checked>
                        <label class="form-check-label" for="resetPageThemeCustomizations">
                            <?=t('Reset page specific theme customizations.')?>
                        </label>
                    </div>
                <?php } ?>
                <div class="dialog-buttons">
                    <button class="btn btn-secondary" data-dialog-action="cancel"><?=t('Cancel')?></button>
                    <button type="submit" onclick="$('form[data-form=reset-customizations]').trigger('submit')" class="btn btn-primary"><?=t('Reset')?></button>
                </div>
            </form>
        </div>
    </div>


<?php } ?>


<?php
if (Config::get('concrete.marketplace.enabled') == true) {
    ?>

    <div class="mt-5">
        <h3><?=t('Want more themes?'); ?></h3>
        <p><?=t('You can download themes and add-ons from the marketplace.'); ?></p>
        <p><a class="btn btn-success" href="<?=URL::to('/dashboard/extend/themes'); ?>"><?=t('Get More Themes'); ?></a></p>
    </div>
    <?php
}
