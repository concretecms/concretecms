<?

$from = array('info@concrete5.org', 'Validate Email Address');
$subject = SITE . " Registration - Validate Email Address";
$body = "

";

if ($invitationType) {
	switch($invitationType) {
		case "group";
			$body .= "You have been invited by a SchoolPulse member to join a group on SchoolPulse!";			
			break;
	}
}

$body .="

You must click the following URL in order to activate your account for " . SITE . ":

" . BASE_URL . DIR_REL . View::url('/login', 'v', $uHash) . " 

Thanks for your interest in " . SITE . "

";

?>