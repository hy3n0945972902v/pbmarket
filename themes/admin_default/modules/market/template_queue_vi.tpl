<!-- BEGIN: main -->
<h2 style="margin-bottom: 6px">{DATA.site_name}</h2>
{DATA.site_description}<br />
<br />
Xin chào <strong>{DATA.fullname}!</strong><br />
Tin của bạn đăng tại&nbsp;<strong>{DATA.site_name}</strong> đã được chúng tôi xem xét, dưới đây là thông tin chi tiết:<br />
<br />
<p style="line-height: 25px; margin: 0">
<strong>Tiêu đề tin:</strong> {DATA.title}<br />
<strong>Trạng thái kiểm tra:</strong> {DATA.queue_status}<br />
<!-- BEGIN: reason -->
<strong>Lý do từ chối:</strong> {DATA.reason}<br />
<!-- END: reason -->
<!-- BEGIN: reason_note -->
<strong>Ghi chú thêm:</strong> {DATA.reason_note}<br />
<!-- END: reason_note -->
<!-- BEGIN: link -->
<strong>Xem chi tiết bài viết:</strong> <a href="{DATA.link}">{DATA.link}</a><br />
<!-- END: link -->

<!-- BEGIN: reason_con -->
<p style="margin-bottom: 0">Vui lòng kiểm tra lại nội dung tin theo như phản hồi bên trên và gửi yêu cầu duyệt lại.</p>
<!-- END: reason_con -->

<br />
--------------------------------------------------------------------------<br />
<br />
Trên đây là thư tự động thông báo về trạng thái duyệt tin. Vui lòng không trả lời thư này.<br />
Xin cảm ơn bạn đã đăng tin tại {DATA.site_name}!
<!-- END: main -->