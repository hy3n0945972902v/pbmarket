<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2-bootstrap.min.css">
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />

<div class="well">
	<form action="{NV_BASE_ADMINURL}index.php" method="get">
		<input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}" /> <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" /> <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}" />
		<div class="row">
			<div class="col-xs-24 col-md-4">
				<div class="form-group">
					<input class="form-control" type="text" value="{SEARCH.q}" name="q" maxlength="255" placeholder="{LANG.search_title}" />
				</div>
			</div>
			<div class="col-xs-24 col-md-4">
				<div class="form-group">
					<select name="catid" class="form-control select2">
						<option value=0>---{LANG.cat_c}---</option>
						<!-- BEGIN: cat -->
						<option value="{CAT.id}"{CAT.selected}>{CAT.space}{CAT.title}</option>
						<!-- END: cat -->
					</select>
				</div>
			</div>
			<div class="col-xs-24 col-md-4">
				<div class="form-group">
					<select name="typeid" class="form-control">
						<option value="-1">---{LANG.type_select}---</option>
						<!-- BEGIN: type -->
						<option value="{TYPE.id}"{TYPE.selected}>{TYPE.title}</option>
						<!-- END: type -->
					</select>
				</div>
			</div>
			<div class="col-xs-24 col-md-4">
				<div class="form-group">
					<div class="input-group">
						<input class="form-control datepicker" value="{SEARCH.from}" type="text" name="from" readonly="readonly" placeholder="{LANG.fromday}" /> <span class="input-group-btn">
							<button class="btn btn-default" type="button">
								<em class="fa fa-calendar fa-fix">&nbsp;</em>
							</button>
						</span>
					</div>
				</div>
			</div>
			<div class="col-xs-24 col-md-4">
				<div class="form-group">
					<div class="input-group">
						<input class="form-control datepicker" value="{SEARCH.to}" type="text" name="to" readonly="readonly" placeholder="{LANG.today}" /> <span class="input-group-btn">
							<button class="btn btn-default" type="button">
								<em class="fa fa-calendar fa-fix">&nbsp;</em>
							</button>
						</span>
					</div>
				</div>
			</div>
			<div class="col-xs-24 col-md-4">
				<div class="form-group">
					<select name="userid" id="userid" class="form-control">
						<option value="0">---{LANG.user_select}---</option>
						<!-- BEGIN: userid -->
						<option value="{SEARCH.userid}" selected="selected">{SEARCH.fullname}</option>
						<!-- END: userid -->
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-24 col-md-4">
				<div class="form-group">
					<select name="status" class="form-control">
						<option value="-1">---{LANG.status_select}---</option>
						<!-- BEGIN: status -->
						<option value="{STATUS.index}"{STATUS.selected}>{STATUS.value}</option>
						<!-- END: status -->
					</select>
				</div>
			</div>
			<div class="col-xs-12 col-md-2">
				<div class="form-group">
					<input class="btn btn-primary" type="submit" value="{LANG.search_submit}" />
				</div>
			</div>
		</div>
	</form>
</div>
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover table-middle">
			<thead>
				<tr>
					<th class="text-center w50"><input name="check_all[]" type="checkbox" value="yes" onclick="nv_checkAll(this.form, 'idcheck[]', 'check_all[]',this.checked);"></th>
					<th class="w100">{LANG.code}</th>
					<th>{LANG.title}</th>
					<th>{LANG.catid}</th>
					<th>{LANG.area}</th>
					<th class="w100 text-center">{LANG.active}</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<!-- BEGIN: generate_page -->
			<tfoot>
				<tr>
					<td class="text-center" colspan="10">{NV_GENERATE_PAGE}</td>
				</tr>
			</tfoot>
			<!-- END: generate_page -->
			<tbody>
				<!-- BEGIN: loop -->
				<tr id="row_{VIEW.id}">
					<td class="text-center"><input class="post" type="checkbox" onclick="nv_UncheckAll(this.form, 'idcheck[]', 'check_all[]', this.checked);" value="{VIEW.id}" name="idcheck[]"></td>
					<td>{VIEW.code}</td>
					<td>
						<a href="{VIEW.link_view}" target="_blank"><strong>{VIEW.title}</strong></a>
						<!-- BEGIN: type --> ({VIEW.type})<!-- END: type --> 
						<span class="help-block">
							{LANG.postby} <strong>{VIEW.adduser}</strong> {LANG.bytime} <strong>{VIEW.addtime}</strong> - {LANG.countview}: <strong>{VIEW.countview}</strong>
							<!-- BEGIN: post_facebook --> - <a href="javascript:void(0);" onclick="nv_post_facebook({VIEW.id}); return !1;">{LANG.post_facebook}</a><!-- END: post_facebook -->
						</span>
					</td>
					<td>{VIEW.cat}</td>
					<td>{VIEW.area}</td>
					<td class="text-center"><input type="checkbox" name="status" id="change_status_{VIEW.id}" value="{VIEW.id}" {VIEW.ck_status_admin} onclick="nv_change_status({VIEW.id});" /></td>
					<td class="text-center" style="white-space: nowrap">
						<!-- BEGIN: refresh --><a href="javascript:void(0);" class="btn btn-default btn-xs" onclick="nv_refresh({VIEW.id}); return !1;" title="{LANG.config_refresh}"><i class="fa fa-refresh"></i></a><!-- END: refresh --> 
						<a class="btn btn-default btn-xs" href="{VIEW.link_edit}" title="{LANG.edit}"><i class="fa fa-edit"></i></a>
						<a class="btn btn-default btn-xs" href="{VIEW.link_delete}" onclick="return confirm(nv_is_del_confirm[0]);" title="{LANG.delete}"><em class="fa fa-trash-o"></em></a>
					</td>
				</tr>
				<!-- END: loop -->
			</tbody>
		</table>
	</div>
</form>

<form class="form-inline m-bottom">
	<select class="form-control" id="action">
		<!-- BEGIN: action -->
		<option value="{ACTION.key}">{ACTION.value}</option>
		<!-- END: action -->
	</select>
	<button class="btn btn-primary" onclick="nv_list_action( $('#action').val(), '{BASE_URL}', '{LANG.error_list_empty}' ); return false;">{LANG.perform}</button>
	<a href="{URL_EXPORT}" target="_blank" class="btn btn-primary <!-- BEGIN: export_disabled -->disabled<!-- END: export_disabled -->"><em class="fa fa-download">&nbsp;&nbsp;</em>{LANG.export}</a>
</form>

<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<script>
	var facebook_post_confirm = '{LANG.post_facebook_confirm}';
	var post_facebook_success = '{LANG.post_facebook_success}';
	
	$(document).ready(function() {
		$('.select2').select2({
			theme : 'bootstrap',
			language : '{NV_LANG_INTERFACE}'
		});
		
		$("#userid").select2({
			theme: 'bootstrap',
			language: '{NV_LANG_INTERFACE}',
			ajax: {
		    url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=main&get_user_json=1',
		    	dataType: 'json',
		    	delay: 250,
		    	data: function (params) {
		      		return {
		      			q: params.term, // search term
		      			page: params.page
		      		};
		      	},
		    	processResults: function (data, params) {
		    		params.page = params.page || 1;
		    		return {
		    			results: data,
		    			pagination: {
		    				more: (params.page * 30) < data.total_count
		    			}
		    		};
		    	},
			cache: true
			},
			escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
			minimumInputLength: 3,
			templateResult: formatRepo, // omitted for brevity, see the source of this page
			templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
		});
		
		$(".datepicker").datepicker({
			dateFormat: "dd/mm/yy",
			changeMonth: !0,
			changeYear: !0,
			showOtherMonths: !0,
			showOn: "focus",
			yearRange: "-90:+0"
		});
	});

	function nv_change_status(id) {
		var new_status = $('#change_status_' + id).is(':checked') ? true : false;
		if (confirm(nv_is_change_act_confirm[0])) {
			var nv_timer = nv_settimeout_disable('change_status_' + id, 5000);
			$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=main&nocache=' + new Date().getTime(), 'change_status=1&id=' + id, function(res) {
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
	
	function formatRepo (repo) {
		if (repo.loading) return repo.text;
		var markup = '<div class="clearfix">' +
		'<div class="col-sm-19">' + repo.username + '</div>' +
	    '<div clas="col-sm-5"><span class="show text-right">' + repo.fullname + '</span></div>' +
	    '</div>';
		markup += '</div></div>';
		return markup;
	}
	
	function formatRepoSelection (repo) {
		$('#username').val( repo.username );
		return repo.username || repo.text;
	}
</script>
<!-- END: main -->