<?php
defined('C5_EXECUTE') or die('Access Denied.');

/** @var bool $includeDownloadLink */

$images = $images ?? [];

if (!isset($bID)) {
    $bID = 0;
}

// Strip out any file instances before serializing
// A better way to do this would be to use an actual class for entries
$images = array_map(function($image) {
    unset($image['file']);
    return $image;
}, $images);
?>

<div id="ccm-gallery-<?= $bID ?>">
    <gallery-edit :gallery="data" :choices="choices"></gallery-edit>
</div>

<script>
    Concrete.Vue.activateContext('gallery', function(Vue, config) {
        new Vue({
            el: '#ccm-gallery-<?= $bID ?>',
            components: config.components,
            data: function() {
                return {
                    data: JSON.parse(<?= json_encode(json_encode($images)) ?>),
                    choices: JSON.parse(<?= json_encode(json_encode($displayChoices)) ?>),
                    includeDownloadLink: <?php echo $includeDownloadLink ? "true" : "false"; ?>
                }
            }
        })
    })
</script>
