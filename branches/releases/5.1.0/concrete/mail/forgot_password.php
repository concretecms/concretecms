<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$subject = t("Forgot Password");
$body = t("

Dear %s,

Here is your information:

Your username: %s
Your password: %s

To login, head here:

%s

Thanks for browsing the site!

", $uName, $uName, $uPassword, BASE_URL . DIR_REL . "/index.php/login/");

?>