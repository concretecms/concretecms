<?php 
$ci = Loader::helper('concrete/interface');
?>
	<h1><span>Inspect Theme</span></h1>
	<div class="ccm-dashboard-inner">
	

	
	<form method="post" id="ccm-inspect-form" action="<?php echo $this->url('/dashboard/themes/inspect/', 'activate_files', $ptID)?>">
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list" id="ccm-template-list">
	<tr>
		<td colspan="3" class="header">File in Theme</td>
	</tr>
	<tr>
		<td class="subheader">File</td>
		<td class="subheader">Type</td>
		<td class="subheader">Action to Take</td>
	</tr>
	<?php 	
	$txt = Loader::helper('text');
	$pf = 0;
	if (count($files) == 0) { ?>
	<tr>
	<td colspan="3">
	There are no templates in this file.
	</td>
	</tr>
	<?php  }
	
	foreach ($files as $f) { ?>
		<tr>
			<td><?php echo $f->getFilename()?></td>
			<td><?php 
				switch($f->getType()) {
					case PageThemeFile::TFTYPE_VIEW:
						print "Wrapper for static pages";
						break;
					case PageThemeFile::TFTYPE_DEFAULT:
						print "Default template";
						break;
					case PageThemeFile::TFTYPE_SINGLE_PAGE:
						print "Template for internal Concrete page.";
						break;
					case PageThemeFile::TFTYPE_PAGE_TYPE_NEW:
						print "New template.";
						break;
					case PageThemeFile::TFTYPE_PAGE_TYPE_EXISTING:
						print "Template for existing page type.";
						break;
				}
			?></td>
			<td><?php 
				switch($f->getType()) {
					case PageThemeFile::TFTYPE_VIEW:
						print '<span class="deem">None. This file will automatically be used.</span>';
						break;
					case PageThemeFile::TFTYPE_DEFAULT:
						print '<span class="deem">None. This file will automatically be used for pages without a template.</span>';
						break;
					case PageThemeFile::TFTYPE_SINGLE_PAGE:
						print '<span class="deem">None. This file will automatically be used by the <strong>' . $txt->uncamelcase($f->getHandle()) . '</strong> page.';
						break;
					case PageThemeFile::TFTYPE_PAGE_TYPE_NEW:
						$pf++;
						print '<input type="checkbox" value="' . $f->getHandle() . '" name="pageTypes[]" checked /> Create page type.';
						break;
					case PageThemeFile::TFTYPE_PAGE_TYPE_EXISTING:
						print '<span class="deem">None. This file will automatically be used by the <strong>' . $txt->uncamelcase($f->getHandle()) . '</strong> page type.';
						break;
				}
			?></td>
		</tr>
		
		<?php  } ?>
	</table>
	</div>
	
	<Br/>
	
	
	<?php 
	$b1 = $ci->button('Return to Themes', $this->url('/dashboard/themes'), 'left');
	if ($pf > 0) { 
		$b2 = $ci->submit('Activate Files', 'ccm-inspect-form'); ?>
		<?php echo $ci->buttons($b1, $b2); ?>
	<?php  } else { ?>
		<?php echo $ci->buttons($b1); ?>
	<?php  } ?>
	</form>
	
	<br/><br/>
	
	</div>
	