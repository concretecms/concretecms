<?php

defined('C5_EXECUTE') or die("Access Denied.");

$formDisplayUrl = URL::to('/dashboard/reports/forms', 'view', $entity->getEntityResultsNodeId());

$submittedData = '';
foreach($attributes as $value) {
    $submittedData .= $value->getAttributeKey()->getAttributeKeyDisplayName() . ":\r\n";
    $submittedData .= $value->getPlainTextValue() . "\r\n\r\n";
}

$body = t("
There has been a submission of the form %s through your concrete5 website.

%s

To view all of this form's submissions, visit %s 

", $formName, $submittedData, $formDisplayUrl);
