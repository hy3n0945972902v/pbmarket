<!-- BEGIN: main -->
<div class="refresh">
	<p class="m-bottom">{CONTENT}</p>
	<!-- BEGIN: guest -->
	<div class="alert alert-danger">
		{LANG.refresh_alert_guest}
	</div>
	<!-- END: guest -->
	
	<!-- BEGIN: member -->
		<!-- BEGIN: nonowner -->
		<div class="alert alert-danger">
			 {LANG.refresh_alert_owner}
		</div>
		<!-- END: nonowner -->
		<!-- BEGIN: owner -->
		<hr />
		<div class="m-bottom text-center">
			<h2>{REFRESH_COUNT}</h2>
		</div>
		<!-- BEGIN: timelimit -->
		<div class="alert alert-danger text-center">
			{TIMELIMIT}
		</div>
		<!-- END: timelimit -->
		<!-- BEGIN: empty -->
		<div class="alert alert-danger text-center">
			{LANG.refresh_alert_buy}
		</div>
		<!-- END: empty -->
		<div class="m-bottom text-center">
			<button class="btn btn-success {DISABLED}" onclick="nv_refresh({ID}, '{CHECKSS}'); return !1;">{LANG.refresh}</button>
			<button class="btn btn-danger" data-dismiss="modal">{LANG.close}</button>
		</div>
		<!-- END: owner -->
	<!-- END: member -->
</div>
<script>
	var LANG = {};
	LANG['refresh_confirm'] = '{LANG.refresh_confirm}';
	LANG['re_queue_success'] = '{LANG.re_queue_success}';
	LANG['re_queue_confirm'] = '{LANG.re_queue_confirm}';
</script>
<!-- END: main -->