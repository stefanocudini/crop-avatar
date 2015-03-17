<?

require('db.inc.php');

try {
	$sql = "SELECT * FROM $mytabname";
	$res = $db->query($sql);
	//return a simple array of all the names
	$rows = $res->fetchAll();	
}catch(PDOException $e){
	die($e);
}

#	print_r($rows);

if(count($rows)>0):
?><table><?

	?><tr><?
	foreach(array_keys($rows[0]) as $c)
		if(!is_numeric($c))
			echo "<th>$c</th>";
	?></tr><?

	foreach($rows as $k=>$r)
		echo '<tr>'.
				'<td>'.$r['id'].'</td>'.
				'<td>'.$r['ejer'].'</td>'.
				'<td>'.basename($r['imagesname']).'</td>'.
				'<td><img src="'.dirname($r['profilbillede']).'/50_'.basename($r['profilbillede']).'" /></td>'.
				'<td>'.$r['type'].'</td>'.
			 '</tr>'."\n";
?></table><?
endif;

?>
