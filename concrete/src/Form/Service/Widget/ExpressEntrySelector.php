<?php

namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Identifier;
use HtmlObject\Element;

class ExpressEntrySelector
{

    public function selectEntry(Entity $entity, $fieldName, Entry $entry = null)
    {
        $permissionChecker = new Checker($entity);
        if ($permissionChecker->canViewExpressEntries()) {
            $entityId = $entity->getID();
            $entryId = $entry instanceof Entry ? $entry->getID() : 0;
            $chooseText = t('Choose Entry');
            $identifier = new Identifier();
            $identifier = $identifier->getString(32);

            $divContainer = new Element("div");
            $divContainer->setAttribute("data-concrete-express-entry-input", $identifier);

            $concreteExpressEntryInput = new Element("concrete-express-entry-input");
            $concreteExpressEntryInput
                ->setAttribute("entity-id", $entityId)
                ->setAttribute("entry-id", $entryId)
                ->setAttribute("input-name", $fieldName)
                ->setAttribute("choose-text", $chooseText);
            $divContainer->appendChild($concreteExpressEntryInput);

            $html = (string)$divContainer . <<<EOL
<script>
$(function() {
    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-concrete-express-entry-input="{$identifier}"]',
            components: config.components
        })
    })
});
</script>
EOL;

        } else {
            $html = '<div class="control-group"><p>' . t('You do not have access to view these entries.') . '</p></div>';
        }

        return $html;
    }


}
