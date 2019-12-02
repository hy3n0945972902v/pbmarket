<!-- BEGIN: main -->
<div class="row">
<div class="list-unstyled item-fb-newsfeed">
<!-- BEGIN: loop -->
    <div class="col-xs-12 col-min-24 col">
        <div class="item box-style" id="post_5d0845ee86e1a">
            <div class="box-big">                <div class="entry">
                    <div class="block-summary">
                        <div class="order-tool ">
                            <div class="entry">
<!--                                 <div class="thumbnail"> -->
<!--                                     <img alt="{ROW.thumbalt}" src="{ROW.thumb}"> -->
<!--                                 </div> -->
                                <div class="summary">
                                    <a href="{ROW.link}" title="{ROW.title}" class="title-post"> {ROW.title} </a>
                                    <div class="box-price">
                                        <div class="price-new pull-left">
                                            {ROW.price}
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                <div class="order-button">
                                    <a href="{ROW.link}" class="btn add-to-cart"> Xem ngay</a>
                                </div>
                            </div>
                        </div>
                        <div class="quick-view">
                            <div class="pull-left">
                                {ROW.addtime} - {ROW.location}
                            </div>
                            <div class="pull-right">{ROW.countview} lượt xem</div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="wrap-box-image row10 m-t-5">
                            <div class="box-image layout-3">
                                   <a href="{ROW.link}" title="{ROW.title}"><img alt="{ROW.title}" data-src="{ROW.thumb}" src="{ROW.thumb}"  class="loaded"></a>
                            </div>
                        </div>
                       
                        <div class="content ">
                        
                            <div class="metaTerm">
                            <a href="{ROW.cat_link}"> {ROW.cat}</a>
                                
                            </div>
                            <div class="des_short">
                            {ROW.homeimgalt}
                            </div>
                            ..<a class="open-readmore" href="{ROW.link}">Xem thêm</a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="footer-bottom-item">
                        <div style="position: relative; padding-right: 75px;">
                            <span class="username btn-style"> <b>{ROW.contact_fullname}</b>
                            </span>
                        </div>
                        <div class="clearfix"></div>
                        <div class="bottomLine">
                            <a href="tel:{ROW.contact_phone}" class="text-black"><b>{ROW.contact_phone}</b></a>
                             {ROW.location}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- BEGIN: block -->
    {BLOCK}
    <!-- END: block -->
   <!-- END: loop -->
    <!-- BEGIN: page -->
    <div class="text-center clear">{PAGE}</div>
    <!-- END: page -->
</div>
</div>
<!-- END: main -->
