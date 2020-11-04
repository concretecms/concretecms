<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Identifier;

/** @var Entity $entity */

$app = Application::getFacadeApplication();
/** @var Identifier $idHelper */
$idHelper = $app->make(Identifier::class);
$containerId = "ccm-express-entry-selector-" . $idHelper->getString();
?>

<div id="<?php echo $containerId; ?>" class="h-100">
    <!--suppress HtmlUnknownTag -->
    <concrete-express-entry-selector entity-id='<?php echo $entity->getId(); ?>'></concrete-express-entry-selector>
</div>

<script type="text/javascript">
    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: '#<?php echo $containerId;?>',
            components: config.components
        })
    })
</script>