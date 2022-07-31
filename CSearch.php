<?php
// Start the session
session_start();
/*
Created by Scott Starker
AJAX from LangSearch.js

MySQL: utf8_general_ci flattens accents as well as lower-casing:
You must ensure that all parties (your app, mysql connection, your table or column) have set utf8 as charset.
- header('Content-Type: text/html; charset=utf-8'); (or <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />)
- ensure your mysqli connection use utf8 before any operation:
	- $mysqli->set_charset('utf8')
- create your table or column using utf8_general_ci
*/

/*
	These are defined at the end of $response:
	countryNotFound = "The country is not found."
	colCountries = "Countries"
*/

// display all of the language names, ROD codes and variant codes from a major and alternate languages names

if (isset($_GET['country'])) $TryCountry = $_GET['country']; else { die('Hack!'); }
// saltillo: ꞌ; U+A78C
if (!preg_match("/[-. ,'ꞌ()A-Za-záéíóúÑñçãõâêîôûäëöüï&]/", $TryCountry)) {
	die('Hack!');
}
if (isset($_GET['st'])) {
	$st = $_GET['st'];
/*	$st = preg_replace('/^([a-z]{3})/', '$1', $st);
	if ($st == NULL) {
	}*/
	if (!preg_match('/^[a-z]{3}/', $st)) {
		die('Hach! 1');
	}
}
else {
	 die('Hack! 2');
}
$st = substr($st, 0, 3);

$response = '';
$MajorLanguage = '';
$Variant_major = '';
foreach($_SESSION['nav_ln_array'] as $code => $array){
	if ($st == $array[0]){
		$MajorLanguage = 'LN_'.$array[1];
		$Variant_major = 'Variant_'.$array[0];
		$SpecificCountry = $array[1];
		break;
	}
}
if ($Variant_major == ''){
	$response = '"st" never found.';
	exit();
}

$hint = 0;

include './include/conn.inc.php';
$db = get_my_db();
include './translate/functions.php';							// translation function

$query="SELECT DISTINCT ISO_Country, $SpecificCountry FROM countries, ISO_countries WHERE countries.ISO_Country = ISO_countries.ISO_countries AND countries.$SpecificCountry LIKE '".$TryCountry."%' ORDER BY $SpecificCountry";														// create a prepared statement
if ($result = $db->query($query)) {
	while ($row = $result->fetch_assoc()) {
		$ISO_Country = $row['ISO_Country'];
		$Country = trim($row[$SpecificCountry]);
		if ($hint == 0) {
			$response = $Country.'|'.$ISO_Country;
			$hint = 1;
		}
		else {
			$response .= '<br />'.$Country.'|'.$ISO_Country;
		}
	}
}
	
if ($hint == 0) {
	$response = translate("This country is not found.", $st, "sys");
}
else {
	$response .= '<br />'.translate("Countries", $st, "sys");
}
echo $response;

?>