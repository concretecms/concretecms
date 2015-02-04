<? 
defined('C5_EXECUTE') or die("Access Denied.");
$subject = t('New Message on Conversation: %s', $pageName);
$body = t("
There is a new reply on a guestbook in your concrete5 website.

%s

To view this guestbook, visit: 
%s 

", $comment, $guestbookURL);
?>
