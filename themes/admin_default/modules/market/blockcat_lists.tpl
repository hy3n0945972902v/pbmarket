<!-- BEGIN: main -->
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<colgroup>
			<col class="w100">
			<col />
			<col class="w200">
			<col class="w200">
			<col class="w100">
			<col class="w150">
			<col class="w150">
		</colgroup>
		<thead>
			<tr>
				<th>{LANG.number}</th>
				<th>{LANG.title}</th>
				<th class="text-center">{LANG.groups_useradd} <em class="fa fa-question-circle fa-pointer text-info" data-toggle="tooltip" data-original-title="{LANG.groups_useradd_note}">&nbsp;</em></th>
				<th class="text-center">{LANG.groups_adddefaultblock}</th>
				<th class="text-center">{LANG.groups_numlinks}</th>
				<th class="text-center">{LANG.groups_prior} <em class="fa fa-question-circle fa-pointer text-info" data-toggle="tooltip" data-original-title="{LANG.groups_prior_note}">&nbsp;</em></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<!-- BEGIN: loop -->
			<tr>
				<td class="text-center"><select class="form-control" id="id_weight_{ROW.bid}" onchange="nv_chang_block_cat('{ROW.bid}','weight');">
						<!-- BEGIN: weight -->
						<option value="{WEIGHT.key}"{WEIGHT.selected}>{WEIGHT.title}</option>
						<!-- END: weight -->
				</select></td>
				<td><a href="{ROW.link}">{ROW.title}</a> ( <a href="{ROW.linksite}">{ROW.numnews} {LANG.groups_topic_num}</a> )</td>
				<td class="text-center"><select class="form-control" id="id_useradd_{ROW.bid}" onchange="nv_chang_block_cat('{ROW.bid}','useradd');">
						<!-- BEGIN: useradd -->
						<option value="{USERADD.key}"{USERADD.selected}>{USERADD.title}</option>
						<!-- END: useradd -->
				</select></td>
				<td class="text-center"><select class="form-control" id="id_adddefault_{ROW.bid}" onchange="nv_chang_block_cat('{ROW.bid}','adddefault');">
						<!-- BEGIN: adddefault -->
						<option value="{ADDDEFAULT.key}"{ADDDEFAULT.selected}>{ADDDEFAULT.title}</option>
						<!-- END: adddefault -->
				</select></td>
				<td class="text-center"><select class="form-control" id="id_numlinks_{ROW.bid}" onchange="nv_chang_block_cat('{ROW.bid}','numlinks');">
						<!-- BEGIN: number -->
						<option value="{NUMBER.key}"{NUMBER.selected}>{NUMBER.title}</option>
						<!-- END: number -->
				</select></td>
				<td class="text-center"><select class="form-control" id="id_prior_{ROW.bid}" onchange="nv_chang_block_cat('{ROW.bid}','prior');">
						<!-- BEGIN: prior -->
						<option value="{PRIOR.key}"{PRIOR.selected}>{PRIOR.title}</option>
						<!-- END: prior -->
				</select></td>
				<td class="text-center"><em class="fa fa-edit fa-lg">&nbsp;</em> <a href="{ROW.url_edit}">{GLANG.edit}</a> &nbsp; <em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="javascript:void(0);" onclick="nv_del_block_cat({ROW.bid})">{GLANG.delete}</a></td>
			</tr>
			<!-- END: loop -->
		</tbody>
	</table>
</div>
<!-- END: main -->