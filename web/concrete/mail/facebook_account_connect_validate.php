<?php defined('C5_EXECUTE') or die("Access Denied.");

$subject = t(/*i18n: %s is the site name*/'A new Facebook account has been connected to your %s account, verify within.', SITE);

$b = array();
$b[] = t('*** DO NOT REPLY TO THE MESSAGE BELOW. ***');
$b[] = t('Someone has tried to attach a Facebook account to your %s account,', SITE);
$b[] = t('If this is you, please go to this link to validate the association: %s', $validateurl);

$body = implode("\n\n", $b);
