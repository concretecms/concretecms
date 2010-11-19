<?php 

defined('C5_EXECUTE') or die("Access Denied.");

$subject = SITE . " " . t("Registration - Approval Required");
$body = t("

A new user has registered on your website. This account must be approved before it is active and may login.

User Name: %s
Email Address: %s
", $uName, $uEmail);

foreach($attribs as $item) {
	$body .= $item . "\n";
}

$body .= t("

You may approve or remove this user account here:

%s", BASE_URL . View::url('/dashboard/users/search?uID=' . $uID));