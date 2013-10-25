<?php defined('C5_EXECUTE') or die("Access Denied.");

$subject = t('A new facebook account has been connected to your %s account, verify within.',SITE);

$b = array();
$b[] = t('** DO NOT REPLY TO THE MESSAGE BELOW. ***');
$b[] = t('Someone has tried to attach a facebook account to your %s account,',SITE)."\n".t('If this is you, please follow the link below:');
$b[] = t('Please go to this like to validate: %s',$validateurl);

$body = implode("\n\n",$b);