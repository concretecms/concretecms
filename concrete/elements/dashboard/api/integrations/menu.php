<?php

use Concrete\Core\Support\Facade\Url as UrlFacade;

defined('C5_EXECUTE') or die("Access Denied.");

?>

    <a class="btn btn-sm btn-secondary" title="<?php echo h(t('Add User')) ?>"
       href="<?php echo (string)UrlFacade::to("/dashboard/system/api/integrations/add"); ?>">
        <?=t('Add Integration')?> <i class="fa fa-plus"></i>
    </a>