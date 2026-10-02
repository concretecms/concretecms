<?php
defined('C5_EXECUTE') or die('Access Denied.');

// Parameters
/* @var string $siteName */
/* @var string $msgSubject */
/* @var string $msgBody */
/* @var string $msgAuthor */
/* @var string $msgDateCreated (in system time zone) */
/* @var League\Url\UrlInterface $profilePreferencesURL */
/* @var League\Url\UrlInterface $myPrivateMessagesURL */
/* @var League\Url\UrlInterface $profileURL (if users profiles are enabled) */
/* @var League\Url\UrlInterface $replyToMessageURL (if users profiles are enabled and message sender has private messages enabled) */

$subject = t('Private message from %s', $msgAuthor);

$body .= t('A message has been sent to you by %s through your profile on %s', $msgAuthor, $siteName);
$body .= "\n\n";
$body .= t('Subject: %s', $msgSubject);
$body .= "\n\n";
$body .= t("Message:\n%s", $msgBody);
$body .= "\n\n";
$body .= "\n\n";
$body .= t('To view your private messages, visit: %s', $myPrivateMessagesURL);
if (isset($profileURL)) {
    $body .= "\n\n";
    $body .= t("To view this user's profile, visit: %s", $profileURL);
}
$body .= "\n\n";
$body .= t('To disable any future messages, change your profile preferences at: %s', $profilePreferencesURL);
