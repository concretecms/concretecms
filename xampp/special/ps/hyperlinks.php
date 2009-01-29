<?php

$ps = ps_new();

if (!ps_open_file($ps, "hyperlinks.ps")) {
	print "Cannot open PostScript file\n";
	exit;
}

ps_set_parameter($ps, "warning", "true");

ps_set_info($ps, "Creator", "hyperlinks.php");
ps_set_info($ps, "Author", "Uwe Steinmann");
ps_set_info($ps, "Title", "Creating Hyperlinks with pdfmarks");

$fontsize = 20.0;

ps_begin_page($ps, 596, 842);
$b1 = ps_add_bookmark($ps, "Content", 0, 0);
ps_add_bookmark($ps, "First Page", $b1, 0);
$psfont = ps_findfont($ps, "Helvetica", "", 0);
ps_setfont($ps, $psfont, $fontsize);
ps_set_value($ps, "leading", 14.0);

ps_show_xy($ps, "This is a web link", 100, 100);
$len = ps_stringwidth($ps, "This is a web link", $psfont, $fontsize);
ps_add_weblink($ps, 100, 100, 100+$len, 130, "http://www.mmk-hagen.de");

ps_show_xy($ps, "This is a pdf link to an external document", 100, 150);
$len = ps_stringwidth($ps, "This is a pdf link to an external document", $psfont, $fontsize);
ps_add_pdflink($ps, 100, 150, 100+$len, 180, "test.pdf", 1, "fitpage");

ps_show_xy($ps, "This is a launch link", 100, 200);
$len = ps_stringwidth($ps, "This is a launch link", $psfont, $fontsize);
ps_add_launchlink($ps, 100, 200, 100+$len, 230, "/usr/bin/gedit");

ps_show_xy($ps, "This is a pdf link within the document", 100, 250);
$len = ps_stringwidth($ps, "This is a pdf link within the document", $psfont, $fontsize);
ps_add_locallink($ps, 100, 250, 100+$len, 280, 2, "fitpage");

ps_end_page($ps);

ps_begin_page($ps, 300, 300);
ps_add_bookmark($ps, "Second Page", $b1, 0);
ps_add_note($ps, 100, 100, 200, 200, "This is the contents of the note", "Title of Note", "help", 1);
ps_end_page($ps);

ps_close($ps);
ps_delete($ps);
?>
