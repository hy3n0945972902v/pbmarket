<!-- BEGIN: main -->
<div class="form-group">
	<label class="col-sm-5 col-md-4 control-label"><strong>{LANG.price}</strong> <span class="red">(*)</span></label>
	<div class="col-sm-19 col-md-20">
		<div class="row">
			<div class="col-xs-24 col-sm-5">
				<select name="pricetype" id="pricetype" class="form-control">
					<!-- BEGIN: pricetype -->
					<option value="{PRICETYPE.index}"{PRICETYPE.selected}>{PRICETYPE.value}</option>
					<!-- END: pricetype -->
				</select>
			</div>
			<div class="col-xs-24 col-sm-11">
				<!-- BEGIN: pricetype_cat_2 -->
				<input class="form-control price" type="text" name="price" id="price" value="{ROW.price}" placeholder="{LANG.pricetype_cat_title_2_note}" />
				<!-- END: pricetype_cat_2 -->
				<!-- BEGIN: pricetype_cat_1 -->
				<div class="row">
					<div class="col-xs-24 col-sm-12">
						<input class="form-control price" type="text" name="price" id="price" value="{ROW.price}" placeholder="{LANG.pricetype_cat_from}" />
					</div>
					<div class="col-xs-24 col-sm-12">
						<input class="form-control price" type="text" name="price1" id="price1" value="{ROW.price1}" placeholder="{LANG.pricetype_cat_to}" />
					</div>
				</div>
				<!-- END: pricetype_cat_1 -->
			</div>
			<div class="col-xs-24 col-sm-8">
				<select name="unitid" id="unitid" class="form-control select2">
					<option value="0">---{LANG.unit_select}---</option>
					<!-- BEGIN: unit -->
					<option value="{UNIT.id}"{UNIT.selected}>{UNIT.title}</option>
					<!-- END: unit -->
				</select>
			</div>
		</div>
	</div>
</div>
<script>
	nv_price_control();

	$(document).ready(function() {
		$('#unitid').select2({
			language: '{NV_LANG_INTERFACE}',
			theme: 'bootstrap',
			tags: true
		});
		
		$('#pricetype').change(function() {
			nv_price_control();
		});

		var Options = {
			aSep : '{DES_POINT}',
			aDec : '{THOUSANDS_SEP}',
			vMin : '0',
			vMax : '999999999999999999'
		};
		$('.price').autoNumeric('init', Options);
		$('.price').bind('blur focusout keypress keyup', function() {
			$(this).autoNumeric('get', Options);
		});
	});
</script>
<!-- END: main -->