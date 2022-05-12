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
    /** @var \Concrete\Core\Block\View\BlockView $this */
    $this->inc('form.php', ['b' => $b, 'a' => $a]);

?>

<input type="hidden" name="arLayoutID" value="<?=$controller->arLayout->getAreaLayoutID()?>" />
<input type="hidden" name="arLayoutEdit" value="1" />

<div id="ccm-layouts-edit-mode" class="ccm-layouts-edit-mode-edit">

<div id="ccm-theme-grid-edit-mode-row-wrapper">

<?=$themeGridFramework->getPageThemeGridFrameworkRowStartHTML()?>

    <?php foreach ($columns as $col) {
    ?>
	<?php $i = $col->getAreaLayoutColumnIndex();
    ?>
	<?php if ($col->getAreaLayoutColumnOffset() > 0) {
    ?>
		<div class="<?=$col->getAreaLayoutColumnOffsetEditClass()?> ccm-theme-grid-offset-column">&nbsp;</div>
	<?php
}
    ?>

	<div class="<?=$col->getAreaLayoutColumnClass()?> ccm-theme-grid-column ccm-theme-grid-column-edit-mode" id="ccm-edit-layout-column-<?=$i?>" data-offset="<?=$col->getAreaLayoutColumnOffset()?>" data-span="<?=$col->getAreaLayoutColumnSpan()?>">
		<div class="ccm-layout-column-inner ccm-layout-column-highlight">
			<input type="hidden" name="span[<?=$i?>]" value="<?=$col->getAreaLayoutColumnSpan()?>" id="ccm-edit-layout-column-span-<?=$i?>" />
			<input type="hidden" name="offset[<?=$i?>]" value="<?=$col->getAreaLayoutColumnOffset()?>" id="ccm-edit-layout-column-offset-<?=$i?>" />
			<?php
            $col->display(true);
    ?>
		</div>
	</div>
<?php
} ?>

    <?=$themeGridFramework->getPageThemeGridFrameworkRowEndHTML()?>

</div>

</div>