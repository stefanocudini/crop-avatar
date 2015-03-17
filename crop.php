<?

if(!isset($_GET['filename']) or empty($_GET['filename']))
{
	header('HTTP/1.1 400 Bad Request');
	exit(0);
}

$fotofile = $_GET['filename'];

$imgdir = './upped/';

$imgs = imagecreatefromjpeg($fotofile);	

$cropscale = 1;
$s['x'] = $_GET['x'] * $cropscale;
$s['y'] = $_GET['y'] * $cropscale;
$s['w'] = $_GET['w'] * $cropscale;
$s['h'] = $_GET['h'] * $cropscale;

$cropfile = $imgdir.'crop_'.implode('-',$s).'_'.basename($fotofile);

if(!file_exists($cropfile)):

	$imgd = imagecreatetruecolor($s['w'],$s['h']);

	imagecopyresampled($imgd, $imgs,
		               0      , 0      , $s['x'], $s['y'],
		               $s['w'], $s['h'], $s['w'], $s['h']);

	imagejpeg($imgd, $cropfile, 80);
	chmod($cropfile, 0775);
	imagedestroy($imgs);
	imagedestroy($imgd);
endif;

require('thumb.php');
$dims['thumbquad'] = 0;
$dims['tnmargin'] = 0;
$dims['thumbcut'] = 0;
$dims['thumbround'] = 0;
$dims['qualit'] = 80;

$outsizes = array(50,100,200);		//dimensioni dei ritagli generati

foreach($outsizes as $tz):
	$dims['tnsize'] = $tz;
	$opts['cachefile'] = $imgdir.$tz.'_'.basename($cropfile);
	
	if(!file_exists($opts['cachefile']))
		thumb($cropfile);
	
	$src = $opts['cachefile'];
?>
<img class="loading" src="<?=$src?>" />
<?
endforeach;

require('save.php');




?>
