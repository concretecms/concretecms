<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Url\Url;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Page;

/** @var string $youtubeDomain */

$responsiveClass = 'youtubeBlockResponsive16by9';
$sizeDisabled = '';
$lazyLoadAttribute = '';
$sizeargs = null;
$sizing = $sizing ?? null;
$videoID = $videoID ?? '';
$bID = $bID ?? 0; // This should always be set but just incase
$c = Page::getCurrentPage();
if (!empty($vWidth) && !empty($vHeight)) {
    $sizeargs = 'width="' . $vWidth . '" height="' . $vHeight . '"';
    $sizeDisabled = 'style="width:' . $vWidth . 'px; height:' . $vHeight . 'px"';
    $responsiveClass = '';
} elseif ($sizing == '4:3') {
    $responsiveClass = 'youtubeBlockResponsive4by3';
}

if (isset($lazyLoad) && $lazyLoad) {
    $lazyLoadAttribute = 'loading="lazy"';
}

$params = [];

if (isset($playlist)) {
    $params['playlist'] = $playlist;
    $videoID = '';
}

if (isset($playListID) && !empty($playListID)) {
    $params['listType'] = 'playlist';
    $params['list'] = $playListID;
}

if (isset($autoplay) && $autoplay) {
    $params['autoplay'] = '1';
}

if (isset($color) && $color) {
    $params['color'] = $color;
}

if (isset($controls) && $controls != '') {
    $params['controls'] = $controls;
}

$params['h1'] = Localization::activeLanguage();

if (isset($iv_load_policy) && $iv_load_policy > 0) {
    $params['iv_load_policy'] = $iv_load_policy;
}

if (isset($loopEnd) && $loopEnd) {
    $params['loop'] = '1';
    if (!isset($playlist) && $videoID !== '') {
        $params['playlist'] = $videoID;
    }
}

if (isset($modestbranding) && $modestbranding) {
    $params['modestbranding'] = '1';
}

if (isset($rel) && $rel) {
    $params['rel'] = '1';
} else {
    $params['rel'] = '0';
}

if (isset($showCaptions) && $showCaptions) {
    $params['cc_load_policy'] = '1';
    $params['cc_lang_pref'] = Localization::activeLanguage();
}

if (!empty($startSeconds)) {
    $params['start'] = $startSeconds;
}

$source = Url::createFromUrl('http://' . $youtubeDomain);
$source->getQuery()->set($params);
$source->getPath()->set(['embed', $videoID]);

// Get rid of the `http:` at the beginning of the url
$source = substr((string) $source, 5);

if (is_object($c) && $c->isEditMode()) {
    $loc = Localization::getInstance();
    $loc->pushActiveContext(Localization::CONTEXT_UI); ?>
    <div class="ccm-edit-mode-disabled-item youtubeBlock <?php echo $responsiveClass; ?>" <?php echo $sizeDisabled; ?>>
        <div><?= t('YouTube Video disabled in edit mode.'); ?></div>
    </div>
    <?php
    $loc->popActiveContext();
} else { ?>
    <div id="youtube<?= $bID; ?>" class="youtubeBlock <?php echo $responsiveClass; ?>">
        <iframe class="youtube-player" <?php echo $sizeargs; ?> <?php echo $title ? 'title="' . h($title) . '"' : ''; ?>
            src="<?= $source ?>"
            allow="autoplay" allowfullscreen <?= $lazyLoadAttribute; ?>></iframe>
    </div>
    <?php
}
