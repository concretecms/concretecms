<?php defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Area\GlobalArea;

$view->inc('elements/header_top.php');

?>

<?php
$a = new GlobalArea('Navigation');
$a->display();
?>
