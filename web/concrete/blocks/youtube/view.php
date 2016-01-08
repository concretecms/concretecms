<? defined('C5_EXECUTE') or die("Access Denied.");

$responsiveClass  = 'youtubeBlockResponsive16by9';
$sizeDisabled = '';

if ($vWidth && $vHeight) {
	$sizeargs = 'width="' . $vWidth . '" height="' . $vHeight . '"';
	$sizeDisabled = 'style="width:' . $vWidth . 'px; height:' . $vHeight . 'px"';
	$responsiveClass = '';
} elseif ($sizing == '4:3') {
	$responsiveClass  = 'youtubeBlockResponsive4by3';
}

$params = array();

if (isset($playlist)) {
	$params[] = 'playlist='. $playlist;
	$videoID = '';
}

if ($playListID) {
	$params[] = 'listType=playlist';
	$params[] = 'list=' . $playListID;
}

if (isset($autoplay)) {
	$params[] = 'autoplay=' . ($autoplay ? '1' : '0');
}

if (isset($color)) {
	$params[] = 'color=' . $color;
}

if (isset($controls)) {
	$params[] = 'controls=' . $controls;
}

$params[] = 'hl=' . Localization::activeLanguage();

if (isset($iv_load_policy)) {
	$params[] = 'iv_load_policy=' . ($iv_load_policy ? '1' : '0');
}

if (isset($loop)) {
	$params[] = 'loop=' . ($loop ? '1' : '0');
}

if (isset($modestbranding)) {
	$params[] = 'modestbranding=' . ($modestbranding ? '1' : '0');
}

if (isset($rel)) {
	$params[] = 'rel=' . ($rel ? '1' : '0');
}

if (isset($showinfo)) {
	$params[] = 'showinfo=' . ($showinfo ? '1' : '0');
}

$paramstring = '?' . implode('&', $params);

if (Page::getCurrentPage()->isEditMode()) { ?>
	<div class="ccm-edit-mode-disabled-item youtubeBlock <?php echo $responsiveClass; ?>" <?php echo $sizeDisabled; ?>>
		<div><?= t('YouTube Video disabled in edit mode.'); ?></div>
	</div>
<? } else { ?>
	<div id="youtube<?= $bID; ?>" class="youtubeBlock <?php echo $responsiveClass; ?>">
		<iframe class="youtube-player" <?php echo $sizeargs; ?> src="//www.youtube.com/embed/<?= $videoID; ?><?= $paramstring;?>" frameborder="0" allowfullscreen></iframe>
	</div>
<? } ?>