<?php

defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Block\PageAttributeDisplay\Controller $controller */
/** @var string|null $displayTag */
/** @var string|null $dateFormat */
/** @var string|null $delimiter */
/** @var int $thumbnailWidth */
/** @var int $thumbnailHeight */
/** @var string|null $attributeTitleText */
/** @var string|null $attributeHandle */
$attributeTitleText = $attributeTitleText ?? null;
$attributeHandle = $attributeHandle ?? null;
$delimiter = $delimiter ?? null;
$displayTag = $displayTag ?? null;
$dh = app('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
echo $this->controller->getOpenTag();
echo $this->controller->getTitle();
try {
    if ($this->controller->dateFormat) {
        echo $dh->formatCustom($dateFormat, $this->controller->getContent());
    } else {
        echo $dh->formatDateTime($this->controller->getContent());
    }
} catch (Exception $e) {
    echo $this->controller->getContent();
}
echo $this->controller->getCloseTag();
