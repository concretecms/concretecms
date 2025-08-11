<?php
    defined('C5_EXECUTE') or die('Access Denied.');
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

    if (isset($formatter) && is_object($formatter)) {
        $rootContainer = $formatter->getLayoutContainerHtmlObject();
        if (!empty($rootContainer)) {
            $container = $rootContainer;
            while ($container->hasChildren()) {
                $container = $container->getChildren()[0];
            }
        }
    }

    foreach ($columns as $column) {
        $html = $column->getColumnHtmlObject();
        if (!empty($container)) {
            $container->appendChild($html);
        } else {
            echo $html;
        }
    }

    if (isset($rootContainer)) {
        echo $rootContainer;
    }
