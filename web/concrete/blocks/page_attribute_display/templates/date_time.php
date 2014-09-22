<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
echo $this->controller->getOpenTag();
echo $this->controller->getTitle();
$format = (strlen($this->controller->dateFormat)?$this->controller->dateFormat:"m/d/y");
echo date($format,strtotime($this->controller->getContent()));
echo $this->controller->getCloseTag();
