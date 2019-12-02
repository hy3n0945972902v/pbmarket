<!-- BEGIN: main -->
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover table-middle">
		<colgroup>
			<col class="w50" />
			<col span="5" />
			<col class="w150" />
		</colgroup>
		<thead>
			<tr>
				<th class="text-center">ID</th>
				<th>{LANG.contact_fullname}</th>
				<th>{LANG.contact_email}</th>
				<th>{LANG.freelance_total}</th>
				<th>{LANG.freelance_pay}</th>
				<th>{LANG.freelance_rest}</th>
				<th class="text-center">{LANG.freelance_fees} <em class="fa fa-question-circle fa-pointer text-info" data-toggle="tooltip" data-original-title="{LANG.freelance_fees_note}">&nbsp;</em></th>
			</tr>
		</thead>
		<tbody>
			<!-- BEGIN: loop -->
			<tr>
				<td class="text-center">{DATA.userid}</td>
				<td>{DATA.fullname}</td>
				<td><a href="mailto:{DATA.email}" title="Mail to: {DATA.email}">{DATA.email}</a></td>
				<td>{DATA.total}</td>
				<td>{DATA.pay}</td>
				<td>{DATA.rest}</td>
				<td><input type="text" data-userid="{DATA.userid}" class="form-control freelance_fees" value="{DATA.salary}"></td>
			</tr>
			<!-- END: loop -->
		</tbody>
	</table>
</div>
<script>
	$('.freelance_fees').blur(function() {
		$.ajax({
			type : 'POST',
			url : script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(),
			data : 'freelance_set_fees=1&userid=' + $(this).data('userid') + '&fees=' + $(this).val(),
			success : function(data) {
				var r_split = data.split('_');
				if (r_split[0] != 'OK') {
					alert(nv_is_change_act_confirm[2]);
				}
			}
		});
	});
</script>
<!-- END: main -->