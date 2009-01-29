<?php

define("LEFT_BORDER", 50);
define("EXAMPLE_BOX_HEIGHT", 120);
define("EXAMPLE_BOX_WIDTH", 100);
define("EXAMPLE_BOX_TITLE_HEIGHT", 20);

function begin_example_box($p, $llx, $lly, $title, $font) {
	ps_save($p);
	ps_translate($p, $llx, $lly);
	ps_setcolor($p, "fill", "gray", 0.5, 0.0, 0.0, 0.0);
	ps_rect($p, 0, EXAMPLE_BOX_HEIGHT-EXAMPLE_BOX_TITLE_HEIGHT,
	           EXAMPLE_BOX_WIDTH, EXAMPLE_BOX_TITLE_HEIGHT);
	ps_fill($p);
	ps_setcolor($p, "stroke", "gray", 1.0, 0.0, 0.0, 0.0);
	ps_setfont($p, $font, 12.0);
	ps_show_xy($p, $title, 10, EXAMPLE_BOX_HEIGHT-EXAMPLE_BOX_TITLE_HEIGHT+5);
	ps_setlinewidth($p, 1.0);
	ps_setcolor($p, "stroke", "gray", 0.0, 0.0, 0.0, 0.0);
	ps_rect($p, 0, 0, EXAMPLE_BOX_WIDTH, EXAMPLE_BOX_HEIGHT);
	ps_stroke($p);
	ps_moveto($p, 0, EXAMPLE_BOX_HEIGHT-EXAMPLE_BOX_TITLE_HEIGHT);
	ps_lineto($p, EXAMPLE_BOX_WIDTH, EXAMPLE_BOX_HEIGHT-EXAMPLE_BOX_TITLE_HEIGHT);
	ps_stroke($p);
}

function end_example_box($p) {
	ps_restore($p);
}

$ps = ps_new();

if (!ps_open_file($ps, "htdocs/xampp/external/ps/draw.ps")) {
	print "Cannot open PostScript file\n";
	exit;
}

ps_set_parameter($ps, "warning", "true");

ps_set_info($ps, "Creator", "draw.php");
ps_set_info($ps, "Author", "Uwe Steinmann");
ps_set_info($ps, "Title", "Many examples");

$pstemplate = ps_begin_template($ps, 30.0, 30.0);
ps_moveto($ps, 0, 0);
ps_lineto($ps, 30, 30);
ps_moveto($ps, 0, 30);
ps_lineto($ps, 30, 0);
ps_stroke($ps);
ps_end_template($ps);

$pspattern1 = ps_begin_pattern($ps, 10.0, 10.0, 10.0, 10.0, 1);
ps_setlinewidth($ps, 0.2);
ps_setcolor($ps, "stroke", "rgb", 0.0, 0.0, 1.0, 0.0);
ps_moveto($ps, 0, 0);
ps_lineto($ps, 7, 7);
ps_stroke($ps);
ps_moveto($ps, 0, 7);
ps_lineto($ps, 7, 0);
ps_stroke($ps);
ps_end_pattern($ps);

$pspattern2 = ps_begin_pattern($ps, 10.0, 10.0, 10.0, 10.0, 2);
ps_moveto($ps, 0, 0);
ps_lineto($ps, 5, 5);
ps_stroke($ps);
ps_end_pattern($ps);


ps_begin_page($ps, 596, 842);

ps_set_parameter($ps, "transition", "wipe");
ps_set_value($ps, "duration", 0.5);
	

$psfont = ps_findfont($ps, "htdocs/xampp/external/ps/Helvetica", "", 0);

$x = 0;
$y = 675;

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Lines", $psfont);
$polydash = array(5.0, 3.0, 1.0, 4.0);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 90, 10);
ps_stroke($ps);

ps_setdash($ps, 5.0, 5.0);
ps_moveto($ps, 10, 15);
ps_lineto($ps, 90, 30);
ps_stroke($ps);

ps_setdash($ps, 0.0, 0.0);
ps_setlinewidth($ps, 2.0);
ps_moveto($ps, 10, 20);
ps_lineto($ps, 90, 50);
ps_stroke($ps);

ps_setlinewidth($ps, 1.0);
ps_setcolor($ps, "stroke", "rgb", 1.0, 0.0, 0.0, 0.0);
ps_moveto($ps, 10, 25);
ps_lineto($ps, 90, 70);
ps_stroke($ps);

ps_setcolor($ps, "stroke", "gray", 0.0, 0.0, 0.0, 0.0);
ps_setpolydash($ps, $polydash);
ps_moveto($ps, 10, 30);
ps_lineto($ps, 90, 90);
ps_stroke($ps);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Rectangles", $psfont);
ps_rect($ps, 10, 10, 35, 35);
ps_stroke($ps);
ps_setcolor($ps, "both", "rgb", 0.0, 1.0, 0.0, 0.0);
ps_rect($ps, 50, 10, 35, 35);
ps_stroke($ps);
ps_setcolor($ps, "both", "rgb", 1.0, 1.0, 0.0, 0.0);
ps_rect($ps, 10, 50, 35, 35);
ps_fill($ps);
ps_setlinewidth($ps, 3.0);
ps_setcolor($ps, "both", "rgb", 1.0, 0.0, 0.0, 0.0);
ps_setdash($ps, 5.0, 5.0);
ps_rect($ps, 50, 50, 35, 35);
ps_stroke($ps);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Circles, Arcs", $psfont);
ps_circle($ps, 30, 30, 20);
ps_stroke($ps);
ps_circle($ps, 70, 30, 20);
ps_fill($ps);
ps_arc($ps, 30, 70, 20, 0, 270);
ps_fill($ps);
ps_setlinewidth($ps, 3.0);
ps_setcolor($ps, "both", "rgb", 1.0, 0.0, 0.0, 0.0);
ps_setdash($ps, 5.0, 5.0);
ps_arc($ps, 70, 70, 20, 0, 270);
ps_stroke($ps);
end_example_box($ps);

$x = 0;
$y -= 150;
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Line joins", $psfont);
ps_setlinewidth($ps, 5.0);
ps_setlinejoin($ps, 0);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 25, 40);
ps_lineto($ps, 40, 10);
ps_stroke($ps);
ps_setlinejoin($ps, 1);
ps_moveto($ps, 60, 10);
ps_lineto($ps, 75, 40);
ps_lineto($ps, 90, 10);
ps_stroke($ps);
ps_setlinejoin($ps, 2);
ps_moveto($ps, 10, 60);
ps_lineto($ps, 25, 90);
ps_lineto($ps, 40, 60);
ps_stroke($ps);

ps_setlinewidth($ps, 0.5);
ps_setcolor($ps, "both", "gray", 1.0, 0.0, 0.0, 0.0);
ps_setlinejoin($ps, 0);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 25, 40);
ps_lineto($ps, 40, 10);
ps_stroke($ps);
ps_moveto($ps, 60, 10);
ps_lineto($ps, 75, 40);
ps_lineto($ps, 90, 10);
ps_stroke($ps);
ps_moveto($ps, 10, 60);
ps_lineto($ps, 25, 90);
ps_lineto($ps, 40, 60);
ps_stroke($ps);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Line caps", $psfont);
ps_setlinewidth($ps, 5.0);
ps_setlinecap($ps, 0);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 25, 40);
ps_lineto($ps, 40, 10);
ps_stroke($ps);
ps_setlinecap($ps, 1);
ps_moveto($ps, 60, 10);
ps_lineto($ps, 75, 40);
ps_lineto($ps, 90, 10);
ps_stroke($ps);
ps_setlinecap($ps, 2);
ps_moveto($ps, 10, 60);
ps_lineto($ps, 25, 90);
ps_lineto($ps, 40, 60);
ps_stroke($ps);

ps_setlinewidth($ps, 0.5);
ps_setcolor($ps, "both", "gray", 1.0, 0.0, 0.0, 0.0);
ps_setlinejoin($ps, 0);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 25, 40);
ps_lineto($ps, 40, 10);
ps_stroke($ps);
ps_moveto($ps, 60, 10);
ps_lineto($ps, 75, 40);
ps_lineto($ps, 90, 10);
ps_stroke($ps);
ps_moveto($ps, 10, 60);
ps_lineto($ps, 25, 90);
ps_lineto($ps, 40, 60);
ps_stroke($ps);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Miter limit", $psfont);
ps_show_xy($ps, "10", 13, 10);
ps_setmiterlimit($ps, 10);
ps_moveto($ps, 11, 10);
ps_lineto($ps, 20, 90);
ps_lineto($ps, 29, 10);
ps_stroke($ps);
ps_show_xy($ps, "5", 47, 10);
ps_setmiterlimit($ps, 5);
ps_moveto($ps, 41, 10);
ps_lineto($ps, 50, 90);
ps_lineto($ps, 59, 10);
ps_stroke($ps);
ps_show_xy($ps, "1", 77, 10);
ps_setmiterlimit($ps, 1);
ps_moveto($ps, 71, 10);
ps_lineto($ps, 80, 90);
ps_lineto($ps, 89, 10);
ps_stroke($ps);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Curves", $psfont);
ps_moveto($ps, 10, 10);
ps_curveto($ps, 10, 40, 40, 20, 50, 20);
ps_curveto($ps, 50, 20, 80, 90, 20, 70);
ps_curveto($ps, 20, 70, 80, 20, 10, 10);
ps_stroke($ps);
ps_setcolor($ps, "both", "rgb", 1.0, 0.0, 0.0, 0.0);
ps_moveto($ps, 50, 10);
ps_curveto($ps, 50, 10, 110, 40, 90, 90);
ps_curveto($ps, 90, 90, 30, 90, 80, 70);
ps_curveto($ps, 80, 70, 100, 10, 50, 10);
ps_fill($ps);
end_example_box($ps);

$x = 0;
$y -= 150;

/* begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "PNG-Image", $psfont);
$psimage = ps_open_image_file($ps, "png", "debian.png", NULL, 0);
ps_place_image($ps, $psimage, 10, 10, 0.5);
ps_place_image($ps, $psimage, 40, 40, 0.8);
ps_close_image($ps, $psimage);
end_example_box($ps); */

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "EPS-Image", $psfont);
$psimage = ps_open_image_file($ps, "eps", "picture.eps", NULL, 0);
ps_place_image($ps, $psimage, 10, 10, 0.3);
ps_close_image($ps, $psimage);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Path (1)", $psfont);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 50, 50);
ps_moveto($ps, 20, 10);
ps_lineto($ps, 60, 50);
ps_circle($ps, 60, 60, 20);
ps_lineto($ps, 90, 70);
ps_stroke($ps);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Path (2)", $psfont);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 20, 20);
ps_rect($ps, 30, 30, 10, 20);
ps_lineto($ps, 50, 10);
ps_stroke($ps);
ps_moveto($ps, 70, 10);
ps_lineto($ps, 70, 50);
ps_arc($ps, 70, 60, 20, 60, 210);
ps_stroke($ps);
end_example_box($ps);

$x = 0;
$y -= 150;
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text", $psfont);
ps_setfont($ps, $psfont, 8.0);
ps_show_xy($ps, "This is text in 8.0", 10, 10);
ps_setfont($ps, $psfont, 12.0);
ps_show_xy($ps, "and in 12.0", 10, 23);
ps_setfont($ps, $psfont, 18.0);
ps_show_xy($ps, "in 18.0", 10, 40);
ps_setfont($ps, $psfont, 36.0);
ps_show_xy($ps, "huge", 10, 65);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text outlined", $psfont);
ps_setfont($ps, $psfont, 36.0);
ps_set_value($ps, "textrendering", 1);
ps_show_xy($ps, "huge", 10, 15);
ps_setlinewidth($ps, 0.5);
ps_show_xy($ps, "huge", 10, 55);
ps_set_value($ps, "textrendering", -1);
end_example_box($ps);

/* begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text clipping", $psfont);
ps_setfont($ps, $psfont, 70.0);
ps_set_value($ps, "textrendering", 5);
ps_show_xy($ps, "82", 10, 15);
$psimage = ps_open_image_file($ps, "png", "debian.png", NULL, 0);
ps_place_image($ps, $psimage, 10, 1, 1.5);
ps_close_image($ps, $psimage);
ps_set_value($ps, "textrendering", 6);
ps_setfont($ps, $psfont, 20.0);
ps_set_parameter($ps, "kerning", "false");
ps_setcolor($ps, "stroke", "gray", 0.5, 0.0, 0.0, 0.0);
ps_show_xy($ps, "Stripes", 10, 80);
ps_set_parameter($ps, "kerning", "true");
ps_setcolor($ps, "stroke", "gray", 0.0, 0.0, 0.0, 0.0);
for($i=75; $i<97; $i=$i+2) {
	ps_moveto($ps, 10, $i);
	ps_lineto($ps, 80, $i);
	ps_stroke($ps);
}
ps_set_value($ps, "textrendering", -1);
end_example_box($ps); */

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "invisible Text", $psfont);
ps_setfont($ps, $psfont, 10.0);
ps_show_xy($ps, "This is ", 5, 15);
ps_set_value($ps, "textrendering", 3);
ps_show($ps, "invisible");
ps_set_value($ps, "textrendering", -1);
ps_show($ps, " text.");
ps_show_xy($ps, "This is ", 5, 35);
ps_show($ps, "invisible");
ps_show($ps, " text.");
end_example_box($ps);

$x = 0;
$y -= 150;
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text in a box (1)", $psfont);
ps_setfont($ps, $psfont, 8.0);
ps_set_value($ps, "leading", 11);
$str1 = "Text can be put into a box of any size. This one is 80x80 pixels and its lower left corner ist at (10, 10). The text is left justified. The font size is 8.0.";
ps_show_boxed($ps, $str1, 10, 10, 80, 80, "left", NULL);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text in a box (2)", $psfont);
ps_setfont($ps, $psfont, 8.0);
ps_set_value($ps, "leading", 11);
$str2 = "Text can be put into a box of any size. This one is 80x80 pixels and its lower left corner ist at (10, 10). The text is left and right justified. The font size is 8.0.";
ps_show_boxed($ps, $str2, 10, 10, 80, 80, "justify", NULL);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text in a box (3)", $psfont);
ps_setfont($ps, $psfont, 8.0);
ps_set_value($ps, "leading", 11);
$str3 = "Text can be put into a box of any size. This one is 80x80 pixels and its lower left corner ist at (10, 10). The text is right justified. The font size is 8.0.";
ps_show_boxed($ps, $str3, 10, 10, 80, 80, "right", NULL);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text with CR", $psfont);
ps_setfont($ps, $psfont, 8.0);
ps_set_value($ps, "leading", 11);
ps_set_parameter($ps, "linebreak", "true");
$str4 = "If the parameter linebreak is turned on\neach line will be ended with a carriage return.\nCR are marked with a '\\n' in this paragraph.";
ps_show_boxed($ps, $str4, 10, 10, 80, 80, "center", NULL);
ps_set_parameter($ps, "linebreak", "false");
end_example_box($ps);

ps_end_page($ps);

/* ps_begin_page($ps, 596, 842);

ps_set_parameter($ps, "transition", "wipe");
ps_set_value($ps, "duration", 0.5);
	
if(0 == ($psfont = ps_findfont($ps, "D:/xampp/htdocs/xampp/external/ps/Helvetica", "", 0))) {
	fprintf(stderr, "Could not load font 'Helvetica'.\n");
	exit(1);
}

$x = 0;
$y = 675;
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text", $psfont);
ps_setfont($ps, $psfont, 10.0);
ps_show_xy($ps, "No lines at all", 5, 75);
ps_set_parameter($ps, "underline", "true");
ps_show_xy($ps, "Text is underlined", 5, 60);
ps_set_parameter($ps, "underline", "false");
ps_set_parameter($ps, "overline", "true");
ps_show_xy($ps, "Text is overlined", 5, 45);
ps_set_parameter($ps, "overline", "false");
ps_set_parameter($ps, "strikeout", "true");
ps_show_xy($ps, "Text is striked out", 5, 30);
ps_set_parameter($ps, "underline", "true");
ps_set_parameter($ps, "overline", "true");
ps_show_xy($ps, "Everything at once", 5, 15);
ps_set_parameter($ps, "overline", "false");
ps_set_parameter($ps, "underline", "false");
ps_set_parameter($ps, "strikeout", "false");
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text width", $psfont);
ps_setfont($ps, $psfont, 14.0);
ps_show_xy($ps, "Geometry", 10, 50);
$width = ps_stringwidth($ps, "Geometry", $psfont, 14);
ps_moveto($ps, 10, 45);
ps_lineto($ps, 10+$width, 45);
ps_stroke($ps);
ps_setfont($ps, $psfont, 6.0);
$str = sprintf("Text is %.2f pixel wide.", $width);
ps_show_xy($ps, $str, 10, 35);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text geometry", $psfont);
ps_setfont($ps, $psfont, 14.0);
ps_show_xy($ps, "Geometry", 10, 50);
$dimension = ps_string_geometry($ps, "Geometry", $psfont, 14);
ps_setlinewidth($ps, 0.4);
ps_moveto($ps, 10, 50);
ps_lineto($ps, 10+$dimension[0], 50);
ps_stroke($ps);
ps_moveto($ps, 10, 50+$dimension[1]);
ps_lineto($ps, 10+$dimension[0], 50+$dimension[1]);
ps_stroke($ps);
ps_moveto($ps, 10, 50+$dimension[2]);
ps_lineto($ps, 10+$dimension[0], 50+$dimension[2]);
ps_stroke($ps);
ps_setfont($ps, $psfont, 6.0);
$str = sprintf("Text is %.2f pixel wide.", $dimension[0]);
ps_show_xy($ps, $str, 10, 35);
$str = sprintf("Ascender is %.2f pixel.", $dimension[2]);
ps_show_xy($ps, $str, 10, 27);
$str = sprintf("Descender is %.2f pixel.", $dimension[1]);
ps_show_xy($ps, $str, 10, 19);
end_example_box($ps);

$x = 0;
$y -= 150;
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text kerning", $psfont);
ps_setfont($ps, $psfont, 14.0);
ps_set_parameter($ps, "kerning", "true");
ps_show_xy($ps, "Tea VA life", 10, 70);
$width = ps_stringwidth($ps, "Tea VA life", $psfont, 14);
ps_moveto($ps, 10, 65);
ps_lineto($ps, 10+$width, 65);
ps_stroke($ps);
ps_setfont($ps, $psfont, 6.0);
$str = sprintf("Text is %.2f pixel wide.", $width);
ps_show_xy($ps, $str, 10, 55);

ps_setfont($ps, $psfont, 14.0);
ps_set_parameter($ps, "kerning", "false");
ps_show_xy($ps, "Tea VA life", 10, 40);
$width = ps_stringwidth($ps, "Tea VA life", $psfont, 14);
ps_moveto($ps, 10, 35);
ps_lineto($ps, 10+$width, 35);
ps_stroke($ps);
ps_setfont($ps, $psfont, 6.0);
$str = sprintf("Text is %.2f pixel wide.", $width);
ps_show_xy($ps, $str, 10, 25);

end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Embedded font", $psfont);
$dicefont = ps_findfont($ps, "D:/xampp/htdocs/xampp/external/ps/dice", "", 1);
ps_setfont($ps, $dicefont, 10.0);
ps_show_xy($ps, "123456", 10, 70);
ps_setfont($ps, $dicefont, 14.0);
ps_show_xy($ps, "123456", 10, 50);
ps_setfont($ps, $dicefont, 18.0);
ps_show_xy($ps, "123456", 10, 24);

end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Protusion", $psfont);
ps_set_parameter($ps, "hyphenation", "true");
ps_set_parameter($ps, "hyphendict", "D:/xampp/htdocs/xampp/external/ps/hyph_en.dic");
ps_set_value($ps, "hyphenminchars", 2);
ps_setfont($ps, $psfont, 6.0);
ps_set_value($ps, "leading", 8);
$str4 = "If text is output in a box left and right justified then the margins can appear bumby due to punction and hyphens. To prevent this, one can allow certain glyphs to exceed the margin. Finding the right values can be a painful task. Look for the hyphen and the exaggerated 'e' in this text.";
ps_show_boxed($ps, $str4, 10, 10, 80, 80, "justify", NULL);
ps_setlinewidth($ps, 0.2);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 10, 90);
ps_stroke($ps);
ps_moveto($ps, 90, 10);
ps_lineto($ps, 90, 90);
ps_stroke($ps);

end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Encoding", $psfont);
$corkfont = ps_findfont($ps, "D:/xampp/htdocs/xampp/external/ps/Helvetica", "", 0);
$builtinfont = ps_findfont($ps, "D:/xampp/htdocs/xampp/external/ps/Helvetica", "builtin", 0);
ps_setfont($ps, $corkfont, 9.0);
ps_show_xy($ps, "cork encoding äüöß", 10, 10);
ps_setfont($ps, $builtinfont, 9.0);
ps_show_xy($ps, "builtin encoding äüöß", 10, 30);

end_example_box($ps);

$x = 0;
$y -= 150;
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text rise", $psfont);
ps_set_value($ps, "textx", 13);
ps_set_value($ps, "texty", 60);
ps_setfont($ps, $psfont, 18.0);
ps_show($ps, "a");
ps_setfont($ps, $psfont, 12.0);
ps_set_value($ps, "textrise", 6);
ps_show($ps, "2");
ps_setfont($ps, $psfont, 18.0);
ps_set_value($ps, "textrise", 0);
ps_show($ps, "+b");
ps_setfont($ps, $psfont, 12.0);
ps_set_value($ps, "textrise", 6);
ps_show($ps, "2");
ps_setfont($ps, $psfont, 18.0);
ps_set_value($ps, "textrise", 0);
ps_show($ps, "=c");
ps_setfont($ps, $psfont, 12.0);
ps_set_value($ps, "textrise", 6);
ps_show($ps, "2");
ps_set_value($ps, "textrise", 0);

ps_set_value($ps, "textx", 13);
ps_set_value($ps, "texty", 30);
ps_setfont($ps, $psfont, 14.0);
ps_show($ps, "some");
ps_setfont($ps, $psfont, 9.0);
ps_set_value($ps, "textrise", 6);
ps_show($ps, "1)");
ps_set_value($ps, "textrise", 0);
ps_setfont($ps, $psfont, 14.0);
ps_show($ps, " Text");
ps_setfont($ps, $psfont, 9.0);
ps_show_xy($ps, "1) and a footnote", 14, 10);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Strange Text", $psfont);
ps_set_value($ps, "textx", 10);
ps_set_value($ps, "texty", 70);
ps_setfont($ps, $psfont, 14.0);
ps_show($ps, "ÅÊÜß¹²³Æ½");
ps_set_value($ps, "textx", 10);
ps_set_value($ps, "texty", 53);
ps_show($ps, "¢¡¶§×Ç");
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Var. box length", $psfont);
ps_setfont($ps, $psfont, 5.0);
ps_set_value($ps, "leading", 7);
$str6 = "Text can be put into a box of any size. This one has a height of 0 pixels, which makes it height as needed to contain all text.";
ps_show_boxed($ps, $str6, 10, 90, 80, 0.0, "left");
$texty = ps_get_value($ps, "texty", 0.0);
$boxheight = ps_get_value($ps, "boxheight", 0.0);
printf("texty = %f\n", $texty);
printf("boxheight = %f\n", $boxheight);
ps_save($ps);
ps_setlinewidth($ps, 0.2);
ps_moveto($ps, 10, $texty);
ps_lineto($ps, 90, $texty);
ps_stroke($ps);
ps_restore($ps);
ps_show_boxed($ps, $str6, 10, 90-$boxheight, 80, 0.0, "left", NULL);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Blind box", $psfont);
ps_setfont($ps, $psfont, 5.0);
ps_set_value($ps, "leading", 7);
ps_show_boxed($ps, $str6, 10, 90, 80, 0.0, "left", "blind");
$texty = ps_get_value($ps, "texty", 0.0);
$boxheight = ps_get_value($ps, "boxheight", 0.0);
printf("texty = %f\n", $texty);
printf("boxheight = %f\n", $boxheight);
ps_save($ps);
ps_setlinewidth($ps, 0.2);
ps_rect($ps, 10, 90-$boxheight, 80, $boxheight);
ps_stroke($ps);
ps_restore($ps);
ps_show_boxed($ps, $str6, 10, 90-$boxheight, 80, 0.0, "left");
end_example_box($ps);

$x = 0;
$y -= 150;
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Templates", $psfont);
ps_place_image($ps, $pstemplate, 20.0, 20.0, 1.0);
ps_place_image($ps, $pstemplate, 50.0, 30.0, 0.5);
ps_place_image($ps, $pstemplate, 70.0, 70.0, 0.6);
ps_place_image($ps, $pstemplate, 30.0, 50.0, 1.3);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Patterns", $psfont);

ps_setcolor($ps, "both", "pattern", $pspattern1, 0.0, 0.0, 0.0);
ps_rect($ps, 10, 10, 35, 35);
ps_fill($ps);
ps_setlinewidth($ps, 10);
ps_setcolor($ps, "stroke", "gray", 0.5, 0.0, 0.0, 0.0);
ps_moveto($ps, 15, 55);
ps_lineto($ps, 40, 90);
ps_stroke($ps);
ps_setcolor($ps, "stroke", "pattern", $pspattern1, 0.0, 0.0, 0.0);
ps_moveto($ps, 15, 55);
ps_lineto($ps, 40, 90);
ps_stroke($ps);
ps_setcolor($ps, "both", "pattern", $pspattern1, 0.0, 0.0, 0.0);
ps_setfont($ps, $psfont, 65.0);
ps_show_xy($ps, "A", 50, 10);
ps_setcolor($ps, "both", "rgb", 1.0, 0.0, 0.0, 0.0);
ps_setcolor($ps, "fill", "pattern", $pspattern2, 0.0, 0.0, 0.0);
ps_rect($ps, 50, 50, 35, 35);
ps_fill($ps);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Graphic States", $psfont);
ps_setcolor($ps, "stroke", "rgb", 1.0, 0.0, 0.0, 0.0);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 90, 10);
ps_stroke($ps);
ps_save($ps);
ps_setcolor($ps, "stroke", "rgb", 0.0, 1.0, 0.0, 0.0);
ps_moveto($ps, 10, 20);
ps_lineto($ps, 90, 20);
ps_stroke($ps);
ps_save($ps);
ps_setlinewidth($ps, 3);
ps_moveto($ps, 10, 30);
ps_lineto($ps, 90, 30);
ps_stroke($ps);
ps_restore($ps);
ps_moveto($ps, 10, 40);
ps_lineto($ps, 90, 40);
ps_stroke($ps);
ps_restore($ps);
ps_moveto($ps, 10, 50);
ps_lineto($ps, 90, 50);
ps_stroke($ps);
end_example_box($ps);

begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "JPG-Image", $psfont);
$psimage = ps_open_image_file($ps, "jpeg", "D:/xampp/htdocs/xampp/external/ps/exiftest.jpg", NULL, 0);
ps_place_image($ps, $psimage, 10, 10, 0.2);
ps_close_image($ps, $psimage);
end_example_box($ps);

ps_end_page($ps); 
*/
ps_close($ps);
ps_delete($ps);

?>
