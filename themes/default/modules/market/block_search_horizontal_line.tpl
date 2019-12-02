<!-- BEGIN: main -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<!-- BEGIN: select2 -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2-bootstrap.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
<!-- END: select2 -->
<div class="block_search_horizontal_line">
    <form action="{SEARCH.action}" method="get">
        <!-- BEGIN: no_rewrite -->
        <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}" /> <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" /> <input type="hidden" name="{NV_OP_VARIABLE}" value="search" />
        <!-- END: no_rewrite -->
        <div class="row">
            <div class="col-xs-24 col-sm-6 col-md-3">
                <div class="form-group">
                    <select name="type" class="form-control">
                        <!-- BEGIN: type -->
                        <option value="{TYPE.id}"{TYPE.selected}>{TYPE.space}{TYPE.title}</option>
                        <!-- END: type -->
                    </select>
                </div>
            </div>
            <div class="col-xs-24 col-sm-6 col-md-4">
                <div class="form-group">
                    <input type="text" class="form-control" name="q" value="{SEARCH.q}" placeholder="{LANG.keywords_input}" />
                </div>
            </div>
            <div class="col-xs-24 col-sm-6 col-md-4">
                <div class="form-group">
                    <select name="catid" class="form-control select2">
                        <option value="0">---{LANG.cat_select}---</option>
                        <!-- BEGIN: cat -->
                        <option value="{CAT.id}"{CAT.selected}>{CAT.space}{CAT.title}</option>
                        <!-- END: cat -->
                    </select>
                </div>
            </div>
            <div class="col-xs-24 col-sm-6 col-md-10">{LOCATION}</div>
            <div class="col-xs-24 col-sm-6 col-md-3">
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
</script>
<!-- END: main -->