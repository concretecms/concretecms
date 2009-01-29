<?php
$ps = ps_new();
if (!ps_open_file($ps, "D:/xampp/htdocs/xampp/external/ps/text.ps")) {
    print "Cannot open PostScript file\n";
    exit;
}

ps_set_parameter($ps, "warning", "false");

ps_set_info($ps, "Creator", "text.php");
ps_set_info($ps, "Author", "Uwe Steinmann");
ps_set_info($ps, "Title", "Text output");
ps_set_info($ps, "BoundingBox", "0 0 596 842");
ps_set_info($ps, "Orientation", "Portrait");

if(!$font = ps_findfont($ps, "Dustismo", "builtin", 1)) {
	echo "Could not find font.\n";
	exit;
}
ps_begin_page($ps, 596, 842);
ps_setfont($ps, $font, 14.0);
ps_show_xy($ps, "Text starting at position (100, 700) with kerning", 100, 700);
ps_set_parameter($ps, "kerning", "false");
ps_show_xy($ps, "Text starting at position (100, 670) without kerning", 100, 670);
ps_set_parameter($ps, "kerning", "true");
ps_set_parameter($ps, "underline", "true");
ps_show_xy($ps, "Text starting at position (100, 640) with underlining", 100, 640);
ps_set_parameter($ps, "underline", "false");
ps_set_parameter($ps, "overline", "true");
ps_show_xy($ps, "Text starting at position (100, 610) with overlining", 100, 610);
ps_set_parameter($ps, "overline", "false");
ps_set_parameter($ps, "strikeout", "true");
ps_show_xy($ps, "Text starting at position (100, 580) striked out", 100, 580);
ps_set_parameter($ps, "strikeout", "false");

ps_show_xy($ps, "Text with some ligatures ffi fi fl ff", 100, 550);

ps_set_parameter($ps, "ligatures", "false");
ps_show_xy($ps, "Text without ligatures ffi fi fl ff", 100, 520);
ps_set_parameter($ps, "ligatures", "true");

ps_set_value($ps, "charspacing", 2.0);
ps_show_xy($ps, "Text with char spacing set to 2.0", 100, 490);
ps_set_value($ps, "charspacing", 0.0);
ps_show_xy($ps, "Text with char spacing set to 0.0", 100, 460);
ps_set_value($ps, "charspacing", -2.0);
ps_show_xy($ps, "Text with char spacing set to -2.0", 100, 430);
ps_set_value($ps, "charspacing", 0.0);
ps_show_xy($ps, "Some text which will be continued ...", 100, 400);
ps_continue_text($ps, "... in the next line ...");
ps_set_value($ps, "leading", 30);
ps_continue_text($ps, "... and another one with leading set to 30.");

ps_setcolor($ps, "both", "rgb", 1.0, 0.0, 0.0, 0.0);
ps_show_xy($ps, "Text can be written in color.", 100, 340);

ps_end_page($ps);

ps_begin_page($ps, 596, 842);
ps_setfont($ps, $font, 14.0);
ps_set_value($ps, "leading", 16);
ps_show_boxed($ps, "Text can be put into a box. It will automatically line wrapped. The text can left or right justified or centered. The given coordinates specify the lower left corner.", 100, 550, 100, 200, "left");
ps_show_boxed($ps, "Text can be put into a box. It will automatically line wrapped. The text can left or right justified or centered. The given coordinates specify the lower left corner.", 235, 550, 100, 200, "center");
ps_show_boxed($ps, "Text can be put into a box. It will automatically line wrapped. The text can left or right justified or centered. The given coordinates specify the lower left corner.", 370, 550, 100, 200, "right");
ps_set_value($ps, "leading", 30);
$text = "The line spacing (leading) can be set as on page 1. If the text does not fit into the box, it will be cut off. The string length of the remaining text is returned. This makes it quite easy to continue the remaining text in a second, third, fourth etc. box.";
$len = ps_show_boxed($ps, $text, 100, 320, 100, 200, "justify");
$len = ps_show_boxed($ps, substr($text, strlen($text)-$len), 235, 320, 100, 200, "justify");
$len = ps_show_boxed($ps, substr($text, strlen($text)-$len), 370, 320, 100, 200, "justify");

ps_set_value($ps, "leading", 20);
ps_set_parameter($ps, "hyphenation", "true");
ps_set_parameter($ps, "hyphendict", "D:/xampp/htdocs/xampp/external/ps/hyph_en.dic");
ps_set_value($ps, "hyphenminchars", 2);
$text = "You wonder if hyphenation is supported? Yes, of course and it uses the hyphenation files from scribus. Scribus provides hyphenation dictionaries for 14 languages. Another nice feature is Protution. This means that certain characters reach over the left or right margin. This is very useful for hyphens or punctuation to make straight looking margins like the one in the first column (it is very exaggerated).";
ps_set_parameter($ps, "RightMarginKerning", "hyphen=1000");
$len = ps_show_boxed($ps, $text, 100, 90, 100, 200, "justify");
$len = ps_show_boxed($ps, substr($text, strlen($text)-$len), 235, 90, 100, 200, "right");
$len = ps_show_boxed($ps, substr($text, strlen($text)-$len), 370, 90, 100, 200, "left");
ps_end_page($ps);

ps_close($ps);
ps_delete($ps);
?>
