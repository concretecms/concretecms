<?
defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Page\Theme\EditableStyle\EditableStyle as PageThemeEditableStyle;
$pk = PermissionKey::getByHandle('customize_themes');
?>


<section id="ccm-panel-page-design-customize">
	<form data-form="panel-page-design-customize" target="ccm-page-preview-frame" method="post" action="<?=$controller->action("preview", $theme->getThemeID())?>">
    <header><a href="" data-panel-navigation="back" class="ccm-panel-back"><span class="glyphicon glyphicon-chevron-left"></span></a> <?=t('Customize Theme')?></header>

	<div id="ccm-panel-page-design-customize-list">
	<? foreach($styleSets as $set) { ?>
		<div class="ccm-panel-page-design-customize-style-set">
			<h5><?=$set->title?></h5>
			<ul class="list-unstyled">
			<? foreach($set->styles as $st) { ?>
				<li>
				<?
				switch($st->getType()) {
					case PageThemeEditableStyle::TSTYPE_COLOR: ?>
						<?=Loader::helper('form/color')->output($st->getFormFieldInputName(), $st->getName(), $st->getValue());?>
					<? 
						break;
					case PageThemeEditableStyle::TSTYPE_FONT: ?>
						<label><?=$st->getName()?></label>
						<input type="hidden" name="<?=$st->getFormFieldInputName()?>" value="<?=$st->getShortValue()?>" />
						<div class="ccm-page-design-customize-font-swatch" id="<?=$st->getFormFieldInputName()?>" font-panel-font="<?=$st->getFamily()?>" font-panel-weight="<?=$st->getWeight()?>" font-panel-style="<?=$st->getStyle()?>" font-panel-size="<?=$st->getSize()?>" id="theme_style_<?=$st->getHandle()?>_<?=$st->getType()?>"><span style="font-family: <?=$st->getFamily()?>; font-weight: <?=$st->getWeight()?>; font-style: <?=$st->getStyle()?>; font-size: <?=$st->getSize()?>">T</span></div>
					<? 
						break;
				}
				?>
				<?//=$st->getName()?></li>
			<? } ?>
			</ul>
		</div>

	<? } ?>
	</div>

    <div style="text-align: center">
        <br/>
       <button class="btn-danger btn" data-panel-detail-action="reset"><?=t('Reset Customizations')?></button>
        <br/><br/>
   </div>

    </form>
</section>


<script type="text/javascript">
    jQuery.CustomPanel = {
        activePanel: false,
        init: function() {
            var html = '<div id="jquery-custom-panel"><textarea><\/textarea><div id="jquery-custom-panel-save"><input type="button" name="save" value="<?=t('Ok')?>" /><\/<div><\/div>';
            
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
                $("#" + afp.attr('id')).val(content);
                //$("#customize-form").get(0).submit()
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
            html +='<\/div><div id="jquery-font-panel-save"><input type="button" name="save" value="<?=t('Ok')?>" /><\/<div><\/div>';
            
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
                $("input[name=" + afp.attr('id') + "]").val(selectedString).trigger('change');
                jfp.hidePanel();
            });
        },
        
        setupFonts: function(font) {
            $('div#jquery-font-panel-list-fonts div').removeClass('font-panel-list-selected');
            $('div#jquery-font-panel-list-fonts div').click(function() {
                $('div#jquery-font-panel-list-fonts div').removeClass('font-panel-list-selected');
                $(this).addClass("font-panel-list-selected");
            });
            $('div#jquery-font-panel-list-fonts div[font-panel-font="' + font + '"]').addClass('font-panel-list-selected');
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
    
    CCMPageDesignPanel = {

        applyDesignToPage: function() {
            var $form = $('form[data-form=panel-page-design-customize]'),
                panel = ConcretePanelManager.getByIdentifier('page');

            $form.prop('target', null);
            $form.attr('action', '<?=$controller->action("apply_to_page", $theme->getThemeID())?>');
            $form.concreteAjaxForm();
            $form.submit();
        },

        applyDesignToSite: function() {
            var $form = $('form[data-form=panel-page-design-customize]'),
                panel = ConcretePanelManager.getByIdentifier('page');

            $form.prop('target', null);
            $form.attr('action', '<?=$controller->action("apply_to_site", $theme->getThemeID())?>');
            $form.concreteAjaxForm();
            $form.submit();
        },

        resetPageDesign: function() {
            var $form = $('form[data-form=panel-page-design-customize]'),
                panel = ConcretePanelManager.getByIdentifier('page');

            $form.prop('target', null);
            $form.attr('action', '<?=$controller->action("reset_page_customizations")?>');
            $form.concreteAjaxForm();
            $form.submit();
        },

        resetSiteDesign: function() {
            var $form = $('form[data-form=panel-page-design-customize]'),
                panel = ConcretePanelManager.getByIdentifier('page');

            $form.prop('target', null);
            $form.attr('action', '<?=$controller->action("reset_site_customizations", $theme->getThemeID())?>');
            $form.concreteAjaxForm();
            $form.submit();
        }


    }

    $(function() {
        panel = ConcretePanelManager.getByIdentifier('page');
        $('button[data-panel-detail-action=submit]').unbind().on('click', function() {
            <? if ($pk->can()) { ?>
                panel.showPanelConfirmationMessage('page-design-customize-apply', "<?=t('Apply this design to just this page, or your entire site?')?>", [
                    {'class': 'btn btn-primary pull-right', 'onclick': 'CCMPageDesignPanel.applyDesignToSite()', 'style': 'margin-left: 10px', 'text': '<?=t("Entire Site")?>'},
                    {'class': 'btn btn-default pull-right', 'onclick': 'CCMPageDesignPanel.applyDesignToPage()', 'text': '<?=t("This Page")?>'}
                ]);
            <? } else { ?>
                CCMPageDesignPanel.applyDesignToPage();
            <? } ?>
            return false;
        });
        $('button[data-panel-detail-action=reset]').unbind().on('click', function() {
            <? if ($pk->can()) { ?>
                panel.showPanelConfirmationMessage('page-design-customize-apply', "<?=t('Reset the theme customizations for just this page, or your entire site?')?>", [
                    {'class': 'btn btn-primary pull-right', 'onclick': 'CCMPageDesignPanel.resetSiteDesign()', 'style': 'margin-left: 10px', 'text': '<?=t("Entire Site")?>'},
                    {'class': 'btn btn-default pull-right', 'onclick': 'CCMPageDesignPanel.resetPageDesign()', 'text': '<?=t("This Page")?>'}
                ]);
            <? } else { ?>
                CCMPageDesignPanel.resetPageDesign();
            <? } ?>
            return false;
        });

        $('div.ccm-page-design-customize-font-swatch').FontPanel();
        //$('div.ccm-theme-style-custom').CustomPanel();
        $('.ccm-panel-page-design-customize-style-set input[type=hidden]').on('change', function() {
            $('form[data-form=panel-page-design-customize]').submit();
        });
    });
</script>
