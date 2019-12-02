<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2-bootstrap.min.css">

<div class="userarea">
	<div class="well">
		<form action="{NV_BASE_SITEURL}index.php" method="get">
			<input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}" /> <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" /> <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}" />
			<div class="row">
				<div class="col-xs-24 col-md-4">
					<div class="form-group">
						<input class="form-control" type="text" value="{SEARCH.q}" name="q" maxlength="255" placeholder="{LANG.search_title}" />
					</div>
				</div>
				<div class="col-xs-24 col-md-5">
					<div class="form-group">
						<select name="catid" class="form-control select2">
							<option value=0>---{LANG.cat_c}---</option>
							<!-- BEGIN: cat -->
							<option value="{CAT.id}"{CAT.selected}>{CAT.space}{CAT.title}</option>
							<!-- END: cat -->
						</select>
					</div>
				</div>
				<div class="col-xs-24 col-md-5">
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
						<select name="status" class="form-control">
							<option value="-1">---{LANG.status_select}---</option>
							<!-- BEGIN: status -->
							<option value="{STATUS.index}"{STATUS.selected}>{STATUS.value}</option>
							<!-- END: status -->
						</select>
					</div>
				</div>
				<div class="col-xs-12 col-md-6">
					<div class="form-group">
						<input class="btn btn-primary" type="submit" value="{LANG.search_submit}" />
						<!-- BEGIN: userpost -->
						<a href="{URL_CONTENT}" class="btn btn-primary">{LANG.content_add}</a>
						<!-- END: userpost -->
					</div>
				</div>
			</div>
		</form>
	</div>

	<!-- BEGIN: maxpostlimit -->
	<div class="alert alert-danger text-center">{LANG.maxpostlimit}</div>
	<!-- END: maxpostlimit -->

	<form action="{NV_BASE_SITEURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover table-middle">
				<thead>
					<tr>
						<th width="50" class="text-center"><input name="check_all[]" type="checkbox" value="yes" onclick="nv_checkAll(this.form, 'idcheck[]', 'check_all[]',this.checked);"></th>
						<th width="100">{LANG.code}</th>
						<th>{LANG.title}</th>
						<th width="130" class="text-center">{LANG.active}</th>
						<th width="100">&nbsp;</th>
					</tr>
				</thead>
				<!-- BEGIN: generate_page -->
				<tfoot>
					<tr>
						<td class="text-center" colspan="8">{NV_GENERATE_PAGE}</td>
					</tr>
				</tfoot>
				<!-- END: generate_page -->
				<tbody>
					<!-- BEGIN: loop -->
					<tr id="row_{VIEW.id}">
						<td class="text-center"><input class="post" type="checkbox" onclick="nv_UncheckAll(this.form, 'idcheck[]', 'check_all[]', this.checked);" value="{VIEW.id}" name="idcheck[]"></td>
						<td>{VIEW.code}</td>
						<td><a href="{VIEW.link_view}" target="_blank"><strong>{VIEW.title}</strong></a> <!-- BEGIN: group_info --> <label class="label pointer" style="background-color: {GROUP_INFO.color"
								<!-- BEGIN: exptime -->title="{GROUP_INFO.exptime}"<!-- END: exptime -->>{GROUP_INFO.title}
						</label> <!-- END: group_info --> <span class="help-block default-info"><em class="fa fa-clock-o">&nbsp;</em>{VIEW.addtime}&nbsp;&nbsp;&nbsp;<em class="fa fa-folder-o">&nbsp;</em>{VIEW.cat}</span> <span class="help-block">
								<ul class="list-inline">
									<!-- BEGIN: refresh_buy -->
									<li><a href="javasctipt:void(0)" onclick="nv_buy_refresh({VIEW.id}, '{MODULE_NAME}');">{LANG.buy_refresh}</a></li>
									<!-- END: refresh_buy -->
									<!-- BEGIN: group_buy -->
									<li><a href="javasctipt:void(0)" onclick="nv_buy_group({VIEW.id}, {GROUP.bid}, '{MODULE_NAME}');" style="color: {GROUP.color" title="{LANG.buy} {GROUP.title}">{LANG.buy} {GROUP.title}</a></li>
									<!-- END: group_buy -->
								</ul>
						</span></td>
						<td class="text-center form-tooltip">
							<!-- BEGIN: queue --> <span>{LANG.queue}</span> <!-- END: queue --> <!-- BEGIN: queue_decline --> <span>{LANG.queue_decline}</span> <!-- BEGIN: queue_info --> <em class="fa fa-question-circle fa-pointer text-info" data-toggle="tooltip" data-original-title="{VIEW.queue_info}">&nbsp;</em> <!-- END: queue_info --> <!-- END: queue_decline --> <!-- BEGIN: checkbox --> <input type="checkbox" name="status" id="change_status_{VIEW.id}" value="{VIEW.id}" {VIEW.ck_status} onclick="nv_change_status({VIEW.id}, '{VIEW.checkss}');" /> <!-- END: checkbox --> <!-- BEGIN: label --> <label title="{LANG.status_disabled}"><input type="checkbox" disabled="disabled" /></label> <!-- END: label -->
						</td>
						<td class="text-center">
							<!-- BEGIN: refresh_allow -->
								<!-- BEGIN: refresh -->
								<a href="javascript:void(0)" onclick="nv_refresh({VIEW.id}, '{VIEW.checkss}'); return !1;" title="{LANG.refresh}" class="btn btn-default btn-xs"><em class="fa fa-refresh"></em></a>
								<!-- END: refresh -->
								<!-- BEGIN: refresh_label -->
								<button class="btn btn-default btn-xs" disabled="disabled" title="{LANG.refresh}"><em class="fa fa-refresh"></em></button>
								<!-- END: refresh_label -->
							<!-- END: refresh_allow -->
							<!-- BEGIN: re_queue --> <a href="javascript:void(0)" title="{LANG.re_queue}" onclick="nv_re_queue({VIEW.id}); return !1;" class="btn btn-default btn-xs"><em class="fa fa-circle-o-notch"></em></a> <!-- END: re_queue --> <a href="{VIEW.link_edit}" title="{LANG.edit}" class="btn btn-default btn-xs"><em class="fa fa-edit"></em></a> <a href="{VIEW.link_delete}" class="btn btn-default btn-xs" title="{LANG.delete}" onclick="return confirm(nv_is_del_confirm[0]);"><em class="fa fa-trash-o"></em></a>
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
	</form>
</div>

<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
<script>
	var LANG = {};
	LANG['refresh_confirm'] = '{LANG.refresh_confirm}';
	LANG['re_queue_success'] = '{LANG.re_queue_success}';
	LANG['re_queue_confirm'] = '{LANG.re_queue_confirm}';
	
	$(document).ready(function() {
		$('.select2').select2({
			theme : 'bootstrap',
			language : '{NV_LANG_INTERFACE}'
		});
	});

	function nv_change_status(id, checkss) {
		var new_status = $('#change_status_' + id).is(':checked') ? true : false;
		if (confirm(nv_is_change_act_confirm[0])) {
			var nv_timer = nv_settimeout_disable('change_status_' + id, 5000);
			$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=userarea&nocache=' + new Date().getTime(), 'change_status=1&id=' + id + '&checkss=' + checkss, function(res) {
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
</script>
<!-- END: main -->