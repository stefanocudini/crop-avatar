<?
/*
    Imagerotate replacement. ignore_transparent is work for png images
    Also, have some standard functions for 90, 180 and 270 degrees.
    Rotation is clockwise
*/

if(!function_exists("imagerotate")) {
    function imagerotate($srcImg, $angle, $bgcolor=0, $ignore_transparent = 0) {
        return imagerotateEquivalent($srcImg, $angle, $bgcolor, $ignore_transparent);
    }
}//su php 5.2 non ce sta piu imagerotate, nella 5.3 si!
//*/

#  foreach($_GET as $k=>$v) echo "$k:$v, ";
	$fotofile = $_GET['filename'];
	//contiene anche imgdir

	$angle = (int)$_GET['angle'];
	
/*	if($angle>0)
		$angle = 360-$angle;
	elseif($angle<0)
		$angle = abs($angle);
		*/
    $angle %= 360;
    $angle = -$angle;
    
	$imgdir = './upped/';
	
	$rotfile = $imgdir.'rot_'.(-$angle).'_'.basename($fotofile);

	if(!file_exists($rotfile) and $fotofile):
		
		$imgs = imagecreatefromjpeg($fotofile);	
		$ws = imagesx($imgs);
		$hs = imagesy($imgs);
		
		$imgr = imagerotate($imgs, $angle, imagecolorallocate($imgs, 255, 255, 255));
		#$imgr = imagerotateEquivalent($imgs, $angle, imagecolorallocate($imgs, 255, 255, 255));
		
		$wr = imagesx($imgr);
		$hr = imagesy($imgr);
		
		$imgd = imagecreatetruecolor($ws,$hs);
		imagefill($imgd, 0, 0, imagecolorallocate($imgd, 255, 255, 255));
		imagecopy($imgd, $imgr, ($ws-$wr)/2, ($hs-$hr)/2, 0, 0, $wr,$hr);
		imagejpeg($imgd, $rotfile, 80);
		chmod($rotfile, 0775);
		imagedestroy($imgs);
		imagedestroy($imgd);
	endif;
	
	echo $rotfile;	


function imagerotateEquivalent($srcImg, $angle, $bgcolor, $ignore_transparent = 0) {
    function rotateX($x, $y, $theta){
        return $x * cos($theta) - $y * sin($theta);
    }
    function rotateY($x, $y, $theta){
        return $x * sin($theta) + $y * cos($theta);
    }

    $srcw = imagesx($srcImg);
    $srch = imagesy($srcImg);

    //Normalize angle
    #$angle %= 360;
    //Set rotate to clockwise
    #$angle = -$angle;

    if($angle == 0) {
        if ($ignore_transparent == 0) {
            imagesavealpha($srcImg, true);
        }
        return $srcImg;
    }

    // Convert the angle to radians
    $theta = deg2rad ($angle);

    //Standart case of rotate
    if ( (abs($angle) == 90) || (abs($angle) == 270) ) {
        $width = $srch;
        $height = $srcw;
        if ( ($angle == 90) || ($angle == -270) ) {
            $minX = 0;
            $maxX = $width;
            $minY = -$height+1;
            $maxY = 1;
        } else if ( ($angle == -90) || ($angle == 270) ) {
            $minX = -$width+1;
            $maxX = 1;
            $minY = 0;
            $maxY = $height;
        }
    } else if (abs($angle) === 180) {
        $width = $srcw;
        $height = $srch;
        $minX = -$width+1;
        $maxX = 1;
        $minY = -$height+1;
        $maxY = 1;
    } else {
        // Calculate the width of the destination image.
        $temp = array (rotateX(0, 0, 0-$theta),
        rotateX($srcw, 0, 0-$theta),
        rotateX(0, $srch, 0-$theta),
        rotateX($srcw, $srch, 0-$theta)
        );
        $minX = floor(min($temp));
        $maxX = ceil(max($temp));
        $width = $maxX - $minX;

        // Calculate the height of the destination image.
        $temp = array (rotateY(0, 0, 0-$theta),
        rotateY($srcw, 0, 0-$theta),
        rotateY(0, $srch, 0-$theta),
        rotateY($srcw, $srch, 0-$theta)
        );
        $minY = floor(min($temp));
        $maxY = ceil(max($temp));
        $height = $maxY - $minY;
    }

    $destimg = imagecreatetruecolor($width, $height);
    if ($ignore_transparent == 0) {
        imagefill($destimg, 0, 0, imagecolorallocatealpha($destimg, 255,255, 255, 127));
        imagesavealpha($destimg, true);
    }

    // sets all pixels in the new image
    for($x=$minX; $x<$maxX; $x++) {
        for($y=$minY; $y<$maxY; $y++) {
            // fetch corresponding pixel from the source image
            $srcX = round(rotateX($x, $y, $theta));
            $srcY = round(rotateY($x, $y, $theta));
            if($srcX >= 0 && $srcX < $srcw && $srcY >= 0 && $srcY < $srch) {
                $color = imagecolorat($srcImg, $srcX, $srcY );
            } else {
                $color = $bgcolor;
            }
            imagesetpixel($destimg, $x-$minX, $y-$minY, $color);
        }
    }
    return $destimg;
}
?>
