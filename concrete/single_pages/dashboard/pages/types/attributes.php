<?php defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;

/**
 * @var Concrete\Core\Filesystem\Element $attributesView
 * @var Concrete\Core\Page\Type\Type $pagetype
 * @var Concrete\Core\View\View $view
 */

?>
<p class="lead"><?= $pagetype->getPageTypeDisplayName() ?></p>

<div class="alert alert-info"><?= t('Attributes set here will automatically be applied to new pages of that type.') ?></div>

<?php
    $attributesView->render();
?>

<div class="ccm-dashboard-header-buttons">
    <a href="<?= Url::to('/dashboard/pages/types') ?>" class="btn btn-secondary"><?= t('Back to List') ?></a>
</div>
