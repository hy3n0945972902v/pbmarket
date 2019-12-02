/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Sun, 20 Nov 2016 07:31:04 GMT
 */

$(function() {
	$('.payment-option').change(function() {
		$('#payment-btn').attr('data_money', $(this).data('price'));
		$('#payment-btn').attr('data_tokenkey', $(this).data('tokenkey'));
		$('#payment-btn').attr('data-checksum', $(this).data('checksum'));
	});

	// taikhoan
	$('.ws_c_d').click(function() {
		if (confirm(LANG.payment_confirm)) {
			var product_id = $(this).attr('data_product_id');
			var checksum = $(this).attr('data-checksum');
			var number = $('.payment-option:checked').val();
			var groupid = $('#groupid').val();
			var mod = $(this).data('mod');

			$.ajax({
				type : "POST",
				url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=taikhoan&' + nv_fc_variable + '=ws&nocache=' + new Date().getTime(),
				data : 'product_id=' + product_id + '&product_title=' + $(this).attr('data_title') + '&module_send=' + nv_module_name + '&money=' + $(this).attr('data_money') + '&money_unit=' + $(this).attr('data_money_unit') + '&tokenkey=' + $(this).attr('data_tokenkey'),
				success : function(result) {
					if (result.status != 200) {
						alert(result.message)
					} else {
						if (mod == 'refresh') {
							$.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(), 'buy_refresh=1&id=' + product_id + '&number=' + number + '&checksum=' + checksum, function(res) {
								var r_split = res.split('_');
								alert(r_split[1]);
								if (r_split[0] = 'OK') {
									window.location.reload(true);	
								}
							});
						} else if (mod == 'group') {
							$.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(), 'buy_group=1&id=' + product_id + '&time=' + number + '&groupid=' + groupid + '&checksum=' + checksum, function(res) {
								var r_split = res.split('_');
								if (r_split[0] != 'OK') {
									alert(r_split[1]);
								} else {
									window.location.reload(true);
								}
							});
						}
					}
				},
				dataType : "json",
			});
		}
	});
});

$(window).on('load', function() {
    fix_news_image();
});

$(window).on("resize", function() {
    fix_news_image();
});

function fix_news_image(){
    var news = $('#content'), newsW, w, h;
    if( news.length ){
        var newsW = news.innerWidth();
        $.each($('img', news), function(){
            if( typeof $(this).data('width') == "undefined" ){
                w = $(this).innerWidth();
                h = $(this).innerHeight();
                $(this).data('width', w);
                $(this).data('height', h);
            }else{
                w = $(this).data('width');
                h = $(this).data('height');
            }
            
            if( w > newsW ){
            	newsW -= 25;
                $(this).prop('width', newsW);
                $(this).prop('height', h * newsW / w);
            }
        });
    }
}

function nv_save_rows(id, mod, is_user) {
	if (!is_user) {
		alert(LANG.error_save_login);
	} else if (confirm(nv_is_change_act_confirm[0])) {
		$.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(), 'save=1&id=' + id + '&mod=' + mod, function(res) {
			var r_split = res.split('_');
			if (r_split[0] != 'OK') {
				alert(r_split[1]);
			} else {
				if (mod == 'add') {
					$('#save').hide();
					$('#saved').show();
				} else {
					$('#saved').hide();
					$('#save').show();
				}
				alert(r_split[1]);
			}
		});
	}
	return false;
}

function nv_delete_save(id, checkss) {
	if (confirm(nv_is_del_confirm[0])) {
		$.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(), 'saved_delete=1&id=' + id + '&checkss=' + checkss, function(res) {
			var r_split = res.split('_');
			if (r_split[0] != 'OK') {
				alert(r_split[1]);
			} else {
				$('#row_' + id).remove();
			}
		});
	}
}

function nv_list_action(action, url_action, del_confirm_no_post, checkss) {
	var listall = [];
	$('input.post:checked').each(function() {
		listall.push($(this).val());
	});

	if (listall.length < 1) {
		alert(del_confirm_no_post);
		return false;
	}

	if (action == 'delete_list_id') {
		if (confirm(nv_is_del_confirm[0])) {
			$.ajax({
				type : 'POST',
				url : url_action,
				data : 'saved_delete_list=1&listall=' + listall + '&checkss=' + checkss,
				success : function(data) {
					var r_split = data.split('_');
					if (r_split[0] == 'OK') {
						window.location.href = window.location.href;
					} else {
						alert(nv_is_del_confirm[2]);
					}
				}
			});
		}
	}

	return false;
}

function nv_auction_register(rowsid) {
	if (confirm(LANG.auction_register_confirm)) {
		$.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(), 'auction_register=1&id=' + rowsid + '&mod=register', function(res) {
			var r_split = res.split('_');
			if (r_split[0] != 'OK') {
				alert(r_split[1]);
			} else {
				$('#btn-auction-register').addClass('hidden');
				$('#btn-auction-cancel').removeClass('hidden');
				alert(LANG.auction_register_success);
			}
		});
	}
}

function nv_auction_cancel(rowsid) {
	if (confirm(LANG.auction_cancel_confirm)) {
		$.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(), 'auction_register=1&id=' + rowsid + '&mod=cancel', function(res) {
			var r_split = res.split('_');
			if (r_split[0] != 'OK') {
				alert(r_split[1]);
			} else {
				$('#btn-auction-register').removeClass('hidden');
				$('#btn-auction-cancel').addClass('hidden');
				alert(LANG.auction_cancel_succes);
			}
		});
	}
}

function nv_refresh(rowsid, checkss) {
	if (confirm(LANG.refresh_confirm)) {
		$.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(), 'refresh=1&id=' + rowsid + '&checkss=' + checkss, function(res) {
			var r_split = res.split('_');
			if (r_split[0] != 'OK') {
				alert(r_split[1]);
			} else {
				window.location.href = window.location.href;
			}
		});
	}
}

function nv_buy_refresh(id, module) {
	$.ajax({
		type : 'POST',
		url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + module + '&' + nv_fc_variable + '=payment',
		cache : !1,
		data : '&id=' + id + '&mod=refresh',
		dataType : "html"
	}).done(function(a) {
		modalShow('', a)
	});
	return !1
}

function nv_buy_group(id, bid, module) {
	$.ajax({
		type : 'POST',
		url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + module + '&' + nv_fc_variable + '=payment',
		cache : !1,
		data : '&id=' + id + '&groupid=' + bid + '&mod=group',
		dataType : "html"
	}).done(function(a) {
		modalShow('', a)
	});
	return !1
}

function nv_re_queue(id) {
	if (confirm(LANG.re_queue_confirm)) {
		$.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(), 're_queue=1&id=' + id, function(res) {
			var r_split = res.split('_');
			if (r_split[0] != 'OK') {
				alert(r_split[1]);
			} else {
				alert(LANG.re_queue_success);
			}
		});
	}
}

function nv_refresh_popup(id) {
	$.ajax({
		type : 'POST',
		url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(),
		cache : !1,
		data : 'refresh_popup=1&id=' + id,
		dataType : "html"
	}).done(function(a) {
		modalShow('', a)
	});
	return !1
}

function nv_price_control() {
	if ($('#pricetype').val() != 0) {
		$('#price, #price1, #unitid').prop('disabled', true);
	} else {
		$('#price, #price1, #unitid').prop('disabled', false);
	}
}

function nv_show_terms(title, ispopup) {
	$.ajax({
		type : 'POST',
		url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(),
		cache : !1,
		data : 'show_terms=1&ispopup=' + ispopup,
		dataType : "html"
	}).done(function(a) {
		modalShow(title, a)
	});
	return !1
}

function nv_popup_content(op) {
	$.ajax({
		type : 'POST',
		url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=' + op + '&ispopup=1',
		cache : !1,
		dataType : "html"
	}).done(function(a) {
		$('#sitemodal .modal-dialog').addClass('modal-lg');
		$('#sitemodal .modal-dialog .modal-header').remove();
		modalShow('', a)
	});
}

function nv_market_cat_change(id, catid){
    // hiển thị các trường tùy biến dữ liệu cho chủ đề
    $.ajax({
        type : 'POST',
        data: 'get_custom_field=1&id=' + id + '&catid=' + catid,
        url : nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=ajax&nocache=' + new Date().getTime(),
        success : function(html) {
            $('#custom_form').html(html);
        }
    });
}