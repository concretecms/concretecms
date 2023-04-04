<?php defined('C5_EXECUTE') or die('Access Denied.'); 

$c = Page::getCurrentPage();

?>



<div class="ccm-block-share-this-page">
    <h3><?=t('Share')?></h3>
    <ul class="list-inline">
    <?php foreach ($selected as $service) { ?>
        <li>
            <a href="<?php echo h($service->getServiceLink()) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo h($service->getDisplayName()) ?>"><?php echo $service->getServiceIconHTML()?></a>
            <p><?php echo h($service->getDisplayName()) ?></p>
        </li>
    <?php } ?>
    </ul>
    <div class="page-link">
        <p class="subtitle-big"><?=t('Page Link')?></p>
        <div class="input-group">
            <input type="text" id="pageLink" name="pageLink" value="<?php echo $c->getCollectionLink(); ?>" class="form-control ccm-input-text">
            <div class="input-group-text">
                <button class="btn copyboard" data-label="Page Link" data-text="<?php echo $c->getCollectionLink(); ?>"><i class="far fa-copy"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
$('.copyboard').on('click', function(e) {
    e.preventDefault();
    var copyText = $(this).attr('data-text');
    var textarea = document.createElement("textarea");
    textarea.textContent = copyText;
    textarea.style.position = "fixed"; // Prevent scrolling to bottom of page in MS Edge.
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand("copy");
    document.body.removeChild(textarea);
    ConcreteAlert.notify({
            type: 'info',
            icon: 'copy',
            title: 'Copied',
            message: 'Copied ' + $(this).attr('data-label') + ' to clipboard',
            hide: true,
    });
})
</script>