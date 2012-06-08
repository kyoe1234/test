<?php
require_once './include/startup.php';
require_once DIR_LIB.'/UserPhoto.php';

$result = UserPhoto::add($_POST['userid'], $_FILES, $warning);

if ( !$result ) {
	$g->alert->back($warning->text);
}

$move_url = $_SERVER['HTTP_REFERER'].'?userid='.$_POST['userid'];
header('Location: '.$move_url);

?>
