<?php
    defined('C5_EXECUTE') or die('Access Denied.');
$minColumns = 1;
$columnsNum = $columnsNum ?? 1;
$maxColumns = $maxColumns ?? 12;
$enableThemeGrid = $enableThemeGrid ?? false;
$columns = $columns ?? [];
    /** @var \Concrete\Block\CoreAreaLayout\Controller $controller */
    /** @var \Concrete\Core\Block\Block $b */
    /** @var \Concrete\Core\Block\View\BlockView $view */
    /** @var \Concrete\Core\Area\Area $a */
    /** @var \Concrete\Core\Page\Theme\GridFramework\GridFramework $themeGridFramework */
    /** @var \Concrete\Core\Area\Layout\Formatter\FormatterInterface $formatter */
    /** @var \Concrete\Core\Block\View\BlockView $this */
    $this->inc('form.php', ['b' => $b, 'a' => $a]);

?>

<input type="hidden" name="arLayoutID" value="<?=$controller->arLayout->getAreaLayoutID()?>" />
<input type="hidden" name="arLayoutEdit" value="1" />

	<div id="ccm-layouts-edit-mode" class="ccm-layouts-edit-mode-edit">

	<?php
    $container = $formatter->getLayoutContainerHtmlObject();
    foreach ($columns as $column) {
        $html = $column->getColumnHtmlObjectEditMode();
        $container->appendChild($html);
    }
    echo $container;

    ?>

	</div>
