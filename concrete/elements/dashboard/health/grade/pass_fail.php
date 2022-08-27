<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $grade \Concrete\Core\Health\Grade\PassFailGrade
 */

if ($grade->hasPassed()) {
    $textClass = 'text-success';
    $title = t('PASS');
} else {
    $textClass = 'text-danger';
    $title = t('FAIL');
}
?>

<div class="card col-md-3 ms-auto me-auto">
    <div class="card-header"><b><?=t('Results')?></b></div>
    <div class="card-body">
        <h1 class="display-1 <?=$textClass?>"><?=$title?></h1>
    </div>
</div>