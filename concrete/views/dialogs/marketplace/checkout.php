<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">
    <?
    $mi = \Concrete\Core\Marketplace\Marketplace::getInstance();
    print $mi->getMarketplacePurchaseFrame($mri, '100%', '100%');
    ?>
</div>
