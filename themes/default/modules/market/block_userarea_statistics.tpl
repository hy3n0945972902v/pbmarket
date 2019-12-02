<!-- BEGIN: main -->
<ul class="block_userarea_statistics form-tooltip">
	<!-- BEGIN: refresh -->
	<!-- BEGIN: refresh_free -->
	<li>{LANG.refresh_free} <em class="fa fa-question-circle fa-pointer text-info" data-toggle="tooltip" data-original-title="{LANG.refresh_free_note}">&nbsp;</em> ({DATA.refresh_free})</li>
	<!-- END: refresh_free -->
	<li>{LANG.refresh} ({DATA.refresh}<!-- BEGIN: buy_refresh -->  - <a href="javascript:void(0);" onclick="nv_buy_refresh(0, '{MODULE_NAME}');">{LANG.buy_refresh}</a><!-- END: buy_refresh -->)</li>
	<!-- END: refresh -->
	<li>{LANG.rowactived} ({DATA.rowactived})</li>
	<li>{LANG.queue_rows} ({DATA.queue_rows})</li>
	<li>{LANG.queue_decline_rows} ({DATA.queue_decline_rows})</li>
</ul>
<!-- END: main -->