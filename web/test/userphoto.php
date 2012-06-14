<?
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
require_once './include/startup.php';
require_once DIR_LIB.'/Photo.php';

$user_id = $_GET['user_id'];
$photo_id = $_GET['photo_id'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Insert title here</title>
<link rel="stylesheet" href="<?=URL_WEB?>/bootstrap/css/bootstrap.min.css" />
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
    <form action="./test/save.php" method="post" enctype="multipart/form-data" class="well form-inline">
        <input type="text" name="user_id" class="input-small" placeholder="idx" />
        <input type="file" name="photo" id="fileInput" class="input-file" />
        <button type="submit" class="btn btn-primary">SAVE</button>
    </form>
	<div>
		<?
		if ( $photo_id ):
			$user_photo = new Photo($photo_id);
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
