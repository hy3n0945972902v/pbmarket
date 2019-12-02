<!-- BEGIN: main -->
<div class="block_button">
    <div class="btn-group">
        <a class="btn btn-success" href="{URL_USERAREA}" id="market-button-userarea">{LANG.manage}</a>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" style="z-index: 9999999">
            <li><a href="{URL_SAVED}" title="{LANG.saved}">{LANG.saved}</a></li>
        </ul>
    </div>
    <a class="btn btn-success" href="{URL_CONTENT}" id="market-button-content" data-module="{DATA.module}" data-op="{CONTENT}">{DATA.text}</a>
</div>
<!-- BEGIN: login -->
<script>
    $('#market-button-content').click(function(e) {
        e.preventDefault();
        loginForm('');
    });
</script>
<!-- END: login -->
<!-- BEGIN: popup_js -->
<script>
    $('#market-button-content').click(function(e) {
        e.preventDefault();
        $.ajax({
            type : 'POST',
            url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + $(this).data('module') + '&' + nv_fc_variable + '=' + $(this).data('op') + '&ispopup=1',
            cache : !1,
            dataType : "html"
        }).done(function(a) {
            $('#sitemodal .modal-dialog').addClass('modal-lg');
            modalShow('', a)
        });
    });
</script>
<!-- END: popup_js -->
<!-- END: main -->