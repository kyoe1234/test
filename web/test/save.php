<?php
require_once './include/startup.php';
require_once DIR_LIB.'/Photo.php';

$user_id = $_POST['user_id'];
$file = $_FILES['photo'];

$photo_id = Photo::add($user_id, $file, $warning);

if ( !$photo_id ) {
	$g->alert->back($warning->text);
}

$move_url = $_SERVER['HTTP_REFERER'].'?photo_id='.$photo_id;
header('Location: '.$move_url);
?>
