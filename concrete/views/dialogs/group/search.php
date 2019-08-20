<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;
?>

<div class="ccm-ui">

<?php Loader::element('group/search', array('controller' => $searchController, 'selectMode' => true))?>

</div>