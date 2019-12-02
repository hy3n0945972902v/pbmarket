<!-- BEGIN: main -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<div class="row">
    <div class="col-xs-24 col-sm-6">
        <div class="form-group">
            <div class="input-group">
                <input class="form-control datepicker" value="{DATE}" id="date-input" type="text" name="date" readonly="readonly" /> <span class="input-group-btn">
                    <button class="btn btn-default" type="button">
                        <em class="fa fa-calendar fa-fix">&nbsp;</em>
                    </button>
                </span>
            </div>
        </div>
    </div>
</div>
<textarea class="form-control" rows="20">
<!-- BEGIN: loop -->
{DATA.content}
——————————————–
<!-- END: loop -->
</textarea>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<script>
    $(".datepicker").datepicker({
        dateFormat : "dd/mm/yy",
        changeMonth : !0,
        changeYear : !0,
        showOtherMonths : !0,
        showOn : "focus",
        yearRange : "-90:+0"
    });
    
    $('#date-input').change(function() {
        window.location.href = script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=news&date=' + $(this).val();
    });
</script>
<!-- END: main -->