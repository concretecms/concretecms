<?php 
/*	Miser 1.2.0
 *	A helper class that attempts to optimise and improve a websites user responsiveness
 * 	by re-organising, consolodating and minifying javascript and style sheets.
 * @author ShaunR
 * @copyright  Copyright ( c ) 2011 ShaunR
 * @license    Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License
 * http://creativecommons.org/licenses/by-nc-sa/3.0/
 * No warrantee  expressed or implied. Use at your own risk. 
 *
 * Credits: Many thanks to Adam Johnson for his enduring patience and testing.
 */

defined( 'C5_EXECUTE' ) or die( "Access Denied." );

class HtmlOptimizerHelper {
		
		//Enable/disable features
		private $endis_all			= TRUE;			// Enable/disable the helper (becomes transparent)	
		// Minifying
		private $endis_js_min 		= 1;		    // Enable/disable javascript minifcation 	0=OFF, 1=INLINE ONLY 2=FILE ONLY 3=INLINE+FILE
		private $endis_css_min 		= 3;			// Enable/disable CSS minification			0=OFF, 1=INLINE ONLY 2=FILE ONLY 3=INLINE+FILE
		// Combining
		private $endis_js_combine	= TRUE;			// Enable/disable combining javascript into 1 file
		private $endis_css_combine	= TRUE;			// Enable/disable combining css into 1 file
		private $inline_js_to_file 	= FALSE;		// Include inline javascript in the merged file (only if endis_js_combine is TRUE)
		private $inline_css_to_file	= FALSE; 		// Include inline css in the merged file 		(only endis_css_combine is TRUE)
		// User Options
		private $use_CDN 			= TRUE;			// Use a pre-defined CDN instead of local scripts (e.g jquery)) 
		private $analytics_top		= FALSE;		// Relocate google analytics code into the head.
		private $ignore_selects		= FALSE; 		// Ignore IE select switches.
		// Bencmark memory
		private $start 				= float;		// Used to benchmark execution time
		private $finish 			= float;		// Used to benchmark execution time
		// Category Lists
		private $top_Head_keys 		= array();		// Scripts that must be placed in the head
		private $top_Foot_keys 		= array();		// scripts that must be placed before script-links in the footer
		private $ignore_keys 		= array();		// Scripts that must be skipped ( ignored ) - leaving them in-place
		private $remove_keys 		= array();		// Scripts to be removed enirely from the page (used to replace CDNs)
		// CDNS holder
		private $CDNS;								// Code Distribution Network entries
		private $u;
		
		const MISER_VERSION = '1.2.0';
		
		// Initialisation
		function __construct() {
		
			$this->top_Head_keys 	= array( 'eea.min.js','@import' );
			$this->analytics_keys	= array( 'ga.js' );
			$this->top_Foot_keys 	= array( 'var CCM_','tiny_mce.js' );
			$this->ignore_keys 		= array( 'google_ad','PMW.EEA', 'show_ads.js', 'gmodules.com');
			$this->remove_keys  	= array( 'jsapi' );
			// this needs to go in a file or DB or something. To make it easy to change from Admin
			$this->CDNS = new miserCDNS;
			$this->CDNS->Add('jquery.js',			'HEAD',0,	'ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js');
			$this->CDNS->Add('prototype.js',		'FOOT',0,	'ajax.googleapis.com/ajax/libs/prototype/1.7/prototype.js');
			$this->CDNS->Add('jquery.ui.js',		'FOOT',1,	'ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js');
			$this->CDNS->Add('jquery.cycle.all.js',	'FOOT',1,	'ajax.microsoft.com/ajax/jquery.cycle/2.88/jquery.cycle.all.js');
			$this->CDNS->Add('swfobject.js',		'FOOT',1,	'ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js');
			$this->CDNS->Add('scriptaculous.js',	'FOOT',1,	'ajax.googleapis.com/ajax/libs/scriptaculous/1.8.3/scriptaculous.js');
		}
		/* Properties and Methods to get/set Misers options */
		/* ToDo. Add the rest....boring! Only needed for an admin interface.*/

		// Retrieves the class version number
		public function version(){
			return self::MISER_VERSION;
		}
		// Gets the list of javascript items to be relocated to the document head
		public function get_list_top() {
			return	$this->$top_Head_keyss;
		}
		// Gets the list of javascript items to be relocated to the top of the foot
		// before the javascript links
		public function get_list_footer() {
			return	$this->$top_Foot_keys;
		}
		// Gets the list of javascript and CSS items to be ignored
		// and not subjected to the sorting and combining process.
		public function get_list_ignore() {
			return	$this->ignore_keys ;
		}
		// Gets the list of javascript items that will be completely
		// removed from the document. This is mainly used by the script to remove
		// any blocks requesting JSAPI, but anything can be removed.
		public function get_list_remove() {
			return	$this->remove_keys ;
		}
		// En(dis)ables the entire script
		// Default: FALSE (ENALED)
		public function enable( $value = TRUE ){
			$this->endis_all = $value;
		}
		// Sets the minfy style sheets option
		// Default: 3 (Minify all)
		public function minify_css( $value = 3 ){
			$this->endis_css_min = $value;
		}
		// Sets the minify javascript option
		// Default: 1 (Minify inline only)
		public function minify_js( $value = 1 ){
			$this->endis_js_min = $value;
		}
		// Use code delivery networks for javascript files rather than locally hosted scripts
		// Default: TRUE (Use CDNS)
		public function use_CDN( $value = TRUE ){
			$this->UseCDN = $value;
		}
		// If TRUE, moves google analytics code in to the document head
		// If false it is placed in the footer.
		//Default: FALSE (Footer)
		public function analytics_in_header( $value = FALSE ){
			$this->analytics_top = $value;
		}
		// Enables setting of the javascript items that will be relocated into the header.
		// These should only be asynchronous javascripts
		function set_list_top( $topList = array() ) {
			if ( empty( $topList ) ) return;
			$this->top_keys = $topList;
		}
		// Includes inline javascript in the merged javascript files
		// Default: FALSE (Do Not Include)
		public function inline_js__to_file( $value = FALSE ){
			$this->inline_js_to_file = $value;
		}
		// Includes inline css in the merged css files
		// Default: FALSE (Do Not Include)
		public function inline_css_to_file( $value = FALSE ){
			$this->inline_css_to_file = $value;
		}
		
		/* The main man. This is where the magic happens */
		
		public function optimise( $html ){			
			// Passthrough if we are disabled
			if ( !$this->endis_all ) return $html;
			
			// Load JSMin only if we need it.
			if ( $this->endis_js_min>0){
				if (defined( 'C5_EXECUTE' )) 
					$min = Loader::library( '3rdparty/jsmin' );
				else 
					{/*ToDo : decide where it will be placed*/ ;}
				}
			
			$this->start = microtime( true );
			$js = array();
			$js_links = array();
			$css = array();
			$css_links = array();
			$ico = array();
			$js_Top_Header= array();
			$js_Top_Footer= array();
			$css_Top_Links= array();
			$css_Top = array();
			
			// Google analytics to head? If not it'll go in the footer.
			// Analytics uses https which is really, really slow so if you can, put it in the footer.
			if ($this->analytics_top)
				$this->top_Head_keys =	$this->safe_merge($this->top_Head_keys, $this->analytics_keys);
			else{
				if (defined( 'C5_EXECUTE' )){			
					$u = new User();
					if ($u->isSuperUser())  
							$this->ignore_keys = $this->safe_merge( $this->ignore_keys, $this->analytics_keys);
				}
			}
			
			
			// find IF selects and add them to the ignores list
			if ( preg_match_all( '#<\s*!\s*--\s*\[\s*if.+-->#smUi',$html,$selects )>0 ) {
				foreach ( $selects[0] as $item ) {
				if ( preg_match_all( '#(?|href|src)="(.+)"#',$item,$urls) >0) {
						foreach ( $urls[1] as $url )
						$this->ignore_keys[] = $url;
						if (!$this->ignore_selects){
							$css_selects[] = $item;
							$html = str_replace( $item,'',$html );
						}
					}
				}
			}
			
			// Inline Javascript
			
			if ( preg_match_all( '#<\s*script\s*(type="text/javascript"\s*)?>(.+)<\s*/script\s*>#smUi',$html,$_js )>0 ) {
				foreach ( $_js[0] as $item ) {
					// Ignored?
					if ( $this->CheckList( $item, $this->ignore_keys ) )			continue;
					// Not ignored - Process it.
					$html = str_replace( $item,'',$html );
					// Categorise
					if ( $this->CheckList( $item, $this->top_Head_keys ) )			$js_Top_Header[] = $item; 
					else
						if ( $this->CheckList( $item, $this->top_Foot_keys ) )		$js_Top_Footer[] = $item;
					else
						if ( !$this->CheckList( $item, $this->remove_keys ) )		$js[] 			 = $item;
				}	
			}
			
			// Javascript links to files			
			
			$remove_items = array();
			if ( preg_match_all( '#<\s*script\s*(type="text/javascript"\s*)?src=.+<\s*/script\s*>#smUi',$html,$_js_links )>0 ) {
				foreach ( $_js_links[0] as $item ) {
					// Ignored?
					if ( $this->CheckList( $item, $this->ignore_keys ) )			continue;
					// Not ignored - Process it.
					$html = str_replace( $item,'',$html );
					// Categorise
					if ( $this->use_CDN && $this->CDNS->Exists( $item) )			$remove_items[] 	= $item;
					else
						if ( $this->CheckList( $item, $this->top_Head_keys ) )		$js_Top_Header[] 	= $item; 
					else 
						if ( $this->CheckList( $item, $this->top_Foot_keys ) ) 		$js_Top_Footer[]	= $item;	
					else
					if ( !$this->CheckList( $item, $this->remove_keys ) )			$js_links[] 		= $item;
						
				}	
				// User wants CDN rather than locally hosted?
				if ( $this->use_CDN ) {
						foreach ($remove_items as $item){
							$js_Top[] = $this->CDNS->CDN($item,'HEAD');
							$js_Foot[] = $this->CDNS->CDN($item,'FOOT');
						}
						$js_Top_Header = $this->safe_merge($js_Top, $js_Top_Header);
						$js_Top_Footer = $this->safe_merge($js_Foot, $js_Top_Footer);
						$js_links = array_diff( $js_links, $remove_items );
				}				
				//force a re-index
				$js_links=  $this->safe_merge( $js_links ); 
			}
			
			// Style-sheet links to files	
			
			if ( preg_match_all( '#<\s*link\s*rel="stylesheet".*>#smUi',$html,$_css_links )>0 ) {
				foreach ( $_css_links[0] as $item ) {
					// Ignored?
					if ( $this->CheckList( $item, $this->ignore_keys ) )			continue;
					else{
						$css_links[] = $item;
						$html = str_replace( $item,'',$html );					
					}
				}			
			}
			
			// Bookmark icons
			
			$ico = $this->find_replace( '#<\s*link.+image/x-icon.+>#iU',$html );
			
			// Inline-style-sheets
			
			$css_inlne = $this->find_replace( '#<\s*style.*>.+<\s*/style\s*\/?>#smUi',$html );
			$css_inlne = @implode( $css_inlne );
			
			// CSS file merge
			
			if ($this->endis_css_combine){
				// Find those we can actually load from out server (i.e get rid of externally hosted and file not found)
				$_css_links= array_filter($css_links, array($this,url_exists));		//See callback "**"
				if ($this->inline_css_to_file) {						// Want inline in the file too.
					$title = $this->write_css($_css_links,$css_inlne);
					$css_inlne='';
				}
				else {
					$title = $this->write_css($css_links);				// Don't want inline, just links.
					if ( $this->endis_css_min & 1 ) $css_inlne = $this->css_minify( $css_inlne ) . "\n";
					}
				// Put it all tegether - Remove the links put in the file from the links list and add the link
				// for the merged file
				if ($title) {
					$css_links = @array_diff($css_links,$_css_links);	
					$css_links[] ='<link rel="stylesheet" media="screen" type="text/css" href="'.$title .'" />';
					}
			}
			// don't want to merge. Just minify if required
			else
					if ( $this->endis_css_min & 1) $css_inlne = $this->css_minify( $css_inlne ) . "\n";
			
			
			// JS file merge
			
			$js_inline = @implode( "\n",$js );
			if ($this->endis_js_combine){
				// Find those we can actually load from out server (i.e get rid of externally hosted and file not found)
				$_js_links= array_filter($js_links, array($this,url_exists));		//See callback "**"
				if ($this->inline_js_to_file) {							// Want inline in the file too.
					$title = $this->write_js( $_js_links,$js_inline);
					$js_inline='';
				}
				else {
					$title = $this->write_js( $_js_links );				// Don't want inline, just links.
					if ( $this->endis_js_min & 1) $js_inline =  JSMin::minify( $js_inline );	
				}
				// Put it all tegether - Remove the links put in the file from the links list and add the link
				// for the merged file
				if ($title) {
					$js_links = @array_diff($js_links,$_js_links);
					$js_links[] = '<script type="text/javascript" src="'.$title .'"></script>' ;
				}
			}
			// don't want to merge. Just minify if required
			else
				if ( $this->endis_js_min & 1 ) $js_inline =  JSMin::minify( $js_inline );
			
			//To the Head
			$head = $this->safe_merge( $ico,$css_links,$css_inlne,$css_selects,$js_Top_Header );
			$head = @implode( "\n",$head ) . "\n</head>\n";
			$html =  str_ireplace( '</head>', $head, $html );
			
			// To the footer
			$body = $this->safe_merge( $js_Top_Footer, $js_links,  $js_inline );
			$rep = @implode( "\n",$body ) . "\n</body>\n";
			$html =  str_ireplace( '</body>', $rep, $html );

			
			// calculate processing time
			$this->finish = microtime( true );
			$exec_time = $this->finish - $this->start;
			//add exec time
			$html =  str_ireplace( '</head>',"\n<!-- Sorted by Miser ".self::MISER_VERSION . " in ". round( $exec_time,3 ) ." Secs -->\n</head>", $html );
			$html = preg_replace( "#(\n{2,}+)|(\t)+#s", "", $html );	
			return $html;	
		}
		
		/* From this point on. Helper functions */
		
		// Find matches to the regex, deletes the text in $html and returns the replaced text;
		private function find_replace( $regex = '', &$html = null ) {
			if ( empty( $regex ) || !isset( $html ) ) return array();	// sanity check
			$found = array();
			// find and replace
			if ( preg_match_all( $regex,$html,$_found ) > 0 )
				foreach ( $_found[0] as $item ) {
					$html = str_replace( $item,'',$html);
					$found[] =  $item;
				}
			return $found;
		}
		// Check List - Return TRUE if it is in the provied list-FALSE if not
		// Does a partial, case insensitive string comparison
		private function CheckList( $item = '',$FilterList= array() ){
			foreach ( $FilterList as $ListItem )		
				if ( stripos( $item,$ListItem ) !== FALSE ) return TRUE;					
			return FALSE;
		}
		
		// Writes the merged CSS file to disk and returns the location
		// ToDo: Need to combine this more elegantly with the JS combine
		// since they basically work the same way.
		private function write_css( $files = array(),$the_rest=''){
			if ( !is_array( $files ) ) return FALSE;
			
			global $c;
			//$dir = "css/";									//ToDo: Make sure it exists
			$dir = DIR_FILES_CACHE;
			$reldir = REL_DIR_FILES_CACHE;
			$title = '_merge.css';	
			
			// Lets begin to build our merged stylesheet
			foreach ( $files as $item ) {
					preg_match( '#href="(.+)"#',$item,$url );
					$path = $url[1];
					// If defailt themes are used, the style sheet is referenced through
					// the index.php so we need to try and find it.
					// (Must be a more elgant way than this!)
					if (stripos($path,'index.php') !== FALSE && defined( 'C5_EXECUTE' )){
						if (!isset( $h)) $h = Loader::helper('html');
						$p = explode('/',$path);
						$fname= array_pop($p);
						$css = $h->css($fname);
						$path =$css->file;
					}	
					$realpath = explode( "?",$path );
					$path = $_SERVER['DOCUMENT_ROOT'] .$realpath[0];
					if (file_exists($path)){
						$_contents = file_get_contents( $path );	
						$contents .= $this->css_fixPaths ( $_contents,$realpath[0]);
					}					
				} 
			$hash=md5($contents);
			if (!file_exists($dir.$hash.$title)){
				if ($this->endis_css_min & 2) $contents = $this->css_minify( $contents );
				$contents .= $this->css_fixPaths (strip_tags($the_rest),"/");
				$this->fsave($dir.$hash.$title, $contents);
			}	
			return $reldir.$hash.$title;
		}
		
		// Writes the merged Javascript file to disk and returns the location
		// ToDo: Need to combine this more elegantly with the css combine
		// since they basically work the same way.
		private function write_js( $files = array(),$the_rest=''){
			if ( !is_array( $files ) ) return FALSE;
			
			global $c;
			//$dir = "js/";								//ToDo: Make sure it exists
			$dir = DIR_FILES_CACHE;
			$reldir = REL_DIR_FILES_CACHE;
			$title = '_merge.js';	
			
			foreach ( $files as $item ) {
					if (preg_match( '#src="(.+)"#',$item,$url )>0){
						$path = $url[1];
						// If defailt themes are used, the style sheet is referenced through
						// the index.php so we need to try and find it.
						if (stripos($path,'index.php') !== FALSE && defined( 'C5_EXECUTE' )){
							if (!isset( $h)) $h = Loader::helper('html');
							$p = explode('/',$path);
							$fname= array_pop($p);
							$css = $h->css($fname);
							$path =$css->file;
						}	
						$realpath = explode( "?",$path );
						$path = $_SERVER['DOCUMENT_ROOT'] .$realpath[0];
						if (file_exists($path)){
							$contents .= file_get_contents( $path ) . ";\n";		
						}					
					} 
				}
			// Now the in-line
			$contents .= preg_replace('#<script\s*(type="text/javascript")?>|</script>#i','',$the_rest) . ";\n";		
			$hash=md5($contents);
			// Dump to minfy and dump to disk if changed
			if (!file_exists($dir.$hash.$title)){
				if ($this->endis_js_min & 2) $contents =   JSMin::minify( $contents );	
				$this->fsave($dir.$hash.$title,$contents);
			}	
			return $reldir.$hash.$title;
		}
		
		// Writes the merged file to disk
		// ToDo: Need to think about clean up. It's not too bad and is limited
		// but I can see people moaning.
		private function fsave($path='',$contents=''){
			$fp = fopen($path, "c");
			// Get exclusive lock to the file.
			// We will just fail if unsuccessful and return.
			if(!@flock($fp, LOCK_EX | LOCK_NB)) {
				@fclose($fp);
				return FALSE;
			}
			ftruncate($fp,0);		
			fwrite($fp,$contents);	
			flock($fp, LOCK_UN);
			fclose($fp);
			return TRUE;
		}
		
		// ** Callback function for array_diff when choosing links to combine (JS and CSS)
		private function url_exists($item=''){
			if (preg_match( '#(?|href|src)="(.+)"#',$item,$url )>0){
				$path = $url[1];
				// If defailt themes are used, the style sheet is referenced through
				// the index.php so we need to try and find it.
				if ((stripos($path,'index.php') !== FALSE) && defined( 'C5_EXECUTE' )){
					if (!isset( $h)) $h = Loader::helper('html');
					$p = explode('/',$path);
					$fname= array_pop($p);
				//	if ((stripos($fname,'.js') !==FALSE) || (stripos($fname,'_js') !==FALSE)) $h->javascript($fname); 
					if (stripos($fname,'.js') !==FALSE) $h->javascript($fname); 
					else $obj = $obj = $h->css($fname);
					$path =$obj->file;
				}	
				$realpath = explode( "?",$path );
				$path = $_SERVER['DOCUMENT_ROOT'] .$realpath[0];
				return file_exists($path);	
			}
			return false;					
		}
		// simple CSS minifactin
		public function css_minify( $css ) {
			$css = preg_replace('#/\*[^*]*\*+([^/][^*]*\*+)*/#', ' ', $css ); //comments
			$css = preg_replace( '#\s+#', ' ', $css );						 //Whitespace
			$css = str_replace( '; ', ';', $css );
			$css = str_replace( ': ', ':', $css );
			$css = str_replace( ' {', '{', $css );
			$css = str_replace( '{ ', '{', $css );
			$css = str_replace( ', ', ',', $css ); 
			$css = str_replace( ';)', ')', $css ); 
			return $css;
		} 
		
		private function safe_merge( $a=array(),$b=array(),$c=array(),$d=array(),$e=array() ){
		// this method is basically lifted from the cores' view.php 
		// theres probably a clever way for any number of arguements. But this works for now
			$_a = ( is_array( $a ) ) ? $a : array( $a );
			$_b = ( is_array( $b ) ) ? $b : array( $b );
			$_c = ( is_array( $c ) ) ? $c : array( $c );
			$_d = ( is_array( $d ) ) ? $d : array( $d );
			$_e = ( is_array( $e ) ) ? $e : array( $e );
			return array_merge( $_a,$_b,$_c,$_d,$_e );
		}
		
		// replaces css relative urls with full ones
		public function css_fixPaths ( $css = '', $scriptPath = '' ){
			if ( ( $scriptPath =='' ) || ( $css == '' ) ) return '<!-- Nothing to see here -->';						
			// remove file name
			$s_path = @explode( '/',$scriptPath );
			@array_pop ( &$s_path );	
			//find css urls 			
			if ( preg_match_all( '#url\((.+)\)#smUi',$css,$urls ) >0 ){
				$urls = array_unique ( $urls[1] );									// Found some - discard duplicates
				foreach( $urls as $url ){											// Process each url
					$spath = implode( '/',$s_path ).'/';
					$num = preg_match_all( '#\.\./#',$url,$backup );				// Detect relative parent paths
					// If none found then it is a sub directry.
					// If found then we need to back up the tree
					if ( $num>0 ) {	
						$path =	$s_path;											//a ). it's off of a parent directory.
						for ( $i = 0; $i < $num; $i++ )  @array_pop ( &$path );
						$new_url =array( str_replace( '../','', $url ) );				//Remove the ../ ( we know how many there are )
						$full_url = @implode( '/',array_merge( $path, $new_url ) );		//Create the full url from the scripts path and the relative path
						$css = str_replace( $url, $full_url, $css ); 					//replace all occurancies
					}
					else {															//b ). it's a subdirectory.
						if (stripos($url,'index.php') === FALSE)
						$css = str_replace( $url, $spath.$url, $css );				//just append and replace
					}
				}
			}
			return $css;
		}	
}

class miserCDNS{
		private $CDNS;
		
		public function __toString(){
			return implode("\n",$this->CDNS);
		}
		
		private function cmp( $a, $b ){ 
				if(  $a->Priority() ==  $b->Priority() ){ return 0 ; } 
				return ($a->Priority() < $b->Priority()) ? -1 : 1;
	   } 
		
		public function Add($name='', $position, $priority=0,$tag=''){
			if (empty($name) || empty($tag)) return FALSE;
			$tag= '<script type="text/javascript" src="//'.$tag. '"></script>';
			$CDN = new miserCDN($name,$position,$priority,$tag);
			$this->CDNS[] =$CDN ;
			return $CDN;
		}
		
		public function Delete($CDN){
		 foreach ($CDNS as $item)
			if ($item === $CDN) {
					unset ($item);
					array_merge($CDNS);
					return TRUE;
			}
		}
		
		public function CDN($name='',$section=''){
			if (empty($name)) return FALSE;
			else
			foreach ($this->CDNS as $CDN){
				if (!empty($section)) {
					if (stripos($name, $CDN->Name()) !== FALSE && ($CDN->Position() == $section)) return $CDN;
				}
				else
					if (stripos($name, $CDN->Name()) !== FALSE) return $CDN;

			}
		}
		
		public function IndexOf($name=''){
			if (empty($name)) return FALSE;
			else
			for ($i=0;$i<count($this->CDNS);$i++){
				if ($this->CDNS[$i]->Name() == $name) return $i;
				}
		}
		
		public function Exists($name=''){
			foreach ( $this->CDNS as $CDN )
				if (stripos($name, $CDN->Name()) !== FALSE) return TRUE;
			return FALSE;
		}
		
		public function Clear(){
				unset ($CDNS);
		}
		
		public function Count(){
			return count($this->CDNS);
		}
		
		public function Sort($items){
			usort(&$items, array($this,"cmp"));
		}	
}

class miserCDN{
		private $name;
		private $priority;
		private $tag;
		private $position;
		
		function __construct($name, $position = 'HEAD',$priority = 0,$tag=''){
			if (empty($name) || empty($tag)) return FALSE;
			$this->name =$name ;
			$this->priority = $priority;
			$this->tag = $tag;
			$this->position = $position;
		}
		
		public function __toString(){
        return $this->tag;
		}
		
		public function Name($name =null){
			if (!is_null($name)) $this->name = $name;
			return (string)$this->name;
		}
		
		public function Priority($priority = null){
			if (!is_null($name)) $this->priority = $priority;
			return $this->priority;
		}
		
		public function Position($position = null){
			if (!is_null($name)) $this->position = $position;
			return $this->position;
		}
		
		public function Tag($tag = null){
			if (!is_null($name)) $this->tag = $tag;
			return $this->tag;
		}		
}
?>