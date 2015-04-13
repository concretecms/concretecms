<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
echo $this->controller->getOpenTag();
echo $this->controller->getTitle();
$format = (strlen($this->controller->dateFormat)?$this->controller->dateFormat:"m/d/y");
echo $dh->formatCustom($format, $this->controller->getContent());
echo $this->controller->getCloseTag();
