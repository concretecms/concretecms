<?php defined('C5_EXECUTE') or die("Access Denied.");
$cnv = Conversation::getByID($_POST['cnvID']);
if (is_object($cnv)) {
	$msgScore = ($_POST['msgScore']);
}
echo $msgScore;

?>