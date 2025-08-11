<?php defined('C5_EXECUTE') or die('Access Denied.');

/** @var string $titleFormat */
/** @var string $title */
/** @var Concrete\Core\Block\View\BlockView $view */
/** @var array<int,array<string,string>> $dates */
/** @var string|null $selectedYear */
/** @var string|null $selectedMonth */
?>

<div class="ccm-block-date-navigation">

    <div class="ccm-block-date-navigation-header">
        <<?php echo $titleFormat; ?>><?=h($title); ?></<?php echo $titleFormat; ?>>
    </div>

    <?php if (count($dates)) {
    ?>
        <ul class="ccm-block-date-navigation-dates">
            <li><a href="<?=$view->controller->getDateLink()?>"><?=t('All')?></a></li>

            <?php
            foreach ($dates as $date) {
    ?>
                <li><a href="<?=$view->controller->getDateLink($date)?>"
                        <?php if ($view->controller->isSelectedDate($date)) {
    ?>
                            class="ccm-block-date-navigation-date-selected"
                        <?php
}
    ?>><?=$view->controller->getDateLabel($date)?></a></li>
            <?php
}
    ?>
        </ul>
    <?php
} else {
    ?>
        <?=t('None.')?>
    <?php
} ?>


</div>
