<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Text;
use Concrete\Core\Entity\File\Version;

/** @var Version $fv */
$app = Application::getFacadeApplication();
/** @var Text $textHelper */
$textHelper = $app->make(Text::class);
?>

<div class="ccm-text-view">
    <pre>
        <?php echo $textHelper->entities($fv->getFileContents())?>
    </pre>
</div>

<!--suppress CssNoGenericFontName -->
<style type="text/css">
    .ccm-text-view {
        text-align: left
    }

    .ccm-text-view pre {
        font-size: 11px;
        font-family: Courier;
    }
</style>