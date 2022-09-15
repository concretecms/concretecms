<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $grade \Concrete\Core\Health\Grade\ScoreGrade
 */

if ($grade->getScore() >= 80) {
    $textClass = 'text-success';
} else if ($grade->getScore() >= 60) {
    $textClass = 'text-warning';
} else {
    $textClass = 'text-danger';
}
$title = $grade->getScore();
?>

<div class="card">
    <div class="card-header"><h5><?=t('Result Score')?></h5></div>
    <div class="card-body">
        <h1 class="display-1 <?=$textClass?>"><?=$title?></h1>
    </div>
</div>