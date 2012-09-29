<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Basics_Editor extends DashboardBaseController {

	public $helpers = array('form'); 

	protected function getRewriteRules() {
		$rewriteRules = "<IfModule mod_rewrite.c>\n";
		$rewriteRules .= "RewriteEngine On\n";
		$rewriteRules .= "RewriteBase " . DIR_REL . "/\n";
		$rewriteRules .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
		$rewriteRules .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
		$rewriteRules .= "RewriteRule ^(.*)$ " . DISPATCHER_FILENAME . "/$1 [L]\n";
//		$rewriteRules .= "RewriteRule . " . DISPATCHER_FILENAME . " [L]\n";
		$rewriteRules .= "</IfModule>";
		return $rewriteRules;
	}

	public function view($updated = false, $aux = false) {
		$u = new User();	
		
		$txtEditorMode = Config::get('CONTENTS_TXT_EDITOR_MODE');
		$this->set( 'txtEditorMode', $txtEditorMode ); 
		$this->set('rewriteRules', $this->getRewriteRules());
		$textEditorWidth = Config::get('CONTENTS_TXT_EDITOR_WIDTH');
		$this->set( 'textEditorWidth', $textEditorWidth );
		$textEditorHeight = Config::get('CONTENTS_TXT_EDITOR_HEIGHT');
		$this->set( 'textEditorHeight', $textEditorHeight );		
				
		$txtEditorCstmCode=Config::get('CONTENTS_TXT_EDITOR_CUSTOM_CODE');
		if( !strlen($txtEditorCstmCode) || $txtEditorMode!='CUSTOM' )
			$txtEditorCstmCode=$this->get_txt_editor_default();
		$this->set('txtEditorCstmCode', $txtEditorCstmCode );
		
		if ($updated) {
			switch($updated) {
				case "txt_editor_config_saved":
					$this->set('message', t('Content text editor settings saved.'));
					break;		
			}
		}
	
	}
	
	function txt_editor_config(){
		if (!$this->token->validate("txt_editor_config")) { 
			$this->error->add($this->token->getErrorMessage());
		}

		$textEditorWidth = intval($this->post('CONTENTS_TXT_EDITOR_WIDTH'));
		if( $textEditorWidth<580 ) {
			$this->error->add(t('The editor must be at least 580 pixels wide.'));
		}
		$textEditorHeight = intval($this->post('CONTENTS_TXT_EDITOR_HEIGHT'));
		if( $textEditorHeight<100 ) {
			$this->error->add(t('The editor must be at least 100 pixels tall.'));
		}
		
		if (!$this->error->has()) { 
 			Config::save('CONTENTS_TXT_EDITOR_MODE', $this->post('CONTENTS_TXT_EDITOR_MODE') );
			Config::save( 'CONTENTS_TXT_EDITOR_WIDTH', $textEditorWidth );
			Config::save( 'CONTENTS_TXT_EDITOR_HEIGHT', $textEditorHeight );	
			
			$db = Loader::db();
			$values=array( $textEditorWidth, $textEditorHeight );
			$db->query( 'UPDATE BlockTypes SET btInterfaceWidth=?, btInterfaceHeight=? where btHandle = "content"', $values );
			
			if($this->post('CONTENTS_TXT_EDITOR_MODE')=='CUSTOM')
				Config::save('CONTENTS_TXT_EDITOR_CUSTOM_CODE', $this->post('CONTENTS_TXT_EDITOR_CUSTOM_CODE') );
 			$this->redirect('/dashboard/system/basics/editor', 'txt_editor_config_saved'); 
		}
	}
	
	function get_txt_editor_default(){ 
		ob_start();
		?>
theme : "concrete", 
plugins: "inlinepopups,spellchecker,safari,advlink",
editor_selector : "ccm-advanced-editor",
spellchecker_languages : "+English=en",	
theme_concrete_buttons1 : "undo,redo,pastetext,pasteword,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect",
theme_concrete_buttons2 : "formatselect,bullist,numlist,|,outdent,indent,|,hr,charmap,|,forecolor,backcolor,|,link,unlink,anchor",
theme_concrete_buttons3 : "image,cleanup,code",
theme_concrete_blockformats : "p,address,pre,h1,h2,h3,div,blockquote,cite",
theme_concrete_toolbar_align : "left",
theme_concrete_fonts : "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats",
theme_concrete_font_sizes : "1,2,3,4,5,6,7",
theme_concrete_styles: "Note=ccm-note",
spellchecker_languages : "+English=en"

/*
// Use the advanced theme for more than two rows of content
plugins: "inlinepopups,spellchecker,safari,advlink,table,advhr,xhtmlxtras,emotions,insertdatetime,paste,visualchars,nonbreaking,pagebreak,style",
editor_selector : "ccm-advanced-editor",
theme : "advanced",
theme_advanced_buttons1 : "cut,copy,paste,pastetext,pasteword,|,undo,redo,|,styleselect,formatselect,fontsizeselect,fontselect",
theme_advanced_buttons2 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,|,forecolor,backcolor,|,image,charmap,emotions",
theme_advanced_fonts : "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats",
// etc.
*/		
		<?php   
		$js=ob_get_contents();
		ob_end_clean();
		return $js;
	}
	
	
}