<?
	$GLOBALS['ccmStatusHelp']['summary'] = "
	<p>Use the summary block to display a list of pages from somewhere else in the site. This is very useful for creating a press room or related links area. The block will automatically update when new content is added anywhere on the site. </p>
	<p>
	<strong>Number of Summaries</strong><br/>
	This determines how many items will be displayed. Use it to display the 'top 5' of something.</p>
	<p>
	<strong>Minimum Score</strong><br/>
	This allows you to specify a minimum score for items in your list. This score is determined by usage of the rating block. 
	</p>
	<p>
	<strong>Display</strong>
	<ol>
		<li>All Relevant Items<br/>This is the default. It behaves as expected.</li>
		<li>Items Pertaining to User<br/>This only shows items rated highly by the particular logged-in user. Summarizing a high level of the tree in this display mode creates an excellent “my favorites” navigation for registered users. </li>
	</ol>
	</p>
	<p>
	<strong>Of Type</strong><br/>
	 Use this select menu if you want to limit the items in the list to a particular template type. For example, Of Type allows you to grab all the new “product” pages out of a site without grabbing any “product categories”.
	</p>
	<p>
	<strong>Display summaries of pages</strong>
	<ol>
		<li>Everywhere<br/>Does not limit the pages returned by location.</li>
		<li>In this category<br/>Only pages that live below the current page will be included in this list (the pages must also meet the above criteria as well).</li>
		<li>Other Category<br/>Using the 'search' button, you may select any other page on the site to start the recursive search down the site tree. </li>
	</ol>
	</p>
	<p>
	<strong>Sort Summaries</strong><br/>
	Determines the way the resulting page list is displayed to the visitor.  The summary block is always displayed in one list, if you’re looking to create a page navigation or hierarchy – look to the auto-navigation block. 
	</p>
	";
	$GLOBALS['ccmStatusTitle']['summary'] = "Summary Block";
?>