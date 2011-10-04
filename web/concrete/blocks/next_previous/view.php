<?php
defined('C5_EXECUTE') or die("Access Denied.");
$ih = Loader::helper('image');
?>

<div id="ccm-next-previous-<?php echo intval($bID)?>" class="ccm-next-previous-wrapper">

    <?php  if( strlen($previousLinkText) > 0){ ?>
      <div class="ccm-next-previous-previouslink">
        <?php  if( is_object($previousCollection) ){ ?>
          <a href="<?php echo View::url($previousCollection->getCollectionPath())?>"><?php echo $previousLinkText ?></a>
        <?php  } else { ?>
          &nbsp;
        <?php  } ?>
      </div>
    <?php } ?>

    <?php  if( strlen($parentLinkText) > 0){ ?>
      <div class="ccm-next-previous-parentlink">
        <?php  if( is_object($parentCollection) && $parentLinkText){ ?>
          <a href="<?php echo View::url($parentCollection->getCollectionPath())?>"><?php echo $parentLinkText ?></a>
        <?php  } else { ?>
          &nbsp;
        <?php  } ?>
      </div>
    <?php } ?>

    <?php  if( strlen($nextLinkText) > 0){ ?>
      <div class="ccm-next-previous-nextlink">
        <?php  if( is_object($nextCollection) ){ ?>
          <a href="<?php echo View::url($nextCollection->getCollectionPath())?>"><?php echo $nextLinkText ?></a>
        <?php  } else { ?>
          &nbsp;
        <?php  } ?>
      </div>
    <?php } ?>

    <div class="spacer"></div>
</div>
