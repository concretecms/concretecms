<?php

use Concrete\Core\Page\Theme\Documentation\DocumentationNavigationFactory;

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $configureTheme \Concrete\Core\Page\Theme\Theme
 */
?>

<div data-vue-app="configure-theme" v-cloak>
    <?php
    if ($configureTheme->hasSkins()) { ?>
        <form method="post" action="<?=$view->action('save_selected_skin')?>">
            <?=$token->output('save_selected_skin')?>
            <section class="container-fluid gx-0 mb-4">
                <div class="row gx-5">
                    <div class="col-md-8">
                        <h3><?= t('Active Skin') ?></h3>
                        <div class="mb-3">
                            <label v-if="darkMode" for="activeSkin" class="form-label"><?=t('Light Mode Skin')?></label>
                            <label v-else="darkMode" for="activeSkin" class="form-label"><?=t('Active Skin')?></label>
                            <select id="activeSkin" name="activeSkinIdentifier" required class="form-select" v-model="activeSkinIdentifier">
                                <option value=""><?=t('** Select Skin')?></option>
                                <template>
                                    <option v-for="(skin, identifier) in skins" :value="identifier">{{skin}}</option>
                                </template>
                            </select>
                        </div>
                        <div class="mb-3" v-if="darkMode">
                            <label for="activeSkinDarkMode" class="form-label"><?=t('Dark Mode Skin')?></label>
                            <select :required="darkMode" v-model="activeSkinIdentifierDark" name="activeSkinIdentifierDark" id="activeSkinDarkMode" class="form-select">
                                <option value=""><?=t('** Select Skin')?></option>
                                <template>
                                    <option v-for="(skin, identifier) in skins" :value="identifier">{{skin}}</option>
                                </template>
                            </select>
                        </div>
                        <div class="mb-3">
                            <h5><?=t('Dark Mode')?></h5>
                            <div class="form-check">
                                <input class="form-check-input" name="darkMode" type="checkbox" v-model="darkMode" id="darkMode" value="1">
                                <label class="form-check-label" for="darkMode"><?=t('Enable separate dark mode skin.')?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-outline-primary" type="submit"><?=t('Save')?></button>
            </section>
        </form>
            <hr>
        <?php
    } ?>

    <?php
    if ($configureTheme->isThemeCustomizable()) { ?>
        <section class="container-fluid gx-0 mb-4">
            <div class="row gx-5">
                <div class="col-md-8">
                    <h3><?= t('Customize Theme') ?></h3>
                    <p class="fw-light"><?= t(
                            'This theme supports customization: you can modify its style properties and save new skins based on your changes.'
                        ) ?>
                    </p>
                    <a href="<?= $view->action('preview', $configureTheme->getThemeID()) ?>"
                       class="btn btn-outline-secondary"><?= t('Launch Customizer') ?></a>
                </div>
            </div>
        </section>
        <hr>
        <?php
    } ?>

    <section class="container-fluid gx-0">
        <div class="row gx-5">
            <div class="col-md-8">
                <h3><?= t('Theme Documentation') ?></h3>
                <p class="fw-light"><?= t(
                        'Theme documentation allows editors to preview common components and blocks in their Concrete theme. Theme documentation is stored in a private area of the sitemap.'
                    ) ?></p>

                <?php
                if ($configureTheme->supportsThemeDocumentation()) {
                    ?>
                    <?php
                    if ($configureTheme->hasThemeDocumentation()) { ?>
                        <?php
                        $documentationPage = $configureTheme->getThemeDocumentationParentPage();
                        if ($documentationPage) {
                            $factory = new DocumentationNavigationFactory($configureTheme);
                            $navigation = $factory->createNavigation();
                            $documentationPages = $navigation->getItems(); ?>
                            <nav class="nav flex-column mb-4">
                                <?php
                                foreach ($documentationPages as $documentationPage) { ?>
                                    <a class="nav-link" target="_blank"
                                       href="<?= $documentationPage->getUrl() ?>"><?= $documentationPage->getName() ?></a>
                                    <?php
                                }
                                ?>
                            </nav>
                            <?php
                        } ?>
                        <h5><?= t('Remove Documentation') ?></h5>
                        <div class="help-block"><?= t(
                                'If you remove the theme documentation you can create it again at any time.'
                            ) ?></div>
                        <a href="javascript:void(0)" class="btn btn-outline-danger"
                           data-dialog="uninstall-documentation-<?= $configureTheme->getThemeID() ?>"><?= t(
                                'Remove Documentation'
                            ) ?></a>
                        <div class="d-none">
                            <div data-dialog-wrapper="uninstall-documentation-<?= $configureTheme->getThemeID() ?>">
                                <form method="post" data-form-uninstall-documentation="<?= $configureTheme->getThemeID() ?>"
                                      action="<?= $view->action(
                                          'uninstall_documentation',
                                          $configureTheme->getThemeID()
                                      ) ?>">
                                    <?php
                                    $token->output("uninstall_documentation") ?>
                                    <p><?= t(
                                            'This will uninstall the theme documentation added to this theme, and remove any files, images or demonstration data added with it.'
                                        ) ?></p>
                                    <div class="dialog-buttons">
                                        <button class="btn btn-secondary" data-dialog-action="cancel"><?= t(
                                                'Cancel'
                                            ) ?></button>
                                        <button type="submit"
                                                onclick="$('form[data-form-uninstall-documentation=<?= $configureTheme->getThemeID(
                                                ) ?>]').trigger('submit')" class="btn btn-primary"><?= t(
                                                'Uninstall'
                                            ) ?></button>
                                    </div>
                                </form>
                            </div>

                        </div>            <?php
                    } else { ?>
                        <a href="javascript:void(0)" class="btn btn-outline-primary"
                           data-dialog="install-documentation-<?= $configureTheme->getThemeID() ?>"><?= t(
                                'Generate Documentation'
                            ) ?></a>
                        <div class="d-none">
                            <div data-dialog-wrapper="install-documentation-<?= $configureTheme->getThemeID() ?>">
                                <form method="post" data-form-install-documentation="<?= $configureTheme->getThemeID() ?>"
                                      action="<?= $view->action('install_documentation', $configureTheme->getThemeID()) ?>">
                                    <?php
                                    $token->output("install_documentation") ?>
                                    <p><?= t(
                                            'This will install documentation for this theme. It may include files, images or other CMS data for demonstration purposes.'
                                        ) ?></p>
                                    <div class="dialog-buttons">
                                        <button class="btn btn-secondary" data-dialog-action="cancel"><?= t(
                                                'Cancel'
                                            ) ?></button>
                                        <button type="submit"
                                                onclick="$('form[data-form-install-documentation=<?= $configureTheme->getThemeID(
                                                ) ?>]').trigger('submit')" class="btn btn-primary"><?= t(
                                                'Install'
                                            ) ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php
                    } ?>
                    <?php
                } else { ?>

                    <p class="text-muted"><?= t('This theme does not support documentation.') ?></p>
                    <?php
                } ?>
            </div>
        </div>
    </section>
</div>

<script>
    $(function () {
        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'div[data-vue-app=configure-theme]',
                data: {
                    darkMode: <?= !empty($activeSkinIdentifierDark) ? 'true' : 'false'?>,
                    skins: <?=json_encode($skins)?>,
                    activeSkinIdentifier: <?=json_encode($activeSkinIdentifier)?>,
                    activeSkinIdentifierDark: <?=!empty($activeSkinIdentifierDark) ? json_encode($activeSkinIdentifierDark) : "''" ?>
                },
            });
        });
    });
</script>