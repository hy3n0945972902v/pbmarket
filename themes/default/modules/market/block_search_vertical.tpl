<!-- BEGIN: main -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<!-- BEGIN: select2 -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2-bootstrap.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
<!-- END: select2 -->
<div class="block_search">
    <form action="{SEARCH.action}" method="get">
        <!-- BEGIN: no_rewrite -->
        <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}" /> <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" /> <input type="hidden" name="{NV_OP_VARIABLE}" value="search" />
        <!-- END: no_rewrite -->
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>{LANG.type}</label> <select name="type" class="form-control">
                        <!-- BEGIN: type -->
                        <option value="{TYPE.id}"{TYPE.selected}>{TYPE.space}{TYPE.title}</option>
                        <!-- END: type -->
                    </select>
                </div>
                <div class="form-group">
                    <label>{LANG.keywords}</label> <input type="text" class="form-control" name="q" value="{SEARCH.q}" placeholder="{LANG.keywords_input}" />
                </div>
                <div class="form-group">
                    <label>{LANG.cat}</label> <select name="catid" class="form-control select2">
                        <option value="0">---{LANG.cat_select}---</option>
                        <!-- BEGIN: cat -->
                        <option value="{CAT.id}"{CAT.selected}>{CAT.space}{CAT.title}</option>
                        <!-- END: cat -->
                    </select>
                </div>
                <div class="form-group">
                    <label>{LANG.location}</label> {LOCATION}
                </div>
                <div class="form-group">
                    <input type="hidden" name="is_search" value="1" /> <input type="submit" class="btn btn-primary" value="{LANG.search}" />
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<script>
    $('.select2').select2({
        theme : 'bootstrap',
        language : '{NV_LANG_INTERFACE}'
    });
    
    $("#date_begin,#date_end").datepicker({
        dateFormat : "dd/mm/yy",
        changeMonth : true,
        changeYear : true,
        showOtherMonths : true,
    });
</script>
<!-- END: main -->