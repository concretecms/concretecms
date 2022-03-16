<?php
    defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Core\Block\View\BlockView $this */
$minColumns = 1;
$columnsNum = $columnsNum ?? 1;
$maxColumns = $maxColumns ?? 12;
$enableThemeGrid = $enableThemeGrid ?? false;
$columns = $columns ?? [];
$iscustom = $iscustom ?? false;
    /** @var \Concrete\Block\CoreAreaLayout\Controller $controller */
    /** @var \Concrete\Core\Block\Block $b */
    /** @var \Concrete\Core\Block\View\BlockView $view */
    /** @var \Concrete\Core\Area\Area $a */
    /** @var \Concrete\Core\Page\Theme\GridFramework\GridFramework $themeGridFramework */
    $this->inc('form.php', ['b' => $b, 'a' => $a]);

?>


<input type="hidden" name="arLayoutID" value="<?=$controller->arLayout->getAreaLayoutID()?>" />
<input type="hidden" name="arLayoutEdit" value="1" />

<div id="ccm-layouts-edit-mode" class="ccm-layouts-edit-mode-edit">

<?php foreach ($columns as $col) {
    ?>
	<?php $i = $col->getAreaLayoutColumnIndex();
    ?>
	<div class="<?=$col->getAreaLayoutColumnClass()?>" id="ccm-edit-layout-column-<?=$i?>" <?php if ($iscustom) {
    ?>data-width="<?=$col->getAreaLayoutColumnWidth()?>" <?php
}
    ?>>
		<div class="ccm-layout-column-inner ccm-layout-column-highlight">
			<input type="hidden" name="width[<?=$i?>]" value="" id="ccm-edit-layout-column-width-<?=$i?>" />
			<?php
            $col->display(true);
    ?>
		</div>
	</div>
<?php
} ?>

</div>
