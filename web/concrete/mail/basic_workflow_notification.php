<?

defined('C5_EXECUTE') or die("Access Denied.");

$subject = SITE . " " . t("Workflow Notification");
$body = t("Dear %s,

%s

You can review, approve or deny all pending actions from here:

%s
", $uName, $description, $waitingURL);