<?
defined('C5_EXECUTE') or die("Access Denied.");

$subject = t('Private message limit exceeded for user: %s on %s', $offenderUname, SITE);

$body = t("User: %s has tried to send more than %s private messages within %s minutes", $offenderUname, USER_PRIVATE_MESSAGE_MAX, USER_PRIVATE_MESSAGE_MAX_TIME_SPAN);
$body .= "\n\n";
$body .= t("This behavior is typically an indication of someone spamming your site, you may want to take a closer look at this users activity and possibly disable or delete this user account.");
$body .= "\n\n";
$body .= t("To view this user's profile, visit: %s", $profileURL);