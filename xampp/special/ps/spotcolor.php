<?php
define(LEFT_BORDER, 50);

function footer($p, $text) {
	$psfont = PS_findfont($p, "Helvetica", "", 0);
	PS_setfont($p, $psfont, 8.0);
	$buffer = sprintf("This file has been created with pslib %s", PS_get_parameter($p, "dottedversion", 0.0));
	PS_show_xy($p, $buffer, LEFT_BORDER, 25);
}

function colorline($ps, $leftborder, $spot) {
	for($i=1; $i<=10; $i++) {
		PS_setcolor($ps, "fill", "spot", $spot["id"], $i*0.1, 0.0, 0.0);
		PS_rect($ps, $leftborder, 35+$i*65, 50, 50);
		PS_fill($ps);
	}
	PS_setcolor($ps, "stroke", "gray", 0.0, 0.0, 0.0, 0.0);
	$psfont = PS_findfont($ps, "Helvetica", "", 0);
	PS_setfont($ps, $psfont, 7.0);
	PS_show_xy($ps, $spot["name"], $leftborder, 100+10*65+13);
	if(!strcmp($spot["colorspace"], "cmyk")) {
		$buffer = sprintf("%.2f, %.2f, %.2f, %.2f", $spot["c1"], $spot["c2"], $spot["c3"], $spot["c4"]);
		PS_show_xy($ps, $buffer, $leftborder, 100+10*65+3);
	} else if(!strcmp($spot["colorspace"], "rgb")) {
		$buffer = sprintf("%.2f, %.2f, %.2f", $spot["c1"], $spot["c2"], $spot["c3"]);
		PS_show_xy($ps, $buffer, $leftborder, 100+10*65+3);
	}
}

	$spotcolors[] = array("id"=>0, "name"=>"PANTONE Violet C", "colorspace"=>"cmyk", "c1"=>0.75, "c2"=>0.94, "c3"=>0.0, "c4"=>0.0);
	$spotcolors[] = array("id"=>0, "name"=>"PANTONE 114 C", "colorspace"=>"cmyk", "c1"=>0.0, "c2"=>0.11, "c3"=>0.69, "c4"=>0.0);
	$spotcolors[] = array("id"=>0, "name"=>"PANTONE 5565 C", "colorspace"=>"cmyk", "c1"=>0.37, "c2"=>0.0, "c3"=>0.34, "c4"=>0.34);
	$spotcolors[] = array("id"=>0, "name"=>"RGB Blue", "colorspace"=>"rgb", "c1"=>0.0, "c2"=>0.0, "c3"=>1.0, "c4"=>0.0);
	$spotcolors[] = array("id"=>0, "name"=>"Gray Black", "colorspace"=>"gray", "c1"=>0.0, "c2"=>0.0, "c3"=>0.0, "c4"=>0.0);

	$ps = PS_new();

	if (0 > PS_open_file($ps, "spotcolor.ps")) {
		printf("Cannot open PostScript file\n");
		exit(1);
	}

	PS_set_parameter($ps, "warning", "true");

	PS_set_info($ps, "Creator", __FILE__);
	PS_set_info($ps, "Author", "Uwe Steinmann");
	PS_set_info($ps, "Title", "Spotcolor demonstration");
	PS_set_info($ps, "Keywords", "Spot color");

	for($i=0; $i<5; $i++) {
		PS_setcolor($ps, "fill", $spotcolors[$i]["colorspace"], $spotcolors[$i]["c1"], $spotcolors[$i]["c2"], $spotcolors[$i]["c3"], $spotcolors[$i]["c4"]);
		$spotcolors[$i]["id"] = PS_makespotcolor($ps, $spotcolors[$i]["name"], 0);
	}

	PS_begin_page($ps, 596, 842);
	footer($ps, "");

	$psfont = PS_findfont($ps, "Helvetica", "", 0);
	PS_setfont($ps, $psfont, 7.0);
	for($i=1; $i<=10; $i++) {
		$buffer = sprintf("%d %%", $i*10);
		PS_show_xy($ps, $buffer, 60, 55+$i*65);
	}

	colorline($ps, 100.0, $spotcolors[0]);
	colorline($ps, 190.0, $spotcolors[1]);
	colorline($ps, 280.0, $spotcolors[2]);
	colorline($ps, 370.0, $spotcolors[3]);
	colorline($ps, 460.0, $spotcolors[4]);

	PS_end_page($ps);

	PS_close($ps);
	PS_delete($ps);

	exit(0);
?>
