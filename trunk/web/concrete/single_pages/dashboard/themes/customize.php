<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<h1><span><?=t('Customize Theme')?></span></h1>
<div class="ccm-dashboard-inner">

<? $h = Loader::helper('concrete/interface'); ?>
<? if (count($styles) > 0) { ?>


<form action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/themes/preview_internal?themeID=<?=$themeID?>&previewCID=1" method="post" target="preview-theme" id="customize-form">
<?=$form->hidden('saveAction', $this->action('save')); ?>
<?=$form->hidden('resetAction', $this->action('reset')); ?>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td valign="top">
<?	
	$lastType = false;
	$customSt = false;
	
	foreach($styles as $st) { 
		if ($st->getType() == PageThemeEditableStyle::TSTYPE_CUSTOM) {
			$customST = $st;
			continue;
		}
		
		if ($st->getType() != $lastType) {
			switch($st->getType()) {
				case PageThemeEditableStyle::TSTYPE_COLOR:
					print '<h2>' . t('Colors') . '</h2>';
					break;
				case PageThemeEditableStyle::TSTYPE_FONT:
					print '<h2>' . t('Text and Type') . '</h2>';
					break;
			}
		}
		
		$lastType = $st->getType();
		
		?>
	
		<div class="ccm-theme-style-attribute">
		<? switch($st->getType()) {
			case PageThemeEditableStyle::TSTYPE_COLOR: ?>
				<?=$st->getName()?>
				<?=$form->hidden('input_color_' . $st->getHandle(), $st->getValue())?>
				<div class="ccm-theme-style-color" id="color_<?=$st->getHandle()?>"><div hex-color="<?=$st->getValue()?>" style="background-color: <?=$st->getValue()?>"></div></div>
			<? 
				break;
			case PageThemeEditableStyle::TSTYPE_FONT: ?>
				<?=$st->getName()?>
				<?=$form->hidden('input_font_' . $st->getHandle(), $st->getShortValue())?>
				<div class="ccm-theme-style-font" font-panel-font="<?=$st->getFamily()?>" font-panel-weight="<?=$st->getWeight()?>" font-panel-style="<?=$st->getStyle()?>" font-panel-size="<?=$st->getSize()?>" id="font_<?=$st->getHandle()?>"><div></div></div>
				
			<? 
				break;
		} ?>
		</div>
		
	<? 
	} 
	
	if (isset($customST)) { ?>
	<h2>Custom Styles</h2>
	<div class="ccm-theme-style-attribute">
		<?=t('Edit Custom Styles')?>
		<?=$form->hidden('input_custom_' . $customST->getHandle(), $customST->getValue())?>
		<div class="ccm-theme-style-custom" id="custom_<?=$customST->getHandle()?>"><div></div></div>
	</div>
	
	<? }

	?>
	
	<? 
		$b1 = $h->button_js(t('Reset'), 'resetCustomizedTheme', 'left');
		$b2 = $h->button_js(t('Save'), 'saveCustomizedTheme');
	?>
	<?=$h->buttons($b1, $b2); ?>
	
	<?=$form->hidden('themeID', $themeID)?>
	<?=$form->hidden('ttask', 'preview_theme_customization')?>
	
	</td>
	<td valign="top" width="100%">
	<div style="padding: 8px; border: 2px solid #eee; margin-left: 10px">
	<iframe name="preview-theme" height="500px" width="100%" src="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/themes/preview_internal?themeID=<?=$themeID?>&previewCID=1" border="0" frameborder="0"></iframe>
	</div>

	
	</td>
	</tr>
	</table>
<? 		
} else {
	print t('This theme contains no styles that can be customized.');
}
?>

</form>
<? $save = t('Save')?>
<? $resetMsg = t('This will remove any theme customizations you have made.'); ?>
<script type="text/javascript">

var lblSave = '<?=$save?>';

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

jQuery.CustomPanel = {
	activePanel: false,
	init: function(font, size, weight, style) {
		var html = '<div id="jquery-custom-panel"><textarea><\/textarea><div id="jquery-custom-panel-save"><input type="button" name="save" value="' + lblSave + '" /><\/<div><\/div>';
		
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
	},
	
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
		html +='<\/div><div id="jquery-font-panel-save"><input type="button" name="save" value="' + lblSave + '" /><\/<div><\/div>';
		
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


saveCustomizedTheme = function() {
	$("#customize-form").attr('target', '_self');
	$("#customize-form").get(0).action = $('#saveAction').val();
	$("#customize-form").get(0).submit();
}

resetCustomizedTheme = function() {
	if (confirm('<?=$resetMsg?>')) { 
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
				cal.fadeOut(300);
			}
		});
	});
});


</script>
</div>