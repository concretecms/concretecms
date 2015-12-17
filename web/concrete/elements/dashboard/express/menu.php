<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>


<div class="btn-group">

<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?=$currentType->getName()?>
    <span class="caret"></span>
</button>

<ul class="dropdown-menu">
    <li class="dropdown-header"><?=t('Types')?></li>
    <?php foreach ($types as $type) { ?>
        <li><a href="<?=URL::to('/dashboard/express/entries', $type->getID())?>">
                <?=$type->getName()?></a></li>
    <?php
    }
    ?>
    <li class="divider"></li>
    <li><a href="<?=URL::to('/dashboard/express/entities', 'add')?>"><?=t('Add Data Object')?></a></li>
</ul>

</div>