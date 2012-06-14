<?php
require_once './include/startup.php';
require_once DIR_LIB.'/Photo.php';

$user_id = $_POST['user_id'];
$file = $_FILES['photo'];

$photo_id = Photo::add($user_id, $file, $warning);
if ( !$photo_id ) { 
	echo $warning->json();
}

$photo = new Photo($photo_id);
echo json_encode($photo->to_array());
?>

