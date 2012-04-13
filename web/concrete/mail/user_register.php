<?

defined('C5_EXECUTE') or die("Access Denied.");

$subject = SITE . " " . t("Registration - A New User Has Registered");
$body = t("

A new user has registered on your website.

User Name: %s
Email Address: %s
", $uName, $uEmail);

foreach($attribs as $item) {
	$body .= $item . "\n";
}

$body .= t("

This account may be managed directly at

%s", BASE_URL . View::url('/dashboard/users/search?uID=' . $uID));