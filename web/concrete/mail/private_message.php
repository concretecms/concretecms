<?php
defined('C5_EXECUTE') or die("Access Denied.");

$subject = t('Private message from %s', $msgAuthor);

$body = t('*** DO NOT REPLY TO THE MESSAGE BELOW. ***');
$body .= "\n\n";
$body .= t("A message has been sent to you by %s through your profile on %s", $msgAuthor, $siteName);
$body .= "\n\n";
$body .= t("Subject: %s", $msgSubject);
$body .= "\n\n";
$body .= t("Message:\n%s", $msgBody);
$body .= "\n\n";
$body .= t("To view this user's profile, visit: %s", $profileURL);
$body .= "\n\n";
$body .= t("To disable any future messages, change your profile preferences at: %s", $profilePreferencesURL);