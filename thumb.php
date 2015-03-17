<?
/*
  generatore di thumbnail
  copyleft 2008 Stefano Cudini
  stefano.cudini@gmail.com

  utilizzo:
  <img src="thumb.php?thumb=nomefile.jpg" />
  <img src="thumb.php?thumb=nomefile.jpg&tnsize=120&thumbquad=1&tnmargin=2&qualit=90&thumbcut=0&thumbround=1&thumbrad=10" />
*/

$dims['tnsize'] = 120;     //dimensioni in pixel delle thumbnails 20-800
$dims['thumbquad'] = 1;    //genera le thumbnails delle foto quadrate con lo sfondo
$dims['tnmargin'] = 2;     //bordo attorno alle thumbnail
$dims['thumbcut'] = 0;     //taglia le parti della foto che uscirebbero dalla thumbnail
$dims['thumbround'] = 1;   //genera thumbnail con angoli smussati con raggio thumbrad
$dims['thumbrad'] = 20;    //raggio degli spigoli
$dims['qualit'] = 80;      //qualita delle thumbnails 0-100 se $opts['typeoutput'] = 'jpeg'

$colors['background'] = hexrgb('#ffffff');  //sfondo della pagina
$colors['bgthumb'] = hexrgb('#cccccc');     //sfondo interno delle thumbnails quadrate

$opts['typesinput'] = array('jpeg','gif','png');  //tipi(non estensioni) di immagini consentite
$opts['typeoutput'] = 'jpeg';  //formato con cui è generata la thumbnail
$opts['cachefile'] = null;  //file di cache
define('CHMOD', 0775);		//permessi sul file di cache creato
///opzioni


if(isset($_GET['thumb'])):
if(isset($_GET['tnsize']))     $dims['tnsize'] = intval($_GET['tnsize']);
if(isset($_GET['thumbquad']))  $dims['thumbquad'] = intval($_GET['thumbquad']);
if(isset($_GET['tnmargin']))   $dims['tnmargin'] = intval($_GET['tnmargin']);
if(isset($_GET['thumbcut']))   $dims['thumbcut'] = intval($_GET['thumbcut']);
if(isset($_GET['thumbround'])) $dims['thumbround'] = intval($_GET['thumbround']);
if(isset($_GET['thumbrad']))   $dims['thumbrad'] = intval($_GET['thumbrad']);
if(isset($_GET['qualit']))     $dims['qualit'] = intval($_GET['qualit']);
endif;

//se viene eseguito standalone
if( basename(__FILE__)==basename($_SERVER['PHP_SELF']) and isset($_GET['thumb']))
  thumb($_GET['thumb']);

function thumb($fotofile)  //manda in ouput la thumbnail
{
	global $dims;
	global $opts;

	$ext = getext($fotofile);

	if(file_exists($fotofile) and $ext!==false ):
		$func = "imagecreatefrom$ext";
		$bigimage = $func($fotofile);
		$tnimage = imagethumb($bigimage);
		imagedestroy($bigimage);
	else:
		$tnimage = imagethumb(); //thumb vuota
	endif;

	if($opts['cachefile']==null)
		header("Content-type: image/".$opts['typeoutput']);

	if($opts['typeoutput']=='jpeg'):
		imagejpeg($tnimage, $opts['cachefile'], $dims['qualit']);
		@chmod($opts['cachefile'], CHMOD);
	else:
		$func = 'image'.$opts['typeoutput'];
		$func($tnimage, $opts['cachefile']);
		@chmod($opts['cachefile'], CHMOD);
	endif;
	imagedestroy($tnimage);
}
//fine thumb()

function imagethumb($fotoimg=false)  //restistuisce una immagine gd
{
  global $dims;
  global $colors;
  global $opts;
  
  $qualit = $dims['qualit'];
  $tnsize = $dims['tnsize'];
  $thumbquad = $dims['thumbquad'];
  $thumbcut = $dims['thumbcut'];
  $thumbround = $dims['thumbround'];
  $margin = $dims['tnmargin'];
  
  $border = 22;  //spessore della croce in caso di immagine originale inesistente

  if($fotoimg===false) //thumb vuota croce
  {
	$bigimage = imagecreatetruecolor($tnsize, $tnsize);
	$back = imagecolorallocate($bigimage, $colors['bgthumb'][0], $colors['bgthumb'][1], $colors['bgthumb'][2]);
	$text = imagecolorallocate($bigimage, $colors['background'][0], $colors['background'][1], $colors['background'][2]);
	imagefill($bigimage, 0, 0, $back);
	imagesetthickness($bigimage, $border);
	imagefilledrectangle($bigimage, 0, 0, $tnsize, $tnsize, $back);
	imagerectangle($bigimage, $margin, $margin, $tnsize-$margin, $tnsize-$margin, $back);
	imageline($bigimage, 0,0,$tnsize,$tnsize, $text);
	imageline($bigimage, $tnsize,0,0,$tnsize, $text);
	$bigimage = imagerotate($bigimage, 0, $back);
  }
  else
  	$bigimage = $fotoimg;

  $sw = imagesx($bigimage);
  $sh = imagesy($bigimage);
    
  $maxtnsize = max($sw,$sh);
  $mintnsize = 20;

  $tnsizem = $tnsize-$margin*2;
  $radius = $dims['thumbrad'];

  if($sw==$sh)
  {
    $sx = 0;
	$sy = 0;
    $dx = $margin;
    $dy = $margin;
    $dw = $tnsizem>$maxtnsize ? $maxtnsize : $tnsizem;
    $dh = $tnsizem>$maxtnsize ? $maxtnsize : $tnsizem;
  }
  elseif($sw>$sh)
  {
  	if($thumbquad and $thumbcut):
		$sx = ($sw-$sh)/2;
		$sy = 0;
		$sw = $sh;
		$sh = $sh;
		$dx = $margin;
		$dw = $tnsizem;
		$dh = ($sw / $sh) * $tnsizem;
		$dy = ($tnsize - $dw) / 2;
	else:
		$sx = 0;
		$sy = 0;
		$dx = $margin;
		$dw = $tnsizem;
		$dh = ($sh / $sw) * $tnsizem;
		$dy = ($tnsize - $dh) / 2;
	endif;
  }
  elseif($sw<$sh)
  {
	  if($thumbquad and $thumbcut):
		$sx = 0;
		$sy = ($sh-$sw)/2;
		$sw = $sw;
		$sh = $sw;
		$dh = $tnsizem;
		$dw = ($sw / $sh) * $tnsizem;
		$dx = $margin;
		$dy = $margin;
	  else:
		$sx = 0;
		$sy = 0;
		$dh = $tnsizem;
		$dw = ($sw / $sh) * $tnsizem;
		$dx = ($tnsize - $dw) / 2;
	    $dy = $margin;
	  endif;
  }
  //calcola dimensioni foto contenuta nella thumbnail

  if($thumbquad)
    $tnimage = imagecreatetruecolor($tnsize, $tnsize);
  else
    $tnimage = imagecreatetruecolor( floor($dw+$margin*2), floor($dh+$margin*2) );

  $back = imagecolorallocate($tnimage, $colors['bgthumb'][0], $colors['bgthumb'][1], $colors['bgthumb'][2]);
  $box = imagecolorallocate($tnimage, $colors['background'][0], $colors['background'][1], $colors['background'][2]);  	

  if($thumbround)
    imagefilledrectangleround($tnimage,$radius,$back,$box);  //crea sfondo thumbnail
  else
    imagefill($tnimage, 0, 0, $back);  //setta sfondo

  if($thumbquad)
    imagecopyresampled($tnimage, $bigimage, $dx, $dy, $sx, $sy, $dw, $dh, $sw, $sh);
  else
    imagecopyresampled($tnimage, $bigimage, $margin, $margin, $sx, $sy, $dw, $dh, $sw, $sh);

  if($thumbround)
    imagerectangleround($tnimage,$radius,$back,$box);  //arrotonda spigoli

  return $tnimage;
}

function imagefilledrectangleround(&$img, $radius, $color, $back)  //crea rettangoli con spigoli arrotondati
{
  global $colors;
  global $dims;

    $x = $y = 0;
    $width = imagesx($img)-1;
    $height = imagesy($img)-1;

    $trasp = imagecolorallocate($img,$colors['background'][0],$colors['background'][1],$colors['background'][2]);
    imagefilledrectangle($img,0,0,$width,$height,$trasp);

    imagefilledrectangle($img, $x+$radius, $y, $width-$radius, $height, $color);
    imagefilledrectangle($img, $x, $y+$radius, $width, $height-$radius, $color);
    imagefilledellipse($img, $x+$radius, $y+$radius, $radius*2, $radius*2, $color);
    imagefilledellipse($img, $width-$radius, $y+$radius, $radius*2, $radius*2, $color);
    imagefilledellipse($img, $x+$radius, $height-$radius, $radius*2, $radius*2, $color);
    imagefilledellipse($img, $width-$radius, $height-$radius, $radius*2, $radius*2, $color);

    imagecolortransparent($img, $trasp);
}

function imagerectangleround(&$img, $radius, $color, $back)  //mia funzione per fare cornici rettangolari con spigoli arrotondati
{
    global $colors;
	global $dims;

    $x = $y = 0;
    $width = imagesx($img);
    $height = imagesy($img);
	$thick = 2;
	imagesetthickness($img,$thick);

	if($dims['tnmargin']>0)
      imagerectangle($img,0,1,$width-1,$height-2,$color);  //questi +1-1-2+2+1-1 sono di origine empirica!

#   imagerectangle($img, $x+$radius, $y, $width-$radius, $height, $color);
#   imagerectangle($img, $x, $y+$radius, $width, $height-$radius, $color);
    $r=$radius+1;

	$radius++;
	if($dims['tnmargin']>0)
	{
    imagearc($img, $x+$r, $y+$r, $radius*2, $radius*2, 180, 270, $color);
    imagearc($img, $width-$r, $y+$r, $radius*2, $radius*2, 270, 0, $color);
    imagearc($img, $width-$r, $height-$r, $radius*2, $radius*2, 0, 90, $color);
    imagearc($img, $x+$r, $height-$r, $radius*2, $radius*2, 90, 180, $color);
	$radius++;
    imagearc($img, $x+$r, $y+$r, $radius*2, $radius*2, 180, 270, $color);
    imagearc($img, $width-$r, $y+$r, $radius*2, $radius*2, 270, 0, $color);
    imagearc($img, $width-$r, $height-$r, $radius*2, $radius*2, 0, 90, $color);
    imagearc($img, $x+$r, $height-$r, $radius*2, $radius*2, 90, 180, $color);
	}

	while(++$radius<$r+10)
	{
    imagearc($img, $x+$r, $y+$r, $radius*2, $radius*2, 180, 270, $back);
    imagearc($img, $width-$r, $y+$r, $radius*2, $radius*2, 270, 0, $back);

    imagearc($img, $width-$r, $height-$r, $radius*2, $radius*2, 0, 90, $back);
    imagearc($img, $x+$r, $height-$r, $radius*2, $radius*2, 90, 180, $back);
	}
}

function hexrgb($color)  //converte colori html in rgb
{
  $rgb = array(0,0,0);
  if( eregi( "[#]?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})", $color, $ret ) )
  {
    $rgb = array(hexdec($ret[1]), hexdec($ret[2]), hexdec($ret[3]));
  }
  return $rgb;
}

function getext($fotofile)  //restituisce l'estensione del file
{
	global $opts;
	
	if(is_array($s = @getimagesize($fotofile)))
	{
	$type = next(explode('/',$s['mime']));

	if(in_array($type,$opts['typesinput']))
		return $type;
	else
		return false;
	}	
	else
		return false;
}


?>
