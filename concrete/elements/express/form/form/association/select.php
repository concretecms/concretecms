<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="mb-3">
    <?php
    if ($view->supportsLabel()) { ?>
        <label class="form-label" for="<?=$view->getControlID()?>"><?=$label?></label>
    <?php } ?>
    <?php if ($view->isRequired()) { ?>
        <span class="text-muted small"><?=t('Required')?></span>
    <?php } ?>

    <?php
    if (!empty($allEntries)) {
        // 1. Let's persist values on POST. This is a bug in the core. If you submit a form
        // with an association in it and the submission is invalid, when the page reloads your
        // values will be lost. The first half of this fixes that. We should add that to the core
        // in some capacity.
        // 2. Enhancement: Let's make it so you can pass in an association through the query string
        // by naming the key the same value as target association. e.g. "?student=3" will propulate
        // The student association select menu with the student w/the ID of 3.
        $targetHandle = $association->getTargetEntity()->getHandle();
        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        $method = $request->getMethod();
        $entryIDFromRequest = null;
        if ($method == 'POST') {
            // Make sure to persist values.
            if ($request->request->has('express_association_' . $control->getId())) {
                $entryIDFromRequest = (int) $request->request->get('express_association_' . $control->getId());
            }
        } else {
            $selectedEntry = $selectedEntries[0] ?? null;
            if (!$selectedEntry) {
                // Check query for entry
                if ($request->query->has($targetHandle)) {
                    $entryIDFromRequest = (int) $request->query->get($targetHandle);
                }
            }
        }
        if (isset($entryIDFromRequest)) {
            $entryFromRrequest = Express::getEntry($entryIDFromRequest);
            if ($entryFromRrequest && $entryFromRrequest->is($targetHandle)) {
                $selectedEntry = $entryFromRrequest;
            }
        }
        ?>
        <select class="form-select" id="<?=$view->getControlID()?>" name="express_association_<?=$control->getId()?>">
            <option value=""><?=t('** Choose %s', $control->getControlLabel())?></option>
            <?php
            foreach ($allEntries as $entry) {
                ?>
                <option
                        value="<?=$entry->getId()?>"
                        <?php if (is_object($selectedEntry) && $selectedEntry->getID() == $entry->getID()) { ?>selected<?php } ?>
                >
                    <?=$formatter->getEntryDisplayName($control, $entry)?>
                </option>
                <?php
            }
            ?>
        </select>
        <?php
    } else {
        ?><p><?=t('No available entries found.')?></p><?php
    } ?>
</div>
