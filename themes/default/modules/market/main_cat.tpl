<!-- BEGIN: main -->
<div class="viewmain-cat">
    <!-- BEGIN: cat -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <ul class="list-inline">
                <li><a href="{CAT.link}" title="{CAT.title}"><strong>{CAT.title}</strong></a></li>
                <!-- BEGIN: subcatloop -->
                <li class="hidden-xs"><h4>
                        <a title="{SUBCAT.title}" href="{SUBCAT.link}">{SUBCAT.title}</a>
                    </h4></li>
                <!-- END: subcatloop -->
                <!-- BEGIN: subcatmore -->
                <a class="pull-right hidden-xs" title="{MORE.title}" href="{MORE.link}"><em class="fa fa-sign-out">&nbsp;</em></a>
                <!-- END: subcatmore -->
            </ul>
        </div>
        <div class="panel-body">{DATA}</div>
    </div>
    <!-- END: cat -->
</div>
<!-- END: main -->
