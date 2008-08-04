<?

$from = array('info@concretecms.com', 'Forgot Password');
$subject = "Forgot Password";
$body = "

Dear {$uName},

Here is your information:

Your username: {$uName}
Your password: {$uPassword}

To login, head here:

" . BASE_URL . DIR_REL . "/index.php/login/

Thanks for browsing the site!

";

?>