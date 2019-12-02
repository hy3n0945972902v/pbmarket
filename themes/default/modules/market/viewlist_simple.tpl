<!-- BEGIN: main -->
<div class="viewlist-simple">
	<table class="table table-striped table-bordered table-hover table-middle">
		<thead>
			<tr>
				<th>{LANG.title}</th>
				<th width="150" class="hidden-xs">{LANG.addtime}</th>
			</tr>
		</thead>
		<tbody>
			<!-- BEGIN: loop -->
			<tr>
				<td><h3><a href="{ROW.link}" title="{ROW.title}" <!-- BEGIN: color -->style="color: {ROW.color}"<!-- END: color --> ><strong>{ROW.title}</strong></a></h3> <span class="help-block"><a href="{ROW.location}" title="{ROW.location}">{ROW.location}</a></span></td>
				<td class="hidden-xs pointer form-tooltip"><span data-toggle="tooltip" data-original-title="{ROW.addtime_f}">{ROW.addtime}</span></td>
			</tr>
			<!-- END: loop -->
		</tbody>
	</table>
	<!-- BEGIN: page -->
	<div class="text-center">{PAGE}</div>
	<!-- END: page -->
</div>
<!-- END: main -->