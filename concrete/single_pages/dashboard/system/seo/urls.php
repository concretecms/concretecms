<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Seo\Urls $controller
 * @var Concrete\Core\Application\Service\Dashboard $dashboard
 * @var Concrete\Core\Error\ErrorList\ErrorList $error
 * @var Concrete\Core\Form\Service\Form $form
 * @var bool $hideDashboardPanel
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var string $pageTitle
 * @var bool $redirectToCanonicalUrl
 * @var array $scopeItems
 * @var bool $showPrivacyPolicyNotice
 * @var Concrete\Theme\Dashboard\PageTheme $theme
 * @var Concrete\Core\Page\View\PageView $this
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var string $canonicalUrl
 * @var string $canonicalUrlAlternative
 * @var bool $urlRewriting
 * @var bool $canonicalTag
 */
?>

<form method="post" action="<?= $controller->action('save_urls') ?>">
    <?php $token->output('save_urls') ?>
    <fieldset>
        <legend><?= t('Pretty URLs') ?></legend>
        <div class="form-check">
            <?= $form->checkbox('url_rewriting', '1', $urlRewriting) ?>
            <label class="form-check-label" for="url_rewriting"><?= t('Remove index.php from URLs') ?></label>
        </div>
    </fieldset>
    <fieldset>
        <legend><?= t('Canonical URLs') ?></legend>
        <div class="form-group">
            <?= $form->label('canonical_url', t('Canonical URL')) ?>
            <?= $form->text('canonical_url', $canonicalUrl, ['placeholder' => 'http://domain.com']) ?>
        </div>
        <div class="form-group">
            <?= $form->label('canonical_url_alternative', t('Alternative canonical URL')) ?>
            <?= $form->text('canonical_url_alternative', $canonicalUrlAlternative, ['placeholder' => 'https://domain.com']) ?>
        </div>
    </fieldset>
    <fieldset>
        <legend><?= t('Options') ?></legend>
        <div class="form-check">
            <?= $form->checkbox('redirect_to_canonical_url', '1', $redirectToCanonicalUrl) ?>
            <label class="form-check-label" for="redirect_to_canonical_url"><?= t('Only render at canonical URLs.') ?></label>
            <div class="alert alert-warning">
                <?= t('If checked, this site will only be available at the host, port and SSL combination chosen above.') ?><br />
                <?= t('Ensure that your site is viewable at the URL(s) above before you check this checkbox. If not, doing so may render your site unviewable until you can manually undo this change.') ?>
            </div>
        </div>
        <div class="form-check">
            <?= $form->checkbox('canonical_tag', '1', $canonicalTag) ?>
            <label class="form-check-label" for="canonical_tag">
                <?= t('Add a %s tag to the site pages.', '<code>' . h('<meta rel="canonical" href="...">') . '</code>') ?>
            </label>
        </div>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?= $interface->submit(t('Save'), '', 'right', 'btn-primary') ?>
        </div>
    </div>
</form>

<script>
$(function () {
    var steps = [{
        element: 'input[name=url_rewriting]',
        content: <?= json_encode('<h3>' . t('Pretty URLs') . '</h3>' . t('Check this checkbox to remove index.php from your URLs.<br/>You will be given code to place in a file named .htaccess in your web root. Concrete will try and place this code in the file for you.')) ?>,
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
        framework: 'bootstrap5',
        template: ccmi18n_tourist.template,
        localization: ccmi18n_tourist.localization,
        storage: false,
        container: '#ccm-tooltip-holder',
        showProgressBar: false,
        sanitizeWhitelist: {
            a: [/^data-/, 'href']
        },
        onPreviouslyEnded: function(tour) {
            tour.restart()
        },
        onStart: function() {
            window.ConcretePanelManager.getByIdentifier('help').hide();
            $('#tourBackdrop').detach() // https://github.com/IGreatlyDislikeJavascript/bootstrap-tourist/issues/42
        },
        onShown: ConcreteHelpGuideManager.updateStepFooter,
        onEnd: function() {
            window.ConcretePanelManager.getByIdentifier('help').show();
        }
    });
    ConcreteHelpGuideManager.register('dashboard-system-urls', tour);
});
</script>
