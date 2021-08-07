<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<h1><?=t('Colors')?></h1>

<div class="row">
<?php foreach ($colors as $color) {

    $key = $color[0];
    $label = $color[1];
    $textColor = 'text-white';
    if (isset($color[2])) {
        $textColor = $color[2];
    }
    ?>

    <div class="col-md-4">
        <div class="p-3 mb-3 bg-<?=$key?> <?=$textColor?>"><?=$label?></div>
    </div>

<?php } ?>
</div>