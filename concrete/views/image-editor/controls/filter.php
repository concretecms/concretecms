<?php

/** @var Concrete\Core\ImageEditor\ImageEditor $editor */
$filters = $editor->getFilterList();

foreach ($filters as $filter) {
    ?>
    <div class="filter filter-<?= $filter->getHandle() ?>">
        <?php

        try {
            $view = $filter->getView();
            $view->addScopeItems(array('filter' => $filter, 'editor' => $editor, 'fv' => $fv));
            echo $view->render();
        } catch (\Exception $e) {
            echo "<h3>", t('Failed to render filter view.'), "</h3>";
            echo "<pre>", $e->getMessage(), "</pre>";
        }
    ?>
    </div>
    <?php

}
?>
