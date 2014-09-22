<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
echo $this->controller->getOpenTag();
echo $this->controller->getTitle();
echo ($this->controller->getContent()?t('Yes'):t('No'));
echo $this->controller->getCloseTag();
