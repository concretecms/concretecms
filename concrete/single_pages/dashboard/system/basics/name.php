<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Entity\Attribute\Set[] $sets
 * @var \Concrete\Core\Page\View\PageView $view
 * @var \Concrete\Core\Form\Service\Form $form
 * @var \Concrete\Core\Entity\Site\Site $site
 * @var \Concrete\Core\Attribute\Form\Renderer $renderer
 * @var \Concrete\Core\Entity\Attribute\Key\SiteKey[] $unassignedAttributes
 * @var int $totalAttributes
 */
?>

<div class="ccm-dashboard-header-buttons">
    <a href="<?= View::url('/dashboard/system/basics/attributes') ?>"
       class="btn btn-secondary"
    ><?= t('Manage Attributes') ?></a>
</div>

<form method="post" class="ccm-dashboard-content-form" action="<?= $view->action('update_sitename') ?>">
    <?= $this->controller->token->output('update_sitename') ?>

    <fieldset>
        <legend><?= t('Core Properties') ?></legend>
        <div class="form-group">
            <label for="SITE"
                   class="launch-tooltip control-label form-label"
                   data-bs-placement="right"
                   title="<?= t('By default, site name is displayed in the browser title bar. It is also the default name for your project on market.concretecms.com') ?>"
            ><?= t('Site Name') ?></label>
            <?= $form->text('SITE', $site->getSiteName()) ?>
        </div>
    </fieldset>

    <fieldset>
        <legend><?= t('Custom Attributes') ?></legend>
        <?php
        if ($totalAttributes > 0) {
            foreach ($sets as $set) {
                $attributes = $set->getAttributeKeys();
                if (count($attributes)) {
                    ?>
                    <h4><?= $set->getAttributeSetDisplayName() ?></h4>
                    <?php
                    foreach ($attributes as $ak) {
                        $renderer->render($ak);
                    }
                }
            }

            if (count($unassignedAttributes)) {
                ?>
                <h4><?= t('Other') ?></h4>
                <?php
                foreach ($unassignedAttributes as $ak) {
                    $renderer->render($ak);
                }
            }
        } else {
            ?>
            <div class="mt-3">
                <p><?= t('You have not defined any <a href="%s">custom attributes</a> for this site.', URL::to('/dashboard/system/basics/attributes')) ?></p>
            </div>
            <?php
        }
        ?>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary" type="submit"><?= t('Save') ?></button>
        </div>
    </div>

</form>
