<?php

use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

$image = date('Ymd') . '.jpg';
if (Config::get('concrete.white_label.background_image') !== 'none' && !Config::get('concrete.white_label.background_url')) {
    $imagePath = Config::get('concrete.urls.background_feed') . '/' . $image;
} else if (Config::get('concrete.white_label.background_url')) {
    $imagePath = Config::get('concrete.white_label.background_url');
}

?>

<?php if (isset($imagePath)) { ?>
    <div class="ccm-page-background-credit d-none">
        <div class="ccm-page-background-privacy-notice" data-photo-provider="unsplash">
            <?=t('Photo by <a href="%s" target="_blank" rel="nofollow"></a> on <a href="https://unsplash.com" target="_blank" rel="nofollow">Unsplash</a>.')?>
        </div>
    </div>


<?php } ?>


<script type="text/javascript">
    $(function() {

        <?php if (isset($imagePath)) { ?>
            $.getJSON(CCM_DISPATCHER_FILENAME + '/ccm/system/backend/dashboard/get_image_data', { image: '<?= $image ?>' }, function (data) {
                if (data.photo.provider === 'unsplash') {
                    var authorBar = $('div[data-photo-provider=unsplash]')
                    authorBar.show()
                    authorBar.find('a').first().text(data.photo.data.user.name)
                    authorBar.find('a').first().attr('href', data.photo.data.user.links.html)
                }

                // By default, our container is absolutely positioned to the bottom of the position: relative container
                // However if the page is short enough that it doesn't scroll, we should change this to position
                // fixed
                if (window.innerHeight > document.body.offsetHeight) {
                    $('.ccm-page-background-credit').css('position', 'fixed')
                }
                $('.ccm-page-background-credit').addClass('animated fadeIn').removeClass('d-none')
            });
            $.backstretch("<?= Config::get('concrete.urls.background_feed') . '/' . $image ?>", {
                fade: 500
            });
        <?php } ?>
        
    });
</script>



