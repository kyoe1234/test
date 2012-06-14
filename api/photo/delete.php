<?
require_once './include/startup.php';
require_once DIR_LIB.'/Photo.php';

$photo_id = $_GET['photo_id'];

$result = Photo::remove($photo_id, $warning);
if ( !$result ) {
	header('HTTP/1.1 400 Bad Request');
	echo $warning->json();
}

echo json_encode(array('result' => true));
?>
