<!-- BEGIN: main -->
<hr />
<div class="form-group">
    <div class="input-group">
        <input type="text" class="form-control" name="maps_address" id="maps_address" value="" placeholder="{LANG.location_maps}"> <span class="input-group-btn">
            <button class="btn btn-default" type="button" id="maps_search">
                <em class="fa fa-search fa-fix">&nbsp;</em>
            </button>
        </span>
    </div>
</div>
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/default/js/market_google_maps.js"></script>
<script>
    $('#maps_search').click(function() {
        initializeMap();
        var geocoder = new google.maps.Geocoder();
        var address = document.getElementById('maps_address').value;
        geocoder.geocode({
            'address' : address
        }, function(results, status) {
            if (status === 'OK') {
                map.setCenter(results[0].geometry.location);
                var marker = new google.maps.Marker({
                    map : map,
                    position : results[0].geometry.location,
                    draggable : true,
                    animation : google.maps.Animation.DROP
                });
            } else {
                alert('Geocode was not successful for the following reason: ' + status);
            }
        });
    });
</script>
<input type="hidden" id="maps_appid" value="{MAPS_APPID}" />
<div id="maps_maparea">
    <div id="maps_mapcanvas" style="margin-top: 10px;" class="form-group"></div>
    <div class="row form-group">
        <div class="col-xs-6">
            <div class="input-group">
                <span class="input-group-addon">L</span> <input type="text" class="form-control" name="maps[maps_mapcenterlat]" id="maps_mapcenterlat" value="{ROW.maps_mapcenterlat}" readonly="readonly">
            </div>
        </div>
        <div class="col-xs-6">
            <div class="input-group">
                <span class="input-group-addon">N</span> <input type="text" class="form-control" name="maps[maps_mapcenterlng]" id="maps_mapcenterlng" value="{ROW.maps_mapcenterlng}" readonly="readonly">
            </div>
        </div>
        <div class="col-xs-6">
            <div class="input-group">
                <span class="input-group-addon">L</span> <input type="text" class="form-control" name="maps[maps_maplat]" id="maps_maplat" value="{ROW.maps_maplat}" readonly="readonly">
            </div>
        </div>
        <div class="col-xs-6">
            <div class="input-group">
                <span class="input-group-addon">N</span> <input type="text" class="form-control" name="maps[maps_maplng]" id="maps_maplng" value="{ROW.maps_maplng}" readonly="readonly">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="input-group">
                <span class="input-group-addon">Z</span> <input type="text" class="form-control" name="maps[maps_mapzoom]" id="maps_mapzoom" value="{ROW.maps_mapzoom}" readonly="readonly">
            </div>
        </div>
    </div>
</div>
<!-- END: main -->
