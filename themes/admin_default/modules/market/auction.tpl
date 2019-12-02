<!-- BEGIN: main -->

<!-- BEGIN: view -->
<button class="btn btn-warning btn-xs pull-right" onclick="window.history.back();"><em class="fa fa-reply">&nbsp;</em>{LANG.comeback}</button>
<!-- BEGIN: result -->
<span class="pull-left">{LANG.auction_price_max}</span>
<!-- END: result -->
<div class="clearfix m-bottom"></div>

<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th class="text-center w50">{LANG.number}</th>
				<th>{LANG.user}</th>
				<th>Email</th>
				<th>{LANG.contact_phone}</th>
				<th>{LANG.contact_address}</th>
				<th class="w150">{LANG.auction_register_time}</th>
				<th class="text-center w150">{LANG.auction_register_ok}</th>
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
				<td class="text-center">{ROW.number}</td>
				<td><a href="">{ROW.fullname}</a></td>
				<td><a href="mailto:{ROW.email}">{ROW.email}</a></td>
				<td>{ROW.phone}</td>
				<td>{ROW.address}</td>
				<td>{ROW.auction_register_time}</td>
				<td class="text-center"><input type="checkbox" name="status" id="change_status_{ROW.id}" value="{ROW.id}" {ROW.checked} onclick="nv_change_status({ROW.id});" <!-- BEGIN: stauts_disabled -->disabled="disabled"<!-- END: stauts_disabled --> /></td>
			</tr>
			<!-- END: loop -->
		</tbody>
	</table>
</div>
<script>
function nv_change_status(id) {
	var new_status = $('#change_status_' + id).is(':checked') ? true : false;
	if (confirm(nv_is_change_act_confirm[0])) {
		var nv_timer = nv_settimeout_disable('change_status_' + id, 5000);
		$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=auction&nocache=' + new Date().getTime(), 'change_status=1&id=' + id, function(res) {
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
<!-- END: view -->

<!-- BEGIN: list -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2-bootstrap.min.css">

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
			<div class="col-xs-12 col-md-3">
				<div class="form-group">
					<input class="btn btn-primary" type="submit" value="{LANG.search_submit}" />
				</div>
			</div>
		</div>
	</form>
</div>
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="text-center w50"><input name="check_all[]" type="checkbox" value="yes" onclick="nv_checkAll(this.form, 'idcheck[]', 'check_all[]',this.checked);"></th>
					<th>{LANG.title}</th>
					<th>{LANG.catid}</th>
					<th>{LANG.auction_begin}</th>
					<th>{LANG.auction_end}</th>
					<th>{LANG.auction_price_begin} ({MONEY_UNIT})</th>
					<th>{LANG.auction_price_step} ({MONEY_UNIT})</th>
					<th>{LANG.adduser}</th>
					<th class="w150">{LANG.addtime}</th>
					<th class="w150">{LANG.status}</th>
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
					<td>[{VIEW.code}] <a href="{VIEW.link_view}">{VIEW.title}</a></td>
					<td>{VIEW.cat}</td>
					<td>{VIEW.auction_begin}</td>
					<td>{VIEW.auction_end}</td>
					<td>{VIEW.auction_price_begin}</td>
					<td>{VIEW.auction_price_step}</td>
					<td>{VIEW.adduser}</td>
					<td>{VIEW.addtime}</td>
					<td>{VIEW.status}</td>
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

<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
<script>
	$(document).ready(function() {
		$('.select2').select2({
			theme : 'bootstrap',
			language : '{NV_LANG_INTERFACE}'
		});
	});

	function nv_change_status(id) {
		var new_status = $('#change_status_' + id).is(':checked') ? true
				: false;
		if (confirm(nv_is_change_act_confirm[0])) {
			var nv_timer = nv_settimeout_disable('change_status_' + id, 5000);
			$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name
					+ '&' + nv_fc_variable + '=main&nocache='
					+ new Date().getTime(), 'change_status=1&id=' + id,
					function(res) {
						var r_split = res.split('_');
						if (r_split[0] != 'OK') {
							alert(nv_is_change_act_confirm[2]);
						}
					});
		} else {
			$('#change_status_' + id)
					.prop('checked', new_status ? false : true);
		}
		return;
	}
</script>
<!-- END: list -->

<!-- END: main -->