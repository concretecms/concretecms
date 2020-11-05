<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\File\Version;

/** @var Version $fv */
$path = $fv->getURL();
?>

<video src="<?php echo $path ?>" width="70%" height="70%" preload="auto" controls="controls">
    <object id="MediaPlayer" width="70%" height="70%">
        <param name="FileName" value="<?php echo $path ?>">
        <embed src="<?php echo $path ?>" name="MediaPlayer" width="70%" height="70%" />
    </object>
</video>