--TEST--
Inserting records with memo fields
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$fp = fopen($dirname."/px018.db", "w+");
$pxdoc = new paradox_db();
$fields = array(array("col1", "M", 20));
@$pxdoc->create_fp($fp, $fields);
$pxdoc->set_blob_file($dirname."/px018.mb");
$pxdoc->set_tablename("testtabelle");
$str1 = "Some text long enough to go into a blob file";
$str2 = "Too short";
for($i=0; $i<=3; $i++) {
	$pxdoc->insert_record(array($str1));
}
for($i=0; $i<=3; $i++) {
	$pxdoc->insert_record(array($str2));
}
for($i=0; $i<=3; $i++) {
	$pxdoc->insert_record(array(NULL));
}
print_r($pxdoc->retrieve_record(1));
print_r($pxdoc->retrieve_record(4));
$rec = $pxdoc->retrieve_record(9);
if(is_null($rec["col1"])) {
	echo "is null";
}
$pxdoc->close();
fclose($fp);
unlink($dirname."/px018.mb");
unlink($dirname."/px018.db");
?>
--EXPECT--
Array
(
    [col1] => Some text long enough to go into a blob file
)
Array
(
    [col1] => Too short
)
is null
