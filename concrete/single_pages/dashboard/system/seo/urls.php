<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<form method="post" action="<?php echo $view->action('save_urls'); ?>">
    <?php echo $this->controller->token->output('save_urls'); ?>

    <fieldset>
        <legend><?= t('Pretty URLs') ?></legend>

        <div class="form-group">
            <div class="checkbox">
                <label>
                    <?php echo $fh->checkbox('URL_REWRITING', 1, $urlRewriting) ?>
                    <?php echo t('Remove index.php from URLs'); ?>
                </label>
            </div>
        </div>
        <?php if (isset($configuration_action)) { ?>
            <div class="form-group">
                <label class="control-label"><?=$configuration_action?></label>
                <textarea rows="8" class="form-control" onclick="this.select()"><?=h($configuration_code)?></textarea>
            </div>
        <?php
        } ?>
    </fieldset>

    <fieldset>
        <legend><?= t('Canonical URLs') ?></legend>

        <div class="form-group">
            <label class="control-label" for="canonical_url"><?= t('Canonical URL') ?></label>
            <?=$form->text('canonical_url', $canonical_url, ['placeholder' => 'http://domain.com'])?>
        </div>

        <div class="form-group">
            <label class="control-label" for="canonical_url_alternative"><?= t('Alternative canonical URL') ?></label>
            <?=$form->text('canonical_url_alternative', $canonical_url_alternative, ['placeholder' => 'https://domain.com'])?>
        </div>

        <div class="alert alert-warning">
            <?= t('Ensure that your site is viewable at the URL(s) above before you check the checkbox below.
            If not, doing so may render your site unviewable until you can manually undo this change.') ?>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip" title="<?= t('If checked, this site will only be available at the host, port and SSL combination chosen above.') ?>" for="redirect_to_canonical_url"><?= t('URL Redirection') ?></label>
            <div class="checkbox">
                <label>
                    <?php echo $fh->checkbox('redirect_to_canonical_url', 1, $redirect_to_canonical_url) ?>
                    <?php echo t('Only render at canonical URLs.'); ?>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label" for="canonical_tag"><?= t('Canonical Tag') ?></label>
            <div class="checkbox">
                <label>
                    <?= $fh->checkbox('canonical_tag', 1, $canonical_tag) ?>
                    <?= t('Add a %s tag to the site pages.', '<code>' . h('<meta rel="canonical" href="...">') . '</code>') ?>
                </label>
            </div>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $interface->submit(t('Save'), 'url-form', 'right', 'btn-primary'); ?>
        </div>
    </div>
</form>

<script>
$(function () {
    var steps = [{
        element: 'input[name=URL_REWRITING]',
        content: <?= json_encode('<h3>' . t('Pretty URLs') . '</h3>' . t('Check this checkbox to remove index.php from your URLs.<br/>You will be given code to place in a file named .htaccess in your web root. concrete5 will try and place this code in the file for you.')) ?>,
        placement: 'bottom',
    },{
        element: 'input[name=canonical_url]',
        content: <?= json_encode('<h3>' . t('Canonical URL') . '</h3>' . t('If you are running a site at multiple domains, enter the canonical domain here. This will be used for sitemap generation, any other purposes that require a specific domain. You can usually leave this blank.')) ?>,
        placement: 'bottom',
    },{
        element: 'input[name=canonical_url_alternative]',
        content: <?= json_encode('<h3>' . t('Alternative URL') . '</h3>' . t('Certain add-ons require a secure SSL URL. Enter that URL here.')) ?>,
        placement: 'bottom',
    },{
        element: 'input[name=redirect_to_canonical_url]',
        content: <?= json_encode('<h3>' . t('Alternative URL') . '</h3>' . t('Ensure that your site ONLY renders at the canonical URL or the alternative URL.')) ?>,
        placement: 'bottom',
    }];

    var tour = new Tour({
        name: 'dashboard-system-urls',
        steps: steps,
        framework: 'bootstrap4',
        template: ccmi18n_tourist.template,
        localization: ccmi18n_tourist.localization,
        storage: false,
        showProgressBar: false,
        sanitizeWhitelist: {
            a: [/^data-/, 'href']
        },
        onPreviouslyEnded: function(tour) {
            tour.restart()
        },
        onStart: function() {
            window.ConcretePanelManager.getByIdentifier('help').hide();
        },
        onShown: ConcreteHelpGuideManager.updateStepFooter,
        onEnd: function() {
            window.ConcretePanelManager.getByIdentifier('help').show();
        }
    });
    ConcreteHelpGuideManager.register('dashboard-system-urls', tour);
});
</script>
