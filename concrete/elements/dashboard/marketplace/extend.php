<?php

defined('C5_EXECUTE') or die("Access Denied.");

if (isset($browseThemesUrl) && isset($browseAddonsUrl) && isset($browseIntegrationsUrl)) { ?>

    <fieldset class="mt-3 mb-3">
        <legend><?= t('Extend Your Site') ?></legend>
        <p><?= t('Extend your site with themes, add-ons and integrations from the Concrete CMS marketplace.') ?></p>

        <div>
            <a href="<?= $browseThemesUrl ?>" class="btn btn-success" target="_blank"><?= t('Themes') ?></a>
            <a href="<?= $browseAddonsUrl ?>" class="btn btn-success" target="_blank"><?= t('Add-Ons') ?></a>
            <a href="<?= $browseIntegrationsUrl ?>" class="btn btn-success" target="_blank"><?= t('Integrations') ?></a>
        </div>

    </fieldset>

<?php
} ?>