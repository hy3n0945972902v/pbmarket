<!-- BEGIN: main -->
<div class="viewcat">
	<h1>{CAT.title}</h1>
	<!-- BEGIN: subcat -->
		<!-- BEGIN: loop -->
		<h2 class="subcat"><a href="{SUBCAT.link}" title="{SUBCAT.title}">{SUBCAT.title}</a></h2>
		<!-- END: loop -->
	<div class="clear"></div>
	<!-- END: subcat -->
	<hr />
	<!-- BEGIN: description_html -->
	<p>{CAT.description_html}</p>
	<!-- END: description_html -->
	{DATA}
</div>
<!-- END: main -->