<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $grade \Concrete\Core\Health\Grade\PassFailGrade
 */

if ($grade->hasPassed()) {
    $textClass = 'text-success';
    $title = t('PASS');
    $icon = 'fa fa-thumbs-up';
} else {
    $textClass = 'text-danger';
    $title = t('FAIL');
    $icon = 'fa fa-exclamation-triangle';
}
?>

<div class="ms-auto me-auto">
    <h1 class="display-1 <?=$textClass?>"><i class="bg-light rounded-circle p-5 <?=$icon?>"></i> <?=$title?></h1>
</div>