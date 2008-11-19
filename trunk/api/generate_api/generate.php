<?

echo("Generating API...\n\n");
passthru("phpdoc/phpdoc -ti 'Concrete5 API Documentation' -dn Pages -d 'source/' -i 'add.php,edit.php,view.php,form_setup*,help*,test*.php,tools/' -t ../api/ -o HTML:frames:c5");
