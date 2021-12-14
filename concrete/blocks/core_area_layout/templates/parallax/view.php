<?php defined('C5_EXECUTE') or die('Access Denied.');
$minColumns = 1;
$columnsNum = $columnsNum ?? 1;
$maxColumns = $maxColumns ?? 12;
$enableThemeGrid = $enableThemeGrid ?? false;
$columns = $columns ?? [];
/** @var \Concrete\Core\Area\Layout\Formatter\FormatterInterface $formatter */
/** @var \Concrete\Block\CoreAreaLayout\Controller $controller */
/** @var \Concrete\Core\Block\Block $b */
/** @var \Concrete\Core\Block\View\BlockView $view */
/** @var \Concrete\Core\Area\Area $a */
/** @var \Concrete\Core\Page\Theme\GridFramework\GridFramework $themeGridFramework */
$a = $b->getBlockAreaObject();
$rootContainer = $formatter->getLayoutContainerHtmlObject();
$container = $rootContainer;
while ($container->hasChildren()) {
    $container = $container->getChildren()[0];
}

$background = '';
$style = $b->getCustomStyle();
if (is_object($style)) {
    $set = $style->getStyleSet();
    $image = $set->getBackgroundImageFileObject();
    if (is_object($image)) {
        $background = $image->getRelativePath();
        if (!$background) {
            $background = $image->getURL();
        }
    }
}

?>

<div data-stripe-wrapper="parallax" data-background-image="<?= $background ?>">
    <?php
    foreach ($columns as $column) {
        $html = $column->getColumnHtmlObject();
        $container->appendChild($html);
    }

    echo $rootContainer;
    ?>
</div>

