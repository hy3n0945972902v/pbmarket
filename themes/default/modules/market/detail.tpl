<!-- BEGIN: main -->
<link type="text/css" rel="stylesheet" href="{NV_BASE_SITEURL}themes/default/js/lightslider/css/lightslider.css" />
<link type="text/css" rel="stylesheet" href="{NV_BASE_SITEURL}themes/default/js/fancybox/jquery.fancybox.css?v=2.1.5" />
<div class="detail m-bottom">
    <div class="row m-bottom">
        <!-- BEGIN: image -->
        <div class="col-xs-24 col-sm-12 col-md-12">
            <div class="demo">
                <ul id="lightSlider">
                    <!-- BEGIN: loop -->
                    <li data-thumb="{IMAGE.thumb}"><a title="{IMAGE.description}" class="fancybox" rel="gallery1" href="{IMAGE.full}"><img src="{IMAGE.path}" alt="{IMAGE.description}" /></a></li>
                    <!-- END: loop -->
                </ul>
            </div>
        </div>
        <!-- END: image -->
        <div class="col-xs-24 <!-- BEGIN: image1 -->col-sm-12 col-md-12<!-- END: image1 --> <!-- BEGIN: image2 -->col-sm-24 col-md-24<!-- END: image2 -->">
            <h1>{DATA.title}</h1>
            <ul class="list-inline list-title">
                <li>{LANG.code}: {DATA.code}</li>
                <li>{LANG.addtime}: {DATA.addtimef}</li>
                <!-- 
				<li>{LANG.countview}: {DATA.countview}</li>
                 -->
                <!-- BEGIN: comment -->
                <li>{LANG.countcomm}: {DATA.countcomment}</li>
                <!-- END: comment -->
            </ul>
            <ul class="list-content">
                <li><em class="fa fa-folder-open-o">&nbsp;</em><label>{LANG.cat}</label>: <a href="{DATA.cat_link}" title="{DATA.cat}">{DATA.cat}</a></li>
                <!-- BEGIN: type -->
                <li><em class="fa fa-cog">&nbsp;</em><label>{LANG.type}</label>: {DATA.type}</li>
                <!-- END: type -->
                <!-- BEGIN: location -->
                <li><em class="fa fa-map-marker">&nbsp;</em><label>{LANG.area}</label>: <a href="{DATA.location_link}" title="{DATA.location}">{DATA.location}</a></li>
                <!-- END: location -->
                <!-- BEGIN: price -->
                <li><em class="fa fa-money">&nbsp;</em><label>{LANG.price}</label>: <span class="money">{DATA.price}</span></li>
                <!-- END: price -->
                <!-- BEGIN: field -->
                <li><label>{FIELD.title}</label>: {FIELD.value}</li>
                <!-- END: field -->
            </ul>
            <!-- BEGIN: contact -->
            <div class="panel panel-default">
                <div class="panel-heading">{LANG.contact_info}</div>
                <div class="panel-body">
                    <ul class="list-contact-info">
                        <!-- BEGIN: fullname -->
                        <li><em class="fa fa-user">&nbsp;</em>{DATA.contact_fullname}</li>
                        <!-- END: fullname -->
                        <!-- BEGIN: email -->
                        <li><em class="fa fa-envelope-o">&nbsp;</em><a href="">{LANG.sendmail}</a></li>
                        <!-- END: email -->
                        <!-- BEGIN: phone -->
                        <li><em class="fa fa-phone">&nbsp;</em>{DATA.contact_phone}</li>
                        <!-- END: phone -->
                        <!-- BEGIN: address -->
                        <li><em class="fa fa-map-pin">&nbsp;</em>{DATA.contact_address}</li>
                        <!-- END: address -->
                    </ul>
                </div>
            </div>
            <!-- END: contact -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="socialicon clearfix">
                        <div class="fb-like pull-left" data-href="{SELFURL}" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true">&nbsp;</div>
                        <!-- <div class="g-plusone pull-left" data-size="medium"></div> -->
                        <!-- <a href="http://twitter.com/share" class="pull-left twitter-share-button">Tweet</a> -->
                        <div class="pull-right">
                            <!-- BEGIN: refresh -->
                            <a id="refresh" class="btn btn-warning btn-xs" href="javascript:void(0)" onclick="nv_refresh_popup({DATA.id}); return !1;" title="{LANG.refresh}"><em class="fa fa-refresh fa-lg text-success">&nbsp;</em>{LANG.refresh}</a>
                            <!-- END: refresh -->
                            <a id="save" class="btn btn-default btn-xs" {DATA.style_save} href="javascript:void(0)" onclick="nv_save_rows({DATA.id}, 'add', {DATA.is_user}); return !1;" title="{LANG.save}"><em class="fa fa-floppy-o fa-lg text-success">&nbsp;</em>{LANG.save}</a> <a id="saved" class="btn btn-default btn-xs" {DATA.style_saved} href="javascript:void(0)" onclick="nv_save_rows({DATA.id}, 'remove', {DATA.is_user}); return !1;" title="{LANG.save_remove}"><em class="fa fa-minus-circle fa-lg text-danger">&nbsp;</em>{LANG.save_remove}</a>
                            <!-- BEGIN: admin -->
                            <a href="{DATA.link_edit}" class="btn btn-default btn-xs"><em class="fa fa-edit">&nbsp;</em>{LANG.edit}</a> <a href="{DATA.link_delete}" class="btn btn-default btn-xs" onclick="return confirm(nv_is_del_confirm[0]);"><em class="fa fa-trash-o">&nbsp;</em>{LANG.delete}</a>
                            <!-- END: admin -->
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- BEGIN: admin_keywords -->
    <link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
    <link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2-bootstrap.min.css">
    <div class="panel panel-default">
        <div class="panel-heading">{LANG.tags} ({LANG.tags_note})</div>
        <div class="panel-body">
            <div class="form-group">
                <select class="form-control" name="keywords[]" id="keywords" multiple="multiple">
                    <!-- BEGIN: keywords -->
                    <option value="{KEYWORDS.tid}" selected="selected">{KEYWORDS.title}</option>
                    <!-- END: keywords -->
                </select>
            </div>
            <button class="btn btn-primary" id="tags-save">{LANG.save_change}</button>
        </div>
    </div>
    <script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
    <script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
    <script>
		$(document).ready(function() {
			$('#keywords').select2({
				tags : true,
				language : '{NV_LANG_INTERFACE}',
				theme : 'bootstrap',
				tokenSeparators : [ ',' ],
				ajax : {
					url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + "=ajax&nocache=" + new Date().getTime() + "&get_keywords=1",
					processResults : function(data, page) {
						return {
							results : data
						};
					}
				}
			});
			
			$('#tags-save').click(function(){
				$.ajax({
					type : 'POST',
					url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + "=ajax&nocache=" + new Date().getTime(),
					data : 'tags_save=1&id={DATA.id}&keywords=' + $('#keywords').val(),
					success : function(res) {
						var r_split = res.split('_');
						if(r_split[0] == 'OK'){
							alert(nv_is_change_act_confirm[1]);
						}else{
							alert(nv_is_change_act_confirm[2]);
						}
					}
				});
			});
		});
	</script>
    <!-- END: admin_keywords -->
    <div class="panel panel-default">
        <div class="panel-body" id="content">
            <!-- BEGIN: auction -->
            <div class="row">
                <div class="col-xs-24 col-sm-16 col-md-16">{DATA.content}</div>
                <div class="col-xs-24 col-sm-16 col-md-8">
                    <div class="panel panel-default" id="auction">
                        <div class="panel-heading">{LANG.auction_info}</div>
                        <div class="panel-body">
                            <div id="auction_heading" class="text-center <!-- BEGIN: auction_heading -->hidden<!-- END: auction_heading -->">
                                <h3 id="auction_begin_note">{LANG.auction_begin_note}:</h3>
                                <h2 class="m-bottom text-danger" id="auction-countdown"></h2>
                            </div>
                            <h2 id="auction_heading_end" class="m-bottom text-danger text-center <!-- BEGIN: auction_heading_end -->hidden<!-- END: auction_heading_end -->">{LANG.auction_status_2}</h2>
                            <hr />
                            <ul>
                                <li><label>{LANG.auction_begin}</label> : {DATA.auction_begin_str}</li>
                                <li><label>{LANG.auction_end}</label> : {DATA.auction_end_str}</li>
                                <li><label>{LANG.auction_price_begin}</label> : <span class="money">{DATA.auction_price_begin_str} {MONEY_UNIT}</span></li>
                            </ul>
                            <hr />
                            <div class="panel panel-default">
                                <div class="panel-body text-center" id="auction-max">
                                    {LANG.auction_price_begins}</label> <span class="money">{DATA.auction_price_begin_str} {MONEY_UNIT} 
                                </div>
                            </div>
                            <div id="messagesDiv" style="height: 200px; overflow: scroll; margin-bottom: 10px"></div>
                            <!-- BEGIN: frm_auction -->
                            <form id="frm-auction" action="" method="post" class="m-bottom">
                                <div class="input-group">
                                    <input class="form-control price" type="text" name="auction_value" id="auction_value"
                                    <!-- BEGIN: auction_value_disabled -->
                                    disabled="disabled"
                                    <!-- END: auction_value_disabled -->
                                    /> <span class="input-group-btn">
                                        <button class="btn btn-default" type="submit" id="auction-send"
                                            <!-- BEGIN: auction_value_disabled_btn -->
                                            disabled="disabled"
                                            <!-- END: auction_value_disabled_btn -->
                                            > <em class="fa fa-sign-in">&nbsp;</em>
                                        </button>
                                    </span>
                                </div>
                            </form>
                            <!-- END: frm_auction -->
                            <div class="text-center">
                                <!-- BEGIN: login -->
                                {LANG.auction_login}
                                <!-- END: login -->
                                <!-- BEGIN: register -->
                                <button class="btn btn-xs btn-primary <!-- BEGIN: register_hidden -->hidden<!-- END: register_hidden -->" id="btn-auction-register" onclick="nv_auction_register({DATA.id}); return !1;">{LANG.auction_register}</button>
                                <button class="btn btn-xs btn-primary <!-- BEGIN: cancel_hidden -->hidden<!-- END: cancel_hidden --> " id="btn-auction-cancel" onclick="nv_auction_cancel({DATA.id}); return !1;">{LANG.auction_cancel}</button>
                                <!-- END: register -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript" src="{NV_BASE_SITEURL}themes/default/js/market_autoNumeric-1.9.41.js"></script>
            <script src='https://cdn.firebase.com/v0/firebase.js'></script>
            <script src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/jquery.countdown.min.js"></script>
            <script type="text/javascript">
				var countdown = {DATA.auction_status};
				
				nv_auction_countdown('{DATA.countdown_begin}');
				
				function nv_auction_countdown(timer, stop)
				{
					
					if(stop){
						$('#auction_heading').addClass('hidden');
						$('#auction_heading_end').removeClass('hidden');
						return !1;
					}
					
					$('#auction-countdown').countdown(timer, function(event) {
						$(this).html(event.strftime('{LANG.auction_countdown}'));
						$(this).on('finish.countdown', function(){
							if(countdown == 0){
								countdown = 1;
								nv_auction_countdown('{DATA.countdown_end}');
							}else if(countdown == 1){
								$('#auction_value').prop('disabled', false);
								$('#auction-send').prop('disabled', false);
								$('#auction_value').focus();
								$('#auction_begin_note').text('{LANG.auction_end_note}:');
								nv_auction_countdown('', true);
							}
						});
					});	
				}
				
				var myDataRef = new Firebase('{FIREBASE_URL}');
				$('#frm-auction').submit(function (e) {
					e.preventDefault();
					var price = $('#auction_value').val();
				 
					if(price == ''){
						$('#auction_value').focus();
						alert('{LANG.error_auction_empty_value}');
					} else{ 
						$.ajax({
							type : 'POST',
							url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(),
							data : 'auction_send=1&rowsid={DATA.id}&price=' + price,
							dataType: 'json',
							success : function(json) {
								console.log(json);
					            if(json.status == 'error'){
					            	alert(json.message);
					            }else{
									myDataRef.push({userid: json.userid, name: json.name, price: json.price, addtime: json.addtime});
									$('#auction_value').val('');
					            }
							}
						});
					}
				});
				
				myDataRef.on('child_added', function(snapshot) {
					var message = snapshot.val();
					displayChatMessage(message.name, message.price, message.addtime);
				});
				
				function displayChatMessage(name, price, addtime) {
					$('#auction-max').html('<strong>' + name + '</strong> {LANG.auction_sended} <span class="money">' + price + '({MONEY_UNIT})</span><br />{LANG.auction_momment} ' + addtime);
					$('<div/>').text(price + '({MONEY_UNIT})').prepend($('<em title="' + addtime + '" />').text(name + ' ')).appendTo($('#messagesDiv'));
					$('#messagesDiv')[0].scrollTop = $('#messagesDiv')[0].scrollHeight;
				};
				
				var Options = {
					aSep : '{DES_POINT}',
					aDec : '{THOUSANDS_SEP}',
					vMin : '0',
					vMax : '999999999999999999'
				};
				$('.price').autoNumeric('init', Options);
				$('.price').bind('blur focusout keypress keyup', function() {
					$(this).autoNumeric('get', Options);
				});
			</script>
            <!-- END: auction -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tab-content" aria-controls="tab-content" role="tab" data-toggle="tab">{LANG.content}</a></li>
                <!-- BEGIN: maps_title -->
                <li role="presentation"><a href="#maps" aria-controls="maps" role="tab" data-toggle="tab">{LANG.maps}</a></li>
                <!-- END: maps_title -->
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tab-content">{DATA.content}</div>
                <!-- BEGIN: maps_content -->
                <div role="tabpanel" class="tab-pane" id="maps">
                    <script>
                                                    if (!$('#googleMapAPI').length) {
                                                        var script = document.createElement('script');
                                                        script.type = 'text/javascript';
                                                         script.id = 'googleMapAPI';
                                                        script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&callback=initializeMap&key={MAPS_ADPI}';
                                                        document.body.appendChild(script);
                                                    } else {
                                                        initializeMap();
                                                    }

                                                    function initializeMap() {
                                                        var ele = 'company-map';
                                                        var map, marker, ca, cf, a, f, z;
                                                        ca = parseFloat($('#' + ele).data('clat'));
                                                        cf = parseFloat($('#' + ele).data('clng'));
                                                        a = parseFloat($('#' + ele).data('lat'));
                                                        f = parseFloat($('#' + ele).data('lng'));
                                                        z = parseInt($('#' + ele).data('zoom'));
                                                        map = new google.maps.Map(document.getElementById(ele), {
                                                            zoom : z,
                                                            center : {
                                                                lat : ca,
                                                                lng : cf
                                                            }
                                                        });
                                                        marker = new google.maps.Marker({
                                                            map : map,
                                                            position : new google.maps.LatLng(a, f),
                                                            draggable : false,
                                                            animation : google.maps.Animation.DROP
                                                        });
                                                    }
                                                </script>
                    <div class="m-bottom" id="company-map" style="width: 100%; height: 500px" data-clat="{DATA.maps.maps_mapcenterlat}" data-clng="{DATA.maps.maps_mapcenterlng}" data-lat="{DATA.maps.maps_maplat}" data-lng="{DATA.maps.maps_maplng}" data-zoom="{DATA.maps.maps_mapzoom}"></div
                </div>
                <!-- END: maps_content -->
            </div>
        </div>
        <!-- BEGIN: keywords -->
        <div class="panel-footer">
            <em class="fa fa-tags">&nbsp;</em>{LANG.keywords}:
            <!-- BEGIN: loop -->
            <a title="{KEYWORD}" href="{LINK_KEYWORDS}"><em>{KEYWORD}</em></a>{SLASH}
            <!-- END: loop -->
        </div>
        <!-- END: keywords -->
    </div>
    {BLOCK_1}
    <!-- BEGIN: other -->
    <div class="other">
        <div class="panel panel-default">
            <div class="panel-heading">
                {LANG.other} {DATA.cat} (<a href="{DATA.cat_link}" title="{LANG.viewall}">{LANG.viewall}</a>)
            </div>
            {OTHER}
        </div>
    </div>
    <!-- END: other -->
</div>
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/default/js/lightslider/js/lightslider.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/default/js/fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>
<script>
	var LANG = [];
	LANG.error_save_login = '{LANG.error_save_login}';
	LANG.auction_register_confirm = '{LANG.auction_register_confirm}';
	LANG.auction_cancel = '{LANG.auction_cancel}';
	LANG.auction_register_success = '{LANG.auction_register_success}';
	LANG.auction_cancel_succes = '{LANG.auction_cancel_succes}';
	LANG.auction_cancel_confirm = '{LANG.auction_cancel_confirm}';
	
	$(document).ready(function() {
		$('#lightSlider').lightSlider({
			gallery : true,
			item : 1,
			loop : true,
			slideMargin : 0,
			thumbItem : 8,
			enableDrag: false
		});
		
		$(".fancybox").fancybox({
			openEffect	: 'none',
			closeEffect	: 'none'
		});
	});
</script>
<!-- END: main -->