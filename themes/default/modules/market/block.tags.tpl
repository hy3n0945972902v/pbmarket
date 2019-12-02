<!-- BEGIN: main -->
<style>
.news-tags-list a{
	border: 1px solid #A3A3A3;
	color: #A3A3A3;
	font-size: 11px;
	padding: 4px 10px;
	margin: 0 0px 4px 0;
	display: inline-block;
	-webkit-border-radius: 2px;
	border-radius: 2px;
	-webkit-transition: all .2s ease;
	transition: all .2s ease;
}
.news-tags-list a:hover{
	border: 1px solid #0079D7;
	color: #0079D7;
	text-decoration: none !important;
	-webkit-transition: all .2s ease;
	transition: all .2s ease;
}
.news-tags-list a i{
	margin-right: 4px;
}
.news-tags-list a:hover i{
	color: #0079D7;
	-webkit-transition: all .2s ease;
	transition: all .2s ease;
}

</style>

<div class="news-tags-list">
	<!-- BEGIN: loop -->
	<a href="{LOOP.link}" title="{LOOP.title}"><i class="fa fa-tag"></i>{LOOP.title}</a>
	<!-- END: loop -->
</div>
<!-- END: main -->