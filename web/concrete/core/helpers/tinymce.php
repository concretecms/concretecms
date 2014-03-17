<?php defined('C5_EXECUTE') or die('Access Denied.');

class Concrete5_Helper_Tinymce {

	/** Builds the TinyMCE options string.
	* @param array $override=array() List of key-values options to override
	*/
	public function getOptions($overrides = array()) {
		if(!is_array($overrides)) {
			$overrides = array();
		}
		// Common options
		$textEditorOptions = array(
			'mode' => 'textareas',
			'inlinepopups_skin' => 'concreteMCE',
			'theme_concrete_buttons2_add' => 'spellchecker',
			'browser_spellcheck' => true,
			'gecko_spellcheck' => true,
			'relative_urls' => false,
			'document_base_url' => BASE_URL . DIR_REL . '/',
			'convert_urls' => false,
			'entity_encoding' => 'raw',
			'editor_selector' => 'ccm-advanced-editor'
		);
		// Width
		$width = @intval(Config::get('CONTENTS_TXT_EDITOR_WIDTH'));
		if($width < 580) {
			$width = 580;
		}
		$textEditorOptions['width'] = $width;
		// Height
		$height = @intval(Config::get('CONTENTS_TXT_EDITOR_HEIGHT'));
		if($height < 100) {
			$height = 380;
		}
		else {
			$height -= 70;
		}
		$textEditorOptions['height'] = $height;
		// Determine the text editor mode
		if(array_key_exists('textEditorMode', $overrides)) {
			$textEditorMode = $overrides['textEditorMode'];
			unset($overrides['textEditorMode']);
		}
		else {
			$textEditorMode = Config::get('CONTENTS_TXT_EDITOR_MODE');
		}
		switch($textEditorMode) {
			case 'CUSTOM':
				$extraOptions = Config::get('CONTENTS_TXT_EDITOR_CUSTOM_CODE');
				if(is_string($extraOptions)) {
					$extraOptions = trim($extraOptions, " \t\n\r,");
					if(strlen($extraOptions)) {
						// PHP's json_decode fails decoding strings containing comments and strings like '{key:"value"}', since it requires this format '{"key":"value"}' (double quotes around keys)
						Loader::library('3rdparty/JSON/JSON');
						$sjs = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
						@$extraOptions = @$sjs->decode('{' . $extraOptions . "\n" . '}');
						if(is_array($extraOptions)) {
							$textEditorOptions = array_merge($textEditorOptions, $extraOptions);
						}
					}
				}
				break;
			case 'ADVANCED':
				$textEditorOptions = array_merge($textEditorOptions, array(
				'plugins' => 'inlinepopups,spellchecker,safari,advlink,table,advhr,advimage,xhtmlxtras,emotions,insertdatetime,paste,visualchars,nonbreaking,pagebreak,style',
				'theme' => 'advanced',
				'theme_advanced_buttons1' => 'cut,copy,paste,pastetext,pasteword,|,undo,redo,|,styleselect,formatselect,fontsizeselect,fontselect',
				'theme_advanced_buttons2' => 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,|,forecolor,backcolor,|,image,charmap,emotions',
				'theme_advanced_buttons3' => 'cleanup,code,help,charmap,insertdate,inserttime,visualchars,nonbreaking,pagebreak,hr,|,tablecontrols',
				'theme_advanced_blockformats' => 'p,address,pre,h1,h2,h3,div,blockquote,cite',
				'theme_advanced_fonts' => 'Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats',
				'theme_advanced_font_sizes' => '1,2,3,4,5,6,7',
				'theme_advanced_more_colors' => 1,
				'theme_advanced_toolbar_location' => 'top',
				//'theme_advanced_styles' => 'Note=ccm-note',
				'theme_advanced_toolbar_align' => 'left',
				'spellchecker_languages' => '+English=en'
						));
						break;
			case 'OFFICE':
				$textEditorOptions = array_merge($textEditorOptions, array(
				'spellchecker_languages' => '+English=en',
				'theme' => 'advanced',
				'plugins' => 'safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras', //,template,imagemanager,filemanager
				'theme_advanced_buttons1' => 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,styleselect,formatselect,fontselect,fontsizeselect,', //save,newdocument,help,|
				'theme_advanced_buttons2' => 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor',
				'theme_advanced_buttons3' => 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,insertdate,inserttime,|,ltr,rtl,',
				'theme_advanced_buttons4' => 'charmap,emotions,iespell,media,advhr,|,fullscreen,preview,|,styleprops,spellchecker,|,cite,del,ins,attribs,|,visualchars,nonbreaking,blockquote,pagebreak', //insertlayer,moveforward,movebackward,absolute,|,|,abbr,acronym,template,insertfile,insertimage
				'theme_advanced_blockformats' => 'p,address,pre,h1,h2,h3,div,blockquote,cite',
				'theme_advanced_fonts' => 'Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats',
				'theme_advanced_font_sizes' => '1,2,3,4,5,6,7',
				'theme_advanced_more_colors' => 1,
				'theme_advanced_toolbar_location' => 'top',
				'theme_advanced_toolbar_align' => 'left',
				'theme_advanced_statusbar_location' => 'bottom',
				//'theme_advanced_styles' => 'Note=ccm-note',
				'theme_advanced_resizing' => true
				));
				break;
			case 'BASIC':
				$textEditorOptions = array_merge($textEditorOptions, array(
					'spellchecker_languages' => '+English=en',
					'theme' => 'simple',
					'plugins' => 'paste,inlinepopups,spellchecker,safari,advlink'
				));
				break;
			case 'SIMPLE':
			default:
				$textEditorOptions = array_merge($textEditorOptions, array(
					'theme' => 'concrete',
					'plugins' => 'paste,inlinepopups,spellchecker,safari,advlink,advimage,advhr',
					'spellchecker_languages' => '+English=en'
				));
				break;
		}
		$textEditorOptions = array_merge($textEditorOptions, $overrides);
		if((Localization::activeLanguage() != 'en') && (!array_key_exists('language', $textEditorOptions))) {
			$textEditorLanguagesFolder = DIR_BASE_CORE . '/' . DIRNAME_JAVASCRIPT . '/tiny_mce';
			if(!empty($textEditorOptions['theme'])) {
				$textEditorLanguagesFolder .= '/themes/' . $textEditorOptions['theme'];
			}
			$textEditorLanguagesFolder .= '/langs/';
			$filenames = array(
				strtolower(str_replace('_', '-', Localization::activeLocale())),
				Localization::activeLanguage()
			);
			foreach($filenames as $filename) {
				if(is_file($textEditorLanguagesFolder . $filename . '.js')) {
					$textEditorOptions['language'] = $filename;
					break;
				}
			}
		}
		return $textEditorOptions;
	}
}
