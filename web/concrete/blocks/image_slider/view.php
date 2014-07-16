<? defined('C5_EXECUTE') or die("Access Denied.");?>
<script>
$(document).ready(function(){
    $(function () {
        $("#ccm-image-slider-<?php echo $bID ?>").responsiveSlides({
            <?php if($navigationType == 0) { ?>
            nav:true
            <?php } else { ?>
            pager: true
            <?php } ?>
        });
    });
});
</script>

<div class="ccm-image-slider-container" >
    <?php if(count($rows) > 0) { ?>
    <ul class="rslides" id="ccm-image-slider-<?php echo $bID ?>">
        <?php foreach($rows as $row) { ?>
            <li>
            <?php if($row['linkURL']) { ?>
                <a href="<?php echo $row['linkURL'] ?>" class="mega-link-overlay"></a>
            <?php } ?>
            <?php if(is_object(File::getByID($row['fID']))) { ?>
            <img src="<?php echo File::getByID($row['fID'])->getURL(); ?>" alt="<?php echo $row['title'] ?>">
            <?php } ?>
            <div class="ccm-image-slider-text">
                <h2 class="ccm-image-slider-title"><?php echo $row['title'] ?></h2>
                <?php echo $row['description'] ?>
            </div>
            </li>
        <?php } ?>
    </ul>
    <?php } else { ?>
    <div class="ccm-image-slider-placeholder">
        <p><?php echo t('No Slides Entered.'); ?></p>
    </div>
    <?php } ?>
</div>