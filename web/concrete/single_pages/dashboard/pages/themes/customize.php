<? 
defined('C5_EXECUTE') or die("Access Denied."); 

// HELPERS
$vt = Loader::helper('validation/token');
$ih = Loader::helper('concrete/interface');

?>

	<? if (count($styles) > 0) {
	
	// If styles can be customised, show edit pane.	
		
	?>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Customize Theme'), false, false, false);?>

    <form action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/themes/preview_internal?themeID=<?=$themeID?>&previewCID=1" method="post" target="preview-theme" id="customize-form">
	
	<div class="ccm-pane-options">
	<?	
		$customSt = false;
		
		foreach($styles as $sto) { 
			$st = $sto[0];
			if ($st->getType() == PageThemeEditableStyle::TSTYPE_CUSTOM) {
				$customST = $st;
				continue;
			}
			
			?>
			
			<div class="ccm-theme-style-attribute <? if ($useSlots) { ?>ccm-theme-style-slots<? } ?>">
			<span class="ccm-theme-style-attribute-name"><?=$st->getName()?></span>
	
			<? 
			for ($i = 0; $i < count($sto); $i++) { 
				$slot = $i + 1;
				$st = $sto[$i];
				switch($st->getType()) {
					case PageThemeEditableStyle::TSTYPE_COLOR: ?>
						<?=$form->hidden('input_theme_style_' . $st->getHandle() . '_' . $st->getType(), $st->getValue())?>
						<div class="ccm-theme-style-color <? if ($useSlots) { ?>ccm-theme-style-slot-<?=$slot?><? } ?>" id="theme_style_<?=$st->getHandle()?>_<?=$st->getType()?>"><div hex-color="<?=$st->getValue()?>" style="background-color: <?=$st->getValue()?>"></div></div>
					<? 
						break;
					case PageThemeEditableStyle::TSTYPE_FONT: ?>
						<?=$form->hidden('input_theme_style_' . $st->getHandle() . '_' . $st->getType(), $st->getShortValue())?>
						<div class="ccm-theme-style-font <? if ($useSlots) { ?>ccm-theme-style-slot-<?=$slot?><? } ?>" font-panel-font="<?=$st->getFamily()?>" font-panel-weight="<?=$st->getWeight()?>" font-panel-style="<?=$st->getStyle()?>" font-panel-size="<?=$st->getSize()?>" id="theme_style_<?=$st->getHandle()?>_<?=$st->getType()?>"><div></div></div>
						
					<? 
						break;
				}
			} // END For loop ?>
			</div>
			
		<? 
		} // END Foreach loop
		
		if (isset($customST)) { ?>
		<div class="ccm-theme-style-attribute <? if ($useSlots) { ?>ccm-theme-style-slots<? } ?>">
			<span class="ccm-theme-style-attribute-name"><?=t('Add Your CSS')?></span>
			<?=$form->hidden('input_theme_style_' . $customST->getHandle() . '_' . $customST->getType(), $customST->getOriginalValue())?>
			<div class="ccm-theme-style-custom <? if ($useSlots) { ?>ccm-theme-style-slot-1<? } ?>" id="theme_style_<?=$customST->getHandle()?>_<?=$customST->getType()?>"><div></div></div>
		</div>
		<? } ?>
	</div>
	
    <div class="ccm-pane-body">  
    
    <?=$vt->output()?>
    <?=$form->hidden('saveAction', $this->action('save')); ?>
    <?=$form->hidden('resetAction', $this->action('reset')); ?>
    <?=$form->hidden('themeID', $themeID)?>
	<?=$form->hidden('ttask', 'preview_theme_customization')?>
    
    <? 
    $useSlots = false;
    // we use the slots if we have more than one style type for any given style
    foreach($styles as $tempStyles) {
        if (count($tempStyles) > 1) {
            $useSlots = true;
            break;
        }
    }
    ?>
		<div id="previewContainer" style="border: 2px solid #eee; height:500px;">
			<div id="previewTheme">
				<iframe name="preview-theme" id="preview-theme" height="100%" width="100%" src="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/themes/preview_internal?themeID=<?=$themeID?>&previewCID=1" border="0" frameborder="0"></iframe>
			</div>
		</div>
        
        
    
    </div><!-- END Pane body -->
    
    <div class="ccm-pane-footer">
    	<? print $ih->button(t('Return to Themes'), $this->url('/dashboard/pages/themes'), 'left'); ?>
        <? print $ih->button_js(t('Save'), 'saveCustomizedTheme()', 'right', 'primary'); ?>
        <? print $ih->button_js(t('Reset'), 'resetCustomizedTheme()', 'right'); ?>
    </div>

    </form>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
    
	<?
    
    // Only include JS if editable styles found.
    
    $ok = t('Ok');
    $resetMsg = t('Reset styles in this theme?');
    
    ?>
    
    <script type="text/javascript">
    
    var lblOk = '<?=$ok?>';
    
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
                    cal.hide();
                }
            });
        });
		
    });
    
    </script>
        
	<?		
    } else { ?>
        
    <? // BEGIN No editable styles found for theme dialogue. ?>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Customize Theme'), false, 'span10 offset1', false)?>
    
        <div class="ccm-pane-body">
            <p style="margin-bottom:18px;"><?=t('This theme contains no styles that can be customized.')?></p>
        </div>
            
        <div class="ccm-pane-footer">
            <? print $ih->button(t('Return to Themes'), $this->url('/dashboard/pages/themes'), 'left'); ?>
        </div>

    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
        
    <? } // END No editable styles dialogue. ?>