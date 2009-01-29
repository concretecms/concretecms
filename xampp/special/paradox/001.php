--TEST--
Reading a simple paradox database
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$pxdoc = px_new();
$fp = fopen($dirname."/simpletest.db", "r");
px_open_fp($pxdoc, $fp);
$info = px_get_info($pxdoc);
print_r($info);
echo "Number of fields: ".px_numfields($pxdoc)."\n";
echo "Number of records: ".px_numrecords($pxdoc)."\n";
print_r(px_get_schema($pxdoc));
px_close($pxdoc);
fclose($fp);
px_delete($pxdoc);
?>
--EXPECT--
Array
(
    [fileversion] => 70
    [tablename] => test.db
    [numrecords] => 1
    [numfields] => 1
    [headersize] => 2048
    [maxtablesize] => 2
    [numdatablocks] => 1
    [numindexfields] => 0
    [codepage] => 1252
)
Number of fields: 1
Number of records: 1
Array
(
    [col1] => Array
        (
            [type] => 3
            [size] => 2
        )

)

