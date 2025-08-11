<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $grade \Concrete\Core\Health\Grade\PassFailGrade
 */

if ($grade->hasPassed()) {
    $textClass = 'text-success';
    $title = tc('CheckResult', 'PASS');
    $icon = 'fa fa-thumbs-up';
} else {
    $textClass = 'text-danger';
    $title = tc('CheckResult', 'FAIL');
    $icon = 'fa fa-exclamation-triangle';
}
?>

<div class="ms-auto me-auto">
    <h1 class="display-1 <?=$textClass?>"><i class="<?=$icon?>"></i></h1>
    <h5 class="display-5 <?=$textClass?>"><?=$title?></h5>
</div>
