<?php

defined('C5_EXECUTE') or die("Access Denied.");

$subject = t(/* i18n: %s is the name of the report */'%s Report Ready' ,$reportName);
$body = t("

The results for the report \"%s\" are now available. You can view the findings here:

%s

Additionally, the full findings are attached to this email as a CSV.

", $reportName, URL::to('/dashboard/reports/health/details', $result->getId()));
