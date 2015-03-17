<?

require('db.inc.php');

if(isset($cropfile)):
  	
	$q = "INSERT INTO $mytabname (ejer, imagesname, profilbillede, type ) ".
		 "VALUES('stef','$fotofile','$cropfile','person')";
try {
	$res = $db->exec($q);
}catch(PDOException $e){
	die($e);
}

endif;
?>
