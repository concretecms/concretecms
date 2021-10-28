<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
* @var Concrete\Core\Config\Repository\Repository $config
*/
?>

<h3><?= t('More Resources') ?></h3>

<section>
    <p><?= t('Read the <a href="%s" target="_blank">User Documentation</a> to learn editing and site management with Concrete CMS.', $config->get('concrete.urls.help.user')) ?></p>
    <p><?= t('The <a href="%s" target="_blank">Developer Documentation</a> covers theming, building add-ons and custom Concrete development.', $config->get('concrete.urls.help.developer')) ?></p>
    <p><?= t('Finally, <a href="%s" target="_blank">the forum</a> is full of helpful community members that make Concrete CMS so great.', $config->get('concrete.urls.help.forum')) ?></p>
</section>
