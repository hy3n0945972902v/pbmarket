<?php
/**
 * upload.php
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

// !! IMPORTANT:
// !! this file is just an example, it doesn't incorporate any security checks and
// !! is not recommended to be used in production environment as it is. Be sure to
// !! revise it and customize to your needs.

// Make sure file is not cached (as it happens for example on iOS devices)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/*
 * // Support CORS
 * header("Access-Control-Allow-Origin: *");
 * // other CORS headers if any...
 * if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
 * exit; // finish preflight CORS requests here
 * }
 */

$token = $nv_Request->get_title('token', 'get', '');
if ($token != md5($nv_Request->session_id . $global_config['sitekey'])) {
    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Invite token."}, "id" : "id"}');
}

// 5 minutes execution time
@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

// Settings
$targetDir = NV_ROOTDIR . DIRECTORY_SEPARATOR . NV_TEMP_DIR;

// $targetDir = 'uploads';
$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds
                        
// Get a file name
if ($nv_Request->isset_request('name', 'post')) {
    $fileName = $nv_Request->get_title('name', 'post', '');
} elseif (! empty($_FILES)) {
    $fileName = $_FILES["file"]["name"];
} else {
    $fileName = uniqid("file_");
}

$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

$user_config = nv_user_config();

$fileupload = '';
if (isset($_FILES['file']) and is_uploaded_file($_FILES['file']['tmp_name'])) {
    $upload = new NukeViet\Files\Upload(array(
        'images'
    ), $global_config['forbid_extensions'], $global_config['forbid_mimes'], $user_config['max_filesize'], $user_config['max_width'], $user_config['max_height']);
    $upload_info = $upload->save_file($_FILES['file'], $targetDir, true, $global_config['nv_auto_resize']);
    @unlink($_FILES['file']['tmp_name']);
    if (empty($upload_info['error'])) {
        if ($global_config['nv_auto_resize'] and ($upload_info['img_info'][0] > $user_config['max_width'] or $upload_info['img_info'][0] > $user_config['max_height'])) {
            $createImage = new NukeViet\Files\Image($targetDir . DIRECTORY_SEPARATOR . $upload_info['basename'], $upload_info['img_info'][0], $upload_info['img_info'][1]);
            $createImage->resizeXY($user_config['max_width'], $user_config['max_height']);
            $createImage->save($targetDir, $upload_info['basename'], 90);
            $createImage->close();
            $info = $createImage->create_Image_info;
            $upload_info['img_info'][0] = $info['width'];
            $upload_info['img_info'][1] = $info['height'];
            $upload_info['size'] = filesize($targetDir . DIRECTORY_SEPARATOR . $upload_info['basename']);
        }
        
        mt_srand((double) microtime() * 1000000);
        $maxran = 1000000;
        $random_num = mt_rand(0, $maxran);
        $random_num = md5($random_num);
        $nv_pathinfo_filename = nv_pathinfo_filename($upload_info['name']);
        $new_name = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $nv_pathinfo_filename . '.' . $random_num . '.' . $upload_info['ext'];
        $rename = nv_renamefile($upload_info['name'], $new_name);
        if ($rename[0] == 1) {
            $fileupload = $new_name;
        } else {
            $fileupload = $upload_info['name'];
        }
        @chmod($fileupload, 0644);
        
        // đóng dấu logo
        nv_image_logo($fileupload);
    } else {
        die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": ' . $upload_info['error'] . '}, "id" : "id"}');
    }
    unset($upload, $upload_info);
}

die(json_encode(array(
    'jsonrpc' => '2.0',
    'result' => null,
    'id' => 'id',
    'basename' => basename($fileupload),
    'path' => str_replace(NV_ROOTDIR, '', $fileupload),
    'homeimgfile' => str_replace(NV_ROOTDIR . '/' . NV_TEMP_DIR . '/', '', $fileupload)
)));
