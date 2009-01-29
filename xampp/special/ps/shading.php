<?php
define(LEFT_BORDER, 50);

function footer($p, $text) {
	$psfont = PS_findfont($p, "Helvetica", "", 0);
	PS_setfont($p, $psfont, 8.0);
	$buffer = sprintf("This file has been created with pslib %s", PS_get_parameter($p, "dottedversion", 0.0));
	PS_show_xy($p, $buffer, LEFT_BORDER, 25);
}

	$ps = PS_new();

	if (0 > PS_open_file($ps, "shading.ps")) {
		printf("Cannot open PostScript file\n");
		exit(1);
	}

	PS_set_parameter($ps, "warning", "false");
	PS_set_value($ps, "parskip", 10);

	PS_set_info($ps, "Creator", __FILE__);
	PS_set_info($ps, "Author", "Uwe Steinmann");
	PS_set_info($ps, "Title", "Shading Examples");
	PS_set_info($ps, "Keywords", "shading");

	$antiqua = PS_findfont($ps, "Helvetica", "", 0);

	/* Page 2 */
	PS_begin_page($ps, 596, 842);
	footer($ps, "");

	$shading1 = PS_shading($ps, "axial", 0.0, 0.0, 90.0, 0.0, 1.0, 0.0, 0.0, 0.0, NULL);
	$shading2 = PS_shading($ps, "axial", 10.0, 10.0, 90.0, 90.0, 1.0, 0.0, 0.0, 0.0, NULL);
	$shading3 = PS_shading($ps, "axial", 10.0, 10.0, 90.0, 90.0, 1.0, 0.0, 0.0, 0.0, "extend0 true extend1 true");
	$shading4 = PS_shading($ps, "radial", 29.0, 29.0, 55.0, 55.0, 1.0, 0.0, 0.0, 0.0, "r0 0 r1 45");
	$shading5 = PS_shading($ps, "radial", 29.0, 29.0, 55.0, 55.0, 1.0, 0.0, 0.0, 0.0, "r0 5 r1 45");
	PS_save($ps);
	PS_translate($ps, 500, 0);
	PS_shfill($ps, $shading1);
	PS_restore($ps);

	PS_setfont($ps, $antiqua, 20.0);
	PS_show_xy($ps, "Shading", LEFT_BORDER, 763);
	PS_setfont($ps, $antiqua, 10.0);
	PS_set_value($ps, "leading", 15.0);
	PS_show_boxed($ps, "pslib supports a PostScript level 3 feature called shading. A shading starts at a certain point on the page with a given color and ends at a second point with different color. The type of shading can be 'axial' or 'radial'. Shadings are used for many purposes like shadows, three dimensional appearance or simply to make a nice background for a diagramm.\n\nThe bar's gradient fill to the right of this page starts at (500, 0) and ends at (590, 0). It extends over the whole page in y direction which is normal. I you want to restrict the gradient fill to a given area, one will have to apply PS_clip() on a path before calling PS_shfill() (see Shading 1).\n\nA grandient fill can easily be drawn in an angle if the start and end position have different x and y coordinates like in Shading 2.\n\nAs you can see at the bar, the shading extends orthogonal to the drawing direction but does not extend in the drawing direction. This behaviour can also be altered by the two parameters 'extend0' and 'extend1'. Both are passed as part of the option list. Setting them to 'true' will extend the gradient fill towards the drawing direction with the color where it left off or starts (see Shading 3).\n\nThe second class of gradient fills are of type 'radial'. They start at one circle and grow/shrink towards a second circle. The coordinates mentioned above now specify the middle points of the circles. The radi of both circles are passed again within the option list, named 'r0' and 'r1' (see Shading 4).\n\nThe previous example starts with a circle whose radius is 0. Making this a value greater 0 will punch a whole into the gradient fill.", LEFT_BORDER, 170, 260, 580, "left", NULL);

	PS_save($ps);
	PS_translate($ps, 350, 680);
	PS_show_xy($ps, "Shading 1", 0, -12);
	PS_rect($ps, 0, 0, 100, 100);
	PS_clip($ps);
	PS_shfill($ps, $shading1);
	PS_restore($ps);

	PS_save($ps);
	PS_translate($ps, 350, 550);
	PS_show_xy($ps, "Shading 2", 0, -12);
	PS_rect($ps, 0, 0, 100, 100);
	PS_clip($ps);
	PS_shfill($ps, $shading2);
	PS_restore($ps);

	PS_save($ps);
	PS_translate($ps, 350, 420);
	PS_show_xy($ps, "Shading 3", 0, -12);
	PS_rect($ps, 0, 0, 100, 100);
	PS_clip($ps);
	PS_shfill($ps, $shading3);
	PS_restore($ps);

	PS_save($ps);
	PS_translate($ps, 350, 290);
	PS_show_xy($ps, "Shading 4", 0, -12);
	PS_rect($ps, 0, 0, 100, 100);
	PS_clip($ps);
	PS_shfill($ps, $shading4);
	PS_restore($ps);

	PS_save($ps);
	PS_translate($ps, 350, 160);
	PS_show_xy($ps, "Shading 5", 0, -12);
	PS_rect($ps, 0, 0, 100, 100);
	PS_clip($ps);
	PS_shfill($ps, $shading5);
	PS_restore($ps);
	PS_end_page($ps);

	/* Page 2 */
	PS_begin_page($ps, 596, 842);
	PS_setcolor($ps, "fill", "rgb", 1, 1, 1, 0);
	$shading1 = PS_shading($ps, "radial", 100.0, 100.0, 550.0, 550.0, 0.5, 0.0, 0.0, 0.0, "N 1 r0 40 r1 795 extend1 false antialias true");
	PS_setcolor($ps, "fill", "rgb", 0, 0.7, 0, 0);
	$shading2 = PS_shading($ps, "radial", 50.0, 50.0, 50.0, 50.0, 1.0, 1.0, 1.0, 0.0, "N 1 r0 40 r1 0 extend1 false extend0 false antialias true");
	$shading3 = PS_shading($ps, "radial", 50.0, 50.0, 50.0, 50.0, 1.0, 1.0, 1.0, 0.0, "N 1 r0 40 r1 0 extend0 true antialias true");
	PS_setcolor($ps, "fill", "rgb", 0, 0, 1, 0);
	$shading4 = PS_shading($ps, "radial", 50.0, 50.0, 50.0, 50.0, 1.0, 1.0, 0.0, 0.0, "N 1 r0 40 r1 0 extend0 true antialias true");
	$shading5 = PS_shading($ps, "radial", 30.0, 30.0, 53.0, 53.0, 0.0, 0.0, 0.9, 0.0, "N 1 r0 20 r1 35 extend0 false extend1 false");
	PS_setcolor($ps, "fill", "cmyk", 0.37, 0.0, 0.34, 0.34);
	$spotcolor = PS_makespotcolor($ps, "PANTONE 5565 C", 0);
	PS_setcolor($ps, "fill", "spot", (float) $spotcolor, 0.2, 0.0, 0.0);
	$shading6 = PS_shading($ps, "radial", 50.0, 50.0, 50.0, 50.0, (float) $spotcolor, 0.8, 0.0, 0.0, "N 1 r0 40 r1 0 extend1 false extend0 false antialias true");

	PS_shfill($ps, $shading1);

	PS_setcolor($ps, "fill", "rgb", 0, 0, 0.7, 0);
	PS_setfont($ps, $antiqua, 20.0);
	PS_show_xy($ps, "The world of color shading", LEFT_BORDER, 763);
	PS_setfont($ps, $antiqua, 10.0);
	PS_set_value($ps, "leading", 15.0);
	PS_show_boxed($ps, "I suppose it does not really supprise you that gradient fills can be colorful. The first example is the background of this page which is a radial gradient fill from white to red (RGB [0,0,0.7]). Its outer circle has a very large radius of 795 pixels. The inner circle's radius is just 40 pixels. If there was something behind the gradient fill it would shine through the inner circle, because the gradient fill does not extend into that direction. There is a continuation of the red color beyond the outer circle, but that does not make a difference in this case, because its outside of the page and therefore not visible.\n\nShading 1 and Shading 2 illustrate the difference of the extend1 parameter being set to false in Shading 1 or true in Shading 2. Using extend is always a bit dangerous because it easily fills up the whole page, unless you clip the drawing area, which was done in all the examples on this page.\n\nUsing white as the start or end color is quite common but not nessecary. Shading 3 shows that any other color can be used as well.\n\nThe tube in Shading 4 is a bit of a misuse of shadings. It used alsmost identical start and end colors for the shading without any extend parameter set. This only works if the two colors are not identical. If they were identical you would see just the start and circle overlapping, because the gradient function has no domain it can run over. Well, this is anyway not the way to draw tubes.\n\nYou wonder if this all works with CMYK colors? Yes, of course it does! And what about spot colors? No problem either, with one little restriction. The start and end spot color must be same one, but with different tint value. That means that the gradient just changes the tint value but not the color itself. I am not sure if this is an unbearable restriction or not. Shading 5 shows an example of Pantone 5565 C starting at a tint value of 0.2 (the outer circle) and ending with 0.8 (the middle point).", LEFT_BORDER, 170, 260, 580, "left", NULL);

	PS_save($ps);
	PS_translate($ps, 350, 650);
	PS_show_xy($ps, "Shading 1", 0, -12);
	PS_rect($ps, 0, 0, 100, 100);
	PS_clip($ps);
	PS_setcolor($ps, "fill", "rgb", 1, 1, 1, 0);
	PS_rect($ps, 0, 0, 100, 100);
	PS_fill($ps);
	PS_shfill($ps, $shading2);
	PS_restore($ps);

	PS_save($ps);
	PS_translate($ps, 350, 520);
	PS_show_xy($ps, "Shading 2", 0, -12);
	PS_rect($ps, 0, 0, 100, 100);
	PS_clip($ps);
	PS_setcolor($ps, "fill", "rgb", 1, 1, 1, 0);
	PS_rect($ps, 0, 0, 100, 100);
	PS_fill($ps);
	PS_shfill($ps, $shading3);
	PS_restore($ps);

	PS_save($ps);
	PS_translate($ps, 350, 390);
	PS_show_xy($ps, "Shading 3", 0, -12);
	PS_rect($ps, 0, 0, 100, 100);
	PS_clip($ps);
	PS_setcolor($ps, "fill", "rgb", 1, 1, 1, 0);
	PS_rect($ps, 0, 0, 100, 100);
	PS_fill($ps);
	PS_shfill($ps, $shading4);
	PS_restore($ps);

	PS_save($ps);
	PS_translate($ps, 350, 260);
	PS_show_xy($ps, "Shading 4", 0, -12);
	PS_rect($ps, 0, 0, 100, 100);
	PS_clip($ps);
	PS_setcolor($ps, "fill", "rgb", 1, 1, 1, 0);
	PS_rect($ps, 0, 0, 100, 100);
	PS_fill($ps);
	PS_shfill($ps, $shading5);
	PS_restore($ps);

	PS_save($ps);
	PS_translate($ps, 350, 130);
	PS_show_xy($ps, "Shading 5", 0, -12);
	PS_rect($ps, 0, 0, 100, 100);
	PS_clip($ps);
	PS_setcolor($ps, "fill", "rgb", 1, 1, 1, 0);
	PS_rect($ps, 0, 0, 100, 100);
	PS_fill($ps);
	PS_shfill($ps, $shading6);
	PS_restore($ps);

	PS_end_page($ps);

	/* Page 3 */
	PS_begin_page($ps, 596, 842);
	PS_setcolor($ps, "fill", "rgb", 0, 0, 1, 0);
	$shading1 = PS_shading($ps, "axial", (float) LEFT_BORDER, 0.0, 470.0, 0.0, 1.0, 1.0, 0.0, 0.0, "N 1");
	$shading2 = PS_shading($ps, "axial", 0.0, 0.0, 100.0, 0.0, 1.0, 1.0, 0.0, 0.0, "N 1");

	PS_setfont($ps, $antiqua, 20.0);
	PS_show_xy($ps, "Shading pattern", LEFT_BORDER, 763);
	PS_setfont($ps, $antiqua, 10.0);
	PS_set_value($ps, "leading", 15.0);
	PS_show_boxed($ps, "The examples so far were using the PS_shading() and PS_shfill() in combination wiht PS_clip(). They are more than sufficient in most cases, but pslib has a second approach to create areas with a gradient fill. Beside regular patterns --- which are used like regular colors --- used for filling an area with a drawing, one can use shading pattern in the same way. The results are similar, they way of doing it is different. Each filled rectangle below this text uses the same shading pattern for filling.", LEFT_BORDER, 170, 360, 580, "left", NULL);

	$pspattern = PS_shading_pattern($ps, $shading1, NULL);
	PS_setcolor($ps, "fill", "pattern", (float) $pspattern, 0.0, 0.0, 0.0);
	PS_rect($ps, LEFT_BORDER, 550, 100, 100);
	PS_fill($ps);
	PS_rect($ps, LEFT_BORDER+130, 550, 100, 100);
	PS_fill($ps);
	PS_rect($ps, LEFT_BORDER+260, 550, 100, 100);
	PS_fill($ps);
	PS_rect($ps, LEFT_BORDER+390, 550, 100, 100);
	PS_fill($ps);

	PS_show_boxed($ps, "The line of rectangles illustrates one important aspect of shading. The shading is defined relativ to the current coordinate system. In this case it starts at x-position 50 and ends at x-position 470 in an unmodified coordinate system. The right most rectangle is not filled completly because of the shadings end. Filling areas is like punching wholes in a white coat on top of the page and peeking through. This is mostly not what you want. If you would like to fill the rectangle with the full range of the shading, you will have to create a shading starting at 0 and ending at the width of the rectangle. Before filling the rectangle you will have to translate the coordinate system to the lower left corner of the rectangle, create the pattern in the modified coordinate system and draw a filled rectangle at (0, 0). The pattern must be created after the translation, because it always uses the active coordinate system.", LEFT_BORDER, 170, 360, 360, "left", NULL);

	PS_save($ps);
	PS_translate($ps, LEFT_BORDER, 240);
	$pspattern = PS_shading_pattern($ps, $shading2, NULL);
	PS_setcolor($ps, "fill", "pattern", (float) $pspattern, 0.0, 0.0, 0.0);
	PS_rect($ps, 0, 0, 100, 100);
	PS_fill($ps);
	PS_restore($ps);

	PS_save($ps);
	PS_translate($ps, LEFT_BORDER+130, 240);
	$pspattern = PS_shading_pattern($ps, $shading2, NULL);
	PS_setcolor($ps, "fill", "pattern", (float) $pspattern, 0.0, 0.0, 0.0);
	PS_rect($ps, 0, 0, 100, 100);
	PS_fill($ps);
	PS_restore($ps);

	PS_end_page($ps);

	/* Page 4 */
	PS_begin_page($ps, 596, 842);
	PS_setcolor($ps, "fill", "rgb", 0, 0, 1, 0);
	$shading1 = PS_shading($ps, "axial", (float) 0, 0.0, 470.0, 0.0, 1.0, 1.0, 0.0, 0.0, "N 1");

	PS_setfont($ps, $antiqua, 20.0);
	PS_show_xy($ps, "Using shading patterns for drawing", LEFT_BORDER, 763);
	PS_setfont($ps, $antiqua, 10.0);
	PS_set_value($ps, "leading", 15.0);
	PS_show_boxed($ps, "A pattern is like a color and be used like one. The examples on the previous pages used the pattern for filling rectangles. Why not use it for something more fancy like filling the outline of a text or drawing with a pattern.", LEFT_BORDER, 170, 360, 580, "left", NULL);

	PS_save($ps);
	PS_translate($ps, LEFT_BORDER, 620);
	$pspattern = PS_shading_pattern($ps, $shading1, NULL);
	PS_setcolor($ps, "stroke", "pattern", (float) $pspattern, 0.0, 0.0, 0.0);
	PS_setfont($ps, $antiqua, 90.0);
	PS_show_xy($ps, "Some text.", 0, 10);
	PS_setlinewidth($ps, 5);
	PS_moveto($ps, 0, 0);
	PS_lineto($ps, 470, 0);
	PS_stroke($ps);
	PS_restore($ps);

	PS_end_page($ps);

	PS_close($ps);
	PS_delete($ps);

	exit(0);
?>
