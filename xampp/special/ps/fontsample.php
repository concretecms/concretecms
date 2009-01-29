<?php

function usage() {
	printf("Usage: fontsample <list of fontnames>\n\n");
}

$ps = ps_new();

if (!ps_open_file($ps, "fontsample.ps")) {
	print "Cannot open PostScript file\n";
	exit;
}

ps_set_parameter($ps, "warning", "true");

ps_set_info($ps, "Creator", "fontsample.php");
ps_set_info($ps, "Author", "Uwe Steinmann");
ps_set_info($ps, "Title", "Font sample");
ps_set_info($ps, "BoundingBox", "0 0 596 842");

$colwidth = 25;
$rowheight = 25;
$leftmargin = 100;
$bottommargin = 270;
$fontsize = 10.0;
$fontsamplesize = 12.0;

if($_SERVER["argc"] < 2) {
	usage();
	exit(1);
}

ps_set_parameter($ps, "hyphenation", "true");
ps_set_parameter($ps, "hyphendict", "hyph_en.dic");

$psfont = ps_findfont($ps, "Helvetica", "", 0);
printf("Creating %d pages\n", $_SERVER["argc"]-1);
for($k=1; $k<$_SERVER["argc"]; $k++) {
	printf("Creating page for %s\n", $_SERVER["argv"][$k]);

	ps_begin_page($ps, 596, 842);
	$samplefont = ps_findfont($ps, $_SERVER["argv"][$k], "", 1);

	ps_setfont($ps, $psfont, 15.0);
	$text = sprintf("Fontsample for '%s'", ps_get_parameter($ps, "fontname", $samplefont));
	ps_show_xy($ps, $text, $leftmargin, $bottommargin+20*$rowheight);
	ps_setfont($ps, $psfont, 10.0);
	ps_show_xy($ps, "Font encoding: ", $leftmargin, $bottommargin+19.0*$rowheight);
	ps_show($ps, ps_get_parameter($ps, "fontencoding", $samplefont));
	ps_show_xy($ps, "capheight: ", $leftmargin, $bottommargin+18.5*$rowheight);
	$text = sprintf("%.2f", ps_get_value($ps, "capheight", $samplefont));
	ps_show($ps, $text);
	ps_show_xy($ps, "ascender: ", $leftmargin, $bottommargin+18.0*$rowheight);
	$text = sprintf("%.2f", ps_get_value($ps, "ascender", $samplefont));
	ps_show($ps, $text);
	ps_show_xy($ps, "descender: ", $leftmargin, $bottommargin+17.5*$rowheight);
	$text = sprintf("%.2f", ps_get_value($ps, "descender", $samplefont));
	ps_show($ps, $text);

	for($i=0; $i<=16; $i++) {
		ps_moveto($ps, $leftmargin+$i*$colwidth, $bottommargin);
		ps_lineto($ps, $leftmargin+$i*$colwidth, $bottommargin+16*$rowheight);
		ps_stroke($ps);
	}
	for($j=0; $j<=16; $j++) {
		ps_moveto($ps, $leftmargin, $bottommargin+$j*$rowheight);
		ps_lineto($ps, $leftmargin+16*$colwidth, $bottommargin+$j*$rowheight);
		ps_stroke($ps);
	}

	ps_setfont($ps, $psfont, $fontsize);
	for($i=0; $i<16; $i++) {
		$text = sprintf("x%X", $i);
		$textwidth = ps_stringwidth($ps, $text,  $psfont, $fontsize);
		ps_show_xy($ps, $text, $leftmargin+$i*$colwidth+$colwidth/2-$textwidth/2, $bottommargin+16*$rowheight+$fontsize/2);
	}
	for($j=0; $j<16; $j++) {
		$text = sprintf("%Xx", $j);
		ps_show_xy($ps, $text, $leftmargin-1.7*$fontsize, $bottommargin+(15-$j)*$rowheight+$rowheight/2-$fontsize/2);
	}

	/* The symbol itself */
	ps_setfont($ps, $samplefont, $fontsamplesize);
	ps_setlinewidth($ps, 0.4);
	for($j=0; $j<16; $j++) {
		for($i=0; $i<16; $i++) {
			$textwidth = ps_symbol_width($ps, $j*16+$i, $psfont, $fontsize);
			ps_set_value($ps, "textx", $leftmargin+$i*$colwidth+$colwidth/2-$textwidth/2);
			ps_set_value($ps, "texty", $bottommargin+(15-$j)*$rowheight+$rowheight/2+3-$fontsize/2);
			//ps_show_xy($ps, $text, $leftmargin+$i*$colwidth+$colwidth/2-$textwidth/2, $bottommargin+(15-$j)*$rowheight+$rowheight/2-$fontsize/2);
//			ps_show($ps, $text);
			ps_symbol($ps, $j*16+$i);
			ps_moveto($ps, $leftmargin+$i*$colwidth+$colwidth/2-$textwidth/2, $bottommargin+(15-$j)*$rowheight+$rowheight/2+3-$fontsize/2);
			ps_lineto($ps, $leftmargin+$i*$colwidth+$colwidth/2+$textwidth/2, $bottommargin+(15-$j)*$rowheight+$rowheight/2+3-$fontsize/2);
			ps_stroke($ps);
		}
	}

	/* The name of the symbol */
	ps_setfont($ps, $psfont, 3);
	for($j=0; $j<16; $j++) {
		for($i=0; $i<16; $i++) {
			$text = ps_symbol_name($ps, $j*16+$i, $samplefont);
			ps_set_value($ps, "textx", $leftmargin+$i*$colwidth+2);
			ps_set_value($ps, "texty", $bottommargin+(15-$j)*$rowheight+2);
			ps_show($ps, $text);
			$text = sprintf(" (%d)", $j*16+$i);
			ps_show($ps, $text);
		}
	}

	$text = "This fontsample matrix is created with the ps_symbol() function. It prints a glyph by its decimal value in the font encoding vector. pslib can use the TeXBase1 encoding (default) or the encoding shipped with the font itself, which is usually AdobeStandardEncoding. The above matrix uses the encoding of the font which is used when the parameter 'encoding' of ps_findfont() is set to 'builtin'. This usually results in a subset of all available glyphs of the font.\n\nQuite a lot of glyphs cannot be reached without the ps_symbol() function. The reason is simple: If text is output with ps_show(), then each character will be first mapped to a glyph by applying the ISO-8859-1 input encoding on it. Since ISO-8859-1 has less characters then a font usually provides glyphs, there will be some glyphs not reachable with regular text.";
	ps_setfont($ps, $psfont, 8);
	ps_set_value($ps, "leading", 12.0);
	ps_set_value($ps, "parindent", 12.0);
	ps_show_boxed($ps, $text, $leftmargin, $bottommargin-250, $colwidth*7.5, 230, "fulljustify", NULL);

	ps_setfont($ps, $samplefont, 8);
	ps_set_value($ps, "leading", 12.0);
	ps_set_value($ps, "parindent", 12.0);
	ps_show_boxed($ps, $text, $leftmargin+$colwidth*8.5, $bottommargin-250, $colwidth*7.5, 230, "fulljustify", NULL);

	ps_end_page($ps);
}

ps_close($ps);
ps_delete($ps);
?>

