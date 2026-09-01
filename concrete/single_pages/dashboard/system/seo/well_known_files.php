<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Seo\WellKnownFiles $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var bool $canEdit
 * @var bool $isMultisite
 * @var string $siteName
 * @var string $canonicalUrl
 * @var array<string, array{exists: bool, lastModified: int|null, url: string}> $files
 * @var array<string, string> $content
 * @var string $nginxConfig
 * @var string $apacheConfig
 */
?>

<div class="ccm-dashboard-header-buttons">
    <a href="<?= h(\Concrete\Core\Support\Facade\Url::to('/dashboard/system/automation/tasks')) ?>" class="btn btn-secondary"><?= t('Scheduled Tasks') ?></a>
</div>

<?php if (!$canEdit) { ?>

    <div class="alert alert-warning"><?= t('You do not have permission to manage well-known files.') ?></div>

<?php } else { ?>

    <p class="lead"><?= sprintf(
        t(/* i18n: %1$s is the site name in bold; %2$s is the task name in bold; %3$s, %4$s are filenames in monospace */'Well-known files for %1$s. The %2$s scheduled task writes %3$s and %4$s; all other files below are managed manually.'),
        '<strong>' . h($siteName) . '</strong>',
        '<strong>' . t('Generate Sitemap') . '</strong>',
        '<code>sitemap.xml</code>',
        '<code>robots.txt</code>'
    ) ?></p>

    <?php if ($canonicalUrl === '') { ?>
        <div class="alert alert-warning"><?= t(
            'This site has no canonical URL configured. Set one in %s before editing its well-known files.',
            '<a href="' . h(\Concrete\Core\Support\Facade\Url::to('/dashboard/system/seo/urls')) . '">' . t('SEO & URLs') . '</a>'
        ) ?></div>
    <?php } ?>

    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th><?= t('File') ?></th>
                <th><?= t('Status') ?></th>
                <th><?= t('URL') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($files as $filename => $file) { ?>
                <tr>
                    <td><code><?= h($filename) ?></code></td>
                    <td>
                        <?php if ($file['exists']) { ?>
                            <span class="badge bg-success"><?= t('Generated') ?></span>
                            <?php if ($file['lastModified'] !== null) { ?>
                                <small class="text-muted"><?= h(date('Y-m-d H:i', $file['lastModified'])) ?></small>
                            <?php } ?>
                        <?php } else { ?>
                            <span class="badge bg-warning text-dark"><?= t('Not yet generated') ?></span>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($file['url'] !== '') { ?>
                            <a href="<?= h($file['url']) ?>" target="_blank" rel="noopener noreferrer" class="small"><?= h($file['url']) ?></a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php foreach ([
        ['file' => 'robots.txt', 'element' => 'robots_form',
         'heading' => t('Edit robots.txt'),
         'note' => sprintf(t(/* i18n: %1$s is the task name in bold; %2$s is the filename in monospace */'Changes take effect immediately. Running %s will update the %s Sitemap directive but preserve your other custom rules.'), '<strong>' . t('Generate Sitemap') . '</strong>', '<code>robots.txt</code>')],
        ['file' => 'llms.txt', 'element' => 'llms_form',
         'heading' => t('Edit llms.txt'),
         'note' => t(/* i18n: %s is a link to llmstxt.org */'Tells AI crawlers about your site. See %s for the format specification.', '<a href="https://llmstxt.org" target="_blank" rel="noopener noreferrer">llmstxt.org</a>')],
        ['file' => 'security.txt', 'element' => 'security_form',
         'heading' => t('Edit security.txt'),
         'note' => sprintf(t(/* i18n: %1$s is the file path in monospace; %2$s is the field name in monospace */'Discloses how to report security vulnerabilities (RFC 9116). Served at %s. The %s field is required — update it at least annually.'), '<code>/.well-known/security.txt</code>', '<code>Expires</code>')],
        ['file' => 'ads.txt', 'element' => 'ads_form',
         'heading' => t('Edit ads.txt'),
         'note' => t('Declares authorized programmatic ad sellers (IAB Authorized Digital Sellers standard). Only relevant for sites running display advertising.')],
        ['file' => 'humans.txt', 'element' => 'humans_form',
         'heading' => t('Edit humans.txt'),
         'note' => t(/* i18n: %s is a link to humanstxt.org */'Credits the people who built the site. See %s for the format.', '<a href="https://humanstxt.org" target="_blank" rel="noopener noreferrer">humanstxt.org</a>')],
        // Future: /.well-known/dnt-policy.txt (EFF Do Not Track policy declaration).
        // Serving this file is a binding public claim that the site honours the DNT header for all visitors.
        // Add an editor entry here once Concrete suppresses tracking/analytics when DNT is set.
    ] as $editor) { ?>

        <hr>
        <h4><?= h($editor['heading']) ?></h4>
        <p class="text-muted"><?= $editor['note'] ?></p>

        <?php if ($canonicalUrl !== '') { ?>
            <?php View::element('dashboard/system/seo/well_known_files/' . $editor['element'], [
                'content' => $content[$editor['file']],
                'siteName' => $siteName,
                'canonicalUrl' => $canonicalUrl,
                'token' => $token,
                'controller' => $controller,
            ]) ?>
        <?php } ?>

    <?php } ?>

<?php } ?>

<?php if ($isMultisite) { ?>
    <hr>
    <h4><?= t('Multisite Server Configuration') ?></h4>
    <p><?= sprintf(
        t(/* i18n: %1$s–%4$s are file paths shown in monospace */'With multiple sites, your web server must route %1$s, %2$s, %3$s, %4$s, and other well-known files to the correct per-site file.'),
        '<code>/sitemap.xml</code>',
        '<code>/robots.txt</code>',
        '<code>/llms.txt</code>',
        '<code>/.well-known/security.txt</code>'
    ) ?></p>
    <p class="text-muted small"><?= t('These rules are required. Without them, files are written to the server but will not be publicly accessible. If you cannot modify your web server configuration, each site should be managed as a separate ConcreteCMS installation.') ?></p>

    <h5><?= t('nginx') ?></h5>
    <p class="text-muted small"><?= sprintf(t(/* i18n: %s is the nginx directive "server {}" shown in monospace */'Add inside your %s block, before the main PHP location block.'), '<code>server {}</code>') ?></p>
    <?= $form->textarea('', h($nginxConfig), [
        'rows' => substr_count($nginxConfig, "\n") + 1,
        'onclick' => 'this.select()',
        'readonly' => 'readonly',
        'class' => 'font-monospace w-100',
    ]) ?>

    <h5 class="mt-3"><?= t('Apache') ?></h5>
    <p class="text-muted small"><?= sprintf(t(/* i18n: %s is the filename ".htaccess" shown in monospace */'Add to the root %s before the existing Concrete CMS rules.'), '<code>.htaccess</code>') ?></p>
    <?= $form->textarea('', h($apacheConfig), [
        'rows' => substr_count($apacheConfig, "\n") + 1,
        'onclick' => 'this.select()',
        'readonly' => 'readonly',
        'class' => 'font-monospace w-100',
    ]) ?>

<?php } ?>
