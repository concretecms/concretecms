<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
* @var Concrete\Core\Config\Repository\Repository $config
*/
?>

<h3><?= t('More Resources') ?></h3>

<section>
    <p><?= t('Read the <a href="%s" target="_blank">User Documentation</a> to learn editing and site management with concrete5.', $config->get('concrete.urls.help.user')) ?></p>
    <p><?= t('The <a href="%s" target="_blank">Developer Documentation</a> covers theming, building add-ons and custom concrete5 development.', $config->get('concrete.urls.help.developer')) ?></p>
    <p><?= t('In the <a href="%s" target="_blank">concrete5 Slack channels</a> you can get in touch with a lot of concrete5 lovers and developers.', $config->get('concrete.urls.help.slack')) ?></p>
    <p><?= t('Finally, <a href="%s" target="_blank">the forum</a> is full of helpful community members that make concrete5 so great.', $config->get('concrete.urls.help.forum')) ?></p>
</section>
