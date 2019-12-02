<!-- BEGIN: main -->
<div class="company">
	<div class="panel panel-default">
		<div class="panel-heading">{LANG.company_info}</div>
		<div class="panel-body" style="position: relative;">
			<!-- BEGIN: image -->
			<div class="logo">
				<a href="#" title=""><img src="{ROW.image}" class="img-thumbnail" width="150" onclick='modalShow("", "<img src=" + $(this).attr("src") + " />" );' /></a>
			</div>
			<!-- END: image -->
			<ul class="company-info">
				<li><label>{LANG.company_name}</label> : {ROW.title}</li>
				<!-- BEGIN: taxcode -->
				<li><label>{LANG.company_taxcode}</label> : {ROW.taxcode}</li>
				<!-- END: taxcode -->
				<li><label>{LANG.province}</label> : {ROW.address} » {ROW.location}</li>
				<li><label>Email</label> : <a href="mailto:{ROW.email}" title="Mail to: {ROW.email}">{ROW.email}</a></li>
				<!-- BEGIN: fax -->
				<li><label>Fax</label> : {ROW.fax}</li>
				<!-- END: fax -->
				<!-- BEGIN: website -->
				<li><label>Website</label> : <a href="{ROW.website}" title="" target="_blank">{ROW.website}</a></li>
				<!-- END: website -->
				<!-- BEGIN: agent -->
				<li><label>{LANG.company_agent}</label> : {ROW.agent}</li>
				<!-- END: agent -->
			</ul>
			<!-- BEGIN: descripion -->
			<hr />
			<p>{ROW.descripion}</p>
			<!-- END: descripion -->
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-body">
			<!-- BEGIN: googlemaps -->
			<input type="hidden" id="maps_appid" value="{GOOGLEMAPS_APPID}"> <input type="hidden" class="form-control" name="maps_address" id="maps_address" value="" placeholder="{LANG.company_maps_search}">
			<script type="text/javascript" src="{NV_BASE_SITEURL}themes/default/js/market_google_maps.js"></script>
			<div id="maps_maparea">
				<div id="maps_mapcanvas" style="margin-top: 10px;" class="m-bottom"></div>
				<div class="row form-group">
					<div class="col-xs-6">
						<div class="input-group">
							<span class="input-group-addon">L</span> <input type="text" class="form-control" name="maps[maps_mapcenterlat]" id="maps_mapcenterlat" value="{ROW.maps.maps_mapcenterlat}" readonly="readonly">
						</div>
					</div>
					<div class="col-xs-6">
						<div class="input-group">
							<span class="input-group-addon">N</span> <input type="text" class="form-control" name="maps[maps_mapcenterlng]" id="maps_mapcenterlng" value="{ROW.maps.maps_mapcenterlng}" readonly="readonly">
						</div>
					</div>
					<div class="col-xs-6">
						<div class="input-group">
							<span class="input-group-addon">L</span> <input type="text" class="form-control" name="maps[maps_maplat]" id="maps_maplat" value="{ROW.maps.maps_maplat}" readonly="readonly">
						</div>
					</div>
					<div class="col-xs-6">
						<div class="input-group">
							<span class="input-group-addon">N</span> <input type="text" class="form-control" name="maps[maps_maplng]" id="maps_maplng" value="{ROW.maps.maps_maplng}" readonly="readonly">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="input-group">
							<span class="input-group-addon">Z</span> <input type="text" class="form-control" name="maps[maps_mapzoom]" id="maps_mapzoom" value="{ROW.maps.maps_mapzoom}" readonly="readonly">
						</div>
					</div>
				</div>
			</div>
			<!-- END: googlemaps -->
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">{LANG.contact_info}</div>
		<div class="panel-body">
			<ul class="company-info">
				<li><label>{LANG.contact_fullname}</label> : {ROW.contact_fullname}</li>
				<li><label>{LANG.contact_email}</label> : {ROW.contact_email}</li>
				<li><label>{LANG.contact_phone}</label> : {ROW.contact_phone}</li>
			</ul>
		</div>
	</div>
</div>
<!-- END: main -->