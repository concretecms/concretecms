<?php
use Concrete\Core\Block\View\BlockViewTemplate;
$bvt = new BlockViewTemplate($b);
$bvt->setBlockCustomTemplate(false);
?>

<div class="row">
    <div class="offset-md-2 col-md-8">
        <?php
        include($bvt->getTemplate());
        ?>
    </div>
</div>