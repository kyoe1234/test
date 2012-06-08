<?
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

require_once './include/startup.php';
require_once DIR_LIB.'/UserPhoto.php';

$user_id = $_GET['userid'];
$photo_id = $_GET['photoid'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Insert title here</title>
<link rel="stylesheet" href="<?=URL_MOBILE?>/bootstrap/css/bootstrap.min.css" />
<style type="text/css">
.content {
    margin:50px auto;
}
</style>
</head>
<body>

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">

            <a class="brand" href="#">Facechart</a>
            <div class="nav-collapse">
                <ul class="nav">
                    <li class="active"><a href="#">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <!--/.nav-collapse -->

            <form class="navbar-search pull-right">
                <input type="text" class="search-query" placeholder="Search">
            </form>
        </div>
    </div>
</div>

<div class="container content">
    <form action="./save.php" method="post" enctype="multipart/form-data" class="well form-inline">
        <input type="text" name="userid" class="input-small" placeholder="idx" />
        <input type="file" name="profile_image" id="fileInput" class="input-file" />
        <button type="submit" class="btn btn-primary">SAVE</button>
    </form>
	<div>
		<?
		if ( $user_id ):
			$user_photo = new UserPhoto($user_id);
			$photo_list = $user_photo->photo;
		?>
		<ul class="thumbnails">
			<?
			foreach ( $photo_list as $photo ):
			?>
        	<li>
          		<a href="#" class="thumbnail">
            		<img src="<?=$photo?>" alt="">
          		</a>
        	</li>
			<? endforeach; ?>
        </ul>
		<? endif; ?>
	</div>
</div>

</body>
</html>
