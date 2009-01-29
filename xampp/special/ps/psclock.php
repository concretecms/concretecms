<?php
$radius = 200;
$margin = 20;
$pagecount = 300;

$ps = ps_new();

if (!ps_open_file($ps, "psclock.ps")) {
	print "Cannot open PostScript file\n";
	exit;
}

ps_set_parameter($ps, "warning", "true");

ps_set_info($ps, "Creator", "psclock.php");
ps_set_info($ps, "Author", "Uwe Steinmann");
ps_set_info($ps, "Title", "Analog Clock");

while($pagecount-- > 0) {
	ps_begin_page($ps, 2 * ($radius + $margin), 2 * ($radius + $margin));

	ps_set_parameter($ps, "transition", "wipe");
	ps_set_value($ps, "duration", 0.5);

	ps_translate($ps, $radius + $margin, $radius + $margin);
	ps_save($ps);
	ps_setcolor($ps, "both", "rgb", 0.0, 0.0, 1.0, 0.0);

	/* minute strokes */
	ps_setlinewidth($ps, 2.0);
	for ($alpha = 0; $alpha < 360; $alpha += 6) {
		ps_rotate($ps, 6.0);
		ps_moveto($ps, $radius, 0.0);
		ps_lineto($ps, $radius-$margin/3, 0.0);
		ps_stroke($ps);
	}

	ps_restore($ps);
	ps_save($ps);

	/* 5 minute strokes */
	ps_setlinewidth($ps, 3.0);
	for ($alpha = 0; $alpha < 360; $alpha += 30) { 
		ps_rotate($ps, 30.0);
		ps_moveto($ps, $radius, 0.0);
		ps_lineto($ps, $radius-$margin, 0.0);
		ps_stroke($ps);
	}

	$ltime = getdate();

	/* draw hour hand */
	ps_restore($ps);
	ps_save($ps);
	ps_rotate($ps,-(($ltime['minutes']/60.0)+$ltime['hours']-3.0)*30.0);
	ps_moveto($ps, -$radius/10, -$radius/20);
	ps_lineto($ps, $radius/2, 0.0);
	ps_lineto($ps, -$radius/10, $radius/20);
	ps_closepath($ps);
	ps_fill($ps);
	ps_restore($ps);

	/* draw minute hand */
	ps_save($ps);
	ps_rotate($ps,-(($ltime['seconds']/60.0)+$ltime['minutes']-15.0)*6.0);
	ps_moveto($ps, -$radius/10, -$radius/20);
	ps_lineto($ps, $radius * 0.8, 0.0);
	ps_lineto($ps, -$radius/10, $radius/20);
	ps_closepath($ps);
	ps_fill($ps);
	ps_restore($ps);

	/* draw second hand */
//    ps_setrgbcolor($ps, 1.0, 0.0, 0.0);
	ps_setlinewidth($ps, 2);
	ps_save($ps);
	ps_rotate($ps, -(($ltime['seconds'] - 15.0) * 6.0));
	ps_moveto($ps, -$radius/5, 0.0);
	ps_lineto($ps, $radius, 0.0);
	ps_stroke($ps);
	ps_restore($ps);

	/* draw little circle at center */
	ps_circle($ps, 0, 0, $radius/30);
	ps_fill($ps);

	ps_end_page($ps);

	# to see some difference
//	sleep(1);
}

/*$buf = ps_get_buffer($ps);
$len = strlen($buf);

header("Content-type: application/pdf");
header("Content-Length: $len");
header("Content-Disposition: inline; filename=foo.pdf");
print $buf;
*/

ps_close($ps);
ps_delete($ps);
?>
