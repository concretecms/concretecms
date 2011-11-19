# 5.5. Style Guide

## Before You Start

* Become familiar with the table styling, form styling, type styling and grid sections from Twitter Bootstrap: 
	http://twitter.github.com/bootstrap/

## Most Important

* Any time you want to use the styles found in Twitter Bootstrap, you have to ensure that a div with the class "ccm-ui" wraps around whatever you want to style. This happens automatically when using the 
getDashboardPaneHeaderWrapper() and getDashboardPaneFooterWrapper() functions below, but otherwise make sure that your dialogs and single pages contain this.

## Markup

* Generally, don't use tables to position form elements. Use the bootstrap form layouts, and grids.
* Tables are fine for tabular data. Feel free to get sassy with the "zebra-striped" class on a table.

## Dashboard Panes

Any time you need to open the primary dialog on a page, use this function:
	
	<?
	$h = Loader::helper('concrete/dashboard');
	print $h->getDashboardPaneHeaderWrapper($title, $helpText, $columnSpanClass, $includeDefaultBody);
	?>
	
	By default, $columnSpanClass and $includeDefaultBody can be left blank. If $columnSpanClass is blank, the dialog will span the 960 width. If includeDefaultBody is left blank, the default body (which does NOT include a button footer but does include the body) will be used.
	
	An example of not including the default body and making a slightly smaller dialog can be found on the sitemap page:
	
	print $h->getDashboardPaneHeaderWrapper(t('Sitemap'), t('The sitemap allows you to view your site as a tree and easily organize its hierarchy.'), 'span14 offset1', false);

	If you don't include a default body, you need to add <div class="ccm-pane-body"> immediately below your content. Then, when you want to add a footer, you need to add <div class="ccm-pane-footer"></div> after that.
	
To close a primary dialog, either include:

	print $h->getDashboardPaneFooterWrapper();
	
	or print $h->getDashboardPaneFooterWrapper(false);
	
	You need to use "false" if you used "false" for $includeDefaultBody above.
	
Dashboard panes ought to use the ccm-pane-footer class with any buttons that are in them. For examples of this, try setting up a page type for composer and writing a draft, or adding a file set.

Examples of Dashboard Panes
* Pane with buttons: Dashboard > System & Settings > Editing > Languages
* Add File Set (also has an example of a search bar)
	
## Button Treatments

* All links or buttons should have a "btn" class. Most of the time when using the buttons found in Loader::helper('concrete/interface') this should already be applied.
* Dashboard panes ought to use "btn error" for the class on any delete buttons (this will make them red)
* Dashboard panes ought to use "btn primary" for any update/add buttons.


# 5.5 Core Editing FAQ

1. You should NOT have to move any pages around. The pages have been created. The controllers mostly have not. So you'll have to move those around/remake those. But if you find yourself moving a page or needing to install a page at a new location there's probably been miscommunication somewhere.
2. All dashboard pages need to have controllers. They should all extend DashboardBaseController. This controller is automatically loaded. No need to use Loader::controller('/dashboard/base') at the top (even though some of them have it.)
3. 5.5 uses LESS for CSS compilation. There are free tools for Mac OS X, Windows and Linux available to manage this. All items in the ccm_app/build/ directory get compiled to minified versions in the css/ directory. ccm_app.less includes all the less files found in the ccm_app directory.
4. All files in js/ccm_app/ are combined into one file, ccm_app.js. This needs to happen any time you want to make a JavaScript change to a file found in that directory. There is a script, build.sh, in the js/ folder that will do this on a Linux/OS X based system. It is very basic. Order is controlled by filename. Eventually ccm_app.js will be minified (note: if anyone wants to work on making this a nicer process, please mention to me how this might happen.)
