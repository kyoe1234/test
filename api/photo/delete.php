<?
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once './include/startup.php';
require_once DIR_LIB.'/Photo.php';

$photo_id = $_GET['photo_id'];

$result = Photo::remove($photo_id, $warning);
if ( !$result ) {
	echo $warning->json();
}

echo json_encode(array('result' => true));
?>
