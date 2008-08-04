<?

$from = array('info@concretecms.com', 'Validate Email Address');
$subject = SITE . " Registration - Validate Email Address";
$body = "

You must click the following URL in order to activate your account for " . SITE . ":

" . BASE_URL . DIR_REL . '/index.php/tools/required/validate_user_email.php?uEmail=' . $uEmail . '&uHash=' . $uHash . "

Thanks for your interest in " . SITE . "

";

?>