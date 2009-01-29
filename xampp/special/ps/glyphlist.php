<?php

function usage() {
	printf("Usage: glyphlist <list of fontnames>\n\n");
}

$ps = ps_new();

if (!ps_open_file($ps, "glyphlist.ps")) {
	print "Cannot open PostScript file\n";
	exit;
}


ps_set_info($ps, "Creator", "glyphlist.php");
ps_set_info($ps, "Author", "Uwe Steinmann");
ps_set_info($ps, "Title", "List of glyphs");
ps_set_info($ps, "Keywords", "glyph, font");
ps_set_info($ps, "BoundingBox", "0 0 596 842");

$colwidth = 30;
$rowheight = 30;
$leftmargin = 70;
$bottommargin = 270;
$fontsize = 10.0;
$fontsamplesize = 14.0;

if($_SERVER["argc"] < 2) {
	usage();
	exit(1);
}

$psfont = ps_findfont($ps, "Helvetica", "", 0);
printf("Creating %d pages\n", $_SERVER["argc"]-1);
for($k=1; $k<$_SERVER["argc"]; $k++) {
	printf("Creating page for %s\n", $_SERVER["argv"][$k]);

	$samplefont = ps_findfont($ps, $_SERVER["argv"][$k], "", 1);

	if($glyphlist = ps_glyph_list($ps, $samplefont)) {
		$listlen = count($glyphlist);
		printf("Font has %d glyphs\n", $listlen);
		$pages = (int) ($listlen / 256) + 1;

		$glc = 0;
		for($p=0; $p<$pages; $p++) {
			ps_begin_page($ps, 596, 842);
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
			ps_setlinewidth($ps, 0.2);
			for($j=0; $j<16; $j++) {
				for($i=0; $i<16; $i++) {
					if($glc+($j*16+$i) < $listlen) {
						$textwidth = ps_glyph_width($ps, $glyphlist[$glc+($j*16+$i)], 0, $fontsamplesize);
						ps_set_value($ps, "textx", $leftmargin+$i*$colwidth+$colwidth/2-$textwidth/2);
						ps_set_value($ps, "texty", $bottommargin+(15-$j)*$rowheight+$rowheight/2+3-$fontsize/2);
						ps_glyph_show($ps, $glyphlist[$glc+($j*16+$i)]);
						ps_moveto($ps, $leftmargin+$i*$colwidth+$colwidth/2-$textwidth/2, $bottommargin+(15-$j)*$rowheight+$rowheight/2+3-$fontsize/2);
						ps_lineto($ps, $leftmargin+$i*$colwidth+$colwidth/2+$textwidth/2, $bottommargin+(15-$j)*$rowheight+$rowheight/2+3-$fontsize/2);
						ps_stroke($ps);
					}
				}
			}

			/* The name of the symbol */
			ps_setfont($ps, $psfont, 3);
			for($j=0; $j<16; $j++) {
				for($i=0; $i<16; $i++) {
					if($glc+($j*16+$i) < $listlen) {
						ps_set_value($ps, "textx", $leftmargin+$i*$colwidth+2);
						ps_set_value($ps, "texty", $bottommargin+(15-$j)*$rowheight+2);
						ps_show($ps, $glyphlist[$glc+($j*16+$i)]);
					}
				}
			}
			ps_end_page($ps);
			$glc += 256;
		}
	}
}
ps_close($ps);
ps_delete($ps);
?>
