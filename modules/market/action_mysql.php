<?php

/**
 * @Project NUKEVIET 4.x
 * @Author mynukeviet (contact@mynukeviet.net)
 * @Copyright (C) 2016 mynukeviet. All rights reserved
 * @Createdate Fri, 30 Dec 2016 01:40:16 GMT
 */
if (!defined('NV_IS_FILE_MODULES')) die('Stop!!!');

$sql_drop_module = array();
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_auction";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_auction_register";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_block";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_block_cat";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_cat";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_company";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_images";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_queue_logs";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_queue_reason";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_rows";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_detail";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_saved";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_type";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_unit";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_econtent";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tags";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tags_id";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_fb_queue";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_mail_queue";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_refresh";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_freelance";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_freelance_payment";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_queue_edit";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_news";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_field";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_info";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_template";

$sql_create_module = $sql_drop_module;
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_auction(
  userid int(11) unsigned NOT NULL,
  rowsid int(11) unsigned NOT NULL,
  price double unsigned NOT NULL DEFAULT '0',
  addtime int(11) unsigned NOT NULL,
  UNIQUE KEY userid (userid,rowsid,price),
  KEY userid_2 (userid,rowsid)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_auction_register(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  userid int(11) unsigned NOT NULL,
  rowsid int(11) unsigned NOT NULL,
  addtime int(11) unsigned NOT NULL,
  status tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  UNIQUE KEY userid (userid,rowsid),
  KEY userid_2 (userid,rowsid)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_block(
  bid smallint(5) unsigned NOT NULL,
  id int(11) unsigned NOT NULL,
  exptime int(11) unsigned NOT NULL DEFAULT '0',
  weight int(11) unsigned NOT NULL,
  UNIQUE KEY bid (bid,id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_block_cat(
  bid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(250) NOT NULL,
  alias varchar(250) NOT NULL,
  description text NOT NULL,
  keywords varchar(255) NOT NULL,
  useradd tinyint(1) unsigned NOT NULL DEFAULT '0',
  adddefault tinyint(4) NOT NULL DEFAULT '0',
  numbers smallint(5) NOT NULL DEFAULT '10',
  image varchar(255) DEFAULT '',
  color varchar(10) NOT NULL,
  weight smallint(5) NOT NULL DEFAULT '0',
  prior smallint(5) unsigned NOT NULL DEFAULT '0',
  add_time int(11) NOT NULL DEFAULT '0',
  edit_time int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (bid),
  UNIQUE KEY title (title),
  UNIQUE KEY alias (alias)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_cat(
  id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  parentid smallint(4) unsigned NOT NULL DEFAULT '0',
  title varchar(250) NOT NULL DEFAULT '',
  alias varchar(250) NOT NULL DEFAULT '',
  custom_title varchar(255) NOT NULL DEFAULT '',
  keywords text NOT NULL,
  description tinytext NOT NULL,
  description_html text NOT NULL,
  groups_view varchar(255) NOT NULL DEFAULT '6',
  image varchar(255) NOT NULL DEFAULT '',
  pricetype tinyint(1) unsigned NOT NULL DEFAULT '1',
  inhome tinyint(1) unsigned NOT NULL DEFAULT '1',
  numlinks tinyint(3) unsigned NOT NULL DEFAULT '15',
  viewtype varchar(50) NOT NULL DEFAULT 'viewlist',
  lev smallint(4) unsigned NOT NULL DEFAULT '0',
  numsub smallint(4) unsigned NOT NULL DEFAULT '0',
  subid varchar(255) NOT NULL DEFAULT '',
  sort smallint(4) unsigned NOT NULL DEFAULT '0',
  form varchar(250) NOT NULL DEFAULT '',
  weight smallint(4) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Trạng thái',
  PRIMARY KEY (id),
  UNIQUE KEY alias (alias)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_company(
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  userid int(11) unsigned NOT NULL DEFAULT '0',
  title varchar(255) NOT NULL COMMENT 'Tên công ty',
  alias varchar(255) NOT NULL,
  provinceid mediumint(4) NOT NULL,
  districtid mediumint(8) unsigned NOT NULL,
  address varchar(255) NOT NULL COMMENT 'Địa chỉ',
  maps tinytext NOT NULL,
  taxcode varchar(50) NOT NULL,
  email varchar(50) NOT NULL COMMENT 'Email',
  fax varchar(20) NOT NULL COMMENT 'Fax',
  website varchar(100) NOT NULL COMMENT 'Website',
  image varchar(255) NOT NULL,
  agent smallint(4) unsigned NOT NULL COMMENT 'Số nhân viên',
  descripion text NOT NULL COMMENT 'Giới thiệu',
  contact_fullname varchar(255) NOT NULL COMMENT 'Họ tên người đại diện',
  contact_email varchar(50) NOT NULL COMMENT 'Email người đại diện',
  contact_phone varchar(20) NOT NULL COMMENT 'Điên thoại người đại diện',
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_images(
  rowsid int(11) unsigned NOT NULL,
  path varchar(255) NOT NULL,
  description text NOT NULL,
  is_main tinyint(1) unsigned NOT NULL,
  weight tinyint(2) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_queue_logs(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  rowsid int(11) unsigned NOT NULL,
  queue tinyint(1) unsigned NOT NULL DEFAULT '1',
  reason text NOT NULL,
  reasonid tinyint(2) unsigned NOT NULL DEFAULT '0',
  addtime int(11) unsigned NOT NULL,
  userid int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_queue_reason(
  id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL,
  note tinytext NOT NULL COMMENT 'Ghi chú',
  weight smallint(4) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL COMMENT 'Trạng thái',
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_rows(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  code varchar(15) NOT NULL DEFAULT '',
  title varchar(255) NOT NULL,
  alias varchar(255) NOT NULL,
  catid smallint(4) NOT NULL,
  groupid varchar(255) NOT NULL DEFAULT '',
  area_p smallint(4) NOT NULL COMMENT 'Tỉnh',
  area_d smallint(4) unsigned NOT NULL COMMENT 'Huyện',
  address varchar(255) NOT NULL DEFAULT '',
  typeid tinyint(1) NOT NULL,
  description text NOT NULL COMMENT 'Mô tả ngắn gọn',
  pricetype tinyint(1) unsigned NOT NULL DEFAULT '0',
  price double unsigned NOT NULL DEFAULT '0',
  price1 double unsigned NOT NULL DEFAULT '0',
  unitid smallint(4) unsigned NOT NULL,
  homeimgfile varchar(255) NOT NULL DEFAULT '',
  homeimgalt varchar(255) NOT NULL DEFAULT '',
  homeimgthumb tinyint(1) unsigned NOT NULL DEFAULT '0',
  countview int(11) unsigned NOT NULL DEFAULT '0',
  countcomment int(11) unsigned NOT NULL DEFAULT '0',
  addtime int(11) unsigned NOT NULL,
  edittime int(11) unsigned NOT NULL DEFAULT '0',
  exptime int(11) unsigned NOT NULL DEFAULT '0',
  auction tinyint(1) unsigned NOT NULL DEFAULT '0',
  auction_begin int(11) unsigned NOT NULL DEFAULT '0',
  auction_end int(11) unsigned NOT NULL DEFAULT '0',
  auction_price_begin double unsigned NOT NULL DEFAULT '0',
  auction_price_step double unsigned NOT NULL DEFAULT '0',
  groupview varchar(255) NOT NULL,
  userid int(11) unsigned NOT NULL COMMENT 'Người đăng',
  prior int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ưu tiên',
  ordertime int(11) unsigned NOT NULL DEFAULT '0',
  refresh_time int(11) unsigned NOT NULL DEFAULT '0',
  count_fb_post smallint(4) unsigned NOT NULL DEFAULT '0',
  is_queue tinyint(1) unsigned NOT NULL DEFAULT '0',
  is_queue_edit tinyint(1) unsigned NOT NULL DEFAULT '0',
  groups_config text NOT NULL DEFAULT '',
  status_admin tinyint(1) unsigned NOT NULL DEFAULT '1',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id),
  UNIQUE KEY code (code),
  KEY alias(alias),
  KEY catid(catid),
  KEY area_p(area_p),
  KEY area_d(area_d),
  KEY typeid(typeid),
  KEY userid(userid),
  KEY status_admin(status_admin),
  KEY status(status)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_detail(
  id int(11) unsigned NOT NULL,
  content text NOT NULL,
  maps varchar(255) NOT NULL,
  display_maps tinyint(1) unsigned NOT NULL DEFAULT '0',
  note text NOT NULL,
  groupcomment varchar(255) NOT NULL,
  contact_fullname varchar(255) NOT NULL,
  contact_email varchar(100) NOT NULL,
  contact_phone varchar(255) NOT NULL,
  contact_address varchar(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_saved(
  userid int(11) unsigned NOT NULL,
  rowsid int(11) unsigned NOT NULL,
  UNIQUE KEY userid (userid,rowsid),
  KEY userid_2 (userid,rowsid)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_type(
  id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(250) NOT NULL,
  alias varchar(250) NOT NULL,
  note text NOT NULL,
  weight smallint(4) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Trạng thái',
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_unit(
  id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL,
  note text NOT NULL,
  weight smallint(4) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Trạng thái',
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_econtent(
  action varchar(100) NOT NULL,
  econtent text NOT NULL,
  PRIMARY KEY (action)
) ENGINE=MyISAM";
$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_econtent (action, econtent) VALUES('queue_status', 'Xin chào <strong>&#91;NAME&#93;. </strong>Chúng tôi<strong>&nbsp;</strong>xin gửi đến bạn thông báo về trạng thái tin của bạn tại&nbsp;<strong>&#91;SITE_NAME&#93;!</strong><br /> <br /> Trạng thái hiện tại: &#91;STATUS&#93;<br /> Thực hiện bởi: &#91;QUEUE_NAME&#93;<br /> Thời gian thực hiện: &#91;QUEUE_TIME&#93;<br /> Ghi chú: &#91;NOTE&#93;<br /> <br /> Mọi ý kiến xin gửi về &#91;SITE_EMAIL&#93; để được giải đáp, xin cảm ơn!')";
$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_econtent (action, econtent) VALUES('refresh', '')";
$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_econtent (action, econtent) VALUES('terms', '<h2>1 .Tiêu đề</h2> - Tiêu đề cần thể hiện ngắn gọn nội dung cần đăng.<br /> Ví dụ: thay vì đặt <strong>Cần tuyển nhân viên</strong>, hãy đặt <strong>Cần tuyển nhân viên bán hàng quần áo tại shops Famy</strong><br /> - Không viết hoa toàn bộ tiêu đề<br /> - Nên viết hoa đầu dòng cũng như các địa danh, tên người, theo quy tắc tiếng việt <h2>2. Nội dung</h2> - Nội dung chi tiết cần mô tả rõ ràng nhất về nội dung muốn truyền tải<br /> - Chỉ chấp nhận nội dung là tiếng việt<br /> - Không đăng lại các nội dung đã được đăng trước đó. Sử dụng chức năng <a href=\"https://raodn.com/huong-dan/Huong-dan-lam-moi-tin.html\"><strong>Làm mới tin</strong></a> nếu bán muốn đăng lại tin cùng nội dung<br /> - Không được chèn các liên kết đến site khác ngoài <strong>raodn.com</strong><br /> - Không chứa các ký tự đặc biệt, cái biểu tượng (icon) <h2>3. Thông tin liên hệ</h2> Nội dung của bạn phải có ít nhất một trường thông tin liên hệ, là số điện thoại hoặc email để người xem có thể chủ động liên hệ với bạn khi cần thiết')";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tags(
  tid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  numnews mediumint(8) NOT NULL DEFAULT '0',
  alias varchar(250) NOT NULL DEFAULT '',
  image varchar(255) DEFAULT '',
  description text,
  keywords varchar(255) DEFAULT '',
  PRIMARY KEY (tid),
  UNIQUE KEY alias (alias)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tags_id(
  id int(11) NOT NULL,
  tid mediumint(9) NOT NULL,
  keyword varchar(65) NOT NULL,
  UNIQUE KEY id_tid (id,tid),
  KEY tid (tid)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_fb_queue(
  rowsid int(11) unsigned NOT NULL,
  UNIQUE KEY rowsid (rowsid)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_mail_queue(
  id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  tomail varchar(100) NOT NULL,
  subject varchar(255) NOT NULL,
  message text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_refresh(
  userid int(11) unsigned NOT NULL,
  count int(11) unsigned NOT NULL DEFAULT '0',
  free smallint(4) unsigned NOT NULL DEFAULT '0',
  free_time int(11) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY userid (userid)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_freelance(
  userid int(11) unsigned NOT NULL,
  salary double unsigned NOT NULL DEFAULT '0' COMMENT 'Lương',
  total double unsigned NOT NULL DEFAULT '0' COMMENT 'Tổng thu nhập',
  pay double unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY userid (userid)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_freelance_payment(
  id smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  userid int(11) unsigned NOT NULL,
  money double unsigned NOT NULL DEFAULT '0' COMMENT 'Số tiền thanh toán',
  addtime int(11) unsigned NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_queue_edit(
  rowsid int(11) unsigned NOT NULL,
  title varchar(255) NOT NULL,
  catid smallint(4) NOT NULL,
  area_p smallint(4) NOT NULL COMMENT 'Vùng',
  area_d smallint(4) unsigned NOT NULL,
  typeid tinyint(1) NOT NULL,
  description text NOT NULL,
  content text NOT NULL,
  maps varchar(255) NOT NULL,
  display_maps tinyint(1) unsigned NOT NULL DEFAULT '0',
  pricetype tinyint(1) unsigned NOT NULL DEFAULT '0',
  price double unsigned NOT NULL DEFAULT '0',
  price1 double unsigned NOT NULL DEFAULT '0',
  unitid smallint(4) unsigned NOT NULL,
  homeimgfile varchar(255) NOT NULL DEFAULT '',
  homeimgalt varchar(255) NOT NULL DEFAULT '',
  homeimgthumb tinyint(1) unsigned NOT NULL DEFAULT '0',
  note text NOT NULL,
  exptime int(11) unsigned NOT NULL DEFAULT '0',
  auction tinyint(1) unsigned NOT NULL DEFAULT '0',
  auction_begin int(11) unsigned NOT NULL DEFAULT '0',
  auction_end int(11) unsigned NOT NULL DEFAULT '0',
  auction_price_begin double unsigned NOT NULL DEFAULT '0',
  auction_price_step double unsigned NOT NULL DEFAULT '0',
  contact_fullname varchar(255) NOT NULL,
  contact_email varchar(100) NOT NULL,
  contact_phone varchar(255) NOT NULL,
  contact_address varchar(255) NOT NULL,
  images TEXT NOT NULL DEFAULT '',
  keywords TEXT NOT NULL DEFAULT '',
  PRIMARY KEY (rowsid)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_news(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  catid smallint(4) unsigned NOT NULL,
  content text NOT NULL,
  addtime int(11) unsigned NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_template (
  id mediumint(8) NOT NULL AUTO_INCREMENT,
  status tinyint(1) NOT NULL DEFAULT '1',
  title VARCHAR(250) NOT NULL DEFAULT '',
  alias VARCHAR(250) NOT NULL DEFAULT '',
  weight mediumint(8) unsigned NOT NULL DEFAULT '1',
  UNIQUE alias (alias),
  PRIMARY KEY (id)
) ENGINE=MyISAM ";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_field (
  fid mediumint(8) NOT NULL AUTO_INCREMENT,
  field varchar(25) NOT NULL,
  listtemplate varchar(25) NOT NULL,
  tab varchar(250) NOT NULL DEFAULT '',
  weight int(10) unsigned NOT NULL DEFAULT '1',
  field_type enum('number','date','textbox','textarea','editor','select','radio','checkbox','multiselect') NOT NULL DEFAULT 'textbox',
  field_choices text NOT NULL,
  sql_choices text NOT NULL,
  match_type enum('none','alphanumeric','email','url','regex','callback') NOT NULL DEFAULT 'none',
  match_regex varchar(250) NOT NULL DEFAULT '',
  func_callback varchar(75) NOT NULL DEFAULT '',
  min_length int(11) NOT NULL DEFAULT '0',
  max_length bigint(20) unsigned NOT NULL DEFAULT '0',
  class varchar(25) NOT NULL DEFAULT '',
  language text NOT NULL,
  default_value varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (fid),
  UNIQUE KEY field (field)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_info (
	rowid int(11) unsigned NOT NULL,
	PRIMARY KEY (rowid)
) ENGINE=MyISAM";

$data = array();
$data['countryid'] = '1'; // Việt Nam
$data['des_point'] = '.';
$data['thousands_sep'] = ',';
$data['membergroup'] = 4;
$data['freelancegroup'] = 0;
$data['money_unit'] = 'đ';
$data['homedata'] = 1; // 0: khong hien thi, 1: tat ca, 2: chu de
$data['hometype'] = 'viewlist'; // 0: danh sach, 1: luoi
$data['per_page'] = 20;
$data['structure_upload'] = 'username_Y';
$data['no_image'] = '';
$data['home_image_size'] = '150x150';
$data['allow_auto_code'] = 1;
$data['code_format'] = 'T%06s';
$data['numother'] = 15;
$data['grouppost'] = 4; // nhom duoc phep dang tin
$data['grouppostconfig'] = '';
$data['maxsizeimage'] = '800x800'; // kich thuoc anh lon nhat
$data['maxsizeupload'] = 1342177; // 1.28mb: dung luong duoc phep upload
$data['auction'] = 0; // kich hoat ban dau gia
$data['auction_group'] = 6; // nhom duoc tao dau gia
$data['auction_register_time'] = 1440; // thoi gian dang ky dau gia, phut
$data['auction_firebase_url'] = ''; // kho dữ liệu thực firebase
$data['googlemaps_appid'] = '';
$data['usergrouppost'] = '';
$data['refresh_allow'] = 0;
$data['refresh_config'] = '';
$data['refresh_default'] = 0;
$data['refresh_free'] = 0;
$data['refresh_timelimit'] = 120; // 120 phut
$data['specialgroup_config'] = '';
$data['style_default'] = 'viewlist_simple';
$data['province_default'] = 0;
$data['tags_alias_lower'] = 1;
$data['tags_alias'] = 0;
$data['auto_tags'] = 0;
$data['tags_remind'] = 1;
$data['fb_enable'] = 0;
$data['fb_appid'] = '';
$data['fb_secret'] = '';
$data['fb_accesstoken'] = '';
$data['fb_pagetoken'] = '';
$data['fb_groupid'] = '';
$data['similar_content'] = 80;
$data['similar_time'] = 5;
$data['payport'] = 0;
$data['auto_link'] = 1;
$data['auto_link_casesens'] = 0;
$data['auto_link_target'] = '';
$data['auto_link_limit'] = 3;
$data['editor_guest'] = 0;
$data['block_viewlist'] = 6;
$data['remove_link'] = 1;
$data['maps_appid'] = '';
$data['priceformat'] = 0;

foreach ($data as $config_name => $config_value) {
    $sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', " . $db->quote($module_name) . ", " . $db->quote($config_name) . ", " . $db->quote($config_value) . ")";
}
