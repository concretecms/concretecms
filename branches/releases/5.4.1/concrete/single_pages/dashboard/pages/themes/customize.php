<?php  
defined('C5_EXECUTE') or die("Access Denied."); 
$vt = Loader::helper('validation/token');
?>
<h1><span><?php echo t('Customize Theme')?></span></h1>
<div class="ccm-dashboard-inner">

<?php  $h = Loader::helper('concrete/interface'); ?>
<?php  if (count($styles) > 0) { ?>


<form action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/themes/preview_internal?themeID=<?php echo $themeID?>&previewCID=1" method="post" target="preview-theme" id="customize-form">
<?php echo $vt->output()?>
<?php echo $form->hidden('saveAction', $this->action('save')); ?>
<?php echo $form->hidden('resetAction', $this->action('reset')); ?>

<?php  
$useSlots = false;
// we use the slots if we have more than one style type for any given style
foreach($styles as $tempStyles) {
	if (count($tempStyles) > 1) {
		$useSlots = true;
		break;
	}
}
?>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td valign="top">
<?php 	
	$customSt = false;
	
	foreach($styles as $sto) { 
		$st = $sto[0];
		if ($st->getType() == PageThemeEditableStyle::TSTYPE_CUSTOM) {
			$customST = $st;
			continue;
		}
		
		?>
	
		<div class="ccm-theme-style-attribute <?php  if ($useSlots) { ?>ccm-theme-style-slots<?php  } ?>">
		<?php echo $st->getName()?>

		<?php  
		for ($i = 0; $i < count($sto); $i++) { 
			$slot = $i + 1;
			$st = $sto[$i];
			switch($st->getType()) {
				case PageThemeEditableStyle::TSTYPE_COLOR: ?>
					<?php echo $form->hidden('input_theme_style_' . $st->getHandle() . '_' . $st->getType(), $st->getValue())?>
					<div class="ccm-theme-style-color <?php  if ($useSlots) { ?>ccm-theme-style-slot-<?php echo $slot?><?php  } ?>" id="theme_style_<?php echo $st->getHandle()?>_<?php echo $st->getType()?>"><div hex-color="<?php echo $st->getValue()?>" style="background-color: <?php echo $st->getValue()?>"></div></div>
				<?php  
					break;
				case PageThemeEditableStyle::TSTYPE_FONT: ?>
					<?php echo $form->hidden('input_theme_style_' . $st->getHandle() . '_' . $st->getType(), $st->getShortValue())?>
					<div class="ccm-theme-style-font <?php  if ($useSlots) { ?>ccm-theme-style-slot-<?php echo $slot?><?php  } ?>" font-panel-font="<?php echo $st->getFamily()?>" font-panel-weight="<?php echo $st->getWeight()?>" font-panel-style="<?php echo $st->getStyle()?>" font-panel-size="<?php echo $st->getSize()?>" id="theme_style_<?php echo $st->getHandle()?>_<?php echo $st->getType()?>"><div></div></div>
					
				<?php  
					break;
			}
		} ?>
		</div>
		
	<?php  
	} 
	
	if (isset($customST)) { ?>
	<div class="ccm-theme-style-attribute <?php  if ($useSlots) { ?>ccm-theme-style-slots<?php  } ?>">
		<?php echo t('Add Your CSS')?>
		<?php echo $form->hidden('input_theme_style_' . $customST->getHandle() . '_' . $customST->getType(), $customST->getOriginalValue())?>
		<div class="ccm-theme-style-custom <?php  if ($useSlots) { ?>ccm-theme-style-slot-1<?php  } ?>" id="theme_style_<?php echo $customST->getHandle()?>_<?php echo $customST->getType()?>"><div></div></div>
	</div>
	
	<?php  }

	?>
	
	<?php  
		$b1 = $h->button_js(t('Reset'), 'resetCustomizedTheme()', 'left');
		$b2 = $h->button_js(t('Save'), 'saveCustomizedTheme()');
	?>
	<?php echo $h->buttons($b1, $b2); ?>
	
	<?php echo $form->hidden('themeID', $themeID)?>
	<?php echo $form->hidden('ttask', 'preview_theme_customization')?>
	
	</td>
	<td valign="top" width="100%">
	<div style="padding: 8px; border: 2px solid #eee; margin-left: 10px">
	<iframe name="preview-theme" height="500px" width="100%" src="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/themes/preview_internal?themeID=<?php echo $themeID?>&previewCID=1" border="0" frameborder="0"></iframe>
	</div>

	
	</td>
	</tr>
	</table>
<?php  		
} else {
	print t('This theme contains no styles that can be customized.');
}
?>

</form>
<?php  $ok = t('Ok')?>
<?php  $resetMsg = t('This will remove any theme customizations you have made.'); ?>
<script type="text/javascript">

var lblOk = '<?php echo $ok?>';

jQuery.CustomPanel = {
	activePanel: false,
	init: function() {
		var html = '<div id="jquery-custom-panel"><textarea><\/textarea><div id="jquery-custom-panel-save"><input type="button" name="save" value="' + lblOk + '" /><\/<div><\/div>';
		
		if ($('#jquery-custom-panel').length == 0) {
			$(document.body).append(html);
		}

		this.setupSubmit();

	},
	
	showPanel: function(parent) {
		var content = $("#input_" + $(parent).attr('id')).val();
		$("#jquery-custom-panel textarea").val(content);
		this.activePanel = parent;
		var jcp = $('#jquery-custom-panel');
		var dim = $(parent).offset();
		jcp.css('top', dim.top + 36);
		jcp.css('left', dim.left + 5);
		jcp.bind('mousedown', function(e) {
			e.stopPropagation();
		});

		$(document).bind('mousedown', function() {
			jQuery.CustomPanel.hidePanel()
		});
		jcp.show();		

	},

	hidePanel: function() {
		var jcp = $('#jquery-custom-panel');
		$(document).unbind('mousedown');
		jcp.hide();
	},
	
	setupSubmit: function() {
		var jcp = this;
		$('div#jquery-custom-panel-save input').click(function() {
			var content = $('div#jquery-custom-panel textarea').get(0).value;
			var afp = $(jQuery.CustomPanel.activePanel);		
			$("#input_" + afp.attr('id')).val(content);
			$("#customize-form").get(0).submit()
			jcp.hidePanel();
		});
	}
	
}

jQuery.FontPanel = {
	fonts: new Array('Arial','Helvetica', 'Georgia', 'Verdana', 'Trebuchet MS', 'Book Antiqua', 'Tahoma', 'Times New Roman', 'Courier New', 'Arial Black', 'Comic Sans MS'),
	sizes: new Array(8, 10, 11, 12, 13, 14, 16, 18, 21, 24, 28, 36, 48, 64),
	styles: new Array('normal', 'italic'),
	weights: new Array('normal', 'bold'),
	activePanel: false,
	init: function(font, size, weight, style) {
		var html = '<div id="jquery-font-panel"><div id="jquery-font-panel-list-fonts" class="jquery-font-panel-list">';	
		for (i = 0; i < this.fonts.length; i++) {
			html += '<div font-panel-font="' + this.fonts[i] + '" style="font-family:' + this.fonts[i] + '">' + this.fonts[i] + '<\/div>';
		}
		html += '<\/div><div id="jquery-font-panel-list-sizes" class="jquery-font-panel-list">';
		for (i = 0; i < this.sizes.length; i++) {
			html += '<div font-panel-size="' + this.sizes[i] + '">' + this.sizes[i] + '<\/div>';
		}
		html += '<\/div><div id="jquery-font-panel-list-styles" class="jquery-font-panel-list">';
		for (i = 0; i < this.styles.length; i++) {
			html += '<div font-panel-style="' + this.styles[i] + '" style="font-style:' + this.styles[i] + '">' + this.styles[i] + '<\/div>';
		}
		html += '<\/div><div id="jquery-font-panel-list-weights" class="jquery-font-panel-list">';
		for (i = 0; i < this.weights.length; i++) {
			html += '<div font-panel-weight="' + this.weights[i] + '" style="font-weight:' + this.weights[i] + '">' + this.weights[i] + '<\/div>';
		}
		html +='<\/div><div id="jquery-font-panel-save"><input type="button" name="save" value="' + lblOk + '" /><\/<div><\/div>';
		
		if ($('#jquery-font-panel').length == 0) {
			$(document.body).append(html);
		}
		this.setupSubmit();

	},
	
	showPanel: function(parent) {

		this.setupFonts($(parent).attr('font-panel-font'));
		this.setupSizes($(parent).attr('font-panel-size'));
		this.setupWeights($(parent).attr('font-panel-weight'));
		this.setupStyles($(parent).attr('font-panel-style'));
		
		this.activePanel = parent;
		var jfp = $('#jquery-font-panel');
		jfp.bind('mousedown', function(e) {
			e.stopPropagation();
		});
		var dim = $(parent).offset();
		jfp.css('top', dim.top + 36);
		jfp.css('left', dim.left + 5);
		$(document).bind('mousedown', function() {
			jQuery.FontPanel.hidePanel()
		});
		jfp.show();		
	},

	hidePanel: function() {
		var jfp = $('#jquery-font-panel');
		$(document).unbind('mousedown');
		jfp.hide();
	},
	
	setupSubmit: function() {
		var jfp = this;
		$('div#jquery-font-panel-save input').click(function() {
			var font = $('div#jquery-font-panel-list-fonts div.font-panel-list-selected').attr('font-panel-font');
			var size = $('div#jquery-font-panel-list-sizes div.font-panel-list-selected').attr('font-panel-size');
			var weight = $('div#jquery-font-panel-list-weights div.font-panel-list-selected').attr('font-panel-weight');
			var style = $('div#jquery-font-panel-list-styles div.font-panel-list-selected').attr('font-panel-style');
			var afp = $(jQuery.FontPanel.activePanel);			
			afp.attr('font-panel-weight', weight);
			afp.attr('font-panel-size', size);
			afp.attr('font-panel-style', style);
			afp.attr('font-panel-font', font);
			var selectedString = style + '|' + weight + '|' + size + '|' + font;
			$("#input_" + afp.attr('id')).val(selectedString);
			$("#customize-form").get(0).submit()
			jfp.hidePanel();
		});
	},
	
	setupFonts: function(font) {
		$('div#jquery-font-panel-list-fonts div').removeClass('font-panel-list-selected');
		$('div#jquery-font-panel-list-fonts div').click(function() {
			$('div#jquery-font-panel-list-fonts div').removeClass('font-panel-list-selected');
			$(this).addClass("font-panel-list-selected");
		});
		$('div#jquery-font-panel-list-fonts div[font-panel-font=' + font + ']').addClass('font-panel-list-selected');
	},

	setupSizes: function(size) {
		$('div#jquery-font-panel-list-sizes div').removeClass('font-panel-list-selected');
		$('div#jquery-font-panel-list-sizes div').click(function() {
			$('div#jquery-font-panel-list-sizes div').removeClass('font-panel-list-selected');
			$(this).addClass("font-panel-list-selected");
		});
		$('div#jquery-font-panel-list-sizes div[font-panel-size=' + size + ']').addClass('font-panel-list-selected');
	},

	setupWeights: function(weight) {
		$('div#jquery-font-panel-list-weights div').removeClass('font-panel-list-selected');
		$('div#jquery-font-panel-list-weights div').click(function() {
			$('div#jquery-font-panel-list-weights div').removeClass('font-panel-list-selected');
			$(this).addClass("font-panel-list-selected");
		});
		$('div#jquery-font-panel-list-weights div[font-panel-weight=' + weight + ']').addClass('font-panel-list-selected');
	},

	setupStyles: function(style) {
		$('div#jquery-font-panel-list-styles div').removeClass('font-panel-list-selected');
		$('div#jquery-font-panel-list-styles div').click(function() {
			$('div#jquery-font-panel-list-styles div').removeClass('font-panel-list-selected');
			$(this).addClass("font-panel-list-selected");
		});
		$('div#jquery-font-panel-list-styles div[font-panel-style=' + style + ']').addClass('font-panel-list-selected');
	}

}

jQuery.fn.CustomPanel = function() {
	jQuery.CustomPanel.init();
	$(this).click(function() {
		jQuery.CustomPanel.showPanel(this);
	});
}

jQuery.fn.FontPanel = function() {
	jQuery.FontPanel.init();
	$(this).click(function() {
		jQuery.FontPanel.showPanel(this);
	});
}


saveCustomizedTheme = function() {
	$("#customize-form").attr('target', '_self');
	$("#customize-form").get(0).action = $('#saveAction').val();
	$("#customize-form").get(0).submit();
}

resetCustomizedTheme = function() {
	if (confirm('<?php echo $resetMsg?>')) { 
		$("#customize-form").attr('target', '_self');
		$("#customize-form").get(0).action = $('#resetAction').val();
		$("#customize-form").get(0).submit();
	}
}

$(function() {
	$('div.ccm-theme-style-font').FontPanel();
	$('div.ccm-theme-style-custom').CustomPanel();
	$('div.ccm-theme-style-color').each(function() {
		var thisID = $(this).attr('id');
		var col = $(this).children(0).attr('hex-color');
		$(this).ColorPicker({
			color: col,
			onSubmit: function(hsb, hex, rgb, cal) {
				$('input#input_' + thisID).val('#' + hex);
				$('div#' + thisID + ' div').css('backgroundColor', '#' + hex);
				$("#customize-form").get(0).submit()
				cal.hide();
			}
		});
	});
});


</script>
</div>