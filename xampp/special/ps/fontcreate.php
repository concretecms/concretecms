<?php

function frame($psdoc) {
	ps_setlinewidth($psdoc, 60);
	ps_moveto($psdoc, 150, 50);
	ps_lineto($psdoc, 750, 50);
	ps_arc($psdoc, 750, 150, 100, 270, 360);
	ps_lineto($psdoc, 850, 750);
	ps_arc($psdoc, 750, 750, 100, 0, 90);
	ps_lineto($psdoc, 150, 850);
	ps_arc($psdoc, 150, 750, 100, 90, 180);
	ps_lineto($psdoc, 50, 150);
	ps_arc($psdoc, 150, 150, 100, 180, 270);
	ps_stroke($psdoc);
}

$psdoc = ps_new();

if (!ps_open_file($psdoc, "fontcreate.ps")) {
	print "Cannot open PostScript file\n";
	exit;
}

ps_set_info($psdoc, "Creator", __FILE__);
ps_set_info($psdoc, "Author", "Uwe Steinmann");
ps_set_info($psdoc, "Title", "Boxed Text");
ps_set_info($psdoc, "Keywords", "Boxes, Text Rendering");
ps_set_info($psdoc, "BoundingBox", "0 0 596 842");

$myfont = ps_begin_font($psdoc, "test", 0.001, 0.0, 0.0, 0.001, 0.0, 0.0);
ps_begin_glyph($psdoc, "a", 800.0, 0.0, 0.0, 800.0, 800.0);
ps_rect($psdoc, 10, 10, 300, 300);
ps_stroke($psdoc);
ps_end_glyph($psdoc);

ps_begin_glyph($psdoc, "b", 800.0, 0.0, 0.0, 800.0, 800.0);
ps_rect($psdoc, 10, 10, 300, 300);
ps_fill($psdoc);
ps_end_glyph($psdoc);

ps_begin_glyph($psdoc, "zero", 900.0, 0.0, 0.0, 900.0, 900.0);
frame($psdoc);
ps_end_glyph($psdoc);

ps_begin_glyph($psdoc, "one", 900.0, 0.0, 0.0, 900.0, 900.0);
frame($psdoc);
ps_circle($psdoc, 450, 450, 80);
ps_fill($psdoc);
ps_end_glyph($psdoc);

ps_begin_glyph($psdoc, "two", 900.0, 0.0, 0.0, 900.0, 900.0);
frame($psdoc);
ps_circle($psdoc, 250, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 650, 80);
ps_fill($psdoc);
ps_end_glyph($psdoc);

ps_begin_glyph($psdoc, "three", 900.0, 0.0, 0.0, 900.0, 900.0);
frame($psdoc);
ps_circle($psdoc, 250, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 450, 450, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 650, 80);
ps_fill($psdoc);
ps_end_glyph($psdoc);

ps_begin_glyph($psdoc, "four", 900.0, 0.0, 0.0, 900.0, 900.0);
frame($psdoc);
ps_circle($psdoc, 250, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 250, 650, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 650, 80);
ps_fill($psdoc);
ps_end_glyph($psdoc);

ps_begin_glyph($psdoc, "five", 900.0, 0.0, 0.0, 900.0, 900.0);
frame($psdoc);
ps_circle($psdoc, 250, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 250, 650, 80);
ps_fill($psdoc);
ps_circle($psdoc, 450, 450, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 650, 80);
ps_fill($psdoc);
ps_end_glyph($psdoc);

ps_begin_glyph($psdoc, "six", 900.0, 0.0, 0.0, 900.0, 900.0);
frame($psdoc);
ps_circle($psdoc, 250, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 250, 450, 80);
ps_fill($psdoc);
ps_circle($psdoc, 250, 650, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 450, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 650, 80);
ps_fill($psdoc);
ps_end_glyph($psdoc);

ps_begin_glyph($psdoc, "seven", 900.0, 0.0, 0.0, 900.0, 900.0);
frame($psdoc);
ps_circle($psdoc, 250, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 250, 450, 80);
ps_fill($psdoc);
ps_circle($psdoc, 250, 650, 80);
ps_fill($psdoc);
ps_circle($psdoc, 450, 450, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 450, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 650, 80);
ps_fill($psdoc);
ps_end_glyph($psdoc);

ps_begin_glyph($psdoc, "eight", 900.0, 0.0, 0.0, 900.0, 900.0);
frame($psdoc);
ps_circle($psdoc, 250, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 250, 450, 80);
ps_fill($psdoc);
ps_circle($psdoc, 250, 650, 80);
ps_fill($psdoc);
ps_circle($psdoc, 450, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 450, 650, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 450, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 650, 80);
ps_fill($psdoc);
ps_end_glyph($psdoc);

ps_begin_glyph($psdoc, "nine", 900.0, 0.0, 0.0, 900.0, 900.0);
frame($psdoc);
ps_circle($psdoc, 250, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 250, 450, 80);
ps_fill($psdoc);
ps_circle($psdoc, 250, 650, 80);
ps_fill($psdoc);
ps_circle($psdoc, 450, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 450, 450, 80);
ps_fill($psdoc);
ps_circle($psdoc, 450, 650, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 250, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 450, 80);
ps_fill($psdoc);
ps_circle($psdoc, 650, 650, 80);
ps_fill($psdoc);
ps_end_glyph($psdoc);

ps_add_kerning($psdoc, "one", "two", 100);
ps_add_ligature($psdoc, "one", "two", "nine");
ps_add_ligature($psdoc, "nine", "three", "zero");
ps_end_font($psdoc);

ps_begin_page($psdoc, 596, 842);
ps_setfont($psdoc, $myfont, 80.0);
ps_show_xy($psdoc, "123456", 80, 700);
ps_setfont($psdoc, $myfont, 60.0);
ps_show_xy($psdoc, "12345678", 80, 600);
ps_setfont($psdoc, $myfont, 40.0);
ps_show_xy($psdoc, "1234567890", 80, 500);
ps_setfont($psdoc, $myfont, 20.0);
ps_show_xy($psdoc, "1234567890", 80, 400);
ps_setfont($psdoc, $myfont, 10.0);
ps_show_xy($psdoc, "1234567890", 80, 300);

ps_end_page($psdoc);

ps_close($psdoc);
ps_delete($psdoc);

?>
