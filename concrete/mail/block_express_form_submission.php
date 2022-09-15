<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $entity \Concrete\Core\Entity\Express\Entity
 * @var $associations \Concrete\Core\Entity\Express\Entry\Association[]
 */
if (!isset($associations)) {
    $associations = [];
}

$formDisplayUrl = $entity->getEntryListingUrl();

$subject = t('Website Form Submission – %s', $formName);

$submittedData = '';
foreach ($attributes as $value) {
    if ("image_file" != $value->getAttributeTypeObject()->getAttributeTypeHandle() || ($dataSaveEnabled && "image_file" == $value->getAttributeTypeObject()->getAttributeTypeHandle())) {
        $submittedData .= $value->getAttributeKey()->getAttributeKeyDisplayName('text') . ":\r\n";
        $submittedData .= $value->getPlainTextValue() . "\r\n\r\n";
    }
}
foreach ($associations as $association) {
    $submittedData .= $association->getAssociation()->getTargetEntity()->getEntityDisplayName() .  ":\r\n";
    $selectedEntries = $association->getSelectedEntries();
    foreach ($selectedEntries as $selectedEntry) {
        $submittedData .= $selectedEntry->getLabel() . "\r\n";
    }
    $submittedData .= "\r\n";
}

if ($dataSaveEnabled) {
    $body = t("
There has been a submission of the form %s through your Concrete website.

%s

To view all of this form's submissions, visit %s 

", $formName, $submittedData, $formDisplayUrl);
} else {
    $body = t("
There has been a submission of the form %s through your Concrete website.

%s
", $formName, $submittedData);
}
