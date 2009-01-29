<?php
define("LEFT_BORDER", 50);

function footer($ps, $text) {
	$psfont = ps_findfont($ps, "Helvetica", "", 0);
	ps_setfont($ps, $psfont, 8.0);
	$buffer = "This file has been created with pslib ".ps_get_parameter($ps, "dottedversion", 0.0);
	ps_show_xy($ps, $buffer, LEFT_BORDER, 25);
}

$ps = ps_new();

if (!ps_open_file($ps, "overprint.ps")) {
	print "Cannot open PostScript file\n";
	exit;
}

ps_set_info($ps, "Creator", "draw.php");
ps_set_info($ps, "Author", "Uwe Steinmann");
ps_set_info($ps, "Title", "Overprint");

ps_begin_page($ps, 596, 842);
footer($ps, "");
ps_setcolor($ps, "fill", "cmyk", 1.0, 0.0, 0.0, 0.0);
ps_rect($ps, 100, 100, 200, 200);
ps_fill($ps);
ps_setoverprintmode($ps, 1);
ps_rect($ps, 120, 120, 100, 100);
ps_fill($ps); 
ps_end_page($ps);

ps_close($ps);
ps_delete($ps);
?>
