<?

$db = Loader::db();
$ab = $db->getADOSChema();
$xml = $ab->ExtractSchema();
print '<textarea style="width: 100%; height: 100%">' . $xml . '</textarea>';
