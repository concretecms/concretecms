<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Block\View\BlockViewTemplate;
$bvt = new BlockViewTemplate($b);
$bvt->setBlockCustomTemplate(false);
?>

<div class="border-top border-primary border-5 pt-5">
<?php
include($bvt->getTemplate());
?>
</div>