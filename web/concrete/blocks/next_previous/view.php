<?php
defined('C5_EXECUTE') or die("Access Denied.");
$ih = Loader::helper('image');
?>

<div id="ccm-next-previous-<?php echo intval($bID)?>" class="ccm-next-previous-wrapper">

    <?php  if( strlen($previousLinkText) > 0){ ?>
      <div class="ccm-next-previous-previouslink">
        <?php  if( is_object($previousCollection) ){ ?>
          <a href="<?php echo Loader::helper('navigation')->getLinkToCollection($previousCollection)?>"><?php echo $previousLinkText ?></a>
        <?php  } else { ?>
          &nbsp;
        <?php  } ?>
      </div>
    <?php } ?>

    <?php  if( strlen($parentLinkText) > 0){ ?>
      <div class="ccm-next-previous-parentlink">
        <?php  if( is_object($parentCollection) && $parentLinkText){ ?>
          <a href="<?php echo Loader::helper('navigation')->getLinkToCollection($parentCollection)?>"><?php echo $parentLinkText ?></a>
        <?php  } else { ?>
          &nbsp;
        <?php  } ?>
      </div>
    <?php } ?>

    <?php  if( strlen($nextLinkText) > 0){ ?>
      <div class="ccm-next-previous-nextlink">
        <?php  if( is_object($nextCollection) ){ ?>
          <a href="<?php echo Loader::helper('navigation')->getLinkToCollection($nextCollection)?>"><?php echo $nextLinkText ?></a>
        <?php  } else { ?>
          &nbsp;
        <?php  } ?>
      </div>
    <?php } ?>

    <div class="spacer"></div>
</div>
