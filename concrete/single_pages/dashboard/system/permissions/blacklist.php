<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\View\PageView $view */

/* @var Concrete\Core\Entity\Permission\IpAccessControlCategory[] $categories */

if (empty($categories)) {
    ?>
    <div class="alert alert-warning">
        <?= t('No IP Access Control Category defined.') ?>
    </div>
    <?php
}
else {
    ?>
    <table class="table table-hover">
        <thead>
            <tr>
                <th><?= t('Handle') ?></th>
                <th><?= t('Name') ?></th>
                <th><?= t('Enabled') ?></th>
                <th><?= t('Package') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($categories as $category) {
            $href = $view->action('configure', $category->getIpAccessControlCategoryID());
            ?>
            <tr class="ccm_ip-access-control-category" onclick="<?= h('window.location.href = ' . json_encode($href) . ';') ?>">
                <td><a href="<?= h($href) ?>"><code><?= h($category->getHandle()) ?></code></a></td>
                <td><a href="<?= h($href) ?>"><?= h($category->getDisplayName()) ?></a></td>
                <td><?= $category->isEnabled() ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-ban text-danger"></i>' ?></td>
                <td>
                    <?php
                    if ($category->getPackage() !== null) {
                        echo h($category->getPackage()->getPackageName());
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <?php
}
