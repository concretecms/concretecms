<?php  
defined('C5_EXECUTE') or die("Access Denied.");
//$from = array($fromEmail);
$body = t("
There is a new reply on a guestbook in your Concrete5 website.

%s

To view this guestbook, visit: 
%s 

", $comment, $guestbookURL);
?>
