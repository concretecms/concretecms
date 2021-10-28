<?php

use Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRecord;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;

/** @var FileUsageRecord[] $records */

?>
<div class="ccm-ui">
    <table class="table table-striped">
        <thead>
            <tr>
                <td>
                    <?php echo  t('Page ID') ?>
                </td>

                <td>
                    <?php echo  t('Version') ?>
                </td>

                <td>
                    <?php echo  t('Handle') ?>
                </td>

                <td>
                    <?php echo  t('Location') ?>
                </td>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($records as $record): ?>
                <?php $page = Page::getByID($record->getCollectionID()); ?>

                <tr>
                    <td>
                        <?php echo  $page->getCollectionID() ?>
                    </td>

                    <td>
                        <?php echo  $page->getVersionID() ?>
                    </td>

                    <td>
                        <?php echo  $page->getCollectionHandle() ?>
                    </td>

                    <td>
                        <a target="_blank" href="<?php echo Url::to($page) ?>">
                            <?php echo h($page->getCollectionPath() ?: '/') ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
