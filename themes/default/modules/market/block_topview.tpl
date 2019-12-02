<div class="line-view">
    <span class="fa fa-clock-o"></span> {blocknews.addtime}
</div>
<!-- BEGIN: main -->
<div class="box-style listUserful">
    <!-- BEGIN: newloop -->
    <a class="linkStyle1" href="{blocknews.link}" title="{blocknews.title}"> <img alt="{blocknews.title}" src="{blocknews.imgurl}" class="loading col-xs-12" width=100'>
        <div class="text col-xs-12">{blocknews.title}</div>
        <div class="post-meta">
            <span class="label-premium Partner"> {LANG.countview}: {blocknews.countview}</span>
        </div>
        <div class="block-info">
            <div class="price-new">{LANG.price}:{blocknews.price}</div>
        </div>
    </a>
    <!-- END: newloop -->
    <div class="clearfix"></div>
</div>
<!-- BEGIN: tooltip -->
<script type="text/javascript">
    $(document).ready(function() {
        $("[data-rel='block_news_tooltip'][data-content!='']").tooltip({
            placement : "{TOOLTIP_POSITION}",
            html : true,
            title : function() {
                return ($(this).data('img') == '' ? '' : '<img class="img-thumbnail pull-left margin_image" src="' + $(this).data('img') + '" width="90" />') + '<p class="text-justify">' + $(this).data('content') + '</p><div class="clearfix"></div>';
            }
        });
    });
</script>
<!-- END: tooltip -->
<!-- END: main -->
