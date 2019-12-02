<!-- BEGIN: main -->
<!-- BEGIN: view -->
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <colgroup>
                <col class="w100" />
                <col />
                <col class="w200" />
                <col class="w150" />
                <col class="w200" />
                <col class="w150" />
                <col class="w150" />
            </colgroup>
            <thead>
                <tr>
                    <th>{LANG.weight}</th>
                    <th>{LANG.cat}</th>
                    <th class="text-center">{LANG.cat_inhome}</th>
                    <th class="text-center">{LANG.cat_numlinks}</th>
                    <th class="text-center">{LANG.cat_hometype}</th>
                    <th class="text-center">{LANG.active}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <!-- BEGIN: generate_page -->
            <tfoot>
                <tr>
                    <td class="text-center" colspan="5">{NV_GENERATE_PAGE}</td>
                </tr>
            </tfoot>
            <!-- END: generate_page -->
            <tbody>
                <!-- BEGIN: loop -->
                <tr>
                    <td><select class="form-control" id="id_weight_{VIEW.id}" onchange="nv_change_weight('{VIEW.id}');">
                            <!-- BEGIN: weight_loop -->
                            <option value="{WEIGHT.key}"{WEIGHT.selected}>{WEIGHT.title}</option>
                            <!-- END: weight_loop -->
                    </select></td>
                    <td><a href="{VIEW.link_view}" title="{VIEW.title}">{VIEW.title}</a> <span class="red">({VIEW.numsub})</span></td>
                    <td class="text-center"><select class="form-control" id="id_inhome_{VIEW.id}" onchange="nv_chang_cat('{VIEW.id}','inhome');">
                            <!-- BEGIN: inhome -->
                            <option value="{INHOME.key}"{INHOME.selected}>{INHOME.value}</option>
                            <!-- END: inhome -->
                    </select></td>
                    <td><select class="form-control" id="id_numlinks_{VIEW.id}" onchange="nv_chang_cat('{VIEW.id}','numlinks');">
                            <!-- BEGIN: numlinks -->
                            <option value="{NUMLINKS.key}"{NUMLINKS.selected}>{NUMLINKS.title}</option>
                            <!-- END: numlinks -->
                    </select></td>
                    <td><select class="form-control" id="id_viewtype_{VIEW.id}" onchange="nv_chang_cat('{VIEW.id}','viewtype');">
                            <!-- BEGIN: viewtype -->
                            <option value="{VIEWTYPE.key}"{VIEWTYPE.selected}>{VIEWTYPE.value}</option>
                            <!-- END: viewtype -->
                    </select></td>
                    <td class="text-center"><input type="checkbox" name="status" id="change_status_{VIEW.id}" value="{VIEW.id}" {CHECK} onclick="nv_change_status({VIEW.id});" /></td>
                    <td class="text-center"><i class="fa fa-edit fa-lg">&nbsp;</i><a href="{VIEW.link_edit}#edit">{LANG.edit}</a> - <em class="fa fa-trash-o fa-lg">&nbsp;</em><a href="{VIEW.link_delete}" onclick="return confirm(nv_is_del_confirm[0]);">{LANG.delete}</a></td>
                </tr>
                <!-- END: loop -->
            </tbody>
        </table>
    </div>
</form>
<!-- END: view -->
<h3>{LANG.cat_add}</h3>
<!-- BEGIN: error -->
<div class="alert alert-warning">{ERROR}</div>
<!-- END: error -->
<div class="panel panel-default">
    <div class="panel-body">
        <form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
            <input type="hidden" name="id" value="{ROW.id}" />
            <div class="form-group">
                <label class="col-sm-4 control-label"><strong>{LANG.title}</strong> <span class="red">*</span></label>
                <div class="col-sm-20">
                    <input class="form-control" type="text" name="title" value="{ROW.title}" required="required" oninvalid="setCustomValidity( nv_required )" oninput="setCustomValidity('')" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.alias}</strong></label>
                <div class="col-sm-14 col-md-20">
                    <div class="input-group">
                        <input class="form-control" type="text" name="alias" value="{ROW.alias}" id="id_alias" /> <span class="input-group-btn">
                            <button class="btn btn-default" type="button">
                                <i class="fa fa-refresh fa-lg" onclick="nv_get_alias('id_alias');">&nbsp;</i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><strong>{LANG.cat_parent}</strong> </label>
                <div class="col-sm-20">
                    <select class="form-control" name="parentid">
                        <!-- BEGIN: parent_loop -->
                        <option value="{pid}"{pselect}>{ptitle}</option>
                        <!-- END: parent_loop -->
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><strong>{LANG.custom_title}</strong></label>
                <div class="col-sm-20">
                    <input class="form-control" type="text" name="custom_title" value="{ROW.custom_title}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><strong>{LANG.description}</strong></label>
                <div class="col-sm-20">
                    <textarea class="form-control" style="height: 100px;" cols="75" rows="5" name="description">{ROW.description}</textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.image}</strong></label>
                <div class="col-sm-19 col-md-20">
                    <div class="input-group">
                        <input class="form-control" type="text" name="image" value="{ROW.image}" id="id_image" /> <span class="input-group-btn">
                            <button class="btn btn-default selectfile" type="button">
                                <em class="fa fa-folder-open-o fa-fix">&nbsp;</em>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><strong>{LANG.keywords}</strong></label>
                <div class="col-sm-20">
                    <input class="form-control" type="text" name="keywords" value="{ROW.keywords}" /> <span class="help-block">{LANG.keywords_note}</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><strong>{LANG.description_html}</strong></label>
                <div class="col-sm-20">{ROW.description_html}</div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 text-right"><strong>{LANG.groups_view}</strong></label>
                <div class="col-sm-20" style="height: 200px; overflow: scroll; border: solid 1px #ddd; padding: 10px">
                    <!-- BEGIN: groups_view -->
                    <label class="show"><input type="checkbox" name="groups_view[]" value="{GROUPS_VIEW.value}" {GROUPS_VIEW.checked} />{GROUPS_VIEW.title}</label>
                    <!-- END: groups_view -->
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><strong>{LANG.pricetype_cat}</strong></label>
                <div class="col-sm-20">
                    <select name="pricetype" class="form-control">
                        <!-- BEGIN: pricetype -->
                        <option value="{PRICETYPE.index}"{PRICETYPE.selected}>{PRICETYPE.value}</option>
                        <!-- END: pricetype -->
                    </select>
                </div>
            </div>
            <!-- BEGIN: cat_form -->
            <div class="form-group">
                <label class="col-sm-4 text-right"><strong>{LANG.cat_form}</strong></label>
                <div class="col-sm-20">
                    <!-- BEGIN: loop -->
                    <div>
                        <label><input type="checkbox" name="cat_form[]" value="{CAT_FORM.value}"{CAT_FORM.checked}> {CAT_FORM.title}</label>
                    </div>
                    <!-- END: loop -->
                </div>
            </div>
            <!-- END: cat_form -->
            <div class="form-group" style="text-align: center">
                <input class="btn btn-primary" name="submit" type="submit" value="{LANG.save}" />
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
	//<![CDATA[
	$(".selectfile").click(function() {
		var area = "id_image";
		var path = "{NV_UPLOADS_DIR}/{MODULE_UPLOAD}";
		var currentpath = "{CURENTPATH}";
		var type = "image";
		nv_open_browse(script_name + "?" + nv_name_variable
				+ "=upload&popup=1&area=" + area + "&path="
				+ path + "&type=" + type + "&currentpath="
				+ currentpath, "NVImg", 850, 420,
				"resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
		return false;
	});

	function nv_change_weight(id) {
		var nv_timer = nv_settimeout_disable('id_weight_' + id, 5000);
		var new_vid = $('#id_weight_' + id).val();
		$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&nocache=' + new Date().getTime(), 'ajax_action=1&id=' + id + '&new_vid=' + new_vid, function(res) {
			var r_split = res.split('_');
			if (r_split[0] != 'OK') {
				alert(nv_is_change_act_confirm[2]);
			}
			clearTimeout(nv_timer);
			window.location.href = window.location.href;
			return;
		});
		return;
	}

	function nv_change_status(id) {
		var new_status = $('#change_status_' + id).is(':checked') ? true : false;
		if (confirm(nv_is_change_act_confirm[0])) {
			var nv_timer = nv_settimeout_disable('change_status_' + id, 5000);
			$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&nocache=' + new Date().getTime(), 'change_status=1&id=' + id, function(res) {
				var r_split = res.split('_');
				if (r_split[0] != 'OK') {
					alert(nv_is_change_act_confirm[2]);
				}
			});
		}
		else{
			$('#change_status_' + id).prop('checked', new_status ? false : true );
		}
		return;
	}

	function nv_get_alias(id) {
		var title = strip_tags($("[name='title']").val());
		if (title != '') {
			$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&nocache=' + new Date().getTime(), 'get_alias_title=' + encodeURIComponent(title), function(res) {
				$("#" + id).val(strip_tags(res));
			});
		}
		return false;
	}
	//]]>
</script>
<!-- BEGIN: auto_get_alias -->
<script type="text/javascript">
	//<![CDATA[
	$("[name='title']").change(function() {
		nv_get_alias('id_alias');
	});
	//]]>
</script>
<!-- END: auto_get_alias -->
<!-- END: main -->