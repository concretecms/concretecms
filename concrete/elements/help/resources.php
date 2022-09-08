<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Config\Repository\Repository $config
 */
?>

<h3><?= t('More Help') ?></h3>

<section>

    <article class="mb-4">
        <div><strong><a target="_blank" href="<?=$config->get('concrete.urls.help.user')?>"><?=t('User Documentation')?></a></strong></div>
        <div class="text-muted"><?=t('Everything about using Concrete CMS to manage content on the web.')?></div>
    </article>

    <article class="mb-4">
        <div><strong><a target="_blank" href="<?=$config->get('concrete.urls.help.developer')?>"><?=t('Developer Documentation')?></a></strong></div>
        <div class="text-muted"><?=t('Theming, building add-ons and custom development information that programmers need.')?></div>
    </article>

    <article class="mb-4">
        <div><strong><a target="_blank" href="<?=$config->get('concrete.urls.help.forum')?>"><?=t('Community Forums')?></a></strong></div>
        <div class="text-muted"><?=t('Friendly people who will do their best to answer clear questions.')?></div>
    </article>

    <article>
        <div><strong><a target="_blank" href="<?=$config->get('concrete.urls.help.support')?>"><?=t('Get Support')?></a></strong></div>
        <div class="text-muted"><?=t('You can open support tickets if you host at ConcreteCMS.com, or have a custom SLA.')?></div>
    </article>

</section>
