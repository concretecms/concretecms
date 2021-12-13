<?php  defined('C5_EXECUTE') or die('Access Denied.');

/** @var bool $useFilterTitle  */
/** @var bool $useFilterTopic */
/** @var bool $useFilterDate */
/** @var bool $useFilterTag */
/** @var bool $formatting */
/** @var Concrete\Block\PageTitle\Controller $controller */
/** @var string $title */
/** @var \Concrete\Core\Tree\Node\Type\Topic | null $currentTopic */

if ($useFilterTitle) {
    $currentTopic = $currentTopic ?? null;
    if (is_object($currentTopic) && $useFilterTopic) {
        $title = $controller->formatPageTitle($currentTopic->getTreeNodeDisplayName(), $topicTextFormat ?? false);
    }
    if (isset($tag) && $useFilterTag) {
        $title = $controller->formatPageTitle($tag, $tagTextFormat ?? false);
    }
    if (isset($year) && isset($month) && $useFilterDate) {
        $srv = app('helper/date');
        $date = strtotime("$year-$month-01");
        $title = $srv->date($filterDateFormat ?? 'F Y', $date);

        $title = $controller->formatPageTitle($title, $dateTextFormat ?? false);
    }
}

if ($title) {
    echo "<$formatting  class=\"ccm-block-page-title page-title\">" . h($title) . "</$formatting>";
}
