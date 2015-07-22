<?php
use Concrete\Core\ImageEditor\Filter;
$filters = Filter::getList();

foreach ($filters as $filter) {
    ?>
    <div class="filter filter-<?= $filter->getHandle() ?>">
        <?php
        $view = new View;
        $view->setInnerContentFile($filter->getViewPath());
        try {
            echo $view->renderViewContents(array('filter' => $filter));
        } catch (\Exception $e) {
            echo "<h3>", t('Failed to render filter view.'), "</h3>";
            echo "<pre>", $e->getMessage(), "</pre>";
        }
        ?>
    </div>
    <?php
}
?>
