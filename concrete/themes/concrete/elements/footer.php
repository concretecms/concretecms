<?php defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;
use Concrete\Core\View\View;

if (isset($c) && is_object($c)) {
    Loader::element('footer_required');
} else {
    View::getInstance()->markFooterAssetPosition();
}
?>

</body>
</html>
