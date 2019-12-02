<!-- BEGIN: main -->
<div class="viewlist">
	<!-- BEGIN: loop -->
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="row">
				<div class="col-xs-8 col-sm-7 col-md-4 hidden-xs">
					<div class="image" style="width: {WIDTH}px;">
						<a href="{ROW.link}" title="{ROW.title}"><img src="{ROW.thumb}" alt="{ROW.thumbalt}" class="img-thumbnail img-responsive" style="max-width: {WIDTH}px;" /></a>
					</div>
				</div>
				<div class="col-xs-24 col-sm-17 col-md-20">
					<h2><a href="{ROW.link}" title="{ROW.title}" <!-- BEGIN: color -->style="color: {ROW.color}"<!-- END: color --> >{ROW.title}</a></h2><!-- BEGIN: type --> <span class="type">({ROW.type})</span><!-- END: type -->
					<div class="row">
						<div class="col-xs-11 col-sm-14 col-md-14">
							<ul class="list-info">
								<li><em class="fa fa-folder-open-o">&nbsp;</em><a href="{ROW.cat_link}" title="{ROW.cat}">{ROW.cat}</a></li>
								<!-- BEGIN: location -->
								<li><em class="fa fa-map-marker">&nbsp;</em><a href="{ROW.location_link}" title="{ROW.location}">{ROW.location}</a></li>
								<!-- END: location -->
								<!-- BEGIN: auction -->
								<li><em class="fa fa-gavel text-success">&nbsp;</em>{LANG.auction}</li>
								<!-- END: auction -->
							</ul>
						</div>
						<div class="col-xs-13 col-sm-10 col-md-10 text-right">
							<ul>
								<li><em class="fa fa-clock-o">&nbsp;</em>{ROW.addtime}</li>
								<li><span class="money">{ROW.price}</span></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- BEGIN: block -->
	{BLOCK}
	<!-- END: block -->
	<!-- END: loop -->
	
	<!-- BEGIN: page -->
	<div class="text-center clear">{PAGE}</div>
	<!-- END: page -->
	
</div>
<!-- END: main -->