<?php
require_once 'facebook.php';
require_once 'config.php';

$fb = new Facebook(array(
				'appId' => $app_id,
				'secret' => $app_secret,
				'cookie' => true
));

$me = null;

$user = $fb->getUser();

try {
$me = $fb->api('/me');
}catch(FacebookApiException $e) {
			error_log($e);
	}

/*
$fb->setFileUploadSupport(true);


$album_details = array('message'=> $albumdesc, 'name'=> $albumname );
$create_album = $fb->api('/me/albums', 'post', $album_details);
$album_uid = $create_album['id'];
$file='img/'.$user.'.jpg';
$photo_details = array( 'message'=> $spam, 'image' => '@' . realpath($file) );
$upload_photo = $fb->api('/'.$album_uid.'/photos', 'post', $photo_details);
$photoid = $upload_photo['id'];
*/
?>
