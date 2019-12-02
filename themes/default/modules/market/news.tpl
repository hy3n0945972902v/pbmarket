<!-- BEGIN: main -->
<form id="frm-submit">
    <input type="hidden" name="submit" value="1" />
    <div class="form-group">
        <textarea class="form-control" name="content" id="content" rows="10" placeholder="{LANG.news_content}"></textarea>
    </div>
    <div class="text-center">
        <button class="btn btn-primary">{LANG.submit}</button>
    </div>
</form>
<script>
    $('#frm-submit').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type : 'POST',
            url : script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=news&nocache=' + new Date().getTime(),
            data : $(this).serialize(),
            success : function(json) {
                alert(json.msg);
                if (json.error) {
                    $('#' + json.input).focus();
                }else{
                    $('#content').val('');
                }
            }
        });
    });
</script>
<!-- END: main -->