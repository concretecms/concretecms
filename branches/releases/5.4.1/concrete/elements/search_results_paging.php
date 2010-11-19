<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  unset($variables); ?>
<div class="ccm-paging">
	<?php  
		$script = $_SERVER['PHP_SELF'];
		if (isset($pOptions['script'])) {
			$script = $pOptions['script'];
		}
		
		if ($pOptions['previous'] > -1) {
			$variables['start'] = $pOptions['previous']; 
			echo('<a href="' . htmlentities($script . Search::qsReplace($variables)) . '">&lt;&lt;</a>');
			echo('&nbsp;&nbsp;');
		}
		
		$start = ($pOptions['current'] > 10) ? $pOptions['current'] - 10 : '1';		
		if (($pOptions['totalPages'] - $pOptions['current']) > 20) {
			if ($pOptions['current'] < 10) {
				$total = '20';
			} else {
				$total = $pOptions['current'] + 10;
			}
		} else {
			$total = $pOptions['totalPages'];
		}
		for ($i = $start; $i <= $total; $i++) {
			if ($i == $pOptions['current']) { 
				echo("<strong>{$i}</strong>&nbsp;&nbsp;");
			} else {
				$variables['start'] = ($i - 1) * $pOptions['chunk'];
				$url = $script . Search::qsReplace($variables);
				echo('<a href="' . $url . '">' . $i . '</a>&nbsp;&nbsp;');
			}
		}
		
		if ($pOptions['next'] ) {
			$variables['start'] = $pOptions['next']; 
			echo('<a href="' . $script . Search::qsReplace($variables) . '">&gt;&gt;</a>');
		}
	?>
</div>