<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? 
Loader::block('autonav');
$nh = Loader::helper('navigation');
$dashboard = Page::getByPath("/dashboard");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<?
$html = Loader::helper('html');
$v = View::getInstance();
if (!isset($enableEditing) || $enableEditing == false) {
	$v->disableEditing();
}

// Required JavaScript

$v->addHeaderItem($html->javascript('jquery.js'));
$v->addHeaderItem($html->javascript('jquery.backstretch.js'));
$v->addHeaderItem($html->javascript('masonry.js'));
$v->addHeaderItem($html->javascript('jquery.ui.js'));
$v->addHeaderItem($html->javascript('ccm.dialog.js'));
$v->addHeaderItem($html->javascript('ccm.base.js'));
$v->addHeaderItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>'); 

$v->addHeaderItem($html->javascript('jquery.rating.js'));
$v->addHeaderItem($html->javascript('jquery.form.js'));
$v->addHeaderItem($html->javascript('ccm.ui.js'));
$v->addHeaderItem($html->javascript('quicksilver.js'));
$v->addHeaderItem($html->javascript('jquery.liveupdate.js'));
$v->addHeaderItem($html->javascript('ccm.search.js'));
$v->addHeaderItem($html->javascript('ccm.filemanager.js'));
$v->addHeaderItem($html->javascript('ccm.themes.js'));
$v->addHeaderItem($html->javascript('jquery.ui.js'));
$v->addHeaderItem($html->javascript('jquery.colorpicker.js'));
$v->addHeaderItem($html->javascript('tiny_mce/tiny_mce.js'));

if (LANGUAGE != 'en') {
	$v->addHeaderItem($html->javascript('i18n/ui.datepicker-'.LANGUAGE.'.js'));
}

// Require CSS
$v->addHeaderItem($html->css('ccm.twitter.bootstrap.css'));
$v->addHeaderItem($html->css('ccm.dashboard.css'));
$v->addHeaderItem($html->css('ccm.colorpicker.css'));
$v->addHeaderItem($html->css('ccm.menus.css'));
$v->addHeaderItem($html->css('ccm.forms.css'));
$v->addHeaderItem($html->css('ccm.search.css'));
$v->addHeaderItem($html->css('ccm.filemanager.css'));
$v->addHeaderItem($html->css('ccm.dialog.css'));
$v->addHeaderItem($html->css('jquery.rating.css'));
$v->addHeaderItem($html->css('jquery.ui.css'));

$valt = Loader::helper('validation/token');
$disp = '<script type="text/javascript">'."\n";
$disp .=  "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';"."\n";
$disp .= '$(function() {'."\n";
$disp .= '	$("div.message").animate({'."\n";
$disp .= "		backgroundColor: 'white'"."\n";
$disp .= "	}, 'fast').animate({"."\n";
$disp .= "		backgroundColor: '#eeeeee'"."\n";
$disp .= "	}, 'fast');"."\n";
 if ($dashboard->getCollectionID() == $c->getCollectionID()) {
		$disp .= "ccm_dashboardRequestRemoteInformation();"."\n";
	}
$disp .= "	});"."\n";
$disp .= "</script>"."\n";
//require(DIR_FILES_ELEMENTS_CORE . '/header_required.php'); 
$v->addHeaderItem($disp);

Loader::element('header_required');
?>

<script type="text/javascript">
	$(function() {
	    $.backstretch("http://farm3.static.flickr.com/2443/3843020508_5325eaf761.jpg" <? if (!$_SESSION['dashboardHasSeenImage']) { ?>,  {speed: 750}<? } ?>);
		
		$('#ccm-dashboard-overlay-main').masonry({
		  itemSelector: '.ccm-dashboard-overlay-module', 
		  isResizable: false
		});
		$('#ccm-dashboard-overlay-packages').masonry({
		  itemSelector: '.ccm-dashboard-overlay-module',
		  isResizable: false
		});

		$("#ccm-dashboard-overlay").css('visibility','visible').hide();

		$("#ccm-nav-intelligent-search-wrapper").click(function() {
			$("#ccm-nav-intelligent-search").focus();
		});
		$("#ccm-nav-intelligent-search").focus(function() {
			$(this).parent().addClass("ccm-system-nav-selected");
			if ($("#ccm-dashboard-overlay").is(':visible')) {
				$('#ccm-dashboard-overlay').fadeOut(90, 'easeOutExpo');
				$(window).unbind('click.dashboard-nav');
			}
		});
		
		$("#ccm-nav-dashboard").click(function() {
			$("#ccm-nav-intelligent-search").val('');
			$("#ccm-intelligent-search-results").fadeOut(90, 'easeOutExpo');
			if ($('#ccm-dashboard-overlay').is(':visible')) {
				$('#ccm-dashboard-overlay').fadeOut(90, 'easeOutExpo');
				$(window).unbind('click.dashboard-nav');
			} else {
				$("#ccm-dashboard-overlay").fadeIn(160, 'easeOutExpo');
				$(window).bind('click.dashboard-nav', function() {
					$('#ccm-dashboard-overlay').fadeOut(90, 'easeOutExpo');
					$(window).unbind('click.dashboard-nav');
				});
			}
			return false;
		});
		$("#ccm-nav-intelligent-search").bind('keydown.ccm-intelligent-search', function(e) {
			if (e.keyCode == 13 || e.keyCode == 40 || e.keyCode == 38) {
				e.preventDefault();
				e.stopPropagation();

				if (e.keyCode == 13 && $("a.ccm-intelligent-search-result-selected").length > 0) {
					var href = $("a.ccm-intelligent-search-result-selected").attr('href');
					if (!href || href == '#' || href == 'javascript:void(0)') {
						$("a.ccm-intelligent-search-result-selected").click();
					} else {
						window.location.href = href;
					}
				}
				var visibleitems = $("#ccm-intelligent-search-results li:visible");
				var sel;
				
				if (e.keyCode == 40 || e.keyCode == 38) {
					$.each(visibleitems, function(i, item) {
						if ($(item).children('a').hasClass('ccm-intelligent-search-result-selected')) {
							if (e.keyCode == 38) {
								io = visibleitems[i-1];
							} else {
								io = visibleitems[i+1];
							}
							sel = $(io).find('a');
						}
					});
					if (sel && sel.length > 0) {
						$("a.ccm-intelligent-search-result-selected").removeClass();
						$(sel).addClass('ccm-intelligent-search-result-selected');				
					}
				}
			} 
		});

		$("#ccm-nav-intelligent-search").bind('keyup.ccm-intelligent-search', function(e) {
			ccm_intelligentSearchDoOffsite($(this).val());
		});

		$("#ccm-nav-intelligent-search").blur(function() {
			$(this).parent().removeClass("ccm-system-nav-selected");
		});
		
		
		$("#ccm-nav-intelligent-search").liveUpdate('ccm-intelligent-search-results', 'intelligent-search');
		$("#ccm-nav-intelligent-search").bind('click', function(e) { if ( this.value=="") { 
			$("#ccm-intelligent-search-results").hide();
		}});

		ccm_intelligentSearchActivateResults();
		
		ccm_intelligentSearchDoOffsite($('#ccm-nav-intelligent-search').val());

	});

	var ajaxtimer = null;
	var ajaxquery = null;
	
	ccm_intelligentSearchActivateResults = function() {
		if ($("#ccm-intelligent-search-results div:visible").length == 0) {
			$("#ccm-intelligent-search-results").hide();
		}
		$("#ccm-intelligent-search-results a").hover(function() {
			$('a.ccm-intelligent-search-result-selected').removeClass();
			$(this).addClass('ccm-intelligent-search-result-selected');
		}, function() {
			$(this).removeClass('ccm-intelligent-search-result-selected');
		});
	}

	ccm_intelligentSearchDoOffsite = function(query) {	
		if (query.trim().length > 2) {
			if (query.trim() == ajaxquery) {
				return;
			}
			
			if (ajaxtimer) {
				window.clearTimeout(ajaxtimer);
			}
			ajaxquery = query.trim();
			ajaxtimer = window.setTimeout(function() {
				ajaxtimer = null;
				$("#ccm-intelligent-search-results-list-marketplace").parent().show();
				$("#ccm-intelligent-search-results-list-help").parent().show();
				$("#ccm-intelligent-search-results-list-marketplace").parent().addClass('ccm-intelligent-search-results-module-loading');
				$("#ccm-intelligent-search-results-list-help").parent().addClass('ccm-intelligent-search-results-module-loading');
	
				$.getJSON('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/marketplace/intelligent_search', {
					'q': ajaxquery
				},
				function(r) {
					$("#ccm-intelligent-search-results-list-marketplace").parent().removeClass('ccm-intelligent-search-results-module-loading');
					$("#ccm-intelligent-search-results-list-marketplace").html('');
					for (i = 0; i < r.length; i++) {
						var rr= r[i];
						$("#ccm-intelligent-search-results-list-marketplace").append('<li><a href="' + rr.href + '"><img src="' + rr.img + '" />' + rr.name + '</a></li>');
					}
					if (r.length == 0) {
						$("#ccm-intelligent-search-results-list-marketplace").parent().hide();
					}
					if ($('.ccm-intelligent-search-result-selected').length == 0) {
						$("#ccm-intelligent-search-results").find('li a').removeClass('ccm-intelligent-search-result-selected');
						$("#ccm-intelligent-search-results li:visible a:first").addClass('ccm-intelligent-search-result-selected');
					}
					ccm_intelligentSearchActivateResults();
				}).error(function() {
					$("#ccm-intelligent-search-results-list-marketplace").parent().hide();
				});
	
				$.getJSON('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/get_remote_help', {
					'q': ajaxquery
				},
				function(r) {

					$("#ccm-intelligent-search-results-list-help").parent().removeClass('ccm-intelligent-search-results-module-loading');
					$("#ccm-intelligent-search-results-list-help").html('');
					for (i = 0; i < r.length; i++) {
						var rr= r[i];
						$("#ccm-intelligent-search-results-list-help").append('<li><a href="' + rr.href + '">' + rr.name + '</a></li>');
					}
					if (r.length == 0) {
						$("#ccm-intelligent-search-results-list-help").parent().hide();
					}
					if ($('.ccm-intelligent-search-result-selected').length == 0) {
						$("#ccm-intelligent-search-results").find('li a').removeClass('ccm-intelligent-search-result-selected');
						$("#ccm-intelligent-search-results li:visible a:first").addClass('ccm-intelligent-search-result-selected');
					}
					ccm_intelligentSearchActivateResults();

				}).error(function() {
					$("#ccm-intelligent-search-results-list-help").parent().hide();
				});
	
			}, 500);
		}
	}
</script>
</head>
<body>

<? if (!$_SESSION['dashboardHasSeenImage']) { 
	$_SESSION['dashboardHasSeenImage'] = true;
} ?>

<div id="ccm-dashboard-page" class="ccm-ui">

<div id="ccm-dashboard-header">
<a href="<?=$this->url('/dashboard/')?>"><img id="ccm-dashboard-logo" src="<?=ASSETS_URL_IMAGES?>/logo_menu.png" height="49" width="49" alt="Concrete5" /></a>

<ul id="ccm-main-nav">
<li><a id="ccm-nav-return" href="<?=$this->url('/')?>"><?=t('Return to Website')?></a></li>
</ul>

<ul id="ccm-system-nav">
<li><a id="ccm-nav-dashboard" href="<?=$this->url('/dashboard')?>"><?=t('Dashboard')?></a></li>
<li id="ccm-nav-intelligent-search-wrapper"><input type="search" placeholder="<?=t('Intelligent Search')?>" id="ccm-nav-intelligent-search" tabindex="1" /></li>
<li><a id="ccm-nav-sign-out" href="<?=$this->url('/login', 'logout')?>"><?=t('Sign Out')?></a></li>
</ul>

</div>

<div id="ccm-intelligent-search-results">
<?
$page = Page::getByPath('/dashboard');
$children = $page->getCollectionChildrenArray(true);

$packagepages = array();
$corepages = array();
foreach($children as $ch) {
	$page = Page::getByID($ch);
	if (!$page->getAttribute("exclude_nav")) {
		if ($page->getPackageID() > 0) {
			$packagepages[] = $page;
		} else {
			$corepages[] = $page;
		}
	}

	if ($page->getAttribute('exclude_search_index')) {
		continue;
	}
	
	
	$ch2 = $page->getCollectionChildrenArray(true);
	?>
	
	<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-onsite">
	
	<h1><?=$page->getCollectionName()?></h1>
	
	
	<ul class="ccm-intelligent-search-results-list">
	<? if (count($ch2) == 0) { ?>
		<li><a href="<?=Loader::helper('navigation')->getLinkTocollection($page)?>"><?=$page->getCollectionName()?></a><span><?=$page->getCollectionName()?></span></li>
	<? } ?>
	
	<?
	foreach($ch2 as $chi) {
		$subpage = Page::getByID($chi); 
		if ($subpage->getAttribute('exclude_search_index')) {
			continue;
		}

		?>
		<li><a href="<?=Loader::helper('navigation')->getLinkTocollection($subpage)?>"><?=$subpage->getCollectionName()?></a><span><?=$page->getCollectionName()?> <?=$subpage->getCollectionName()?></span></li>
		<? 
	}
	?>
	</ul>
	
	</div>
	<? } ?>
	
	<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-onsite">
	
	<h1><?=t('Dashboard Home')?></h1>
	
	
	<ul class="ccm-intelligent-search-results-list">
		<li><a href="<?=$this->url('/dashboard/home')?>"><?=t('Customize')?> <span><?=('Customize Dashboard Home')?></span></a></li>
	</ul>
	
	</div>
	
	
	<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-offsite ccm-intelligent-search-results-module-loading">
	<h1><?=t('Help')?></h1>
	<ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-help">
	</ul>
	
	</div>

	<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-offsite ccm-intelligent-search-results-module-loading">
	<h1><?=t('Add-Ons &amp; Themes')?></h1>
	<ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-marketplace">
	</ul>
	
	</div>
	
</div>

<div id="ccm-dashboard-overlay">
<div id="ccm-dashboard-overlay-core">
<div class="ccm-dashboard-overlay-inner" id="ccm-dashboard-overlay-main">

<?php


foreach($corepages as $page) {
	?>
	
	<div class="ccm-dashboard-overlay-module">
	
	<h1><a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>"><?=$page->getCollectionName()?></a></h1>
	
	
	<ul>
	
	<?
	$ch2 = $page->getCollectionChildrenArray(true);
	foreach($ch2 as $chi) {
		$subpage = Page::getByID($chi); 
		if ($subpage->getAttribute('exclude_nav')) {
			continue;
		}

		?>
		<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($subpage)?>"><?=$subpage->getCollectionName()?></a></li>
		<? 
	}
	?>
	</ul>
	
	</div>
	
	<?
}
	
?>

	<div class="ccm-dashboard-overlay-module">
	
	<h1><a href="<?=$this->url('/dashboard')?>"><?=t('Dashboard Home')?></a></h1>
	
	
	<ul>
		<li><a href="<?=$this->url('/dashboard/home')?>"><?=t('Customize')?></a></li>
	</ul>
	
	</div>


</div>
</div>
<? if (count($packagepages) > 0) { ?>
<div id="ccm-dashboard-overlay-footer">
<div class="ccm-dashboard-overlay-inner" id="ccm-dashboard-overlay-packages">
<?php


foreach($packagepages as $page) {
	?>
	
	<div class="ccm-dashboard-overlay-module">
	
	<h1><a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>"><?=$page->getCollectionName()?></a></h1>
	
	
	<ul>
	
	<?
	$ch2 = $page->getCollectionChildrenArray(true);
	foreach($ch2 as $chi) {
		$subpage = Page::getByID($chi); 
		if ($subpage->getAttribute('exclude_nav')) {
			continue;
		}
		
		?>
		<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($subpage)?>"><?=$subpage->getCollectionName()?></a></li>
		<? 
	}
	?>
	</ul>
	
	</div>
	
	<?
}
	
?>
</div>
</div>
<? } ?>
</div>



<div id="ccm-dashboard-content">

	<div class="container">


	<? if (isset($error)) { ?>
		<? 
		if ($error instanceof Exception) {
			$_error[] = $error->getMessage();
		} else if ($error instanceof ValidationErrorHelper) {
			$_error = array();
			if ($error->has()) {
				$_error = $error->getList();
			}
		} else {
			$_error = $error;
		}
		
		if (count($_error) > 0) {
			?>
			<?php Loader::element('system_errors', array('format' => 'block', 'error' => $_error)); ?>
		<? 
		}
	}
	
	if (isset($message)) { ?>
		<div class="block-message alert-message info success"><?=$message?></div>
	<? } ?>