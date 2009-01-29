<?php

define("LEFT_BORDER", 50);
define("EXAMPLE_BOX_HEIGHT", 190);
define("EXAMPLE_BOX_WIDTH", 150);
define("EXAMPLE_BOX_TITLE_HEIGHT", 40);

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

if (!ps_open_file($ps, "D:/xampp/htdocs/xampp/external/ps/image.ps")) {
	print "Cannot open PostScript file\n";
	exit;
}

ps_set_parameter($ps, "warning", "true");

ps_set_info($ps, "Creator", "image.php");
ps_set_info($ps, "Author", "Uwe Steinmann");
ps_set_info($ps, "Title", "Image examples");

ps_begin_page($ps, 596, 842);
$psfont = ps_findfont($ps, "D:/xampp/htdocs/xampp/external/ps/Helvetica", "", 0);
ps_setfont($ps, $psfont, 12.0);

	$x = 0;
	$y = 625;
	begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Indexed image without alpha channel", $psfont);
	$psimage = ps_open_image_file($ps, "png", "indexed.png", NULL, 0);
	ps_place_image($ps, $psimage, 10, 30, 1.0);
	$buffer = sprintf("%.0f x %.0f pixel", ps_get_value($ps, "imagewidth", $psimage), ps_get_value($ps, "imageheight", $psimage));
	ps_setfont($ps, $psfont, 10.0);
	ps_show_xy($ps, $buffer, EXAMPLE_BOX_WIDTH-10-ps_stringwidth($ps, $buffer, $psfont, 10), 10);
	end_example_box($ps);

	begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "RGB image without alpha channel", $psfont);
	$psimage = ps_open_image_file($ps, "png", "rgb.png", NULL, 0);
	ps_place_image($ps, $psimage, 10, 30, 1.0);
	end_example_box($ps);

	begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Indexed image with alpha channel", $psfont);
	$psimage = ps_open_image_file($ps, "png", "indexed-alpha.png", NULL, 0);
	ps_place_image($ps, $psimage, 10, 30, 1.0);
	end_example_box($ps);

	$x = 0;
	$y = 405;
	begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "RGB image with alpha channel", $psfont);
	$psimage = ps_open_image_file($ps, "png", "rgb-alpha.png", NULL, 0);
	ps_place_image($ps, $psimage, 10, 30, 1.0);
	end_example_box($ps);

	begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Jpeg gray scale image", $psfont);
	$psimage = ps_open_image_file($ps, "jpeg", "gnu-head.jpg", NULL, 0);
	ps_place_image($ps, $psimage, 10, 20, 0.45);
	$buffer = sprintf("%.0f x %.0f pixel", ps_get_value($ps, "imagewidth", $psimage), ps_get_value($ps, "imageheight", $psimage));
	ps_setfont($ps, $psfont, 10.0);
	ps_show_xy($ps, $buffer, EXAMPLE_BOX_WIDTH-10-ps_stringwidth($ps, $buffer, $psfont, 10), 10);
	end_example_box($ps);

	begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "", $psfont);
	$psimage = ps_open_image_file($ps, "jpeg", "exiftest.jpg", NULL, 0);
	ps_place_image($ps, $psimage, 30, 10, 0.30);
	end_example_box($ps);

	$x = 0;
	$y = 185;
	begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Images created in memory", $psfont);
	$imagedata = "01234567890123456";
	/* RGB */
	for($i=0; $i<16; $i++)
		$imagedata[$i] = chr(0);
	$imagedata[0] = chr(255);
	$imagedata[4] = chr(255);
	$imagedata[8] = chr(255);
	$psimage = ps_open_image($ps, "memory", "memory", $imagedata, 12, 2, 2, 3, 8, NULL);
	ps_place_image($ps, $psimage, 20, 20, 20.0);
	/* Gray */
	for($i=0; $i<16; $i++)
		$imagedata[$i] = chr(0);
	$imagedata[0] = chr(192);
	$imagedata[1] = chr(128);
	$imagedata[2] = chr(64);
	$imagedata[3] = chr(0);
	$psimage = ps_open_image($ps, "memory", "memory", $imagedata, 4, 2, 2, 1, 8, NULL);
	ps_place_image($ps, $psimage, 90, 20, 20.0);
	/* CMYK */
	for($i=0; $i<16; $i++)
		$imagedata[$i] = chr(255);
	$imagedata[0] = chr(0);
	$imagedata[5] = chr(0);
	$imagedata[10] = chr(0);
	$imagedata[15] = chr(0);
	$psimage = ps_open_image($ps, "memory", "memory", $imagedata, 16, 2, 2, 4, 8, NULL);
	ps_place_image($ps, $psimage, 20, 90, 20.0);
	ps_setfont($ps, $psfont, 8.0);
	ps_show_xy($ps, "CMYK", 20, 80);
	ps_show_xy($ps, "RGB", 20, 10);
	ps_show_xy($ps, "Gray", 90, 10);

	end_example_box($ps);

	begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Jpeg cmyk image, rotated", $psfont);
	$psimage = ps_open_image_file($ps, "jpeg", "cne-cmyk.jpg", NULL, 0);
	ps_save($ps);
	ps_translate($ps, 72, 10);
	ps_rotate($ps, 45);
	ps_place_image($ps, $psimage, 0, 0, 0.45);
	ps_restore($ps);
	$buffer = sprintf("%.0f x %.0f pixel", ps_get_value($ps, "imagewidth", $psimage), ps_get_value($ps, "imageheight", $psimage));
	ps_setfont($ps, $psfont, 10.0);
	ps_show_xy($ps, $buffer, EXAMPLE_BOX_WIDTH-10-ps_stringwidth($ps, $buffer, $psfont, 10), 10);
	end_example_box($ps);

	begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "EPS read from memory", $psfont);
	$data = file_get_contents("picture.eps");	
	$psimage = ps_open_image($ps, "eps", "memory", $data, strlen($data), 0, 0, 0, 0, NULL);
	ps_place_image($ps, $psimage, 15, 25, 0.45);
	$buffer = sprintf("%.0f x %.0f pixel", ps_get_value($ps, "imagewidth", $psimage), ps_get_value($ps, "imageheight", $psimage));
	ps_setfont($ps, $psfont, 10.0);
	ps_show_xy($ps, $buffer, EXAMPLE_BOX_WIDTH-10-ps_stringwidth($ps, $buffer, $psfont, 10), 10);
	end_example_box($ps);

ps_end_page($ps);

ps_begin_page($ps, 596, 842);
$psfont = ps_findfont($ps, "Helvetica", "", 0);
ps_setfont($ps, $psfont, 12.0);

	$x = 0;
	$y = 625;
	begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Gif image", $psfont);
	$psimage = ps_open_image_file($ps, "gif", "debian.gif", NULL, 0);
	ps_place_image($ps, $psimage, 25, 10, 2.0);
	end_example_box($ps);

	begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Gif image with transparency", $psfont);
	$psimage = ps_open_image_file($ps, "gif", "debian-transparent.gif", NULL, 0);
	ps_place_image($ps, $psimage, 25, 10, 2.0);
	end_example_box($ps);

	begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Gif image interlaced", $psfont);
	$psimage = ps_open_image_file($ps, "gif", "interlaced.gif", NULL, 0);
	ps_place_image($ps, $psimage, 10, 10, 0.65);
	end_example_box($ps);

	$x = 0;
	$y = 405;
	begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Tiff image", $psfont);
	$psimage = ps_open_image_file($ps, "tiff", "debian.tiff", NULL, 0);
	ps_place_image($ps, $psimage, 25, 10, 2.0);
	end_example_box($ps);

	$x = 0;
	$y = 185;
	begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Bmp image", $psfont);
	$psimage = ps_open_image_file($ps, "bmp", "debian.bmp", NULL, 0);
	ps_place_image($ps, $psimage, 25, 10, 2.0);
	end_example_box($ps);

ps_end_page($ps);

ps_close($ps);
ps_delete($ps);
?>
