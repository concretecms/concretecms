<?php

$ps = ps_new();

if (!ps_open_file($ps, "-")) {
	print "Cannot open PostScript file\n";
	exit;
}

ps_set_info($ps, "Creator", "draw.php");
ps_set_info($ps, "Author", "Uwe Steinmann");
ps_set_info($ps, "Title", "Creating document in memory");

ps_begin_page($ps, 596, 842);
ps_end_page($ps);

ps_close($ps);
echo ps_get_buffer($ps);
ps_delete($ps);

?>
