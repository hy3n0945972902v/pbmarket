<!-- BEGIN: main -->
<div class="viewlist">
	<!-- BEGIN: loop -->
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="row">
				<div class="col-xs-24 col-sm-6 col-md-5">
					<div class="image" style="width: {WIDTH}px;">
						<a href="{ROW.link}" title="{ROW.title}"><img src="{ROW.thumb}" alt="{ROW.thumbalt}" class="img-thumbnail img-responsive" /></a>
					</div>
				</div>
				<div class="col-xs-24 col-sm-18 col-md-19">
					<h2><a href="{ROW.link}" title="{ROW.title}">{ROW.title}</a></h2><!-- BEGIN: type --> <span class="type">({ROW.type})</span><!-- END: type -->
					<div class="row">
						<div class="col-xs-24 col-sm-14 col-md-14">
							<ul class="list-info">
								<li><em class="fa fa-folder-open-o">&nbsp;</em><a href="{ROW.cat_link}" title="{ROW.cat}">{ROW.cat}</a></li>
								<!-- BEGIN: location -->
								<li><em class="fa fa-map-marker">&nbsp;</em>{ROW.location}</li>
								<!-- END: location -->
								<!-- BEGIN: auction -->
								<li><em class="fa fa-gavel text-success">&nbsp;</em>{LANG.auction}</li>
								<!-- END: auction -->
							</ul>
						</div>
						<div class="col-xs-24 col-sm-10 col-md-10 text-right">
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
	<!-- END: loop -->	
</div>
<!-- END: main -->