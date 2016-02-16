<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<?php
if (isset($attributeHeader)) {
    $attributeHeader->render();
} ?>

<?php
$attributeView->render();
?>