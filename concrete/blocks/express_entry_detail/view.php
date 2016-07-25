<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php

if (isset($entry) && is_object($entry)) { ?>

<?php
echo $renderer->render($expressForm, $entry);
?>


<?php } ?>