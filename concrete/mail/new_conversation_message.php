<?php

defined('C5_EXECUTE') or die("Access Denied.");
$subject = t('New Message on Conversation: %s', $title);
$message = $body;
$body = t("
%s has posted a new message to the conversation \"%s\":

%s

You can view the whole conversation at

%s

", $poster, $title, $message, $link);

$bodyHTML = t(
    '%s has posted a new message to the conversation "%s":<br /><br />%s<br /><br />You can view the whole conversation at<br /><br /><a href="%s">%s</a>',
    $posterHTML,
    h($title),
    nl2br(h($message)),
    $link,
    h($link)
);
