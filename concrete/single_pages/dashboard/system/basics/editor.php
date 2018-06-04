<?php
/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Form\Service\Form $form */

/* @var bool $filemanager */
/* @var bool $sitemap */

/* @var Concrete\Core\Editor\PluginManager $manager */
/* @var Concrete\Core\Editor\Plugin[] $plugins */
/* @var string[] $selected_hidden */

?>
<form method="post" class="ccm-dashboard-content-form" action="<?= $view->action('submit') ?>">
    <?php $token->output('submit') ?>
    <fieldset>
        <?= $form->label('', t('concrete5 Extensions')) ?>
        <div class="checkbox">
            <label>
                <?= $form->checkbox('enable_filemanager', 1, $filemanager) ?>
                <?= t('Enable file selection from file manager.') ?>
            </label>
        </div>
        <div class="checkbox">
            <label>
                <?= $form->checkbox('enable_sitemap', 1, $sitemap) ?>
                <?= t('Enable page selection from sitemap.') ?>
            </label>
        </div>
    </fieldset>
    <fieldset>
        <?= $form->label('', t('Editor Plugins')) ?>
        <?php
        foreach ($plugins as $key => $plugin) {
            if (!in_array($key, $selected_hidden)) {
                 ?>
                 <div class="checkbox">
                    <label>
                        <?= $form->checkbox('plugin[]', $key, $manager->isSelected($key)) ?>
                        <?= $plugin->getName() ?>
                    </label>
                </div>
                <?php
            }
        }
        ?>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-primary" type="submit"><?= t('Save') ?></button>
        </div>
    </div>
</form>
