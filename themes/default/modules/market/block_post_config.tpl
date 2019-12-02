<!-- BEGIN: main -->
<tr>
	<td>{LANG.type}</td>
	<td>
		<!-- BEGIN: type -->
		<label><input type="radio" name="config_type" value="{TYPE.index}" {TYPE.checked} />{TYPE.value}&nbsp;&nbsp;&nbsp;</label>
		<!-- END: type -->
	</td>
</tr>
<tr>
	<td>{LANG.template}</td>
	<td>
		<!-- BEGIN: template -->
		<label><input type="radio" name="config_template" value="{TEMPLATE.index}" {TEMPLATE.checked} />{TEMPLATE.value}&nbsp;&nbsp;&nbsp;</label>
		<!-- END: template -->
	</td>
</tr>
<tr>
	<td>{LANG.numrow}</td>
	<td>
		<input type="number" name="config_numrow" value="{DATA.numrow}" class="form-control" />
	</td>
</tr>
<tr>
	<td>{LANG.title_lenght}</td>
	<td>
		<input type="number" name="config_title_lenght" value="{DATA.title_lenght}" class="form-control" />
	</td>
</tr>
<!-- END: main -->