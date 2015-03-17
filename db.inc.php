<?
$myhost = 'localhost';
$myuser = 'labs';
$mypass = 'l4b5';
$mydbname =  'labs_crop';
$mytabname = 'cropped';

try{
	$db = new PDO("mysql:host=$myhost;dbname=$mydbname", $myuser,$mypass);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
	die("problema connessione al database");
}//connect db
?>
