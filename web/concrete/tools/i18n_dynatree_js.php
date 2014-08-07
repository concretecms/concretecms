<?php
use \Concrete\Core\Localization\Localization;

header('Content-type: text/javascript; charset=' . APP_CHARSET);

$jh = Core::make('helper/json');

?>
jQuery.ui.dynatree.prototype.options.strings.loading = <?=$jh->encode(t('Loading...'))?>;
jQuery.ui.dynatree.prototype.options.strings.loadError = <?=$jh->encode(t('Load error!'))?>;
