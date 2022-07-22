<?php

$index = 0;
$first = '';
$marks = [];

require_once '../include/conn.inc.php';															// connect to the database named 'scripture'
$db = get_my_db();

include 'include/v.key.php';																	// get v and key

include 'include/idx.iso.php';																	// get idx or iso

if ($index == 0) {
	die ('HACK!');
}

$rel = '';
if (isset($_GET['rel'])) {
	$rel = strtolower(trim($_GET['rel']));
	if ($rel != 'android' && $rel != 'ios') {
		$rel = '';
	}
}

if ($index == 1) {
    $stmt_app = $db->prepare("SELECT `ISO`, `ROD_Code`, `Variant_Code`, ISO_ROD_index, `Cell_Phone_Title`, `Cell_Phone_File` FROM `CellPhone` WHERE ISO_ROD_index = ? AND (`Cell_Phone_Title` = 'iOS Asset Package' OR `Cell_Phone_Title` = 'Android App') ORDER BY `Cell_Phone_Title`");
    $stmt_app->bind_param('i', $idx);															// bind parameters for markers
}
else {
	if ($iso == 'ALL') {
		$stmt_app = $db->prepare("SELECT `ISO`, `ROD_Code`, `Variant_Code`, ISO_ROD_index, `Cell_Phone_Title`, `Cell_Phone_File` FROM `CellPhone` WHERE (`Cell_Phone_Title` = 'iOS Asset Package' OR `Cell_Phone_Title` = 'Android App') ORDER BY `ISO`, `ROD_Code`, `Variant_Code`, `Cell_Phone_Title`");
	}
	elseif ($rod == 'ALL' && $var == 'ALL') {
		$stmt_app = $db->prepare("SELECT `ISO`, `ROD_Code`, `Variant_Code`, ISO_ROD_index, `Cell_Phone_Title`, `Cell_Phone_File` FROM `CellPhone` WHERE `ISO` = ? AND (`Cell_Phone_Title` = 'iOS Asset Package' OR `Cell_Phone_Title` = 'Android App') ORDER BY `Cell_Phone_Title`");
		$stmt_app->bind_param('s', $iso);														// bind parameters for markers
	}
	elseif ($rod == 'ALL') {
		$stmt_app = $db->prepare("SELECT `ISO`, `ROD_Code`, `Variant_Code`, ISO_ROD_index, `Cell_Phone_Title`, `Cell_Phone_File` FROM `CellPhone` WHERE `ISO` = ? AND `Variant_Code` = ? AND (`Cell_Phone_Title` = 'iOS Asset Package' OR `Cell_Phone_Title` = 'Android App') ORDER BY `Cell_Phone_Title`");
		$stmt_app->bind_param('ss', $iso, $var);												// bind parameters for markers
	}
	elseif ($var == 'ALL') {
			$stmt_app = $db->prepare("SELECT `ISO`, `ROD_Code`, `Variant_Code`, ISO_ROD_index, `Cell_Phone_Title`, `Cell_Phone_File` FROM `CellPhone` WHERE `ISO` = ? AND `ROD_Code` = ? AND (`Cell_Phone_Title` = 'iOS Asset Package' OR `Cell_Phone_Title` = 'Android App') ORDER BY `Cell_Phone_Title`");
			$stmt_app->bind_param('ss', $iso, $rod);											// bind parameters for markers
	}
	else {
		$stmt_app = $db->prepare("SELECT `ISO`, `ROD_Code`, `Variant_Code`, ISO_ROD_index, `Cell_Phone_Title`, `Cell_Phone_File` FROM `CellPhone` WHERE `ISO` = ? AND `ROD_Code` = ? AND `Variant_Code` = ? AND (`Cell_Phone_Title` = 'iOS Asset Package' OR `Cell_Phone_Title` = 'Android App') ORDER BY `Cell_Phone_Title`");
		$stmt_app->bind_param('sss', $iso, $rod, $var);											// bind parameters for markers
	}
}

$stmt_app->execute();															        		// execute query
$result_app = $stmt_app->get_result();

if ($result_app->num_rows == 0) {
	die ('The record(s) in the App does not exist. Try a different iso or idx.');
}

$stmt_var = $db->prepare("SELECT Variant_Eng FROM Variants WHERE Variant_Code = ?");

$m=0;
$n=0;
$p=0;
$idxTemp = '';

$first = '{';
if ($rel == '') {
	while ($row_links = $result_app->fetch_assoc()) {
		$idx = (int)$row_links['ISO_ROD_index'];
		$app_name = $row_links['Cell_Phone_Title'];
		$app_download = $row_links['Cell_Phone_File'];
		if ($idx != $idxTemp) {																		// if idx doesn't equal to the idx of the last record
			if ($m != 0) {																			// if the id doesn't equal the first id
				$first = rtrim($first, ',');
				$first .= '}}},';
			}
			$idxTemp = $idx;
			$m++;																					// id
			$p=0;																					// Android App count
			$n=0;																					// iOS Asset Package count
			$iso = $row_links['ISO'];
			$rod = $row_links['ROD_Code'];
			$var = $row_links['Variant_Code'];
			$Variant_name = '';
			if ($var != '') {
				$stmt_var->bind_param('s', $var);													// bind parameters for markers
				$stmt_var->execute();																// execute query
				$result_temp = $stmt_var->get_result();
				$row_temp = $result_temp->fetch_assoc();
				$Variant_name = $row_temp['Variant_Eng'];
			}
			$first .= '"'.($m-1).'": ';
			$first .= '{"type":                     "Apps",';
			$first .= '"id":                        "'.$m.'",';
			$first .= '"attributes": {';
			$first .= '"iso":                       "'.$iso.'",';
			$first .= '"rod":				        "'.$rod.'",';
			$first .= '"var_code":		    	    "'.$var.'",';
			$first .= '"var_name":					"'.$Variant_name.'",';
			$first .= '"iso_query_string":	        "sortby=lang&iso='.$iso;
			if ($rod != '00000') {
				$first .= '&rod='.$rod;
			}
			if ($var != '') {
				$first .= '&var='.$var;
			}
			$first .= '",';
			$first .= '"idx":		                '.$idx.',';
			$first .= '"idx_query_string":          "sortby=lang&idx='.$idx.'"';	
			$first .= '},';
			$first .= '"relationships": {';
		}
		// 'Android App'
		if ($app_name == 'Android App') {															// Android App
			if ($p === 0) {
				$first .= '"android_app": {';
			}
			if (strpos($app_download, 'http') !== false) {
				$first .= '"'.$p.'":						"'.$app_download.'",';
			}
			else {
				$first .= '"'.$p.'":						"data/'.$iso.'/study/'.$app_download.'",';
			}
			$p++;
		}
		if ($app_name == 'iOS Asset Package' && $p != 0) {											// if record equal 'iOS Asset Package' and if 'Android App' count is not eqaul to 0
			$p = 0;
			$first = rtrim($first, ',');
		}
		//'iOS Asset Package'
		if ($app_name == 'iOS Asset Package') {														// iOS Asset Package
			if ($n === 0) {
				$first .= '},"iOS": {';
			}
			if (strpos($app_download, 'http') !== false) {
				$first .= '"'.$n.'":						"'.$app_download.'",';
			}
			else {
				$first .= '"'.$n.'":						"data/'.$iso.'/study/'.$app_download.'",';
			}
			$n++;
		}
	}
}
elseif ($rel == 'android') {																		// only 'Android App'
	while ($row_links = $result_app->fetch_assoc()) {
		$idx = (int)$row_links['ISO_ROD_index'];
		$app_name = $row_links['Cell_Phone_Title'];
		if ($app_name != 'Android App') continue;
		$app_download = $row_links['Cell_Phone_File'];
		if ($idx != $idxTemp) {																		// if idx doesn't equal to the idx of the last record
			if ($m != 0) {																			// if the id doesn't equal the first id
				$first = rtrim($first, ',');
				$first .= '}}},';
			}
			$idxTemp = $idx;
			$m++;																					// id
			$p=0;																					// Android App count
			$n=0;																					// iOS Asset Package count
			$iso = $row_links['ISO'];
			$rod = $row_links['ROD_Code'];
			$var = $row_links['Variant_Code'];
			$Variant_name = '';
			if ($var != '') {
				$stmt_var->bind_param('s', $var);													// bind parameters for markers
				$stmt_var->execute();																// execute query
				$result_temp = $stmt_var->get_result();
				$row_temp = $result_temp->fetch_assoc();
				$Variant_name = $row_temp['Variant_Eng'];
			}
			$first .= '"'.($m-1).'": ';
			$first .= '{"type":                     "Apps",';
			$first .= '"id":                        "'.$m.'",';
			$first .= '"attributes": {';
			$first .= '"iso":                       "'.$iso.'",';
			$first .= '"rod":				        "'.$rod.'",';
			$first .= '"var_code":		    	    "'.$var.'",';
			$first .= '"var_name":					"'.$Variant_name.'",';
			$first .= '"iso_query_string":	        "sortby=lang&iso='.$iso;
			if ($rod != '00000') {
				$first .= '&rod='.$rod;
			}
			if ($var != '') {
				$first .= '&var='.$var;
			}
			$first .= '",';
			$first .= '"idx":		                '.$idx.',';
			$first .= '"idx_query_string":          "sortby=lang&idx='.$idx.'"';	
			$first .= '},';
			$first .= '"relationships": {';
		}
		if ($p === 0) {
			$first .= '"android_app": {';
		}
		if (strpos($app_download, 'http') !== false) {
			$first .= '"'.$p.'":						"'.$app_download.'",';
		}
		else {
			$first .= '"'.$p.'":						"data/'.$iso.'/study/'.$app_download.'",';
		}
		$p++;
	}
}
elseif ($rel == 'ios') {																			// only 'iOS Asset Package'
	while ($row_links = $result_app->fetch_assoc()) {
		$idx = (int)$row_links['ISO_ROD_index'];
		$app_name = $row_links['Cell_Phone_Title'];
		if ($app_name != 'iOS Asset Package') continue;
		$app_download = $row_links['Cell_Phone_File'];
		if ($idx != $idxTemp) {																		// if idx doesn't equal to the idx of the last record
			if ($m != 0) {																			// if the id doesn't equal the first id
				$first = rtrim($first, ',');
				$first .= '}}},';
			}
			$idxTemp = $idx;
			$m++;																					// id
			$p=0;																					// Android App count
			$n=0;																					// iOS Asset Package count
			$iso = $row_links['ISO'];
			$rod = $row_links['ROD_Code'];
			$var = $row_links['Variant_Code'];
			$Variant_name = '';
			if ($var != '') {
				$stmt_var->bind_param('s', $var);													// bind parameters for markers
				$stmt_var->execute();																// execute query
				$result_temp = $stmt_var->get_result();
				$row_temp = $result_temp->fetch_assoc();
				$Variant_name = $row_temp['Variant_Eng'];
			}
			$first .= '"'.($m-1).'": ';
			$first .= '{"type":                     "Apps",';
			$first .= '"id":                        "'.$m.'",';
			$first .= '"attributes": {';
			$first .= '"iso":                       "'.$iso.'",';
			$first .= '"rod":				        "'.$rod.'",';
			$first .= '"var_code":		    	    "'.$var.'",';
			$first .= '"var_name":					"'.$Variant_name.'",';
			$first .= '"iso_query_string":	        "sortby=lang&iso='.$iso;
			if ($rod != '00000') {
				$first .= '&rod='.$rod;
			}
			if ($var != '') {
				$first .= '&var='.$var;
			}
			$first .= '",';
			$first .= '"idx":		                '.$idx.',';
			$first .= '"idx_query_string":          "sortby=lang&idx='.$idx.'"';	
			$first .= '},';
			$first .= '"relationships": {';
		}
		if ($n === 0) {
			$first .= '"iOS": {';
		}
		if (strpos($app_download, 'http') !== false) {
			$first .= '"'.$n.'":						"'.$app_download.'",';
		}
		else {
			$first .= '"'.$n.'":						"data/'.$iso.'/study/'.$app_download.'",';
		}
		$n++;
	}
}

$first = rtrim($first, ',');
$first .= '}}}}';

echo $first;
exit;

$marks = [];
$marks = json_decode($first);

header('Content-Type: application/json');														// instead of <pre></pre>
// An associative array
$json_string = json_encode($marks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//echo '<pre>'.$json_string.'</pre>';
echo $json_string;

?>
