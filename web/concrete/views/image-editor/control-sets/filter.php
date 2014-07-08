<?php
use Concrete\Core\ImageEditor\Filter;
$filters = Filter::getList();

foreach ($filters as $filter) {
    ?>
    <div class="filter filter-<?= $filter->getHandle() ?>">
        <?php
        $view = new View;
        $view->setInnerContentFile($filter->getViewPath());
        echo $view->renderViewContents(array('filter' => $filter));
        ?>
    </div>
    <?php
}
?>
<div class="form-group">
    <button class="btn cancelfilter"><?= t('Cancel') ?></button>
    <button class="btn savefilter btn-primary pull-right"><?= t('Save') ?></button>
</div>
