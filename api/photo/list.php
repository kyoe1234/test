<?
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

require_once './include/startup.php';
require_once DIR_LIB.'/Photo.php';

$user_id = $_GET['user_id'];
$photo_id = $_GET['photo_id'];
$res = $_GET['res'];

$data = array();
// 회원의 사진 목록 
if ( $user_id ) {
	$photo_id_list = $g->db->fetch_col("
		SELECT id FROM test.userphoto
		WHERE userid = {$user_id}
		ORDER BY id DESC
	");

	foreach ( $photo_id_list as $photo_id ) {
		$photo = new Photo($photo_id);
		$data[] = $photo->to_array();
	}
}

// 회원의 사진
if ( $photo_id ) {
	$photo = new Photo($photo_id);
	$data = $photo->to_array();
}

$json_photo = json_encode($data);
if ( $res ) {
	echo $json_photo;
} else {
	echo '('.$json_photo.')';
}

?>
