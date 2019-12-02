<!-- BEGIN: main -->
<div class="well">
	<form action="{NV_BASE_ADMINURL}index.php" method="get" onsubmit="return nv_search_tag();">
		<div class="row">
			<div class="col-xs-24 col-md-4">
				<input class="form-control" type="text" value="{Q}" name="q" id="q" maxlength="64" placeholder="{LANG.search_title}" />
			</div>
			<div class="col-xs-12 col-md-3">
				<input class="btn btn-primary" type="submit" value="{LANG.search_submit}" />
			</div>
		</div>
	</form>
</div>

<!-- BEGIN: incomplete_link -->
<div class="alert alert-info">
	<a class="text-info" href="{ALL_LINK}">{LANG.tags_all_link}.</a>
</div>
<!-- END: incomplete_link -->

<div id="module_show_list">{TAGS_LIST}</div>
<br />

<!-- BEGIN: error -->
<div class="alert alert-warning">{ERROR}</div>
<!-- END: error -->

<form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php" method="post">
	<input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" /> <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}" /> <input type="hidden" name="tid" value="{tid}" /> <input name="savecat" type="hidden" value="1" />
	<!-- BEGIN: incomplete -->
	<input name="incomplete" type="hidden" value="1" />
	<!-- END: incomplete -->
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-3 control-label"><strong>{LANG.alias}</strong></label>
				<div class="col-sm-21">
					<input class="form-control" name="alias" id="idalias" type="text" value="{alias}" maxlength="250" /> <span class="text-middle">{GLANG.length_characters}: <span id="aliaslength" class="red">0</span>. {GLANG.title_suggest_max}
					</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-3 control-label"><strong>{LANG.keywords}</strong></label>
				<div class="col-sm-21">
					<input class="form-control" name="keywords" type="text" value="{keywords}" maxlength="255" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-3 control-label"><strong>{LANG.description}</strong></label>
				<div class="col-sm-21">
					<textarea class="form-control" id="description" name="description" cols="100" rows="5">{description}</textarea>
					<span class="text-middle">{GLANG.length_characters}: <span id="descriptionlength" class="red">0</span>. {GLANG.description_suggest_max}
					</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-3 control-label"><strong>{LANG.image}</strong></label>
				<div class="col-sm-21">
					<div class="input-group">
						<input class="form-control" type="text" name="image" value="{image}" id="image" /> <span class="input-group-btn">
							<button class="btn btn-default selectfile" type="button">
								<em class="fa fa-folder-open-o fa-fix">&nbsp;</em>
							</button>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="text-center">
		<input class="btn btn-primary loading" name="submit1" type="submit" value="{LANG.save}" />
	</div>
</form>


<form action="{NV_BASE_ADMINURL}index.php" method="post">
	<input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" /> <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}" /> <input type="hidden" name="tid" value="{tid}" /> <input name="savecat" type="hidden" value="1" />
	<!-- BEGIN: incomplete -->
	<input name="incomplete" type="hidden" value="1" />
	<!-- END: incomplete -->
</form>
<script type="text/javascript">
	var CFG = [];
	$(document).ready(function() {
		$("#aliaslength").html($("#idalias").val().length);
		$("#idalias").bind("keyup paste", function() {
			$("#aliaslength").html($(this).val().length);
		});

		$("#descriptionlength").html($("#description").val().length);
		$("#description").bind("keyup paste", function() {
			$("#descriptionlength").html($(this).val().length);
		});

		$(".selectfile").click(function() {
			var area = "image";
			var path = "{UPLOAD_PATH}";
			var currentpath = "{UPLOAD_CURRENT}";
			var type = "image";
			nv_open_browse(script_name + "?" + nv_name_variable + "=upload&popup=1&area=" + area + "&path=" + path + "&type=" + type + "&currentpath=" + currentpath, "NVImg", 850, 420, "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
			return false;
		});
	});
</script>
<!-- END: main -->