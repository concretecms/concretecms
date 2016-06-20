<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($controller->getTask() == 'view_detail') { ?>

    <div class="ccm-marketplace-detail-add-on-details-wrapper">

    <div class="ccm-marketplace-detail-add-on-gallery">
        <?
        $detailShots = $item->getScreenshots();
        ?>
        <ul data-gallery="marketplace-addon">
            <?php foreach($detailShots as $i => $image) { ?>
                <li><a href="<?=$image->src?>"><?=t('Launch')?></a></li>
            <? } ?>
        </ul>
    </div>

    <div class="ccm-marketplace-detail-add-on-details">
        <div class="ccm-marketplace-list-item-add-on-thumbnail"><?
            $thumb = $item->getRemoteIconURL();
            printf('<img src="%s">', $thumb);
            ?>
        </div>
        <h2><?=$item->getName()?></h2>
        <div><i class="<?=$item->getSkillLevelClassName()?>"></i> <?=$item->getSkillLevelDisplayName()?></div>
    </div>

    <div class="ccm-marketplace-detail-add-on-nav">
        <div class="ccm-marketplace-detail-add-on-buy">
            <div class="btn-group">
                <button onclick="ConcreteMarketplace.purchaseOrDownload({mpID: <?=$item->getMarketplaceItemID()?>})" class="btn btn-price" style="background-color: #1888d3"><?=$item->getDisplayPrice()?></button>
                <button onclick="ConcreteMarketplace.purchaseOrDownload({mpID: <?=$item->getMarketplaceItemID()?>})" class="btn btn-description"><? if ($item->purchaseRequired()) { ?><?=t('Purchase')?><? } else { ?><?=t('Download')?><? } ?></button>
            </div>
        </div>
        <nav>
            <li><a href="#" data-launch="marketplace-gallery"><i class="fa fa-image"></i> <?=t('Screenshots')?></a></li>
        </nav>
    </div>

    </div>

    <div class="ccm-marketplace-detail-columns">
        <div class="row">
            <div class="col-md-4">
                <ul class="list-group">
                    <li class="list-group-item"><?=Loader::helper('rating')->outputDisplay($item->getAverageRating())?>
                    <? if ($item->getTotalRatings() > 0) { ?>
                        <a href="<?=$item->getRemoteReviewsURL()?>" target="_blank" class="ccm-marketplace-detail-reviews-link">
                    <? } ?>
                    <?=t2('%d review', '%d reviews', $item->getTotalRatings(), $item->getTotalRatings())?>
                    <? if ($item->getTotalRatings() > 0) { ?>
                        </a>
                    <? } ?>
                    </li>
                    <? if ($item->getExampleURL()) { ?>
                        <li class="list-group-item"><a href="<?=$item->getExampleURL()?>" target="_blank"><?=t('Live Example')?></a></li>
                    <? } ?>
                    <li class="list-group-item"><a href="<?=$item->getRemoteHelpURL()?>" target="_blank"><i class="fa fa-comment"></i> <?=t('Get Help')?></a></li>
                </ul>
            </div>
            <div class="col-md-7 col-md-offset-1">
                <?=$item->getBody()?>
            </div>
        </div>
    </div>


    <script type="text/javascript">
    $(function () {
        $('a[data-launch=marketplace-gallery]').on('click', function(e) {
            e.preventDefault();
            $('ul[data-gallery=marketplace-addon] li:first-child a').trigger('click');
        });

        $('ul[data-gallery=marketplace-addon]').magnificPopup({
          delegate: 'a',
          type: 'image',
          closeOnContentClick: false,
          closeBtnInside: false,
          mainClass: 'mfp-zoom-in mfp-img-mobile',
          image: {
            verticalFit: true
          },
          gallery: {
            enabled: true
          },
          callbacks: {
              open: function() {
                $('.mfp-content').addClass('ccm-ui');
              }
          },
          zoom: {
            enabled: true,
            duration: 300,
            opener: function(element) {
              return element.find('img');
            }
          }

        });
    });
    </script>
<? } else if (count($items)) { ?>

    <? foreach($items as $mi) { ?>

        <div class="ccm-marketplace-list-item">
            <div class="ccm-marketplace-list-item-add-on-thumbnail"><a href="<?=$mi->getLocalURL()?>"><?
                $thumb = $mi->getRemoteIconURL();
                printf('<img src="%s">', $thumb);
                ?></a></div>
            <div class="ccm-marketplace-list-item-add-on-description">
                <h2><a href="<?=$mi->getLocalURL()?>"><?=$mi->getName()?></a></h2>
                    <p><?=$mi->getDescription()?></p>
                <a href="<?=$mi->getLocalURL()?>"><?=t('Learn More')?></a>
            </div>
            <div class="ccm-marketplace-list-item-add-on-price">
                <div class="btn-group">
                    <button onclick="ConcreteMarketplace.purchaseOrDownload({mpID: <?=$mi->getMarketplaceItemID()?>})" class="btn btn-price" style="background-color: #1888d3"><?=$mi->getDisplayPrice()?></button>
                    <button onclick="ConcreteMarketplace.purchaseOrDownload({mpID: <?=$mi->getMarketplaceItemID()?>})" class="btn btn-description"><? if ($mi->purchaseRequired()) { ?><?=t('Purchase')?><? } else { ?><?=t('Download')?><? } ?></button>
                </div>
            </div>
        </div>

    <? } ?>

    <?=$list->displayPagingV2()?>

<? } else { ?>
   <div class="ccm-marketplace-list-item">
   <div class="ccm-marketplace-list-item-add-on-description">
    <p><?=t('No add-ons found.')?></p>
    </div>
    </div>
<? } ?>