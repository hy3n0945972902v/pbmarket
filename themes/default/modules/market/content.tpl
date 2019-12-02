<!-- BEGIN: main -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{NV_BASE_SITEURL}themes/default/js/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css" />
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2-bootstrap.min.css" />
<div class="content <!-- BEGIN: popup -->popup<!-- END: popup -->">
    <!-- BEGIN: error -->
    <div class="alert alert-warning">{ERROR}</div>
    <!-- END: error -->
    <div class="alert alert-info">
        <ul class="box-note">
            <!-- BEGIN: guest_note -->
            <li>{LANG.content_guest_note}</li>
            <!-- END: guest_note -->
            <li>{LANG.required_note}</li>
            <li>{LANG.terms_note}</li>
        </ul>
    </div>
    <form class="form-horizontal" action="{NV_BASE_SITEURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}&amp;id={ROW.id}&amp;ispopup={ISPOPUP}&amp;redirect={REDIRECT}" method="post" id="frm-content" autocomplete="off">
        <div class="panel panel-default">
            <div class="panel-heading">{LANG.rowsinfo}</div>
            <div class="panel-body">
                <input type="hidden" name="id" value="{ROW.id}" />
                <div class="form-group">
                    <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.catid}</strong></label>
                    <div class="col-sm-19 col-md-20">
                        <div class="row">
                            <div class="col-xs-24 col-sm-12 col-md-12">
                                <select name="catid" class="form-control required tooltip-focus m-bottom" required="required" oninvalid="setCustomValidity( nv_required )" oninput="setCustomValidity('')" placeholder="{LANG.title}" data-toggle="tooltip" title="{LANG.tooltip_focus_cat}" id="catid">
                                    <option value="">---{LANG.cat_c}---</option>
                                    <!-- BEGIN: cat -->
                                    <option value="{CAT.id}"{CAT.selected}>{CAT.space}{CAT.title}</option>
                                    <!-- END: cat -->
                                </select>
                            </div>
                            <div class="col-xs-24 col-sm-12 col-md-12">
                                <input class="form-control required tooltip-focus" type="text" name="title" value="{ROW.title}" required="required" oninvalid="setCustomValidity( nv_required )" oninput="setCustomValidity('')" placeholder="{LANG.title}" data-toggle="tooltip" title="{LANG.tooltip_focus_title}" maxlength="60" />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- BEGIN: typeid -->
                <div class="form-group">
                    <label class="col-sm-5 col-md-4 text-right"><strong>{LANG.type}</strong></label>
                    <div class="col-sm-19 col-md-20">
                        <!-- BEGIN: loop -->
                        <label><input type="radio" name="typeid" value="{TYPE.id}" {TYPE.checked} />{TYPE.title}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <!-- END: loop -->
                    </div>
                </div>
                <!-- END: typeid -->
                <div id="div-pricetype">{PRICETYPE}</div>
                <!-- BEGIN: description -->
                <div class="form-group form-tooltip">
                    <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.description_s}</strong> <em class="fa fa-question-circle fa-pointer text-info" data-toggle="tooltip" data-original-title="{LANG.description_note}">&nbsp;</em></label>
                    <div class="col-sm-19 col-md-20">
                        <textarea class="form-control" name="description">{ROW.description}</textarea>
                    </div>
                </div>
                <!-- END: description -->
                <div class="form-group">
                    <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.content}</strong></label>
                    <div class="col-sm-19 col-md-20">
                        {ROW.content}
                        <!-- BEGIN: editor_guest_note -->
                        <small class="help-block"><em>{LANG.editor_guest_note}</em></small>
                        <!-- END: editor_guest_note -->
                    </div>
                </div>
                <!-- BEGIN: note -->
                <div class="form-group">
                    <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.note}</strong></label>
                    <div class="col-sm-19 col-md-20">
                        <textarea class="form-control required" cols="75" rows="5" name="note">{ROW.note}</textarea>
                    </div>
                </div>
                <!-- END: note -->
                <!-- BEGIN: exptime -->
                <div class="form-group">
                    <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.exptime}</strong></label>
                    <div class="col-sm-19 col-md-20">
                        <div class="row m-bottom">
                            <div class="col-xs-8 col-sm-8 col-md-6">
                                <select name="begintime_hour" class="form-control">
                                    <option value="0">---{LANG.hour_select}---</option>
                                    <!-- BEGIN: hour -->
                                    <option value="{HOUR.index}"{HOUR.selected}>{HOUR.index}</option>
                                    <!-- END: hour -->
                                </select>
                            </div>
                            <div class="col-xs-8 col-sm-8 col-md-6">
                                <select name="begintime_min" class="form-control">
                                    <option value="0">---{LANG.min_select}---</option>
                                    <!-- BEGIN: min -->
                                    <option value="{MIN.index}"{MIN.selected}>{MIN.index}</option>
                                    <!-- END: min -->
                                </select>
                            </div>
                            <div class="col-xs-8 col-sm-8 col-md-12">
                                <div class="input-group">
                                    <input class="form-control datepicker" type="text" name="exptime" value="{ROW.exptimef}" pattern="^[0-9]{2,2}\/[0-9]{2,2}\/[0-9]{1,4}$" /> <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="exptime-btn">
                                            <em class="fa fa-calendar fa-fix"> </em>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: exptime -->
            </div>
        </div>
        <!-- BEGIN: auction -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <label><input type="checkbox" name="auction" value="1" {ROW.ck_auction} />{LANG.auction}</label>
            </div>
            <div class="panel-body" id="auction-block"{ROW.auction_style}>
                <label><strong>{LANG.auction_begin}</strong></label>
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-6">
                            <select name="auction_begin_hour" class="form-control">
                                <option value="0">---{LANG.hour_select}---</option>
                                <!-- BEGIN: auction_begin_hour -->
                                <option value="{HOUR.index}"{HOUR.selected}>{HOUR.index}</option>
                                <!-- END: auction_begin_hour -->
                            </select>
                        </div>
                        <div class="col-xs-6">
                            <select name="auction_begin_min" class="form-control">
                                <option value="0">---{LANG.min_select}---</option>
                                <!-- BEGIN: auction_begin_min -->
                                <option value="{MIN.index}"{MIN.selected}>{MIN.index}</option>
                                <!-- END: auction_begin_min -->
                            </select>
                        </div>
                        <div class="col-xs-12">
                            <div class="input-group">
                                <input class="form-control datepicker required" type="text" name="auction_begin_date" value="{ROW.auction_beginf}" pattern="^[0-9]{2,2}\/[0-9]{2,2}\/[0-9]{1,4}$" /> <span class="input-group-btn">
                                    <button class="btn btn-default" type="button">
                                        <em class="fa fa-calendar fa-fix"> </em>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <label><strong>{LANG.auction_end}</strong></label>
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-6">
                            <select name="auction_end_hour" class="form-control">
                                <option value="0">---{LANG.hour_select}---</option>
                                <!-- BEGIN: auction_end_hour -->
                                <option value="{HOUR.index}"{HOUR.selected}>{HOUR.index}</option>
                                <!-- END: auction_end_hour -->
                            </select>
                        </div>
                        <div class="col-xs-6">
                            <select name="auction_end_min" class="form-control">
                                <option value="0">---{LANG.min_select}---</option>
                                <!-- BEGIN: auction_end_min -->
                                <option value="{MIN.index}"{MIN.selected}>{MIN.index}</option>
                                <!-- END: auction_end_min -->
                            </select>
                        </div>
                        <div class="col-xs-12">
                            <div class="input-group">
                                <input class="form-control datepicker required" type="text" name="auction_end_date" value="{ROW.auction_endf}" pattern="^[0-9]{2,2}\/[0-9]{2,2}\/[0-9]{1,4}$" /> <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="exptime-btn">
                                        <em class="fa fa-calendar fa-fix"> </em>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <label><strong>{LANG.auction_price_begin}</strong></label>
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" name="auction_price_begin" value="{ROW.auction_price_begin}" class="form-control required price" /> <span class="input-group-addon">{MONEY_UNIT}</span>
                    </div>
                </div>
                <label><strong>{LANG.auction_price_step}</strong></label>
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" name="auction_price_step" value="{ROW.auction_price_step}" class="form-control required price" /> <span class="input-group-addon">{MONEY_UNIT}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- END: auction -->
        <!-- BEGIN: images -->
        <div class="panel panel-default">
            <div class="panel-heading">{LANG.image}</div>
            <div class="panel-body">
                <blockquote>
                    <ul>
                        <li>{LANG.maxsizeimage}: {USER_CONFIG.max_width}x{USER_CONFIG.max_height}</li>
                        <li>{LANG.maxsize}: {USER_CONFIG.max_filesize}</li>
                    </ul>
                </blockquote>
                <div class="row">
                    <div class="col-xs-24 col-sm-12 col-md-12">
                        <div id="uploader">
                            <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
                        </div>
                    </div>
                    <div class="col-xs-24 col-sm-12 col-md-12">
                        <div id="imagelist" style="border: solid 1px #ddd; padding: 10px; height: 360px">
                            <!-- BEGIN: loop -->
                            <div class="image <!-- BEGIN: is_main -->is_main<!-- END: is_main -->">
                                <em class="fa fa-times-circle fa-lg fa-pointer" title="{LANG.image_delete}" onclick="$(this).parent().remove();">&nbsp;</em>
                                <div class="row m-bottom">
                                    <div class="col-xs-24 col-sm-4 col-md-4 text-center">
                                        <input type="hidden" name="images[{IMAGE.index}][path]" value="{IMAGE.homeimgfile}" /> <img class="img-thumbnail" src="{IMAGE.path}" width="100%" />
                                    </div>
                                    <div class="col-xs-24 col-sm-20 col-md-20" style="overflow: hidden;">
                                        <h2>{IMAGE.basename}</h2>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-18 col-md-18">
                                                <input type="text" name="images[{IMAGE.index}][description]" value="{IMAGE.description}" class="form-control input-sm" placeholder="{LANG.image_description}" />
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-6">
                                                <label class="is_main"><input type="radio" name="is_main" onclick="nv_image_main($(this));" value="{IMAGE.index}" {IMAGE.ch_is_main} />{LANG.image_main}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END: loop -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END: images -->
        <div class="panel panel-default">
            <div class="panel-heading">
                {LANG.location}<label class="pull-right"><input type="checkbox" name="display_maps" value="1" id="display_maps"{ROW.ck_display_maps}>{LANG.display_maps}</label>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">
                            <input type="text" class="form-control" name="address" value="{ROW.address}" placeholder="{LANG.contact_address}" />
                        </div>
                        <div class="col-xs-12">{LOCATION}</div>
                    </div>
                </div>
                <div id="maps">
                    <!-- BEGIN: maps -->
                    {MAPS}
                    <!-- END: maps -->
                </div>
                <!-- BEGIN: required_maps_appid -->
                <div class="alert alert-danger">{LANG.error_required_maps_appid}</div>
                <!-- END: required_maps_appid -->
            </div>
        </div>
        <div id="custom_form">{DATACUSTOM_FORM}</div>
        <div class="panel panel-default">
            <div class="panel-heading">{LANG.contact_info}</div>
            <div class="panel-body">
                <div class="row m-bottom">
                    <div class="col-xs-24 col-sm-12 col-md-12">
                        <label><strong>{LANG.contact_fullname}</strong></label> <input class="form-control" type="text" name="contact_fullname" value="{ROW.contact_fullname}" />
                    </div>
                    <div class="col-xs-24 col-sm-12 col-md-12 form-tooltip">
                        <label><strong>{LANG.contact_email}</strong> <em class="fa fa-question-circle fa-pointer text-info" data-toggle="tooltip" data-original-title="{LANG.contact_email_note}">&nbsp;</em></label> <input class="form-control" type="email" name="contact_email" value="{ROW.contact_email}" oninvalid="setCustomValidity( nv_email )" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-24 col-sm-12 col-md-12">
                        <label><strong>{LANG.contact_phone}</strong></label> <input class="form-control" type="text" name="contact_phone" value="{ROW.contact_phone}" />
                    </div>
                    <div class="col-xs-24 col-sm-12 col-md-12">
                        <label><strong>{LANG.contact_address}</strong></label> <input class="form-control" type="text" name="contact_address" value="{ROW.contact_address}" />
                    </div>
                </div>
            </div>
        </div>
        <!-- BEGIN: requeue -->
        <div class="alert alert-warning text-center">
            <p>{LANG.requeue_note}</p>
            <label class="show"><input type="checkbox" name="requeue" value="1">{LANG.requeue}</label>
        </div>
        <!-- END: requeue -->
        <!-- BEGIN: captcha -->
        <div class="form-group text-center">
            <div class="middle clearfix">
                <img width="{GFX_WIDTH}" height="{GFX_HEIGHT}" title="{LANG.captcha}" alt="{LANG.captcha}" src="{NV_BASE_SITEURL}index.php?scaptcha=captcha&t={NV_CURRENTTIME}" class="captchaImg display-inline-block"> <em onclick="change_captcha('.fcode');" title="{GLANG.captcharefresh}" class="fa fa-pointer fa-refresh margin-left margin-right"></em> <input type="text" placeholder="{LANG.captcha}" maxlength="{NV_GFX_NUM}" value="" name="fcode" class="fcode required form-control display-inline-block" style="width: 100px;" data-pattern="/^(.){{NV_GFX_NUM},{NV_GFX_NUM}}$/" onkeypress="nv_validErrorHidden(this);" data-mess="{LANG.error_captcha}" />
            </div>
        </div>
        <!-- END: captcha -->
        <!-- BEGIN: recaptcha -->
        <div class="form-group">
            <div class="middle text-center clearfix">
                <div class="nv-recaptcha-default">
                    <div id="{RECAPTCHA_ELEMENT}"></div>
                </div>
                <script type="text/javascript">
                nv_recaptcha_elements.push({
                    id: "{RECAPTCHA_ELEMENT}",
                    btn: $('[type="submit"]', $('#{RECAPTCHA_ELEMENT}').parent().parent().parent().parent())
                })
                </script>
            </div>
        </div>
        <!-- END: recaptcha -->
        <div class="form-group text-center">
            <!-- BEGIN: fullform -->
            <a class="btn btn-success" href="{URL_CONTENT}">{LANG.fullform}</a>
            <!-- END: fullform -->
            <input class="btn btn-primary loading" name="submit" type="submit" value="{LANG.submit}" />
        </div>
    </form>
</div>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/default/js/market_autoNumeric-1.9.41.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
<!-- BEGIN: images_js -->
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/default/js/plupload/plupload.full.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/default/js/plupload/jquery.plupload.queue/jquery.plupload.queue.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/default/js/plupload/i18n/{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript">
// Initialize the widget when the DOM is ready
$(function() {
	
	var i = {COUNT};
	
	$('#imagelist').slimScroll({
        height: '360px'
    });
	
    // Setup html5 version
    $("#uploader").pluploadQueue({
        // General settings
        runtimes : 'html5,flash,silverlight,html4',
        url : "{UPLOAD_URL}",
         
        chunk_size : '1mb',
        rename : true,
        dragdrop: true,
 
        // Flash settings
        flash_swf_url : '{NV_BASE_SITEURL}themes/default/js/plupload/Moxie.swf',
     
        // Silverlight settings
        silverlight_xap_url : '{NV_BASE_SITEURL}themes/default/js/plupload/Moxie.xap',
        
        init: {
			FilesAdded: function (up, files) {
				this.start();
				return false;
			},
			UploadComplete: function (up, files) {
				$('#imagelist').slimScroll({
	                height: '360px'
	            });
			},
            FileUploaded: function(up, file, response) {
				var content = $.parseJSON(response.response);
				var item = '';
				item += '<div class="image">';
				item += '<em class="fa fa-times-circle fa-lg fa-pointer" title="{LANG.image_delete}" onclick="$(this).parent().remove();">&nbsp;</em>';
				item += '<div class="row m-bottom">';
				item += '	<div class="col-xs-24 col-sm-4 col-md-4 text-center">';
				item += '		<input type="hidden" name="images[' + i + '][path]" value="' + content.homeimgfile + '" />';
				item += '		<img class="img-thumbnail" src="' + content.path + '" width="100%" />';
				item += '	</div>';
				item += '	<div class="col-xs-24 col-sm-20 col-md-20" style="overflow: hidden;">';
				item += '		<h2>' + content.basename + '</h2>';
				item += '		<div class="row">';
				item += '			<div class="col-xs-12 col-sm-18 col-md-18">';
				item += '				<input type="text" name="images[' + i + '][description]" class="form-control input-sm" placeholder="{LANG.image_description}" />';
				item += '			</div>';
				item += '			<div class="col-xs-12 col-sm-6 col-md-6">';
				item += '				<label class="is_main"><input type="radio" name="is_main" onclick="nv_image_main($(this));" value="' + i + '" />{LANG.image_main}</label>';
				item += '			</div>';
				item += '		</div>';
				item += '	</div>';
				item += '</div>';
				item += '</div>';
				$('#imagelist').append(item);
				nv_image_main_check();
				++i;
            }
		}
    });
});
</script>
<!-- END: images_js -->
<script>
	$(document).ready(function() {

		$('.tooltip-focus').tooltip({
		    trigger: "focus"
		});
		
		$(".datepicker").datepicker({
			dateFormat : "dd/mm/yy",
			changeMonth : true,
			changeYear : true,
			showOtherMonths : true,
			showOn : "focus",
			yearRange : "-90:+10",
		});

		$('#catid').change(function() {
			$.ajax({
				type : 'POST',
				url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(),
				data : 'load_pricetype=1&catid=' + $(this).val() + '&pricetype=' + $('#pricetype').val() + '&price=' + $('#price').val() + '&price1=' + $('#price1').val() + '&unitid=' + $('#unitid').val(),
				success : function(data) {
					$('#div-pricetype').html(data);
				}
			});
			
			nv_market_cat_change({ROW.id}, $(this).val());
		});
		
		$('input[name="auction"]').change(function(){
			if($(this).is(':checked')){
				$('#auction-block').slideDown();
			}else{
				$('#auction-block').slideUp();
			}
		});
		
		$('#display_maps').change(function(){
		    if($(this).is(':checked')){
		        $.ajax({
					type : 'POST',
					url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(),
					data : 'load_maps=1',
					success : function(html) {
					    $('#maps').html(html);
					}
				});
		    }else{
		        $('#maps').html('');
		    }
		});
	});
	
	function nv_get_alias(id) {
		var title = strip_tags($("[name='title']").val());
		if (title != '') {
			$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name
					+ '&' + nv_fc_variable + '=content&nocache='
					+ new Date().getTime(), 'get_alias_title='
					+ encodeURIComponent(title), function(res) {
				$("#" + id).val(strip_tags(res));
			});
		}
		return false;
	}
	
	function nv_image_main($_this)
	{
		$('#imagelist .image').each(function(){
			$(this).removeClass('is_main');
		});
		
		if($_this.is(':checked')){
			$_this.closest('.image').addClass('is_main');
		}
	}

	function nv_image_main_check()
	{
		if($('input[name="is_main"]:checked').length == 0){
			$('#imagelist .image:first').addClass('is_main');
			$('input[name="is_main"]:first').prop('checked', true);
		}
	}
</script>
<!-- BEGIN: auto_get_alias -->
<script>
	//<![CDATA[
	$("[name='title']").change(function() {
		nv_get_alias('id_alias');
	});
	//]]>
</script>
<!-- END: auto_get_alias -->
<!-- BEGIN: check_similar_content -->
<script>
	$(document).ready(function() {
		CKEDITOR.instances.market_content.on('blur', function() {
			var content = CKEDITOR.instances['market_content'].getData();
			if(content != ''){
				$.ajax({
					type : 'POST',
					url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&check_similar_content=1&nocache=' + new Date().getTime(),
					data : {
						html: content
					},
					success : function(res) {
						if(res != 'OK'){
							alert('{LANG.error_similar_content}');
							CKEDITOR.instances['market_content'].focus();
						}
					}
				});	
			}
		});
	});
</script>
<!-- END: check_similar_content -->
<!-- END: main -->