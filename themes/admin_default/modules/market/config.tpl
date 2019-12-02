<!-- BEGIN: main -->
<form action="" method="post" class="form-horizontal">
    <div class="panel panel-default">
        <div class="panel-heading">{LANG.config_system}</div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_homedata}</strong></label>
                <div class="col-sm-21">
                    <div class="row">
                        <div class="col-xs-24 col-sm-12">
                            <select name="homedata" class="form-control">
                                <!-- BEGIN: homedata -->
                                <option value="{HOMEDATA.index}"{HOMEDATA.selected}>{HOMEDATA.value}</option>
                                <!-- END: homedata -->
                            </select>
                        </div>
                        <div class="col-xs-24 col-sm-12">
                            <select name="hometype" class="form-control">
                                <!-- BEGIN: hometype -->
                                <option value="{HOMETYPE.index}"{HOMETYPE.selected}>{HOMETYPE.value}</option>
                                <!-- END: hometype -->
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_style_default}</strong></label>
                <div class="col-sm-21">
                    <select name="style_default" class="form-control">
                        <!-- BEGIN: style_default -->
                        <option value="{STYLE_DEFAULT.index}"{STYLE_DEFAULT.selected}>{STYLE_DEFAULT.value}</option>
                        <!-- END: style_default -->
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_structure_upload}</strong></label>
                <div class="col-sm-21">
                    <select name="structure_upload" class="form-control">
                        <!-- BEGIN: structure_upload -->
                        <option value="{STRUCTURE_UPLOAD.key}"{STRUCTURE_UPLOAD.selected}>{STRUCTURE_UPLOAD.title}</option>
                        <!-- END: structure_upload -->
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_money_unit}</strong></label>
                <div class="col-sm-21">
                    <input type="text" name="money_unit" class="form-control" value="{DATA.money_unit}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 text-right"><strong>{LANG.config_allow_auto_code}</strong></label>
                <div class="col-sm-21">
                    <label><input type="checkbox" name="allow_auto_code" value="1" {DATA.ck_allow_auto_code} />{LANG.config_allow_auto_code_note}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_code_format}</strong></label>
                <div class="col-sm-21">
                    <input type="text" name="code_format" class="form-control" value="{DATA.code_format}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_numother}</strong></label>
                <div class="col-sm-21">
                    <input type="number" name="numother" class="form-control" value="{DATA.numother}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_per_page}</strong></label>
                <div class="col-sm-21">
                    <input type="number" name="per_page" class="form-control" value="{DATA.per_page}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_no_image}</strong></label>
                <div class="col-sm-21">
                    <input type="text" name="no_image" class="form-control" value="{DATA.no_image}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_home_image_size}</strong></label>
                <div class="col-sm-21">
                    <div class="row">
                        <div class="col-xs-12">
                            <input type="text" name="home_image_size_w" class="form-control" value="{DATA.home_image_size_w}" />
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="home_image_size_h" class="form-control" value="{DATA.home_image_size_h}" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 text-right"><strong>{LANG.config_freelancegroup}</strong></label>
                <div class="col-sm-21" style="border: 1px solid #ddd; padding: 10px; height: 200px; overflow: scroll;">
                    <!-- BEGIN: freelancegroup -->
                    <label class="show"><input type="radio" name="freelancegroup" value="{FREELANCEGROUP.value}" {FREELANCEGROUP.checked} />{FREELANCEGROUP.title}</label>
                    <!-- END: freelancegroup -->
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_similar_content}</strong> <em class="fa fa-question-circle fa-pointer text-info" data-toggle="tooltip" data-original-title="{LANG.config_similar_content_note}">&nbsp;</em></label>
                <div class="col-sm-21">
                    <input type="number" name="similar_content" class="form-control" value="{DATA.similar_content}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_similar_time}</strong> <em class="fa fa-question-circle fa-pointer text-info" data-toggle="tooltip" data-original-title="{LANG.config_similar_time_note}">&nbsp;</em></label>
                <div class="col-sm-21">
                    <input type="number" name="similar_time" class="form-control" value="{DATA.similar_time}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_payport}</strong></label>
                <div class="col-sm-21">
                    <select name="payport" class="form-control">
                        <option value="0">---{LANG.config_payport_select}---</option>
                        <!-- BEGIN: payport -->
                        <option value="{PAYPORT.index}"{PAYPORT.selected}>{PAYPORT.value}</option>
                        <!-- END: payport -->
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_maps_appid}</strong></label>
                <div class="col-sm-21">
                    <input type="text" name="maps_appid" class="form-control" value="{DATA.maps_appid}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_priceformat}</strong></label>
                <div class="col-sm-21">
                    <select name="priceformat" class="form-control">
                        <!-- BEGIN: priceformat -->
                        <option value="{PFORMAT.index}"{PFORMAT.selected}>{PFORMAT.value}</option>
                        <!-- END: priceformat -->
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">{LANG.tags}</div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-3 text-right"><strong>{LANG.tags_alias_lower}</strong></label>
                <div class="col-sm-21">
                    <label><input type="checkbox" value="1" name="tags_alias_lower" {DATA.ck_tags_alias_lower}/>{LANG.tags_alias_lower_note}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 text-right"><strong>{LANG.tags_alias}</strong></label>
                <div class="col-sm-21">
                    <label><input type="checkbox" value="1" name="tags_alias" {DATA.ck_tags_alias}/>{LANG.tags_alias_note}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 text-right"><strong>{LANG.auto_tags}</strong></label>
                <div class="col-sm-21">
                    <label><input type="checkbox" value="1" name="auto_tags" {DATA.ck_auto_tags}/>{LANG.auto_tags_note}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 text-right"><strong>{LANG.tags_remind}</strong></label>
                <div class="col-sm-21">
                    <label><input type="checkbox" value="1" name="tags_remind" {DATA.ck_tags_remind}/>{LANG.tags_remind_note}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">{LANG.config_fb}</div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-3 text-right"><strong>{LANG.config_fb}</strong></label>
                <div class="col-sm-21">
                    <label><input type="checkbox" value="1" name="fb_enable" {DATA.ck_fb_enable}/>{LANG.config_fb_enable}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">{LANG.config_user}</div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-3 text-right"><strong>{LANG.config_grouppost}</strong></label>
                <div class="col-sm-21">
                    <div class="row">
                        <div class="col-xs-24 col-sm-6">
                            <div style="border: 1px solid #ddd; padding: 10px; height: 200px; overflow: scroll;">
                                <!-- BEGIN: grouppost -->
                                <label class="show"><input title="{GROUPPOST.title}" class="grouppost" type="checkbox" name="grouppost[]" value="{GROUPPOST.value}" {GROUPPOST.checked} />{GROUPPOST.title}</label>
                                <!-- END: grouppost -->
                            </div>
                        </div>
                        <div class="col-xs-24 col-sm-18">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{LANG.config_groupuser}</th>
                                        <th class="w150 text-center">{LANG.queue_add}</th>
                                        <th class="w150 text-center">{LANG.queue_edit}</th>
                                        <th class="w150 text-center">{LANG.config_maxpost} <em class="fa fa-question-circle fa-pointer text-info" data-toggle="tooltip" data-original-title="{LANG.config_maxpost_note}">&nbsp;</em></th>
                                    </tr>
                                </thead>
                                <tbody id="groupconfig">
                                    <!-- BEGIN: groupconfig -->
                                    <tr id="row_{GROUPCONFIG.groupid}">
                                        <td class="hidden"><input type="hidden" name="grouppostconfig[{GROUPCONFIG.groupid}][groupid]" value="{GROUPCONFIG.groupid}" /></td>
                                        <td>{GROUPCONFIG.title}</td>
                                        <td class="text-center"><input type="checkbox" name="grouppostconfig[{GROUPCONFIG.groupid}][queue]" value="1" {GROUPCONFIG.ck_queue} /></td>
                                        <td class="text-center"><input type="checkbox" name="grouppostconfig[{GROUPCONFIG.groupid}][queue_edit]" value="1" {GROUPCONFIG.ck_queue_edit} /></td>
                                        <td><input type="number" class="form-control input-sm" name="grouppostconfig[{GROUPCONFIG.groupid}][maxpost]" value="{GROUPCONFIG.maxpost}" /></td>
                                    </tr>
                                    <!-- END: groupconfig -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_maxsizeimage}</strong></label>
                <div class="col-sm-21">
                    <div class="row">
                        <div class="col-xs-12">
                            <input type="text" name="maxsizeimage_w" class="form-control" value="{DATA.maxsizeimage_w}" />
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="maxsizeimage_h" class="form-control" value="{DATA.maxsizeimage_h}" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_maxsizeupload}</strong></label>
                <div class="col-sm-21">
                    <select name="maxsizeupload" class="form-control">
                        <!-- BEGIN: maxfilesize -->
                        <option value="{SIZE.key}"{SIZE.selected}>{SIZE.title}</option>
                        <!-- END: maxfilesize -->
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_googlemaps_appid}</strong></label>
                <div class="col-sm-21">
                    <input type="text" name="googlemaps_appid" class="form-control" value="{DATA.googlemaps_appid}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_row_default_group} <em class="fa fa-question-circle fa-pointer text-info" data-toggle="tooltip" data-original-title="{LANG.config_row_default_group_note}">&nbsp;</em></strong></label>
                <div class="col-sm-21">
                    <div style="border: 1px solid #ddd; padding: 10px; height: 200px; overflow: scroll;">
                        <!-- BEGIN: usergrouppost -->
                        <label class="show"><input title="{USERGROUPPOST.title}" type="checkbox" name="usergrouppost[]" value="{USERGROUPPOST.index}" {USERGROUPPOST.checked} />{USERGROUPPOST.value}</label>
                        <!-- END: usergrouppost -->
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 text-right"><strong>{LANG.config_editor_guest}</strong></label>
                <div class="col-sm-21">
                    <label><input type="checkbox" value="1" name="editor_guest" {DATA.ck_editor_guest}/>{LANG.config_editor_guest_note}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">{LANG.auction}</div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-3 text-right"><strong>{LANG.config_auction}</strong></label>
                <div class="col-sm-21">
                    <label><input type="checkbox" value="1" name="auction" {DATA.ck_auction} />{LANG.config_auction_note}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 text-right"><strong>{LANG.config_auction_group}</strong></label>
                <div class="col-sm-21">
                    <div style="border: 1px solid #ddd; padding: 10px; height: 200px; overflow: scroll;">
                        <!-- BEGIN: auction_group -->
                        <label class="show"><input title="{AUCTION_GROUP.title}" type="checkbox" name="auction_group[]" value="{AUCTION_GROUP.value}" {AUCTION_GROUP.checked} />{AUCTION_GROUP.title}</label>
                        <!-- END: auction_group -->
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 text-right"><strong>{LANG.config_auction_register_time}</strong></label>
                <div class="col-sm-21">
                    <input type="number" name="auction_register_time" value="{DATA.auction_register_time}" class="form-control" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_auction_firebase_url}</strong></label>
                <div class="col-sm-21">
                    <input type="url" name="auction_firebase_url" value="{DATA.auction_firebase_url}" class="form-control" />
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">{LANG.config_refresh}</div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-3 text-right"><strong>{LANG.config_refresh_allow}</strong></label>
                <div class="col-sm-21">
                    <label><input type="checkbox" value="1" name="refresh_allow" {DATA.ck_refresh_allow} />{LANG.config_refresh_allow_note}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_refresh_free}</strong> <em class="fa fa-question-circle fa-pointer text-info" data-toggle="tooltip" data-original-title="{LANG.config_refresh_free_note}">&nbsp;</em></label>
                <div class="col-sm-21">
                    <input type="text" class="form-control" value="{DATA.refresh_free}" name="refresh_free" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_refresh_default}</strong> <em class="fa fa-question-circle fa-pointer text-info" data-toggle="tooltip" data-original-title="{LANG.config_refresh_default_note}">&nbsp;</em></label>
                <div class="col-sm-21">
                    <input type="text" class="form-control" value="{DATA.refresh_default}" name="refresh_default" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><strong>{LANG.config_refresh_timelimit}</strong></label>
                <div class="col-sm-21">
                    <input type="text" class="form-control" value="{DATA.refresh_timelimit}" name="refresh_timelimit" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 text-right"><strong>{LANG.config}</strong></label>
                <div class="col-sm-21">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" style="margin-bottom: 7px">
                            <thead>
                                <tr>
                                    <th>{LANG.config_refresh_config_number}</th>
                                    <th>{LANG.price}</th>
                                    <th class="w50"></th>
                                </tr>
                            </thead>
                            <tbody id="refresh_config">
                                <!-- BEGIN: refresh_config -->
                                <tr>
                                    <td><input type="number" value="{REFRESH_CONFIG.number}" name="refresh_config[{REFRESH_CONFIG.index}][number]" class="form-control" /></td>
                                    <td><input type="text" value="{REFRESH_CONFIG.price}" name="refresh_config[{REFRESH_CONFIG.index}][price]" class="form-control" /></td>
                                    <td class="text-center"><em class="fa fa-trash-o fa-lg fa-pointer" onclick="$(this).closest('tr').remove(); return !1;">&nbsp;</em></td>
                                </tr>
                                <!-- END: refresh_config -->
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-primary btn-xs" onclick="nv_config_refresh_add(); return !1;">{LANG.config_add}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">{LANG.config_group_useradd}</div>
        <div class="panel-body">
            <div class="form-group">
                <script>var specialgroup_count = {};</script>
                <!-- BEGIN: specialgroup -->
                <div class="specialgroup">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" style="margin-bottom: 7px">
                            <caption>{SPECIALGROUP.value}</caption>
                            <thead>
                                <tr>
                                    <th>{LANG.time}</th>
                                    <th>{LANG.price}</th>
                                    <th class="w50"></th>
                                </tr>
                            </thead>
                            <tbody id="specialgroup_{SPECIALGROUP.index}">
                                <!-- BEGIN: specialgroup_config -->
                                <tr>
                                    <td><input type="text" value="{SPECIALGROUP_CONFIG.time}" name="specialgroup_config[{SPECIALGROUP.index}][{SPECIALGROUP_CONFIG.number}][time]" class="form-control" /></td>
                                    <td><input type="text" value="{SPECIALGROUP_CONFIG.price}" name="specialgroup_config[{SPECIALGROUP.index}][{SPECIALGROUP_CONFIG.number}][price]" class="form-control" /></td>
                                    <td class="text-center"><em class="fa fa-trash-o fa-lg fa-pointer" onclick="$(this).closest('tr').remove(); return !1;">&nbsp;</em></td>
                                </tr>
                                <!-- END: specialgroup_config -->
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-primary btn-xs" onclick="nv_config_specialgroup_add({SPECIALGROUP.index}); return !1;">{LANG.config_add}</button>
                </div>
                <script>specialgroup_count[{SPECIALGROUP.index}] = {SPECIALGROUP.count};</script>
                <!-- END: specialgroup -->
            </div>
        </div>
    </div>
    <div class="text-center">
        <input type="submit" class="btn btn-primary" value="{LANG.save}" name="savesetting" />
    </div>
</form>
<script>
	var number = {COUNT};
	$('.grouppost').change(function(){
		if($(this).is(':checked')){
			var html = '';
			html += '<tr id="row_' + $(this).val() + '">';
			html += '	<td class="hidden"><input type="hidden" name="grouppostconfig[' + $(this).val() + '][groupid]" value="' + $(this).val() + '" /></td>';
			html += '	<td>' + $(this).attr('title') + '</td>';
			html += '	<td class="text-center"><input type="checkbox" name="grouppostconfig[' + $(this).val() + '][queue]" value="1" checked="checked" /></td>';
			html += '	<td><input type="number" class="form-control input-sm" name="grouppostconfig[' + $(this).val() + '][maxpost]" value="20" /></td>';
			html += '</tr>';
			$('#groupconfig').append(html);
		}else{
			$('#row_' + $(this).val()).remove();
		}
	});
	
	var refresh_count = {REFRESH_COUNT};
	function nv_config_refresh_add()
	{
		var html = '';
		html += '<tr>';
		html += '	<td><input type="number" name="refresh_config[' + refresh_count + '][number]" class="form-control" /></td>';
		html += '	<td><input type="text" name="refresh_config[' + refresh_count + '][price]" class="form-control" /></td>';
		html += '	<td class="text-center">';
		html += '		<em class="fa fa-trash-o fa-lg fa-pointer" onclick="$(this).closest(\'tr\').remove(); return !1;">&nbsp;</em>';
		html += '	</td>';
		html += '</tr>';
		refresh_count++;
		$('#refresh_config').append(html);
	}
</script>
<script>
	function nv_config_specialgroup_add(bid)
	{
		var html = '';
		html += '<tr>';
		html += '	<td><input type="text" name="specialgroup_config[' + bid + '][' + specialgroup_count[bid] + '][time]" class="form-control" /></td>';
		html += '	<td><input type="text" name="specialgroup_config[' + bid + '][' + specialgroup_count[bid] + '][price]" class="form-control" /></td>';
		html += '	<td class="text-center">';
		html += '		<em class="fa fa-trash-o fa-lg fa-pointer" onclick="$(this).closest(\'tr\').remove(); return !1;">&nbsp;</em>';
		html += '	</td>';
		html += '</tr>';
		specialgroup_count[bid]++;
		$('#specialgroup_' + bid).append(html);
	}
</script>
<!-- BEGIN: main -->