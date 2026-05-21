<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Seo\WellKnownFiles $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var array<array{id: int, handle: string, name: string, canonicalUrl: string, sitemapUrl: string, robotsUrl: string, llmsUrl: string, securityUrl: string, adsUrl: string, humansUrl: string, sitemap: array{exists: bool, lastModified: int|null}, robots: array{exists: bool, lastModified: int|null}, llms: array{exists: bool, lastModified: int|null}, security: array{exists: bool, lastModified: int|null}, ads: array{exists: bool, lastModified: int|null}, humans: array{exists: bool, lastModified: int|null}, robotsContent: string, llmsContent: string, securityContent: string, adsContent: string, humansContent: string}> $siteData
 * @var bool $isMultisite
 * @var string $nginxConfig
 * @var string $apacheConfig
 */

/**
 * @param array{exists: bool, lastModified: int|null} $status
 */
$renderStatus = static function (array $status): string {
    if (!$status['exists']) {
        return '<span class="badge bg-warning text-dark">' . t('Not yet generated') . '</span>';
    }
    $badge = '<span class="badge bg-success">' . t('Generated') . '</span>';
    if ($status['lastModified'] !== null) {
        $badge .= ' <small class="text-muted">' . h(date('Y-m-d H:i', $status['lastModified'])) . '</small>';
    }

    return $badge;
};

$noCanonicalWarning = '<div class="alert alert-warning">' . t(
    'This site has no canonical URL configured. Set one in %s before editing.',
    '<a href="' . h(\Concrete\Core\Support\Facade\Url::to('/dashboard/system/seo/urls')) . '">' . t('SEO & URLs') . '</a>'
) . '</div>';
?>

<div class="ccm-dashboard-header-buttons">
    <a href="<?= h(\Concrete\Core\Support\Facade\Url::to('/dashboard/system/automation/tasks')) ?>" class="btn btn-secondary"><?= t('Scheduled Tasks') ?></a>
</div>

<p class="lead"><?= sprintf(
    t(/* i18n: %1$s is the task name in bold; %2$s, %3$s are filenames in monospace; %4$s is a directory path in monospace */'The %s scheduled task writes %s and %s for each site into %s. All other files below are managed manually.'),
    '<strong>' . t('Generate Sitemap') . '</strong>',
    '<code>sitemap.xml</code>',
    '<code>robots.txt</code>',
    '<code>application/files/site-specific/{host}/</code>'
) ?></p>

<div class="table-responsive">
<table class="table table-striped table-sm">
    <thead>
        <tr>
            <th><?= t('Site') ?></th>
            <th><?= t('sitemap.xml') ?></th>
            <th><?= t('robots.txt') ?></th>
            <th><?= t('llms.txt') ?></th>
            <th><?= t('security.txt') ?></th>
            <th><?= t('ads.txt') ?></th>
            <th><?= t('humans.txt') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($siteData as $row): ?>
            <tr>
                <td>
                    <?= h($row['name']) ?>
                    <?php if ($row['canonicalUrl'] !== ''): ?>
                        <br><small class="text-muted"><?= h($row['canonicalUrl']) ?></small>
                    <?php else: ?>
                        <br><small class="text-warning"><?= t('No canonical URL configured') ?></small>
                    <?php endif ?>
                </td>
                <?php foreach ([
                    ['status' => $row['sitemap'],   'url' => $row['sitemapUrl']],
                    ['status' => $row['robots'],    'url' => $row['robotsUrl']],
                    ['status' => $row['llms'],      'url' => $row['llmsUrl']],
                    ['status' => $row['security'],  'url' => $row['securityUrl']],
                    ['status' => $row['ads'],       'url' => $row['adsUrl']],
                    ['status' => $row['humans'],    'url' => $row['humansUrl']],
                ] as $col): ?>
                    <td>
                        <?= $renderStatus($col['status']) ?>
                        <?php if ($col['url'] !== ''): ?>
                            <br><a href="<?= h($col['url']) ?>" target="_blank" rel="noopener noreferrer" class="small"><?= h($col['url']) ?></a>
                        <?php endif ?>
                    </td>
                <?php endforeach ?>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
</div>

<?php foreach ([
    ['key' => 'robots', 'file' => 'robots.txt', 'contentKey' => 'robotsContent', 'urlKey' => 'robotsUrl', 'partial' => 'robots_form.php',
     'heading' => t('Edit robots.txt'),
     'note' => sprintf(t(/* i18n: %1$s is the task name in bold; %2$s is the filename in monospace */'Changes take effect immediately. Running %s will update the %s Sitemap directive but preserve your other custom rules.'), '<strong>' . t('Generate Sitemap') . '</strong>', '<code>robots.txt</code>')],
    ['key' => 'llms', 'file' => 'llms.txt', 'contentKey' => 'llmsContent', 'urlKey' => 'llmsUrl', 'partial' => 'llms_form.php',
     'heading' => t('Edit llms.txt'),
     'note' => t(/* i18n: %s is a link to llmstxt.org */'Tells AI crawlers about your site. See %s for the format specification.', '<a href="https://llmstxt.org" target="_blank" rel="noopener noreferrer">llmstxt.org</a>')],
    ['key' => 'security', 'file' => 'security.txt', 'contentKey' => 'securityContent', 'urlKey' => 'securityUrl', 'partial' => 'security_form.php',
     'heading' => t('Edit security.txt'),
     'note' => sprintf(t(/* i18n: %1$s is the file path in monospace; %2$s is the field name in monospace */'Discloses how to report security vulnerabilities (RFC 9116). Served at %s. The %s field is required — update it at least annually.'), '<code>/.well-known/security.txt</code>', '<code>Expires</code>')],
    ['key' => 'ads', 'file' => 'ads.txt', 'contentKey' => 'adsContent', 'urlKey' => 'adsUrl', 'partial' => 'ads_form.php',
     'heading' => t('Edit ads.txt'),
     'note' => t('Declares authorized programmatic ad sellers (IAB Authorized Digital Sellers standard). Only relevant for sites running display advertising.')],
    ['key' => 'humans', 'file' => 'humans.txt', 'contentKey' => 'humansContent', 'urlKey' => 'humansUrl', 'partial' => 'humans_form.php',
     'heading' => t('Edit humans.txt'),
     'note' => t(/* i18n: %s is a link to humanstxt.org */'Credits the people who built the site. See %s for the format.', '<a href="https://humanstxt.org" target="_blank" rel="noopener noreferrer">humanstxt.org</a>')],
    // Future: /.well-known/dnt-policy.txt (EFF Do Not Track policy declaration).
    // Serving this file is a binding public claim that the site honours the DNT header for all visitors.
    // Add an editor entry here once Concrete suppresses tracking/analytics when DNT is set.
] as $editor): ?>

    <hr>
    <h4><?= h($editor['heading']) ?></h4>
    <p class="text-muted"><?= $editor['note'] ?></p>

    <?php if ($isMultisite): ?>
        <ul class="nav nav-tabs mb-3" role="tablist">
            <?php foreach ($siteData as $i => $row): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link<?= $i === 0 ? ' active' : '' ?>"
                            id="<?= h($editor['key']) ?>-tab-<?= h($row['handle']) ?>"
                            data-bs-toggle="tab"
                            data-bs-target="#<?= h($editor['key']) ?>-pane-<?= h($row['handle']) ?>"
                            type="button" role="tab">
                        <?= h($row['name']) ?>
                    </button>
                </li>
            <?php endforeach ?>
        </ul>
        <div class="tab-content">
            <?php foreach ($siteData as $i => $row): ?>
                <div class="tab-pane<?= $i === 0 ? ' show active' : '' ?>"
                     id="<?= h($editor['key']) ?>-pane-<?= h($row['handle']) ?>"
                     role="tabpanel">
                    <?php if ($row['canonicalUrl'] === ''): ?>
                        <?= $noCanonicalWarning ?>
                    <?php else: ?>
                        <?php include(__DIR__ . '/well_known_files/' . $editor['partial']) ?>
                    <?php endif ?>
                </div>
            <?php endforeach ?>
        </div>
    <?php else: ?>
        <?php $row = $siteData[0] ?? null ?>
        <?php if ($row !== null): ?>
            <?php if ($row['canonicalUrl'] === ''): ?>
                <?= $noCanonicalWarning ?>
            <?php else: ?>
                <?php include(__DIR__ . '/well_known_files/' . $editor['partial']) ?>
            <?php endif ?>
        <?php endif ?>
    <?php endif ?>

<?php endforeach ?>

<?php if ($isMultisite): ?>
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

<?php endif ?>
