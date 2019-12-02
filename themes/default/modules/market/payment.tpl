<!-- BEGIN: main -->
<div class="payment">
	<!-- BEGIN: refresh -->
	<p>{LANG.payment_refresh_note}</p>
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover">
			<colgroup>
				<col width="50" />
			</colgroup>
			<thead>
				<tr>
					<th></th>
					<th>{LANG.refresh_number}</th>
					<th>{LANG.price}</th>
				</tr>
			</thead>
			<tbody>
				<!-- BEGIN: option -->
				<tr>
					<td class="text-center"><input type="radio" name="option" value="{OPTION.number}" data-price="{OPTION.price}" data-tokenkey="{OPTION.tokenkey}" data-checksum="{OPTION.checksum}" class="payment-option" {OPTION.checked} /></td>
					<td>{OPTION.number}</td>
					<td>{OPTION.price_format}{MONEY_UNIT}</td>
				</tr>
				<!-- END: option -->
			</tbody>
		</table>
	</div>
	<!-- END: refresh -->
	<!-- BEGIN: group -->
	<div class="payment-group">
		<p>{GROUP.description}</p>
		<input type="hidden" value="{GROUP.bid}" id="groupid" />
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover">
				<colgroup>
					<col width="50" />
				</colgroup>
				<thead>
					<tr>
						<th></th>
						<th>{LANG.time}</th>
						<th>{LANG.price}</th>
					</tr>
				</thead>
				<tbody>
					<!-- BEGIN: option -->
					<tr>
						<td class="text-center"><input type="radio" name="option" value="{OPTION.time}" data-groupid="{GROUP.bid}" data-price="{OPTION.price}" data-tokenkey="{OPTION.tokenkey}" data-checksum="{OPTION.checksum}" class="payment-option" {OPTION.checked} /></td>
						<td>{OPTION.time} {GLANG.day}</td>
						<td>{OPTION.price_format}{MONEY_UNIT}</td>
					</tr>
					<!-- END: option -->
				</tbody>
			</table>
		</div>
	</div>
	<!-- END: group -->
	<div class="text-center">
		<button class="btn btn-warning ws_c_d" id="payment-btn" data_product_id="{INFO.id}" data_title="{INFO.title}" data_money="{FIRST.price}" data_money_unit="VND" data_tokenkey="{FIRST.tokenkey}" data-checksum="{FIRST.checksum}" data-mod="{INFO.mod}">{LANG.payment}</button>
	</div>
</div>
<script>
	var LANG = {};
	LANG['payment_confirm'] = '{LANG.payment_confirm}';
</script>
<!-- END: main -->