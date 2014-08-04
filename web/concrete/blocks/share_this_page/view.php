<?php
defined('C5_EXECUTE') or die("Access Denied.");
foreach($selected as $service) { ?>

    <a href="<?=$service->getServiceLink()?>"><?=$service->getServiceIconHTML()?></a>

<?
}

