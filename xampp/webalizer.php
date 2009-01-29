<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta name="author" content="Kai Oswald Seidler, Kay Vogelgesang, Carsten Wiedmann">
		<link href="xampp.css" rel="stylesheet" type="text/css">
		<title></title>
	</head>

	<body>
		&nbsp;<p>
		<pre>
			<?php
				shell_exec("..\webalizer\webalizer.bat");
			?>
			<script type="text/javascript">
				document.location = "/webalizer/";
			</script>
		</pre>
	</body>
</html>
