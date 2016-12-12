<div class="ccm-ui">

    <table class="table table-striped">
        <thead>
        <tr>
            <td><?= t('Page ID') ?></td>
            <td><?= t('Version') ?></td>
            <td><?= t('Handle') ?></td>
            <td><?= t('Location') ?></td>
        </tr>
        </thead>
        <?php
        /** @var \Concrete\Core\Page\Collection\Collection $page */
        foreach ($records as $page) {
            ?>
            <tr>
                <td><?= $page->getCollectionID() ?></td>
                <td><?= $page->getVersionID() ?></td>
                <td><?= $page->getCollectionHandle() ?></td>
                <td><a target="_blank" href="<?= \URL::to($page) ?>"><?= h($page->getCollectionPath() ?: '/') ?></a></td>
            </tr>
            <?php
        }
        ?>
    </table>

</div>
