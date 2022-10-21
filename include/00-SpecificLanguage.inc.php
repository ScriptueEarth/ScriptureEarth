<?php
	if (!isset($LN)) die('Hacked!');
	if (!isset($ISO_ROD_index)) {
		die('Hacked!');
	}
	if (!preg_match('/^[0-9]+$/', $ISO_ROD_index)) {
		die('Hacked!');
	}
?>

<!-- These css have to be on this page! -->
<style>
body {
	background-color: white;
}
a {
	color: navy;
	text-decoration: none;
	/*margin-left: 10px;*/
}
tr {
	text-align: left;
	margin: 0;
	padding: 0;
}
td {
	text-align: left;
}

li.aboutText {
	display: block;
	margin-top: 20px;
	padding: 0;
}
a.aboutWord {
	color: white;
	text-decoration: none;
}
a.aboutWord:hover {
	color: red;
}
img.iconActions {
	margin-top: 4px;
	margin-bottom: 4px;
	padding: 0;
	vertical-align: middle;
	border-style: none;
	height: 45px;
	width: 45px;
	min-width: 45px;
	margin-right: 6px;
}
div.linePointer {
	cursor: pointer;
	display: inline;
}
div.linePointer:hover{
    border-bottom:2px solid red;
}

/* The following css are from Lærke Roager. */
/* Style the buttons that are used to open the tab content */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
} 
</style>

<?php
// Created by Scott Starker
// Updated by Scott Starker, Lærke Roager

/*************************************************************************************************************************************
*
* 			CAREFUL when your making any additions! Any "onclick", "change", etc. that occurs on "input", "a", "div", etc.
* 			should be placed in "_js/user_events.js". Also, in "_js/user_events.js" any errors in previous statements will
* 			not works in any of the satesments then on. It can also help in the Firefox browser (version 79.0+) run
* 			"00-SpecificLanguage.inc.php", menu "Tools", "Web developement", and "Toggle Tools". Then menu "Debugger". In the left
* 			side of the windows click on "00-SpecificLanguage.inc.php", Localhost", "_js", and "user_events.js". Look down the js file
* 			and find out if there are errors using the "underline" indicator and fix them if there are any. You can also
* 			use "Scripture_[Add|Edit].php" just to make sure that the document.getElementById('...') name is current.
*			But, BE CAREFUL!
*
**************************************************************************************************************************************/

$check_iOS = 0;
if (preg_match('/Macintosh|iPhone|iPod|iPad/', $_SERVER['HTTP_USER_AGENT'])) {
	/* This is iOS */
	$check_iOS = 1;
}

$mobile=0;
$i=0;											// used in 00-DBLanguageCountryName.inc.php include

/*
	*******************************************************************************************************************
		select the default primary language name to be used by displaying the Countries and indigenous langauge names
	*******************************************************************************************************************
*/
	$query = "SELECT DISTINCT scripture_main.*, $SpecificCountry, countries.ISO_Country FROM scripture_main, countries, ISO_countries WHERE countries.ISO_Country = ISO_countries.ISO_countries AND ISO_countries.ISO_ROD_index = scripture_main.ISO_ROD_index AND scripture_main.ISO_ROD_index = '$ISO_ROD_index'";
	$result=$db->query($query) or die (translate('Query failed:', $st, 'sys') . ' ' . $db->error . '</body></html>');
	if (!$result) {
		die ("&ldquo;$ISO&rdquo; " . translate('is not found.', $st, 'sys') . '</div></body></html>');
	}
	$rowSM = $result->fetch_array(MYSQLI_ASSOC);
	// already have $ISO=$rowSM['ISO'];, $ROD_Code=$rowSM['ROD_Code'];, $Variant_Code=$rowSM['Variant_Code'];
	$ISO_Country=$rowSM['ISO_Country'];				// country = ZZ
	$GetName = $ISO_Country;						// country = ZZ
	$SAB=$rowSM['SAB'];								// boolean
	$BibleIs=$rowSM['BibleIs'];						// boolean
	$viewer=$rowSM['viewer'];						// boolean
	$OT_PDF=$rowSM['OT_PDF'];						// boolean
	$NT_PDF=$rowSM['NT_PDF'];						// boolean
	$OT_Audio=$rowSM['OT_Audio'];					// boolean
	$NT_Audio=$rowSM['NT_Audio'];					// boolean
	$PlaylistAudio=$rowSM['PlaylistAudio'];			// boolean
	$PlaylistVideo=$rowSM['PlaylistVideo'];			// boolean
	$watch=$rowSM['watch'];							// boolean
	$YouVersion=$rowSM['YouVersion'];				// boolean
	$buy=$rowSM['buy'];								// boolean
	$Biblesorg=$rowSM['Bibles_org'];				// boolean
	$GRN=$rowSM['GRN'];								// boolean
	$CellPhone=$rowSM['CellPhone'];					// boolean
	$study=$rowSM['study'];							// boolean
	$other_titles=$rowSM['other_titles'];			// boolean
	$links=$rowSM['links'];							// boolean
	$eBible=$rowSM['eBible'];						// boolean
	$SILlink=$rowSM['SILlink'];						// boolean
	$AddTheBibleIn=$rowSM['AddTheBibleIn'];					// boolean
	$AddTheScriptureIn=$rowSM['AddTheScriptureIn'];			// boolean
	$whichBible = '';

/*
	*************************************************************************************************************
		Get the Language name to display.
	*************************************************************************************************************
*/
	?>
	<h1 id='<?php echo $LN; ?>'>
		<?php
		// font-size: 22pt
		if ($AddTheBibleIn) {
			$whichBible = translate('The Bible in', $st, 'sys');
			echo $whichBible . '&nbsp;<br />';
		}
		else if ($AddTheScriptureIn) {
			$whichBible = translate('The Holy Scripture in', $st, 'sys');
			echo $whichBible . '&nbsp;<br />';
		}
		echo $LN;															// Language Name (LN)
			
		if (!is_null($Variant_Code) && $Variant_Code != '') {
			$Variant_Lang = 'Variant_'.ucfirst($st);
			$query = "SELECT $Variant_Lang FROM Variants WHERE Variant_Code = '$Variant_Code'";
			$resultVar=$db->query($query) or die (translate('Query failed:', $st, 'sys') . ' ' . $db->error . '</body></html>');
			if ($resultVar->num_rows > 0) {
				$rowVar = $resultVar->fetch_array();
				$VD = $rowVar["$Variant_Lang"];
				echo " <span style='font-style: italic; font-size: 1em; '>($VD)</span>";
			}
		}
		?>
	</h1>

<script>
	setTitle("<?php echo ($whichBible == '' ? '' : $whichBible . ' ') . $LN . ($ISO == 'qqq' ? '' : '['.$ISO.']'); ?>");		// qqq equals Deaf
</script>

<div style='display: inline; clear: both; '>

<div id='SpecLang' style='margin-left: auto; margin-right: auto; text-align: left; '>
<br />

<?php

/*
	*************************************************************************************************************
		Get the alternate language name, if there is any, to display.
	*************************************************************************************************************
*/
	$query_alt="SELECT alt_lang_name FROM alt_lang_names WHERE ISO_ROD_index = '$ISO_ROD_index'";				// then look to the alt_lang_name table
	$result_alt=$db->query($query_alt);
	if ($result_alt && $result_alt->num_rows > 0) {
		?>
		<br />
		<h2 id='<?php echo $ISO; ?>'>
			<div class='alternativeLanguageNames' style='width: 100%; '><?php echo translate('Alternative Language Names:', $st, 'sys'); ?>
				<span class='alternativeLanguageName'>
				<?php
				$i_alt=0;
				while ($r_alt = $result_alt->fetch_array(MYSQLI_ASSOC)) {
					$alt_lang_name=trim($r_alt['alt_lang_name']);
					$alt_lang_name = htmlspecialchars($alt_lang_name, ENT_QUOTES, 'UTF-8');
					if ($i_alt != 0) {
						echo ', ';
					}
					if ($i_alt >= 2) {
						echo '<span style="font-size: 85%; ">'.$alt_lang_name.'</span>';
					}
					else {
						echo $alt_lang_name;
					}
					$i_alt++;
				}
				?>
				</span>
			</div>
		</h2>
		<?php
	}

/*
	*************************************************************************************************************
		Get the name(s) of the country(ies).
	*************************************************************************************************************
*/
	$query="SELECT $SpecificCountry, ISO_countries FROM ISO_countries, countries WHERE ISO_countries.ISO_ROD_index = '$ISO_ROD_index' AND ISO_countries.ISO_countries = countries.ISO_Country";
	$result_ISO_countries=$db->query($query);
	$r_ISO_countries = $result_ISO_countries->fetch_array(MYSQLI_ASSOC);
	$countryTemp = $SpecificCountry;
	if (strpos("$SpecificCountry", '.')) $countryTemp = substr("$SpecificCountry", strpos("$SpecificCountry", '.')+1);					// In case there's a "." in the "country"
	$country = trim($r_ISO_countries["$countryTemp"]);											// name of the country in the language version
	$ISO_countries = trim($r_ISO_countries["ISO_countries"]);									// 2 upper case letters
	$country = '<a href="'.$Scriptname.'?sortby=country&name=' . $ISO_countries . '">' . $country . '</a>';
	while ($r_ISO_countries = $result_ISO_countries->fetch_array(MYSQLI_ASSOC)) {
		$country = $country.',&nbsp;<a href="'.$Scriptname.'?sortby=country&name=' . trim($r_ISO_countries["ISO_countries"]) . '">'.trim($r_ISO_countries["$countryTemp"]).'</a>';			// name of the country in the language version
	}

/*
	*************************************************************************************************************
		Displays the country and the ISO code.
	*************************************************************************************************************
*/
	?>
	<h2>
		<div style='width: 100%; '>
			<div class='Country' style='margin-bottom: 8px; '><?php echo translate('Country:', $st, 'sys'); ?>&nbsp;<span class='Country'><?php echo $country; ?></span></div>
			<?php if ($ISO != 'qqq') { ?>
			<div class='languageCode'><?php echo translate('Language Code:', $st, 'sys'); ?>&nbsp;<?php echo $ISO; ?></div>
			<?php } ?>
		</div>
	</h2>
	&nbsp;<br />

	<table id='individualLanguage'>
	<?php

	$read_array = [];
	$listen_array = [];
	$view_array = [];
	$app_array = [];
	$other_array = [];
	$all_array = [];
	$string_temp = '';

	$string_temp = "<table class='individualLanguage'>";
	array_push($read_array, $string_temp);
	array_push($listen_array, $string_temp);
	array_push($view_array, $string_temp);
	array_push($app_array, $string_temp);
	array_push($other_array, $string_temp);
	array_push($all_array, $string_temp);

/*
	*************************************************************************************************************
		interested?
	*************************************************************************************************************
*/
	$query="SELECT interest_index FROM interest WHERE ISO_ROD_index = '$ISO_ROD_index' AND NoLang = 1";
	$result2=$db->query($query);
	if ($result2) {
		if ($result2->num_rows == 1) {
			$query="SELECT Goto_ISO_ROD_index, Goto_ISO, Goto_ROD_Code, Goto_Variant_Code, Percentage FROM GotoInterest WHERE ISO_ROD_index = '$ISO_ROD_index'";
			$result2=$db->query($query);
			if ($result2) {
				$GotoInterest = $result2->num_rows;
				if ($GotoInterest > 0) {
					$i_GI=0;
					?>
					<tr>
						<td colspan="2">
							<?php
							echo translate('Speakers of this language may be able to use media in', $st, 'sys');
							while ($row_Goto = $result2->fetch_array(MYSQLI_ASSOC)) {
								$Goto_ISO_ROD_index=trim($row_Goto['Goto_ISO_ROD_index']);
								$Goto_ISO=trim($row_Goto['Goto_ISO']);
								$Goto_ROD_Code=trim($row_Goto['Goto_ROD_Code']);
								if ($Goto_ROD_Code == '') $Goto_ROD_Code='00000';
								$Goto_Variant_Code=trim($row_Goto['Goto_Variant_Code']);
								$Percentage=trim($row_Goto['Percentage']);
								/*
									*********************************************************************************************
										Get the "Goto' language name.
									*********************************************************************************************
								*/
								$query="SELECT * FROM scripture_main WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
								$result3=$db->query($query);
								$row_Goto_ISO = $result3->fetch_array(MYSQLI_ASSOC);
								$ML_Interest=$row_Goto_ISO["$MajorLanguage"];										// boolean
								$def_LN_Interest=$row_Goto_ISO['Def_LN'];											// default langauge (a 2 digit number for the national langauge)
								if (!$ML_Interest) {																// if the country then the major default langauge name
									foreach ($_SESSION['nav_ln_array'] as $code => $nav_ln_temp_array){
										if ($nav_ln_temp_array[3] == $def_LN_Interest){
											$query="SELECT LN_".$nav_ln_temp_array[1]." FROM LN_".$nav_ln_temp_array[1]." WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
											$result_LN=$db->query($query);
											$row_LN=$result_LN->fetch_array(MYSQLI_ASSOC);
											$LN=trim($row_LN['LN_'.$nav_ln_temp_array[1]]);
										}
									}
								}
								else {
									$query="SELECT $MajorLanguage FROM $MajorLanguage WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
									$result_LN=$db->query($query);
									$row_LN = $result_LN->fetch_array(MYSQLI_ASSOC);
									$LN=trim($row_LN["$MajorLanguage"]);
								}

								if ($i_GI > 0) 
									echo ", " . translate('or', $st, 'sys');
								echo " <a href='https://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'] . "?sortby=lang&name=".$Goto_ISO."&ROD_Code=".$Goto_ROD_Code."&Variant_Code=".$Goto_Variant_Code."' style='text-decoration: underline; color: red; '>" . $LN . "</a> (" . $Percentage . ")";
								$i_GI++;
							}
							echo ".";
							?>
						</td>
					</tr>
					<?php
				}
			}
		}
	}
			
/*
	*************************************************************************************************************
		Is it the SAB Scripture App Builder (SAB) HTML?
	*************************************************************************************************************
*/
	$SynchronizedTextAndAudio = 0;
	// The comments are for the implemenation of the chapters. Loren and Bill decided to delete the chapters.
	if ($SAB) {
		/*
			$SAB (bitwise):
				decimal		binary		meaning
				1			000001		NT Text with audio
				2			000010		OT Text with audio
				4			000100		NT Text (with audio where available)
				8			001000		OT Text (with audio where available)
				16			010000		NT View text
				32			100000		OT View text
		*/
	/*
		How about creating a new table called SAB_folder table and a new field called sab_folder. ("tzoCHA") Then grab the data and create new records for SAB_folder using the code below.
		But it won't work for previous records pre scriptoria.
	*/
	/*
		Note: the js sessionStorage is extended to any new tabs and windows when they are opened from the parent window!
	*/
	$SABindex=0;
	$query="SELECT `url`, `subfolder`, `description`, `pre_scriptoria` FROM `SAB_scriptoria` WHERE `ISO_ROD_index` = '$ISO_ROD_index'";			// parent table of SAB table
	$result_sub=$db->query($query);
	$num_sub=$result_sub->num_rows;
	if ($result_sub && $num_sub > 0) {
		while ($row_sub=$result_sub->fetch_array(MYSQLI_ASSOC)) {												// SAB_scriptoria table
			$SABurl=trim($row_sub['url']);
			$subfolder=trim($row_sub['subfolder']);
			$description=trim($row_sub['description']);
			$preScriptoria=trim($row_sub['pre_scriptoria']);
			if ($SABurl != '') {
				echo '<tr>';
					echo '<td>';
						echo "<div class='linePointer' onclick='SAB_Scriptoria_Other(\"$SABurl\")'><img class='iconActions' ";
						echo "src='../images/SAB-readListen-icon.png' alt='".translate('Read/Listen/View', $st, 'sys')."' title='".translate('Read/Listen/View', $st, 'sys')."' /></div>";
					echo '</td>';
					echo '<td>';
						echo "<div class='linePointer' title='".translate('Read/Listen/View', $st, 'sys')."' onclick='SAB_Scriptoria_Other(\"$SABurl\")'>" . translate('Read/Listen/View', $st, 'sys');
						if ($description != '') {
							echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $description;
						}
						echo '</div>';
					echo '</td>';
				echo '</tr>';
			}
			elseif ($preScriptoria != '') {																		// field set with preScriptoria
				$SAB_Path = './data/'.$ISO.'/sab/';
				if (file_exists("./data/$ISO/sab/js/".(strlen($preScriptoria) == 3 ? '' : $preScriptoria.'-') . 'book-names.js')) {			// not on the PHP server but my office/home oomputer
					$SAB_Read = file("./data/$ISO/sab/js/".(strlen($preScriptoria) == 3 ? '' : $preScriptoria.'-') . 'book-names.js');		// read the '[ISO][SABnum]-book-names.js' file
					foreach ($SAB_Read as $line_num => $line) {													// read through the lines
						$l = '';
						$l = preg_replace('/.*-([0-9]{2}-[A-Z0-9][A-Z]{2})-.*/', "$1", $line);					// set $l to e.g. '01-GEN'
						if ($l == $line) continue;																// if not $ln = $line
						$ln = (int)substr($l, 0, 2);															// get the number of the book"
						if ($ln > 0 && $ln <= 39) {							// OT books							// if the book from the OT
							$SAB_OT_lists[] = $ln;
						}
						elseif ($ln >= 41 && $ln <= 66) {					// NT books							// if the book from the NT
							$SAB_NT_lists[] = $ln;
						}
						else {
							// this line perminently left empty
						}
					}
				}
				else {
					continue;
				}

				$SAB_OT_lists = [];
				$SAB_NT_lists = [];
				/*
					 OT Scripture App Builder (SAB) HTML
				*/
				$query="SELECT `ISO_ROD_index` FROM `SAB` WHERE `ISO_ROD_index` = '$ISO_ROD_index' AND `SAB_Book` <= 39 LIMIT 1";	// test to see if OT is there
				$result2=$db->query($query);
				$num=$result2->num_rows;
				if ($result2 && $num > 0) {
					$OT_SAB_Book = [];
					$OT_SAB_Book_Chapter = [];
					$OT_SAB_a_index = 0;
					?>
					<tr>
						<td>
							<?php
							echo "<div style='display: inline; ' id='OT_SABRL_a'><img class='iconActions' ";
							if ($SAB & 2) {
								echo "src='../images/SAB-readListen-icon.png' alt='".translate('Text with audio', $st, 'sys')."' title='".translate('Text with audio', $st, 'sys')."'";
								$SynchronizedTextAndAudio = 1;
							}
							else if ($SAB & 8) {
								echo "src='../images/SAB-readListen-icon.png' alt='".translate('Text (with audio where available)', $st, 'sys')."' title='".translate('Text (with audio where available)', $st, 'sys')."'";
							}
							else {				// $SAB & 32
								echo "src='../images/SAB-text-icon.jpg' alt='".translate('View text', $st, 'sys')."' title='".translate('View text', $st, 'sys')."'";
							}
							echo "/></div>";
						echo "</td>";
						echo "<td>";
							?>
							<div class='SABReadListen'>
								<?php
								echo "<div style='display: inline; ' id='OT_SABRL_b'>";
								if ($SAB & 2) {
									echo translate('Text with audio', $st, 'sys') . "</div>:";
								}
								else if ($SAB & 8) {
									echo translate('Text (with audio where available)', $st, 'sys') . "</div>:";
								}
								else {		// $SAB & 32
									echo translate('View text', $st, 'sys') . "</div>:";
								}
								echo " <div style='display: inline; ' id='OTSABSelects' style='display: inline; '>";
								// Get and display Books
								$query_array="SELECT * FROM SAB WHERE ISO_ROD_index = '$ISO_ROD_index' AND SAB_Book = ? AND (Book_Chapter_HTML IS NOT null AND trim(Book_Chapter_HTML) <> '') ORDER BY Book_Chapter_HTML ASC";
								$stmt = $db->prepare($query_array);										// create a prepared statement
								echo "<form name='form_OT_SAB_Books' id='form_OT_SAB_Books' style='display: inline; '>";
								echo "<select name='OT_SAB_Book' id='OT_SAB_Book' class='selectOption'>";
								echo "<option>".translate('Choose One...', $st, 'sys')."</option>";
								foreach ($OT_array[OT_EngBook] as $a) {									// display the OT books in the English language. i.e. $a = 'Genesis', etc.
									if (!empty($SAB_OT_lists)) {										// not on the PHP server but my office/home oomputer OR if $temp_Book_Chapter_HTML == ''
										$t = 1;
										foreach ($SAB_OT_lists as $SAB_OT_list) {						// go through the 'book-names.js' array from above
											if ((int)$OT_array[0][$t] == ($SAB_OT_list + 0)) {			// see if the number of the book 'book-names.js' array matches the number of the $OT_array[0] book number
												break;
											}
											$t++;
										}
										if ($t > count($OT_array[0])) continue;							// if the match is not found then continue
									}
									$temp = ($OT_SAB_a_index)+1;
									$stmt->bind_param("i", $temp);										// bind parameters for markers
									$stmt->execute();													// execute query
									$result_array = $stmt->get_result();								// instead of bind_result (used for only 1 record):
									$num_array=$result_array->num_rows;
									if ($result_array && $num_array > 0) {
										$OT_SAB_Book[] = $OT_SAB_a_index;
										$r_array = $result_array->fetch_array(MYSQLI_ASSOC);			// now you can fetch the results into an array for 'for' - NICE (as oppossed to bind_result)
										$OT_Book_Chapter_HTML = trim($r_array['Book_Chapter_HTML']);	// 1st chapter
										$SAB_Audio = $r_array['SAB_Audio'];								// is there audio in the 1st chapter?
										echo "<option id='OT_SAB_Book_${OT_SAB_a_index}' name='OT_SAB_Book_${OT_SAB_a_index}' class='speaker' value='${OT_Book_Chapter_HTML}'>".($SAB_Audio ? '&#128266; ' : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').$a."</option>";
									}
									$OT_SAB_a_index++;
								}
								echo "</select>";
								echo "</form>";
								$stmt->close();															// close statement
								if ($description != '') {
									echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $description;
								}
								echo "</div>";
								?>
							</div>
						</td>
					 </tr>
					<?php
				}
				/*
					 NT Scripture App Builder (SAB) HTML
				*/
				$query="SELECT `ISO_ROD_index` FROM `SAB` WHERE `ISO_ROD_index` = '$ISO_ROD_index' AND `SAB_Book` >= 41 LIMIT 1";	// test to see if NT is there
				$result2=$db->query($query);
				$num=$result2->num_rows;
				if ($result2 && $num > 0) {
					$NT_SAB_Book = [];
					$NT_SAB_Book_Chapter = [];
					$NT_SAB_a_index = 0;
					?>
					<tr>
						<td>
							<?php
							echo "<div style='display: inline; ' id='NT_SABRL_a'><img class='iconActions' ";
							if ($SAB & 1) {
								echo "src='../images/SAB-readListen-icon.png' alt='".translate('Text with audio', $st, 'sys')."' title='".translate('Text with audio', $st, 'sys')."'";
								$SynchronizedTextAndAudio = 1;
							}
							else if ($SAB & 4) {
								echo "src='../images/SAB-readListen-icon.png' alt='".translate('Text (with audio where available)', $st, 'sys')."' title='".translate('Text (with audio where available)', $st, 'sys')."'";
							}
							else {		// $SAB & 16
								echo "src='../images/SAB-text-icon.jpg' alt='".translate('View text', $st, 'sys')."' title='".translate('View text', $st, 'sys')."'";
							}
							echo "/></div>";
						echo "</td>";
						echo "<td>";
							?>
							<div class='SABReadListen'>
								<?php
								echo "<div style='display: inline; ' id='NT_SABRL_b'>";
								if ($SAB & 1) {
									echo translate('Text with audio', $st, 'sys') . "</div>";
								}
								else if ($SAB & 4) {
									echo translate('Text (with audio where available)', $st, 'sys') . "</div>";
								}
								else {		// $SAB & 16
									echo translate('View text', $st, 'sys') . "</div>";
								}
								echo " <div style='display: inline; ' id='NTSABSelects' style='display: inline; '>";
								$query_array="SELECT * FROM SAB WHERE ISO_ROD_index = '$ISO_ROD_index' AND SAB_Book = ? AND (Book_Chapter_HTML IS NOT null AND trim(Book_Chapter_HTML) <> '') ORDER BY Book_Chapter_HTML ASC";
								$stmt = $db->prepare($query_array);										// create a prepared statement
								echo "<form name='form_NT_SAB_Books' id='form_NT_SAB_Books' style='display: inline; '>";
								echo "<select name='NT_SAB_Book' id='NT_SAB_Book' class='selectOption'>";
								echo "<option>".translate('Choose One...', $st, 'sys')."</option>";
								foreach ($NT_array[NT_EngBook] as $a) {									// display the NT books in the MAJOR language!
									if (!empty($SAB_NT_lists)) {										// not on the PHP server but my office/home oomputer OR if $temp_Book_Chapter_HTML == ''
										$t = 1;
										foreach ($SAB_NT_lists as $SAB_NT_list) {						// go through the 'book-names.js' array from above
											if ((int)$NT_array[0][$t] == ($SAB_NT_list - 40)) {			// see if the number of the book 'book-names.js' array matches the number of the $OT_array[0] book number
												break;
											}
											$t++;
										}
										if ($t > count($NT_array[0])) continue;							// if the match is not found then continue
									}
									$temp = ($NT_SAB_a_index)+41;
									$stmt->bind_param("i", $temp);										// bind parameters for markers
									$stmt->execute();													// execute query
									$result_array = $stmt->get_result();								// instead of bind_result (used for only 1 record):
									$num_array=$result_array->num_rows;
									if ($result_array && $num_array > 0) {
										$NT_SAB_Book[] = $NT_SAB_a_index;
										$r_array = $result_array->fetch_array(MYSQLI_ASSOC);			// now you can fetch the results into an array for 'for' - NICE (as oppossed to bind_result)
										$NT_Book_Chapter_HTML = trim($r_array['Book_Chapter_HTML']);	// 1st chapter
										$SAB_Audio = $r_array['SAB_Audio'];								// is there audio in the 1st chapter?
										echo "<option id='NT_SAB_Book_".$NT_SAB_a_index."' name='NT_SAB_Book_".$NT_SAB_a_index."' class='speaker' value='".$NT_Book_Chapter_HTML."'>".($SAB_Audio ? '&#128266; ' : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').$a."</option>";
									}
									$NT_SAB_a_index++;
								}
								echo "</select>";
								echo "</form>";
								$stmt->close();															// close statement
								if ($description != '') {
                                    	echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $description;
									}
								echo "</div>";
                                ?>
							</div>
						</td>
					</tr>
					<?php
				}
			}
			else {
				$SABindex++;
				echo '<tr>';
					echo '<td>';
						echo "<div class='linePointer' onclick='SAB_Scriptoria_Index(\"$subfolder\")'><img class='iconActions' ";
						echo "src='../images/SAB-readListen-icon.png' alt='".translate('Read/Listen/View', $st, 'sys')."' title='".translate('Read/Listen/View', $st, 'sys')."'/></div>";
					echo '</td>';
					echo '<td>';
						echo "<div class='linePointer' onclick='SAB_Scriptoria_Index(\"$subfolder\")'>" . translate('Read/Listen/View', $st, 'sys') . " ";
						if ($description != '') {
							echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $description;
						}
						echo '</div>';
					echo '</td>';
				echo '</tr>';
			}
		}
	}
}

/*
	*************************************************************************************************************
		Is it Bible.is? (if "Text with audio" does not exists here and if exists then below)
	*************************************************************************************************************
*/
	if ($Internet && $BibleIs && !$SAB) {
		$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND NOT BibleIs = 0";
		$result2=$db->query($query);
		$num=$result2->num_rows;
		if ($result2 && $num > 0) {
			while ($r_links=$result2->fetch_array(MYSQLI_ASSOC)) {
				$URL=trim($r_links['URL']);
				if (preg_match('/^(.*\/)[a-zA-Z0-9][a-zA-Z]{2}\/[0-9]+$/', $URL, $matches)) {		// remove e.g. Mat/1
					$URL = $matches[1];
				}
				$BibleIsVersion=trim($r_links['company_title']);
				$BibleIsLink=$r_links['BibleIs'];
				$BibleIsIcon = '';
				$BibleIsActText = '';
				switch ($BibleIsLink) {
					case 1:
						$BibleIsIcon = 'BibleIs-icon.jpg';
						$BibleIsActText = translate('Read and Listen', $st, 'sys');
						break;
					case 2:
						$BibleIsIcon = 'BibleIs-icon.jpg';
						$BibleIsActText = translate('Read', $st, 'sys');
						break;			
					case 3:
						$BibleIsIcon = 'BibleIsAudio.jpg';
						$BibleIsActText = translate('Read and Listen', $st, 'sys');
						break;			
					case 4:
						$BibleIsIcon = 'BibleIsVideo.jpg';
						$BibleIsActText = translate('Read, Listen, and View', $st, 'sys');
						break;			
					default:
						break;
				}
				?>
				<tr>
					<td>
						<?php
						echo "<div class='linePointer' onclick='LinkedCounter(\"BibleIs_".$counterName."_".$GetName."_".$ISO."\", \"".$URL."\")'><img class='iconActions' src='../images/".$BibleIsIcon."' alt='".$BibleIsActText."' title='".$BibleIsActText."' /></div>";
					echo "</td>";
					echo "<td>";
						echo "<div class='linePointer' onclick='LinkedCounter(\"BibleIs_".$counterName."_".$GetName."_".$ISO."\", \"".$URL."\")'>" . $BibleIsActText . " ";
						//if (stripos($URL, '/Gen/') !== false)
						/*if ($BibleIsLink == 1)
							echo translate('to the New Testament', $st, 'sys');
						else if ($BibleIsLink == 2)
							echo translate('to the Old Testament', $st, 'sys');
						else	// $BibleIs == 3
							echo translate('to the Bible', $st, 'sys');*/
						echo translate('on Bible.is', $st, 'sys');
						if ($BibleIsVersion!='') {
							echo ' ' . $BibleIsVersion;
						}
						echo "</div>";
						?>
					</td>
				</tr>
				<?php
			}
		}
	}

/*
	*************************************************************************************************************
		Can it be viewed? (if not (Bible.is && YouVersion && Bibles.org && "Text with audio") then do not display "viewer")
	*************************************************************************************************************
*/
	if ($viewer && !($YouVersion && $Biblesorg && $SAB && $BibleIs) && $Internet) {
		$ROD_Var='';
		$rtl = 0;
		$query="SELECT viewer_ROD_Variant, rtl FROM viewer WHERE ISO_ROD_index = '$ISO_ROD_index' AND Variant_Code = '$Variant_Code'";						// check if there is a viewer
		$resultViewer=$db->query($query);
		if ($resultViewer && $resultViewer->num_rows >= 1) {
			$r_Viewer = $resultViewer->fetch_array(MYSQLI_ASSOC);
			$ROD_Var=trim($r_Viewer['viewer_ROD_Variant']);
			$rtl=trim($r_Viewer['rtl']);
		}
		?>
		<tr>
			<td style='width: 45px; '>
				<?php
				echo "<div class='linePointer' onclick=\"window.open('./viewer/views.php?iso=$ISO&ROD_Code=$ROD_Code&Variant_Code=$Variant_Code&ROD_Var=$ROD_Var&rtl=$rtl&st=$st')\"><img class='iconActions' src='../images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' /></div>";
			echo "</td>";
			echo "<td>";
				echo "<div class='linePointer' onclick=\"window.open('./viewer/views.php?iso=$ISO&ROD_Code=$ROD_Code&Variant_Code=$Variant_Code&ROD_Var=$ROD_Var&rtl=$rtl&st=$st')\" title='".translate('Viewer for the Language Name', $st, 'sys')."'>".translate('Go to', $st, 'sys')." ".translate('the online viewer', $st, 'sys')."</div>";
				?>
			</td>
		</tr>
		<?php
	}

/*
	*************************************************************************************************************
		Is it PDF?
	*************************************************************************************************************
*/
	$SB_PDF=0;														// boolean
	$query_SB="SELECT Item, Scripture_Bible_Filename FROM Scripture_and_or_Bible WHERE ISO_ROD_index = '$ISO_ROD_index'";		// then look to the Scripture_and_or_Bible table
	$result_SB=$db->query($query_SB);
	if (!$result_SB)
		$SB_PDF = 0;
	else
		$SB_PDF=$result_SB->num_rows;
	if ($NT_PDF > 0 || $OT_PDF > 0 || $SB_PDF > 0) {				// if it is 1 then
		if ($SB_PDF > 0) {
			while ($r_SB = $result_SB->fetch_array(MYSQLI_ASSOC)) {
				?>
				<tr>
					<td>
						<?php
						$Item = $r_SB['Item'];
						if ($Item == 'B') {
							$whole_Bible=trim($r_SB['Scripture_Bible_Filename']);
							echo "<div class='linePointer' onclick=\"window.open('./data/$ISO/PDF/$whole_Bible')\"><img class='iconActions' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')." (PDF)"."' title='".translate('Read', $st, 'sys')."' /></div>";
							echo "</td>";
							echo "<td>";
							echo "<div class='linePointer' onclick=\"window.open('./data/$ISO/PDF/$whole_Bible')\" title='".translate('Read the Bible.', $st, 'sys')."'>".translate('Read', $st, 'sys')." ".translate('the Bible', $st, 'sys')." (PDF)</div>";
						}
						else {
							$complete_Scripture=trim($r_SB['Scripture_Bible_Filename']);
							echo "<div class='linePointer' onclick=\"window.open('./data/$ISO/PDF/$complete_Scripture')\"><img class='iconActions' src='../images/read-icon.jpg' alt='".translate('Read', $st, 'sys')." (PDF)"."' title='".translate('Read', $st, 'sys')."' /></div>";
							echo "</td>";
							echo "<td>";
							echo "<div class='linePointer' onclick=\"window.open('./data/$ISO/PDF/$complete_Scripture')\" title='".translate('Read a Scripture portion.', $st, 'sys')."' target='_blank'>".translate('Read', $st, 'sys')." ".translate('a Scripture portion', $st, 'sys')." (PDF)</div>";
						}
						?>
					</td>
				</tr>
				<?php
			}
		}
		if ($OT_PDF) {
			$query="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = 'OT'";			// check if there is a OT
			$result1=$db->query($query);
			$num=$result1->num_rows;
			if ($result1 && $num > 0) {
				if ($r1 = $result1->fetch_array(MYSQLI_ASSOC)) {
					$OT_PDF_Filename = trim($r1['OT_PDF_Filename']);												// there is a OT
					?>
					<tr>
						<td>
							<?php
							echo "<div class='linePointer' onclick=\"window.open('./data/$ISO/PDF/$OT_PDF_Filename')\"><img  class='iconActions' src='../images/read-icon.jpg' alt='".translate('Read', $st, 'sys')." (PDF)"."' title='".translate('Read', $st, 'sys')."' /></div>";
						echo "</td>";
						echo "<td>";
							echo "<div class='linePointer' onclick=\"window.open('./data/$ISO/PDF/$OT_PDF_Filename')\" title='".translate('Read the Old Testament.', $st, 'sys')."' target='_blank'>".translate('Read', $st, 'sys')." ".translate('the Old Testament', $st, 'sys')." (PDF)</div>";
							?>
						</td>
					</tr>
					<?php
				}
			}
			if ($num <= 0) {
				$query="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF != 'OT'";		// check if there is any other book but the OT
				$result1=$db->query($query);
				$num=$result1->num_rows;
				if ($result1 && $num > 0) {
					if ($r1 = $result1->fetch_array(MYSQLI_ASSOC)) {
						$a_index = 0;
						?>
						<tr>
							<td>
								<?php
								echo "<img class='iconActions' src='../images/read-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' />";
							echo "</td>";
							echo "<td>";
								echo "<form name='PDF_OT' id='PDF_OT'>";
								echo translate('Read', $st, 'sys')." ".translate('a book from the Old Testament:', $st, 'sys');
								if (isset($mobile) && $mobile == 1) {
									echo "<br />";
								}
								else {
									echo " ";
								}
								echo "<select class='selectOption' name='OT_PDF' onchange='if (this.options[this.selectedIndex].text != \"".translate('Choose One...', $st, 'sys')."\") { window.open(this.options[this.selectedIndex].value, \"_blank\"); }'>";
								echo "<option>".translate('Choose One...', $st, 'sys')."</option>";
								$query_array="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = ?";
								$stmt = $db->prepare($query_array);													// create a prepared statement
								foreach ($OT_array[OT_EngBook] as $a) {												// there is/are book(s)
									$stmt->bind_param("i", $a_index);												// bind parameters for markers
									$stmt->execute();																// execute query
									$result_array = $stmt->get_result();											// instead of bind_result (used for only 1 record):
									if ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {
										$OT_PDF = $r_array['OT_PDF'];
										while ($OT_PDF !== null && !is_numeric($OT_PDF)) {							// important: not 'int' but 'numeric'! Any number and except not 'OT' ('OT' which is 0 in bind_param("i", $a_index)), etc.
											$r_array = $result_array->fetch_array(MYSQLI_ASSOC);
											$OT_PDF = $r_array['OT_PDF'];
										}
										$OT_PDF_Filename = trim($r_array['OT_PDF_Filename']);
										//$a = str_replace(" ", "&nbsp;", $a);
										if (!empty($OT_PDF_Filename)) {
											echo "<option id='OT_PDF_Media_$a' value='./data/$ISO/PDF/$OT_PDF_Filename'>$a</option>";
										}
									}
									$a_index++;
								}
								$stmt->close();																		// close statement
								$query_array="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = '100'";	// appendice
								$result_array=$db->query($query_array);
								if ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {
									$OT_PDF_Filename = trim($r_array['OT_PDF_Filename']);
									if (!empty($OT_PDF_Filename)) {
										echo "<option value='./data/$ISO/PDF/$OT_PDF_Filename'>".translate('Appendix', $st, 'sys')."</option>";
									}
								}
								$query_array="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = '101'";	// glossary
								$result_array=$db->query($query_array);
								if ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {
									$OT_PDF_Filename = trim($r_array['OT_PDF_Filename']);
									if (!empty($OT_PDF_Filename)) {
										echo "<option value='./data/$ISO/PDF/$OT_PDF_Filename'>".translate('Glossary', $st, 'sys')."</option>";
									}
								}
								echo "</select>";
								echo "</form>";
								?>
							</td>
						</tr>
						<?php
					}
				}
			}
		}
		if ($NT_PDF > 0) {
			$query="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF = 'NT'";		// check if there is a NT
			$result1=$db->query($query);
			$num=$result1->num_rows;
			if ($result1 && $num > 0) {
				if ($r_NT = $result1->fetch_array(MYSQLI_ASSOC)) {
					$NT_PDF_Filename = trim($r_NT['NT_PDF_Filename']);											// there is a NT
					?>
					<tr>
						<td>
							<?php
							echo "<div class='linePointer' onclick=\"window.open('./data/$ISO/PDF/$NT_PDF_Filename')\"><img class='iconActions' src='../images/read-icon.jpg' alt='".translate('Read', $st, 'sys')." (PDF)"."' title='".translate('Read', $st, 'sys')."' /></div>";
						echo "</td>";
						echo "<td>";
							echo "<div class='linePointer' onclick=\"window.open('./data/$ISO/PDF/$NT_PDF_Filename')\" title='".translate('Read the New Testament.', $st, 'sys')."'>".translate('Read', $st, 'sys')." ".translate('the New Testament', $st, 'sys')." (PDF)</div>";
							?>
						</td>
					</tr>
					<?php
				}
			}
			if ($num <= 0) {
				$query="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF != 'NT'";		// check if there is any other book but the NT
				$result1=$db->query($query);
				$num=$result1->num_rows;
				if ($result1 && $num > 0) {
					if ($r_NT_PDF = $result1->fetch_array(MYSQLI_ASSOC)) {
						$a_index = 0;
						?>
						<tr>
							<td>
								<?php
								echo "<img class='iconActions' src='../images/read-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' />";
							echo "</td>";
							echo "<td>";
								echo "<form name='PDF_NT'>";
								echo translate('Read', $st, 'sys')." ".translate('a book from the New Testament:', $st, 'sys');
								if (isset($mobile) && $mobile == 1) {
									echo '<br />';
								}
								else {
									echo ' ';
								}
								echo "<select class='selectOption' name='NT_PDF' onchange='if (this.options[this.selectedIndex].text != \"".translate('Choose One...', $st, 'sys')."\") { window.open(this.options[this.selectedIndex].value, \"_blank\"); }'>";
								echo "<option>".translate('Choose One...', $st, 'sys')."</option>";
								$query_array="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF = ?";
								$stmt = $db->prepare($query_array);										// create a prepared statement
								foreach ($NT_array[NT_EngBook] as $a) {									// there is/are book(s) (from NT_Books.php)
									$stmt->bind_param("i", $a_index);									// bind parameters for markers								// 
									$stmt->execute();													// execute query
									$result_array = $stmt->get_result();								// instead of bind_result (used for only 1 record):
									if ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {
										$NT_PDF = $r_array['NT_PDF'];
										while ($NT_PDF !== null && !is_numeric($NT_PDF)) {				// important: not 'int' but 'numeric'! Any number and except not 'NT' ('NT' which is 0 in bind_param("i", $a_index)), etc.
											$r_array = $result_array->fetch_array(MYSQLI_ASSOC);
											$NT_PDF = $r_array['NT_PDF'];
										}
										$NT_PDF_Filename = trim($r_array['NT_PDF_Filename']);
										$a = str_replace(" ", "&nbsp;", $a);
										if (!empty($NT_PDF_Filename)) {
											echo "<option value='../data/$ISO/PDF/$NT_PDF_Filename'>$a</option>";
										}
									}
									$a_index++;
								}
								$stmt->close();															// close statement
								$query_array="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF = '200'";	// appendice
								$result_array=$db->query($query_array);
								if ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {
									$NT_PDF_Filename = trim($r_array['NT_PDF_Filename']);
									if (!empty($NT_PDF_Filename)) {
										echo "<option value='../data/$ISO/PDF/$NT_PDF_Filename'>".translate('Appendix', $st, 'sys')."</option>";
									}
								}
								$query_array="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF = '201'";	// glossary
								$result_array=$db->query($query_array);
								if ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {
									$NT_PDF_Filename = trim($r_array['NT_PDF_Filename']);
									if (!empty($NT_PDF_Filename)) {
										echo "<option value='../data/$ISO/PDF/$NT_PDF_Filename'>".translate('Glossary', $st, 'sys')."</option>";
									}
								}
								echo "</select>";
								echo "</form>";
								?>
							</td>
						</tr>
						<?php
					}
				}
			}
		}
	}

/*
	*************************************************************************************************************
		Is it GooglePlay? (table links)
	*************************************************************************************************************
*/
	if ($links) {
		$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND GooglePlay = 1";
		$result2=$db->query($query);
		if ($result2) {
			while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
				$company_title=trim($r2['company_title']);
				$company=trim($r2['company']);
				$URL=trim($r2['URL']);
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						echo "<div class='linePointer' onclick=\"window.open('$URL')\" title='".translate('Link to organization.', $st, 'sys')."'><img class='iconActions' src='../images/Google_Play-icon.jpg' alt='".translate('Google Play', $st, 'sys')."' title='".translate('Google Play', $st, 'sys')."' /></div>";
					echo "</td>";
					echo "<td>";
						echo "<div class='linePointer' onclick=\"window.open('$URL')\" title='".translate('Link to organization.', $st, 'sys')."'>".translate('Link', $st, 'sys')." : ";
						if ($company_title != "" && $company_title != NULL) {
							echo "$company_title: ";
						}
						echo "$company</div>";
						?>
					</td>
				</tr>
				<?php
			}
		}
	}

/*
	*************************************************************************************************************
		Is it a cell phone module = Android App (apk) and iOS Asset Package?
	*************************************************************************************************************
*/
	/*
		Android App (apk) and iOS Asset Package
	*/
if ($CellPhone) {
	$query="SELECT * FROM CellPhone WHERE ISO_ROD_index = '$ISO_ROD_index' AND (Cell_Phone_Title = 'Android App' OR Cell_Phone_Title = 'iOS Asset Package') ORDER BY Cell_Phone_Title";
	$result2=$db->query($query);
	if ($result2) {
		$c = 0;
		while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
			$Cell_Phone_Title=$r2['Cell_Phone_Title'];
			if (!$check_iOS && $Cell_Phone_Title=='iOS Asset Package') {
				/*
do this later when IOS packages become live (9.10.2022)
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						echo "<img class='iconActions' src='../images/iOS_App.jpg' alt='".translate('Cell Phone', $st, 'sys')."' title='".translate('Cell Phone', $st, 'sys')."' />";
					echo "</td>";
					echo "<td>";
						echo translate('The ScriptureEarth App is available in the Apple Store.', $st, 'sys');
						?>
					</td>
				</tr>
				<?php
				*/
			}
			else {
				$Cell_Phone_File=trim($r2['Cell_Phone_File']);
				$Cell_Phone_File = str_replace("&", "%26", $Cell_Phone_File);
				$optional=trim($r2['optional']);
				$pos = strpos($Cell_Phone_File, '://');															// check to see if "://" is present (https;//zzzzz)
				$c++;
				if ($pos === false) {
					if (!file_exists('./data/' . $ISO . '/study/' . $Cell_Phone_File)) {
						$matches = [];
						preg_match('/(.*-)[0-9.]+\.apk/', $Cell_Phone_File, $matches);							// SE (keep track of everything but the number)
						$list = [];
						$list = glob('./data/' . $ISO . '/study/' . $matches[1] . '*.apk');						// server (glob = find a file based on wildcards)
						if (empty($list)) {
							echo 'WARNING: Android App (apk) downloadable cell phone file is not there!<br />';
						}
						else {
							$matches = [];
							preg_match('/.*\/(.*\.apk)/', $list[0], $matches);									// server
							if (empty($matches)) {
								echo 'WARNING: Android App (apk) downloadable cell phone file is not there!<br />';
							}
							else {
								$Cell_Phone_File = $matches[1];
								if (file_exists('./data/' . $ISO . '/study/' . $Cell_Phone_File)) {
									//$db->query("UPDATE CellPhone SET Cell_Phone_File = '$Cell_Phone_File' WHERE ISO_ROD_index = '$ISO_ROD_index' AND Cell_Phone_Title = 'Android App'");
									echo $c . ') would have UPDATE CellPhone<br />';
								}
								else {
									echo 'WARNING: Android App (apk) downloadable cell phone file is not there!<br />';
								}
							}
						}
					}
					else {
						// file exists so don't do anything right now
					}
				}
				if ($Cell_Phone_Title == 'Android App') {
					?>
					<tr>
						<td style='width: 45px; '>
							<?php
							echo "<div class='linePointer' title='" . translate('Download the app for', $st, 'sys') . " $Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><img class='iconActions' src='../images/android_module-icon.jpg' alt='".translate('Cell Phone', $st, 'sys')."' title='".translate('Cell Phone', $st, 'sys')."' />";
						echo "</td>";
						echo "<td>";
							echo "<div class='linePointer' title='" . translate('Download the app for', $st, 'sys') . " $Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'>" . translate('Download', $st, 'sys') . " " . translate('the app for', $st, 'sys') . ' ' . ($Cell_Phone_Title == 'Android App' ? 'Android' : $Cell_Phone_Title);
							echo ' ' . $optional . '</div>';
							?>
						</td>
					</tr>
					<?php
				}
				/*
do this later when IOS packages become live (9.10.2022)
				else {
					?>
					<tr>
						<td style='width: 45px; '>
							<?php
							$query = "SELECT Cell_Phone_File FROM CellPhone WHERE ISO_ROD_index = $ISO_ROD_index AND Cell_Phone_Title = 'iOS Asset Package'";
							$result_iOS = $db->query($query);
							$row_iOS = $result_iOS->fetch_array(MYSQLI_ASSOC);
							$Cell_Phone_File = $row_iOS['Cell_Phone_File'];
							echo "<div class='linePointer' onclick='iOSAssetPackage(\"".$Cell_Phone_File."\")'><img class='iconActions' src='../images/iOS_App.jpg' alt='".translate('Cell Phone', $st, 'sys')."' title='".translate('Cell Phone', $st, 'sys')."' /><div>";
						echo "</td>";
						echo "<td>";
							echo "<div class='linePointer' title='" . translate('Download the Scripture Earth app for iOS', $st, 'sys') . "' onclick='iOSAssetPackage(\"".$Cell_Phone_File."\")'>" . translate('Download', $st, 'sys') . " " . translate('the Scripture Earth app for iOS', $st, 'sys');
						echo ' ' . $optional . '</div>';
							?>
						</td>
					</tr>
					<?php
				}
				*/
			}
		}
	}
}

/*
	*************************************************************************************************************
		Is it audio playable?
	*************************************************************************************************************
*/
	if (!$SynchronizedTextAndAudio || !$BibleIs) {
		if ($NT_Audio > 0 || $OT_Audio > 0) {							// if the boolean is 1
			if (!$Internet) {											// $Internet = 0
				// echo "Not on the Internet";
			}
			else {
				/*
					*************************************************************************************************************
						$Internet = 1 and is audio
					*************************************************************************************************************
				*/
				if ($OT_Audio) {
					$query="SELECT * FROM OT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code'";	// ISO_ROD_index = '$ISO_ROD_index'";
					$result2=$db->query($query);
					if ($result2) {
						$OTNT = $NT_Audio + $OT_Audio;
						echo '<tr>';
						echo '<td>';
							$OT_Book = array();
							$OT_Book_Chapter = array();
							$a_index = 0;
							echo "<div class='linePointer' title='".translate('Listen to the Old Testament.', $st, 'sys')."' onclick='ListenAudio(document.form_OT_Chapters_mp3.OT_Chapters_mp3, true, \"OTListenNow\", $OTNT)'><img  class='iconActions' src='../images/listen-icon.jpg' alt='".translate('Listen', $st, 'sys')."' title='".translate('Listen', $st, 'sys')."' /></div>";
						echo '</td>';
						echo '<td>';
						?>
						<div class='OTAudio'>
						<?php
						echo "<div class='linePointer' title='".translate('Listen to the Old Testament.', $st, 'sys')."' onclick='ListenAudio(document.form_OT_Chapters_mp3.OT_Chapters_mp3, true, \"OTListenNow\", $OTNT)'>".translate('Listen', $st, 'sys')." ".translate('to the audio Old Testament:', $st, 'sys').'</div>';
						?>
						<div id='OTAudioSelects' style='display: inline; '>
						<?php
						if (isset($mobile) && $mobile == 1) {
							echo "<br />";
						}
						else {
							echo " ";
						}
						// Get and display Books
						$query_array="SELECT * FROM OT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code' AND Variant_Code = '$Variant_Code' AND OT_Audio_Book = ? AND (OT_Audio_Filename IS NOT NULL AND trim(OT_Audio_Filename) <> '')";		// ISO_ROD_index = '$ISO_ROD_index'
						$stmt_OT = $db->prepare($query_array);											// create a prepared statement
						echo "<select id='OT_Book_mp3' name='OT_Book_mp3' class='selectOption' onchange='AudioChangeChapters(\"OT\", \"$ISO\", \"$ROD_Code\", this.options[this.selectedIndex].value); ListenAudio(document.form_OT_Chapters_mp3.OT_Chapters_mp3, true, \"OTListenNow\", $OTNT)'>";
						foreach ($OT_array[OT_EngBook] as $a) {											// display the OT books in the MAJOR language!
							$stmt_OT->bind_param("i", $a_index);										// bind parameters for markers; $a_index increaments by 1 starting at 0
							$stmt_OT->execute();														// execute query
							$result_array = $stmt_OT->get_result();										// instead of bind_result (used for only 1 record):
							$num_array=$result_array->num_rows;
							if ($result_array && $num_array > 0) {
								$OT_Book[] = $a_index;
								$i=0;
								$j=(string)$a_index;
								while ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {						// display the chapters using a drop-down box
									$OT_Audio_Filename = trim($r_array['OT_Audio_Filename']);
									if (!empty($OT_Audio_Filename)) {
										$OT_Audio_Chapter = trim($r_array['OT_Audio_Chapter']);
										$OT_Book_Chapter[$a_index][] = $OT_Audio_Chapter;
										$j = $j . "," . $OT_Audio_Chapter . "," . $OT_Audio_Filename;
									}
									$i++;
								}
								echo "<option id='OT_Book_$a_index' name='OT_Book_$a_index' value='$j'>$a</option>";
							}
							$a_index++;
						}
						echo "</select>";
						$stmt_OT->close();																	// close statement
						// Get and display chapters
						?>
						<form name='form_OT_Chapters_mp3' id='form_OT_Chapters_mp3' style='display: inline; '>
						<select name='OT_Chapters_mp3' id='OT_Chapters_mp3' class='selectOption' onchange='ListenAudio(this, true, "OTListenNow", <?php echo $OTNT; ?>)'>
						<?php
						$a_index = 0;
						$query_array="SELECT * FROM OT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code' AND Variant_Code = '$Variant_Code' AND OT_Audio_Book = ? AND (OT_Audio_Filename IS NOT NULL AND trim(OT_Audio_Filename) <> '')";		// ISO_ROD_index = '$ISO_ROD_index'
						$stmt_OT = $db->prepare($query_array);												// create a prepared statement
						foreach ($OT_array[OT_EngBook] as $a) {											// display the OT books in the MAJOR language!
							$stmt_OT->bind_param("i", $a_index);											// bind parameters for markers								// 
							$stmt_OT->execute();															// execute query
							$result_array = $stmt_OT->get_result();										// instead of bind_result (used for only 1 record):
							$num_array=$result_array->num_rows;
							if ($result_array && $num_array > 0) {
								$i=0;
								while ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {						// display the chapters using a drop-down box
									$OT_Audio_Filename = trim($r_array['OT_Audio_Filename']);
									if (!empty($OT_Audio_Filename)) {
										$OT_Audio_Chapter = trim($r_array['OT_Audio_Chapter']);
										echo "<option id='OT_Audio_Chapters_$i' name='OT_Audio_Chapters_$i' value='$a^./data/$ISO/audio/$OT_Audio_Filename'>$OT_Audio_Chapter</option>";
									}
									$i++;
								}
								break;
							}
							$a_index++;
						}
						$stmt_OT->close();																	// close statement
						?>
						</select>
						</form>
						</div>
						</div>
						<div id='OTListenNow' class='ourListenNow' style='margin-top: 0px; '>
							<?php
							if (isset($mobile) && $mobile == 1) {
							}
							else {
							?>
								<div class='ourFlashPlayer'>
							<?php
							}
							?>
								<span id='OTBookChapter' style='vertical-align: top; '> listenBook " " listenChapter </span> &nbsp;&nbsp; 
								<div id="jquery_jplayer_<?php echo ( $OT_Audio > 0 ? '2' : '1' ) ?>" class="jp-jplayer"></div>
								<div id="jp_container_<?php echo ( $OT_Audio > 0 ? '2' : '1' ) ?>" class="jp-audio">
									<div class="jp-type-single">
										<div class="jp-gui jp-interface">
											<ul class="jp-controls">
												<li><a href="#" class="jp-play" tabindex="1">play</a></li>
												<li><a href="#" class="jp-pause" tabindex="1">pause</a></li>
												<li><a href="#" class="jp-stop" tabindex="1">stop</a></li>
												<?php
												if (isset($mobile) && $mobile == 1) {
												}
												else {
													?>
													<li><a href="#" class="jp-mute" tabindex="1" title="mute">mute</a></li>
													<li><a href="#" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
													<li><a href="#" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
													<?php
												}
												?>
											</ul>
											<div class="jp-progress">
												<div class="jp-seek-bar">
													<div class="jp-play-bar"></div>
												</div>
											</div>
											<?php
											if (isset($mobile) && $mobile == 1) {
											}
											else {
												?>
												<div style="margin-top: 14px; margin-left: 340px; " class="jp-volume-bar">
													<div class="jp-volume-bar-value"></div>
												</div>
												<div class="jp-time-holder">
													<div class="jp-current-time"></div>
													<div class="jp-duration"></div>
													<!--ul class="jp-toggles">
														<li><a href="#" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
														<li><a href="#" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
													</ul-->
												</div>
												<?php
											}
											?>
										</div>
										<div class="jp-no-solution">
											<span>Update Required</span>
											To play the media you will need to either update your browser to a recent version or update your <a href="https://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
										</div>
									</div>
								</div>
							<?php
							if (isset($mobile) && $mobile == 1) {
							}
							else {
								echo '</div>';
							}
							?>
							</div>
						</td>
						</tr>
						<?php
					}
				}
			
				if ($NT_Audio) {
					$query="SELECT * FROM NT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code'";	// ISO_ROD_index = '$ISO_ROD_index'";
					$result2=$db->query($query);
					if ($result2) {
						$OTNT = $NT_Audio + $OT_Audio;
						?>
						<tr>
						<td>
						<?php
						$NT_Book = array();
						$NT_Book_Chapter = array();
						$a_index = 0;
						echo "<div class='linePointer' title='".translate('Listen to the New Testament.', $st, 'sys')."' onclick='ListenAudio(document.form_NT_Chapters_mp3.NT_Chapters_mp3, true, \"NTListenNow\", $OTNT)'><img class='iconActions' src='../images/listen-icon.jpg' alt='".translate('Listen', $st, 'sys')."' title='".translate('Listen', $st, 'sys')."' />";
						echo "</div>";
						echo "</td>";
						echo "<td>";
						?>
						<div class='NTAudio'>
						<?php
						echo "<div class='linePointer' title='".translate('Listen to the New Testament.', $st, 'sys')."' onclick='ListenAudio(document.form_NT_Chapters_mp3.NT_Chapters_mp3, true, \"NTListenNow\", $OTNT)'>".translate('Listen', $st, 'sys')." ".translate('to the audio New Testament:', $st, 'sys').'</a>';
						echo '</div>';
						?>
						<div id='NTAudioSelects' style='display: inline; '>
						<?php
						if (isset($mobile) && $mobile == 1) {
							echo '<br />';
						}
						else {
							echo ' ';
						}
						// Get and display Books
						echo "<select id='NT_Book_mp3' name='NT_Book_mp3' class='selectOption' onchange='AudioChangeChapters(\"NT\", \"$ISO\", \"$ROD_Code\", this.options[this.selectedIndex].value); ListenAudio(document.form_NT_Chapters_mp3.NT_Chapters_mp3, true, \"NTListenNow\", $OTNT)'>";
						$query_array="SELECT * FROM NT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code' AND Variant_Code = '$Variant_Code' AND NT_Audio_Book = ? AND (NT_Audio_Filename is not null AND trim(NT_Audio_Filename) <> '')";		// ISO_ROD_index = '$ISO_ROD_index'
						$stmt = $db->prepare($query_array);												// create a prepared statement
						foreach ($NT_array[NT_EngBook] as $a) {											// display the NT books in the MAJOR language!
							$stmt->bind_param("i", $a_index);											// bind parameters for markers								// 
							$stmt->execute();															// execute query
							$result_array = $stmt->get_result();										// instead of bind_result (used for only 1 record):
							$num_array=$result_array->num_rows;
							if ($result_array && $num_array > 0) {
								$NT_Book[] = $a_index;
								$j=(string)$a_index;
								while ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {						// display the chapters
									$NT_Audio_Filename = trim($r_array['NT_Audio_Filename']);
									if (!empty($NT_Audio_Filename)) {
										$NT_Audio_Chapter = trim($r_array['NT_Audio_Chapter']);
										$NT_Book_Chapter[$a_index][] = $NT_Audio_Chapter;
										$j = $j . "," . $NT_Audio_Chapter . "," . $NT_Audio_Filename;
									}
								}
								echo "<option id='NT_Book_$a_index' name='NT_Book_$a_index' value='$j'>$a</option>";
							}
							$a_index++;
						}
						$stmt->close();																	// close statement
						echo "</select>";
						// Get and display chapters
						?>
						<form name='form_NT_Chapters_mp3' id='form_NT_Chapters_mp3' style='display: inline; '>
						<select name='NT_Chapters_mp3' id='NT_Chapters_mp3' class='selectOption' onchange='ListenAudio(this, true, "NTListenNow", <?php echo $OTNT; ?>)'>
						<?php
						$a_index = 0;
						$query_array="SELECT * FROM NT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code' AND Variant_Code = '$Variant_Code' AND NT_Audio_Book = ? AND (NT_Audio_Filename is not null AND trim(NT_Audio_Filename) <> '')";		// ISO_ROD_index = '$ISO_ROD_index'
						$stmt = $db->prepare($query_array);												// create a prepared statement
						foreach ($NT_array[NT_EngBook] as $a) {											// display the NT books in the MAJOR language!
							$stmt->bind_param("i", $a_index);											// bind parameters for markers								// 
							$stmt->execute();															// execute query
							$result_array = $stmt->get_result();										// instead of bind_result (used for only 1 record):
							$num_array=$result_array->num_rows;
							if ($result_array && $num_array > 0) {
								$i=0;
								while ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {						// display the chapters
									$NT_Audio_Filename = trim($r_array['NT_Audio_Filename']);
									if (!empty($NT_Audio_Filename)) {
										$NT_Audio_Chapter = trim($r_array['NT_Audio_Chapter']);
										echo "<option id='NT_Audio_Chapters_$i' name='NT_Audio_Chapters_$i' value='$a^./data/$ISO/audio/$NT_Audio_Filename'>$NT_Audio_Chapter</option>";
									}
									$i++;
								}
								break;
							}
							$a_index++;
						}
						$stmt->close();																	// close statement
						?>
						</select>
						</form>
						</div>
						</div>
						<div id='NTListenNow' class='ourListenNow' style='margin-top: 0px; '>
							<?php
							if (isset($mobile) && $mobile == 1) {
							}
							else {
							?>
								<div class='ourFlashPlayer'>
							<?php
							}
							?>
								<!--div id="slideshow"></div test for 00-SpecificLanguage.js for function ListenAudio(mp3Info, autostart, whichListenTo, OTNT)-->
								<span id='NTBookChapter' style='vertical-align: top; '> listenBook " " listenChapter </span> &nbsp;&nbsp; 
								<div id="jquery_jplayer_1" class="jp-jplayer"></div>
								<div id="jp_container_1" class="jp-audio">
									<div class="jp-type-single">
										<div class="jp-gui jp-interface">
											<ul class="jp-controls">
												<li><a href="#" class="jp-play" tabindex="1">play</a></li>
												<li><a href="#" class="jp-pause" tabindex="1">pause</a></li>
												<li><a href="#" class="jp-stop" tabindex="1">stop</a></li>
												<?php
												if (isset($mobile) && $mobile == 1) {
												}
												else {
													?>
													<li><a href="#" class="jp-mute" tabindex="1" title="mute">mute</a></li>
													<li><a href="#" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
													<li><a href="#" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
													<?php
												}
												?>
											</ul>
											<div class="jp-progress">
												<div class="jp-seek-bar">
													<div class="jp-play-bar"></div>
												</div>
											</div>
											<?php
											if (isset($mobile) && $mobile == 1) {
											}
											else {
												?>
												<div style="margin-top: 14px; margin-left: 340px; " class="jp-volume-bar">
													<div class="jp-volume-bar-value"></div>
												</div>
												<div class="jp-time-holder">
													<div class="jp-current-time"></div>
													<div class="jp-duration"></div>
													<!--ul class="jp-toggles">
														<li><a href="#" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
														<li><a href="#" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
													</ul-->
												</div>
												<?php
											}
											?>
										</div>
										<div class="jp-no-solution">
											<span>Update Required</span>
											To play the media you will need to either update your browser to a recent version or update your <a href="https://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
										</div>
									</div>
								</div>
							<?php
							if (isset($mobile) && $mobile == 1) {
							}
							else {
							?>
								</div>
							<?php
							}
							?>
							</div>
						<!--/div-->
						</td>
						</tr>
						<?php
					}
				}
			}
		}
	}

/*
	*************************************************************************************************************
		Is it audio downloadable?
	*************************************************************************************************************
*/
	if ($NT_Audio > 0 || $OT_Audio > 0) {							// if it is a 1 then
		if ($OT_Audio > 0) {
			?>
				<div id='otaudiofiles' class='otaudiofiles' style='display: block; '>
				<tr style='margin-top: -2px; '>
					<td style='width: 45px; '>
						<?php
						echo "<div class='linePointer' onclick='OTTableClick()'><img class='iconActions' src='../images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' /></div>";
					echo "</td>";
					echo "<td>";
						echo "<div class='linePointer' title='".translate('Download the audio Old Testament files.', $st, 'sys')."' onclick='OTTableClick()'>".translate('Download', $st, 'sys')." ".translate('the Old Testament audio files (MP3)', $st, 'sys')."</div>";
						?>
					</td>
				</tr>
				<tr>
					<td colspan="2" width="100%">
					<form>
					<table id='OTTable'>
						<tr>
							<td colspan='4' width='100%'>
								<?php
								echo "<input style='float: right; margin-top: 0px; margin-right: 20px; font-size: 11pt; font-weight: bold; ' type='button' value='".translate('Download Selected OT Audio', $st, 'sys')."' onclick='OTAudio(\"$st\", \"$ISO\", \"$ROD_Code\", \"$mobile\", \"".translate('Please wait!<br />Creating the ZIP file<br />which will take a while.', $st, 'sys')."\")' />";
								?>
								<div id='OT_Download_MB' style='float: right; vertical-align: bottom; margin-top: 6px; '></div>
							</td>
						</tr>
						<?php
						$media_index = 4;
						$num_array_col = "25%";
						$col_span = 1;
						if (isset($mobile) && $mobile == 1) {
							$media_index = 2;
							$num_array_col = "50%";
							$col_span = 2;
						}
						$a_index = 0;
						$j = 0;
						?>
						<tr>
							<?php
							$query_array="SELECT * FROM OT_Audio_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_Audio_Book = ? AND (OT_Audio_Filename IS NOT NULL AND trim(OT_Audio_Filename) <> '')";
							$stmt = $db->prepare($query_array);												// create a prepared statement
							foreach ($OT_array[OT_EngBook] as $a) {											// display Eng. NT books
								$stmt->bind_param("i", $a_index);											// bind parameters for markers								// 
								$stmt->execute();															// execute query
								$result_array = $stmt->get_result();										// instead of bind_result (used for only 1 record):
								$num_array=$result_array->num_rows;
								if ($result_array && $num_array > 0) {
									if ($j == $media_index) {
										?>
										</tr>
										<tr>
										<?php
										$j = 0;
									}
									?>
									<td width='<?php echo $num_array_col; ?>' colspan='<?php echo $col_span; ?>'>
									<?php
									$ZipFile = 0;
									while ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {						// display the chapters
										$OT_Audio_Filename = trim($r_array['OT_Audio_Filename']);
										if (file_exists("./data/$ISO/audio/$OT_Audio_Filename")) {
											$temp = filesize("./data/$ISO/audio/$OT_Audio_Filename");
											$temp = intval($temp/1024);			// MB
											$ZipFile += round($temp/1024, 2);
											$ZipFile = round($ZipFile, 1);
										}
									}
									echo "<input type='checkbox' id='OT_audio_$a_index' name='OT_audio_$a_index' onclick='OTAudioClick(\"$a_index\", $ZipFile)' />&nbsp;&nbsp;$a<span style='font-size: .9em; font-weight: normal; '> (~$ZipFile MB)</span>";
									?>
									</td>
									<?php
									$j++;
								}
								$a_index++;
							}
							$stmt->close();																	// close statement
							for (; $j <= $media_index; $j++) {
								?>
								<td width='<?php echo $num_array_col; ?>' colspan='<?php echo $col_span; ?>'>&nbsp;</td>
								<?php
							}
							?>
						</tr>
					</table>
					</form>
					</td>
				</tr>
				</div>
			<?php
		}
		if ($NT_Audio > 0) {
			?>
				<div id='ntaudiofiles' class='ntaudiofiles'>
				<tr>
					<td style='width: 45px; '>
						<?php
						echo "<div class='linePointer' onclick='NTTableClick()'><img class='iconActions' src='../images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' /></div>";
					echo "</td>";
					echo "<td>";
						echo "<div class='linePointer' title='".translate('Download the audio New Testament files.', $st, 'sys')."' onclick='NTTableClick()'>".translate('Download', $st, 'sys')." ".translate('the New Testament audio files (MP3)', $st, 'sys')."</div>";
						?>
					</td>
				</tr>
				<tr>
					<td colspan="2" width="100%" style='margin-bottom: -50px; '>
					<form>
					<table id='NTTable' style='margin-bottom: 15px; width: 100%; '>
						<tr>
							<td colspan='4' width='100%'>
								<?php
								echo "<input style='float: right; margin-top: 0px; margin-right: 20px; font-size: 11pt; font-weight: bold; ' type='button' value='".translate('Download Selected NT Audio', $st, 'sys')."' onclick='NTAudio(\"$st\", \"$ISO\", \"$ROD_Code\", \"$mobile\", \"".translate('Please wait!<br />Creating the ZIP file<br />which will take a while.', $st, 'sys')."\")' />";
								?>
								<div id='NT_Download_MB' style='float: right; vertical-align: bottom; margin-top: 6px; '></div>
							</td>
						</tr>
						<?php
						$media_index = 4;
						$num_array_col = "25%";
						$col_span = 1;
						if (isset($mobile) && $mobile == 1) {
							$media_index = 2;
							$num_array_col = "50%";
							$col_span = 2;
						}
						$a_index = 0;
						$j = 0;																				// mod
						?>
						<tr>
							<?php
							$query_array="SELECT * FROM NT_Audio_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_Audio_Book = ? AND (NT_Audio_Filename IS NOT NULL AND trim(NT_Audio_Filename) <> '')";
							$stmt = $db->prepare($query_array);												// create a prepared statement
							foreach ($NT_array[NT_EngBook] as $a) {											// display Eng. NT books
								$stmt->bind_param("i", $a_index);											// bind parameters for markers
								$stmt->execute();															// execute query
								$result_array = $stmt->get_result();										// instead of bind_result (used for only 1 record):
								$num_array=$result_array->num_rows;
								if ($result_array && $num_array > 0) {										// if ISO_ROD_index and NT_Audio_Book exist
									if ($j == $media_index) {
										?>
										</tr>
										<tr>
										<?php
										$j = 0;
									}
									?>
									<td style='width: <?php echo $num_array_col; ?>; ' colspan='<?php echo $col_span; ?>'>
									<?php
									$ZipFile = 0;
									while ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {			// get all of the chapters in a single book
										$NT_Audio_Filename = trim($r_array['NT_Audio_Filename']);
										if (file_exists("./data/$ISO/audio/$NT_Audio_Filename")) {
											$temp = filesize("./data/$ISO/audio/$NT_Audio_Filename");
											$temp = intval($temp/1024);										// MB
											$ZipFile += round($temp/1024, 2);
											$ZipFile = round($ZipFile, 1);
										}
									}
									echo "<input type='checkbox' id='NT_audio_$a_index' name='NT_audio_$a_index' onclick='NTAudioClick(\"$a_index\", $ZipFile)' />&nbsp;&nbsp;$a<span style='font-size: .9em; font-weight: normal; '> (~$ZipFile MB)</span>";
									?>
									</td>
									<?php
									$j++;
								}
								$a_index++;
							}
							$stmt->close();																	// close statement
							for (; $j <= $media_index; $j++) {
								?>
								<td style='width: <?php echo $num_array_col; ?>; ' colspan='<?php echo $col_span; ?>'>&nbsp;</td>
								<?php
							}
							?>
						</tr>
					</table>
					</form>
					</td>
				</tr>
				</div>
			<?php
		}
	}

/*
	*************************************************************************************************************
		Is it playlist video?
	*************************************************************************************************************
*/
	if ($PlaylistVideo) {															//  test for $Internet is 1 1/2 screens down
		$query="SELECT PlaylistVideoTitle, PlaylistVideoFilename FROM PlaylistVideo WHERE ISO_ROD_index = '$ISO_ROD_index' AND PlaylistVideoDownload = 0";
		$result_Playlist=$db->query($query);
		$z=0;
		$SEVideoPlaylist=10;
		while ($r_Playlist = $result_Playlist->fetch_array(MYSQLI_ASSOC)) {
			$PlaylistVideoTitle = $r_Playlist['PlaylistVideoTitle'];
			$PlaylistVideoFilename = $r_Playlist['PlaylistVideoFilename'];
			$SEVideoPlaylistIndex=0;
			$SEVideoPlaylistArray = [];
			
			$PLVideo = $PlaylistVideoFilename;
			$ISO_dialect = $ISO;

			/*******************************************************************************************************************
					set $PLVideo in order to set $PlaylistVideoTitle for the 'tool tip' (see about 100 lines below)
			********************************************************************************************************************/
			// JESUSFilm-bzs.txt, etc.
			if (preg_match('/^[a-zA-Z]+-('.$ISO.'[a-zA-Z0-9]*)(-|\.)/', $PlaylistVideoFilename, $matches)) {	// get the ISO code and if there is anything attached before the '-'
				$ISO_dialect = $matches[1];
				preg_match('/^([a-zA-Z]+)-/i', $PlaylistVideoFilename, $matches);	// get the left most letters before the '-'
				$PLVideo = strtolower($matches[1]);									// to lower case ('jesusfilm', etc.)
			}
			// bzj-ScriptureAnim.txt, etc.
			elseif (preg_match('/^('.$ISO.'[a-zA-Z0-9]*)-/', $PlaylistVideoFilename, $matches)) {	// get the ISO code and if there is anything attached before the '-'
				$ISO_dialect = $matches[1];
				if (preg_match('/-([a-zA-Z]+)(-|\.)/i', $PlaylistVideoFilename, $matches)) {	// get the left most letters before the '-'
					if (empty($matches[1])) {
						//die('ERROR. Non-alphabetic characters in '.$matches[0]);		// produces "PHP Warning:  Undefined array key 1 in /home/se/public_html/include/00-SpecificLanguage.inc.php"
					}
					else {
						$PLVideo = strtolower($matches[1]);									// to lower case ('jesusfilm', etc.)
					}
				}
				else {
					//die('ERROR. Non-alphabetic characters in ' . $PlaylistVideoFilename . '.');
				}
			}
			
			/*******************************************************************************************************************
					set $filename
			********************************************************************************************************************/
			$filename = './data/'.$ISO.'/video/' . substr($PlaylistVideoFilename, 0, strlen($PlaylistVideoFilename)-3) . $MajorCountryAbbr . '.txt';
			if (!file_exists($filename)) {
				$filename = './data/'.$ISO.'/video/' . $PlaylistVideoFilename;
				if (!file_exists($filename)) {
					echo '<div>Warning: '.$PlaylistVideoTitle.' filename does not exist.</div>';
					continue;
				}
			}
			else {
				$PlaylistVideoFilename = substr($PlaylistVideoFilename, 0, strlen($PlaylistVideoFilename)-3) . $MajorCountryAbbr . '.txt';
			}
			
			/*******************************************************************************************************************
				returns a string of the contents of the txt file
			********************************************************************************************************************/
			$VideoFilenameContents = file_get_contents($filename);					// returns a string of the contents of the txt file
			$VideoConvertContents = explode("\n", $VideoFilenameContents);			// create array separate by new line
			
			$VideoConvertWithTab = [];
			$i = 0;
			$BibleStory = 0;
			$VideoName = '';
			$image = 0;																// just the ISO
			$bookName = '';
			$tempArray = [];
			
			$tempArray = explode("\t", $VideoConvertContents[1]);					// $Internet test
			if (count($tempArray) < 3) {
				file_put_contents('SpecificLanguage.txt', 'filename: #'.$filename.'#; $VideoConvertContents[1]: #'.$VideoConvertContents[1]."#\n", FILE_APPEND | LOCK_EX);
			}
			if (stripos($tempArray[3], 'http', 0) === 0 && !$Internet) {			// returns 0 means 'http' starts at column 0. === needs to be this way (and not ==) because jPlayer wont work.
				continue;
			}
			
			if (preg_match("/image/i", $VideoConvertContents[0])) {					// first line of $filename
				preg_match("/^([^\t]*)\t/", $VideoConvertContents[0], $match);		// first word of the first line (Genesis, Luke, Acts, Jesus Film, etc.)
				$bookName = str_replace(' ', '', $match[0]);						// take out spaces
				$bookName = substr($bookName, 0, strlen($bookName)-1);				// take out /t
				$image = 1;
			}
			else if (preg_match("/only ISO/i", $VideoConvertContents[0]) || preg_match("/ISO only/i", $VideoConvertContents[0])) {
				$image = 2;															// just only the ISO used here but 
			}
			else {
			}
			
			/*******************************************************************************************************************
					set $PlaylistVideoTitle for 'tool tip'
			********************************************************************************************************************/
			if ($PLVideo == 'jesusfilm' || $PLVideo == 'magdalena' || $PLVideo == 'scriptureanim' || $PLVideo == 'johnanim' || $PLVideo == 'johnslide' || $PLVideo == 'lukevid' || $PLVideo == 'actsvid' || $PLVideo == 'genvid' || $PLVideo == 'johnmovie' || substr($PlaylistVideoFilename, 0, strlen('scripture-videos')) == 'scripture-videos') {																		// first word of the first line (The Jesus Film, etc.) of txt file
				$VT = '';																				// get everything after "-" from $PlaylistVideoTitle
				if (preg_match("/- *(.*)/", $VideoConvertContents[0], $match)) {
					$VT = $match[1];
				}
				if (preg_match("/\t(.*) — /", $VideoConvertContents[0], $match)) {
					$PlaylistVideoTitle = $match[1];
				}
				else if (preg_match("/\t(.*)\t.*\timages/", $VideoConvertContents[0], $match)) {
					$PlaylistVideoTitle = $match[1];
				}
				else {
					// just $PlaylistVideoTitle
				}
			}
			
			for (; $i < count($VideoConvertContents); $i++) {							// iterate through $VideoConvertContents looking for a 0 and 1 or a letter (the very first line of the txt file).
				if (strstr($VideoConvertContents[$i], "\t", true) == '0') {				// true = before the first occurance (\t); the title
					$BibleStory = $i;													// Bible Story = $i
					$VideoNames = explode("\t", $VideoConvertContents[0]);
					$VideoName = $VideoNames[1];
					continue;
				}
				if (strstr($VideoConvertContents[$i], "\t", true) == '1') {				// true = before the first occurance (\t); the 1st new testament entry
					break;
				}
			}
			if ($st == 'spa') {
				if ($PlaylistVideoTitle == 'Luke Video') $PlaylistVideoTitle = 'video de San Lucas';
				if ($PlaylistVideoTitle == 'Genesis Video') $PlaylistVideoTitle = 'video de Genesis';
				if ($PlaylistVideoTitle == 'Acts Video') $PlaylistVideoTitle = 'video de Hechos';
				if ($PlaylistVideoTitle == 'the Luke Video') $PlaylistVideoTitle = 'el video de San Lucas';
				if ($PlaylistVideoTitle == 'the Genesis Video') $PlaylistVideoTitle = 'el video de Genesis';
				if ($PlaylistVideoTitle == 'the Acts Video') $PlaylistVideoTitle = 'el video de Hechos';
			}
			// $i = the number of rows beginning with the number 1 in the first column
			?>
			<script>
				var orgVideoPixels_<?php echo $z; ?> = 0;
			</script>
			<tr>
				<td style='width: 45px; '>
					<?php
					echo "<div class='linePointer' onclick='PlaylistVideo(orgVideoPixels_$z, \"PlaylistVideoNow_$z\", $mobile)'><img class='iconActions' src='../images/youtube-icon.jpg' alt='".translate('View', $st, 'sys')."' title='".translate('View', $st, 'sys')."' /></div>";
					?>
				</td>
				<td>
					<?php
					echo "<div class='linePointer' title='".translate('View', $st, 'sys')." $PlaylistVideoTitle' onclick='PlaylistVideo(orgVideoPixels_$z, \"PlaylistVideoNow_$z\", $mobile)'>".translate('View', $st, 'sys').' '.$PlaylistVideoTitle . "</div>";
					// Get and display Playlist
					?>
				</td>
			</tr>
			<tr id="PlaylistVideoNow_<?php echo $z; ?>" style="display: none; overflow: hidden; float: left; line-height: 0px; ">	<?php // The extra styles are for the mobile Andoid to work! (6/17/17) ?>
				<td style='width: 45px; '>&nbsp;
				</td>
				<td>
					<?php
					if ($BibleStory != 0 ) {
						$VideoConvertContents[$BibleStory] = str_replace("\r", "", $VideoConvertContents[$BibleStory]);		// Windows text files have a carrage return at the end.
						$VideoConvertWithTab = explode("\t", $VideoConvertContents[$BibleStory]);							// split the line up by tabs
						// $VideoConvertWithTab[0] = number; $VideoConvertWithTab[1] = text; $VideoConvertWithTab[2] = data filename for png or jpg; $VideoConvertWithTab[3] = URL
						if ($st == 'spa') {
							if ($VideoName == 'Luke Video') $VideoName = 'video de San Lucas';
							if ($VideoName == 'Genesis Video') $VideoName = 'video de Genesis';
							if ($VideoName == 'Acts Video') $VideoName = 'video de Hechos';
							if ($VideoName == 'the Luke Video') $VideoName = 'el video de San Lucas';
							if ($VideoName == 'the Genesis Video') $VideoName = 'el video de Genesis';
							if ($VideoName == 'the Acts Video') $VideoName = 'el video de Hechos';
						}
						if (count($VideoConvertWithTab) < 3) {
							file_put_contents('SpecificLanguage.txt', 'filename: #'.$filename.'#; count($VideoConvertWithTab): ' . count($VideoConvertWithTab) . '; $BibleStory: #' . $BibleStory . '#; $VideoConvertContents[$BibleStory]: #'.$VideoConvertContents[$BibleStory]."#\n", FILE_APPEND | LOCK_EX);
						}
						if (stripos($VideoConvertWithTab[3], 'http', 0) === 0) {									// returns 0 means 'http' starts at column 0. === needs to be this way (and not ==) because jPlayer wont work.
							echo "<div style='text-align: center; '><div style='cursor: pointer; display: inline; text-align: center; '";
							echo " onclick='window.open(\"$VideoConvertWithTab[3]\")'>";
						}
						if ($image != 2) {																			// != 2 ScriptureAnim to make sure the first picture is skipped
							if ($image == 1) {
								echo "<img src='./data/~images/$bookName/";
							}
							else if ($image == 0) {
								echo "<img src='./data/$ISO/video/";
							}
							else {
							}
							echo $VideoConvertWithTab[2]."' alt='".translate('View', $st, 'sys')." ".$VideoName."' title='".translate('View', $st, 'sys')." ".$VideoName."' /></div></div>";
							echo '<div style="text-align: center; font-size: .9em; margin-bottom: 10px; font-weight: normal; ">'.$VideoName.'</div>';
							echo '<hr style="color: navy; text-align: center; width: 75%; " />';
						}
					}
					$resource = '';
					?>
					<table style='width: 100%'>
						<tr style="margin-top: 8px; margin-bottom: 8px; ">
							<?php
							$c = 0;
							for ($a = $i; $i < count($VideoConvertContents); $i++, $c++) {							// continue using $i to iterate through $VideoConvertContents
								if (trim($VideoConvertContents[$i]) == "") {										// test for blank line at the end of the txt file
									continue;
								}
								if ($c % 4 == 0) {
									if ($a != $i) {
										echo '</tr>';
										echo '<tr style="margin-top: 8px; margin-bottom: 8px; ">';
									}
								}
								$VideoConvertContents[$i] = str_replace("\r", "", $VideoConvertContents[$i]);		// Windows text files have a carrage return at the end.
								$VideoConvertWithTab = explode("\t", $VideoConvertContents[$i]);					// split the line up by tabs
								// $VideoConvertWithTab[0] = number; $VideoConvertWithTab[1] = text; $VideoConvertWithTab[2] = data filename for png or jpg; $VideoConvertWithTab[3] = URL
								echo '<td style="width: 25%; text-align: center; vertical-align: top; ">';
									echo "<div class='linePointer' title='".$VideoConvertWithTab[1]."'";
									if (stripos($VideoConvertWithTab[3], 'http', 0) === 0) {						// returns 0 means 'http' starts at column 0. === needs to be this way (and not ==) because jPlayer wont work.
										$resource = preg_replace(array('/^the /i', '/^a /i', '/^el /i'), '', $PlaylistVideoFilename);	// not needed but...
										$resource = preg_replace('/^(.*[^-])+\-.*/', '$1', $resource);									// delete the first ungreedy "-" and from through $
										$resource = preg_replace('/^(.*[^_])+_.*/', '$1', $resource);									// delete the first ungreedy "_" and from through $
										$resource = preg_replace('/^(.*[^.])+\..*/', '$1', $resource);									// delete the first ungreedy "." and from through $
										echo " onclick='LinkedCounter(\"".$resource."_".$counterName."_".$GetName."_".$ISO."\", \"".$VideoConvertWithTab[3]."\")'>";	// $resource = like "JESUSFilm"
										//echo " onclick='window.open(\"".$VideoConvertWithTab[3]."\",\"_blank\")'>";
										// onclick='LinkedCounter(\"BibleIs_".$counterName."_".$GetName."_".$ISO."\", \"".$URL."\")'
									}
									else {
										$SEVideoPlaylistArray[$SEVideoPlaylistIndex] = $VideoConvertWithTab[1]. '|' . $VideoConvertWithTab[2] . '|' . $VideoConvertWithTab[3];
										echo " id='PV_".$SEVideoPlaylist."_".$SEVideoPlaylistIndex."' onclick='PlaylistVideo_$SEVideoPlaylist($SEVideoPlaylistIndex)'>";
										$SEVideoPlaylistIndex++;
									}
									if ($image == 1) {
										echo "<img src='./data/~images/$bookName/";
									}
									else if ($image == 0) {
										echo "<img src='./data/$ISO/video/";
									}
									else {
										echo "<img src='./data/~images/ScriptureAnim/";
									}
									echo $VideoConvertWithTab[2]."' alt='".translate('View', $st, 'sys')." ".$VideoConvertWithTab[1]."' title='".translate('View', $st, 'sys')." ".$VideoConvertWithTab[1]."' /></div>";
									echo '<div style="text-align: center; font-size: .7em; font-weight: normal; ">'.$VideoConvertWithTab[1].'</div>';
								echo '</td>';
							}
							for (; $c % 4 != 0; $c++) {
								echo "<td>&nbsp;</td>";
							}
							?>
						</tr>
					</table>
					<?php
					// $VideoConvertWithTab[0] = number; $VideoConvertWithTab[1] = text; $VideoConvertWithTab[2] = data filename for png or jpg; $VideoConvertWithTab[3] = URL
					
					/**************************************************************************************************************************************
						returns FALSE if the 'http' is not there so PlaylistVideoFilename is on the SE server (also, look up above where the 'onclick's)
					**************************************************************************************************************************************/
					if (stripos($VideoConvertWithTab[3], 'http', 0) === false) {									// returns FALSE if it is not there
						$j = 0;
						?>
						<div id="PlaylistVideoListenNow_<?php echo $SEVideoPlaylist; ?>" class='ourPlaylistVideoNow' style='margin-top: 0px; '>
							<script type="text/javascript">
								$(document).ready(function(){
									var currentpath = "";
									var myPlaylist_<?php echo $SEVideoPlaylist; ?> = new jPlayerPlaylist({ 			// Create a var containing the playlist object
											jPlayer: "#jquery_jplayer_<?php echo $SEVideoPlaylist; ?>",
											cssSelectorAncestor: "#jp_container_<?php echo $SEVideoPlaylist; ?>"
										}, [
											<?php
											foreach ($SEVideoPlaylistArray as $key => $SEVP) {
												$pieces = explode('|', $SEVP);
												?>
												{
													title: "<?php echo $pieces[0]; ?>",
													m4v: "<?php echo $pieces[2]; ?>",
													poster: "./data/~images/ScriptureAnim/<?php echo $pieces[1]; ?>"
												},
												<?php
												$j++;
											}
											?>
										], {
										swfPath: "_js",
										supplied: "m4v",
										size: {
											width: "640px",
											height: "360px",
											cssClass: "jp-video-360p"
										},
										useStateClassSkin: true,
										autoBlur: false,
										smoothPlayBar: true,
										keyEnabled: true,
										remainingDuration: true,
										toggleDuration: true
									});
									myPlaylist_<?php echo $SEVideoPlaylist; ?>.select(0);
									//myPlaylist.play(0);															// Option 1 : Plays the 1st item
									//myPlaylist.pause(0);
									<?php
									for ($a=0; $a < $j; $a++) {
										?>
										$("#PV_<?php echo $SEVideoPlaylist; ?>_<?php echo $a; ?>").click( function() {
											myPlaylist_<?php echo $SEVideoPlaylist; ?>.play(<?php echo $a; ?>);
											<?php $VideoConvertWithTab = explode("\t", $VideoConvertContents[$a+2]); ?>;		// split the line up by tabs. $VideoConvertContents[$a+1] with title. $VideoConvertContents[$a+2] without title. ?>
											$( "#videoTitle<?php echo $SEVideoPlaylist; ?>" ).html( "<?php echo $VideoConvertWithTab[1]; ?>" );
											// This is a weird one with the ' and " and / !
											$( "#VideoDownloadButton<?php echo $SEVideoPlaylist; ?>" ).html( "<button type='button' tabindex='0' onclick='saveAsVideo(\"<?php echo $ISO; ?>\", \"<?php echo $st; ?>\", <?php echo $mobile; ?>, \"<?php echo $VideoConvertWithTab[3]; ?>\", \"<?php echo translate("Please wait!<br>Creating the ZIP file<br>which will take a while.", $st, 'sys'); ?>\")'><?php echo translate("Download this video", $st, 'sys'); ?><\/button>" );
										});
										<?php
									}
									?>
								});

								// http://ryanve.com/lab/dimensions/ = "Documents" heights
								var orgPixels_<?php echo $SEVideoPlaylist; ?> = 0;
								function PlaylistVideo_<?php echo $SEVideoPlaylist; ?>(futureNumber) {
									//totalNumber = < ?php echo $SEVideoPlaylistIndex; ?>;
									//var divHeight = 0;
									//var currentNumber = 0;
									/*for (var a=1; a <= totalNumber; a++) {
										if (document.getElementById('PlaylistVideoListenNow_'+a).style.display == "block") {
										//                                        $("#jquery_jplayer_playlist_"+a).jPlayer("stop");
											document.getElementById('PlaylistVideoListenNow_'+a).style.display = "none";
											currentNumber = a;
										}
									}*/
									$(document).ready(function() {
										/*if (currentNumber != futureNumber) {
											orgPixels_< ?php echo $SEVideoPlaylist; ?> = document.body.scrollHeight - 42;
											document.getElementById('PlaylistVideoListenNow_'+futureNumber).style.display = "block";
											divHeight = document.body.scrollHeight - 31;
										}
										else {
											divHeight = orgPixels_< ?php echo $SEVideoPlaylist; ?>;
										}
										document.getElementById("container").style.height = divHeight + "px";*/
										// if the table is long enough IE goes to dark black (blur and opacity). I don't know why.
										$("#container").redrawShadow({left: 5, top: 5, blur: 2, opacity: 0.5, color: "black", swap: false});
									});
									//$("#jquery_jplayer_< ?php echo $SEVideoPlaylist; ?>").jPlayer("play("+futureNumber+")");
								}
							</script>
							
							<div id="jp_container_<?php echo $SEVideoPlaylist; ?>" class="jp-video jp-video-270p" role="application" aria-label="media player">
								<div class="jp-type-playlist">
									<div id="jquery_jplayer_<?php echo $SEVideoPlaylist; ?>" class="jp-jplayer"></div>
									<div class="jp-gui" style='background-color: white; '>
										<div class="jp-video-play">
											<button class="jp-video-play-icon" role="button" tabindex="0">play</button>
										</div>
										<div class="jp-interface">
											<div class="jp-progress">
												<div class="jp-seek-bar">
													<div class="jp-play-bar"></div>
												</div>
											</div>
											<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
											<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
											<div class="jp-details">
												<div class="jp-title" aria-label="title" style='margin-top: 30px; background-color: #369'>&nbsp;</div>
											</div>
											<div class="jp-controls-holder">
												<div class="jp-volume-controls" style='margin-bottom: 10px; '>
													<button class="jp-mute" role="button" tabindex="0">mute</button>
													<button class="jp-volume-max" role="button" tabindex="0">max volume</button>
													<div class="jp-volume-bar">
														<div class="jp-volume-bar-value"></div>
													</div>
												</div>
												<div class="jp-controls">
													<!--button class="jp-previous" role="button" tabindex="0">previous</button-->
													<button class="jp-play" role="button" tabindex="0">play</button>
													<button class="jp-stop" role="button" tabindex="0">stop</button>
													<!--button class="jp-next" role="button" tabindex="0">next</button-->
												</div>
												<div class="jp-toggles">
													<button class="jp-full-screen" role="button" tabindex="0">full screen</button>
												</div>
											</div>
										</div>
									</div>
									<!--div class="jp-playlist">
										<ul-->
											<!-- The method Playlist.displayPlaylist() uses this unordered list -->
											<!--li></li>
										</ul>
									</div-->
									<div class="jp-no-solution">
										<span>Update Required</span>
										To play the media you will need to either update your browser to a recent version or update your <a href="https://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
									</div>
								</div>
							</div>
							
							<!-- The following lines are NOT assiciated with jPlayer! -->
							<div class="jp-details">
								<?php $VideoConvertWithTab = explode("\t", $VideoConvertContents[2]);			// split the line up by tabs. $VideoConvertContents[1] with title and $VideoConvertContents[2] without title ?>
								<div class="jp-title" aria-label="title" style='background-color: #369; width: 88%; '><span id="videoTitle<?php echo $SEVideoPlaylist; ?>" style='margin-left: auto; margin-right: auto; color: #EEE; '><?php echo $VideoConvertWithTab[1]; ?></span></div>
								<div id="VideoDownloadButton<?php echo $SEVideoPlaylist; ?>" style="text-align: right; margin-right: 12%; "><button type="button" tabindex="0" onClick="saveAsVideo('<?php echo $ISO; ?>', '<?php echo $st; ?>', <?php echo $mobile; ?>, '<?php echo $VideoConvertWithTab[3]; ?>', '<?php echo translate('Please wait!<br />Creating the ZIP file<br />which will take a while.', $st, 'sys'); ?>')"><?php echo translate("Download this video", $st, "sys"); ?></button></div>
							</div>
						</div>
					<?php
					}
					?>
				</td>
			</tr>
			<?php
			$z++;
		}
	}

/*
	*************************************************************************************************************
		Is the playlist video downloadable?
	*************************************************************************************************************
*/
	if ($PlaylistVideo) {																//  test for $Internet is 1 1/2 screens down
		$query="SELECT PlaylistVideoTitle, PlaylistVideoFilename FROM PlaylistVideo WHERE ISO_ROD_index = '$ISO_ROD_index' AND PlaylistVideoDownload = 1";
		$result_Playlist=$db->query($query);
		$z=0;
		$SEVideoPlaylist=0;
		while ($r_Playlist = $result_Playlist->fetch_array(MYSQLI_ASSOC)) {
			$PlaylistVideoTitle = $r_Playlist['PlaylistVideoTitle'];
			$PlaylistVideoFilename = $r_Playlist['PlaylistVideoFilename'];
			$SEVideoPlaylistIndex=0;
			$SEVideoPlaylistArray = [];

			$PLVideo = $PlaylistVideoFilename;
			$ISO_dialect = $ISO;
			// JESUSFilm-bzs.txt or
			$found = preg_match('/^[a-zA-Z]+-('.$ISO.'[a-zA-Z0-9]*)(-|\.)/', $PlaylistVideoFilename, $matches);	// get the ISO code and if there is anything attached before the '-'
			if ($found) {
				$ISO_dialect = $matches[1];
				preg_match('/^([a-zA-Z]+)-/i', $PlaylistVideoFilename, $matches);		// get the left most letters before the '-'
				$PLVideo = strtolower($matches[1]);										// to lower case
			}
			// bzj-ScriptureAnim.txt
			$found = preg_match('/^('.$ISO.'[a-zA-Z0-9]*)-/', $PlaylistVideoFilename, $matches);	// get the ISO code and if there is anything attached before the '-'
			if ($found) {
				$ISO_dialect = $matches[1];
				if (preg_match('/-([a-zA-Z0-9ñáéíóú]+)(-|\.)/i', $PlaylistVideoFilename, $matches)) {	// get the left most letters before the '-'
					$PLVideo = strtolower($matches[1]);										// to lower case
				}
				else {
					die('ERROR. Non-alphabetic characters in '.$PlaylistVideoFilename);
				}
			}
			
			$filename = './data/'.$ISO.'/video/' . substr($PlaylistVideoFilename, 0, strlen($PlaylistVideoFilename)-3) . $MajorCountryAbbr . '.txt';
			if (!file_exists($filename)) {
				$filename = './data/'.$ISO.'/video/' . $PlaylistVideoFilename;
			}
			else {
				$PlaylistVideoFilename = substr($PlaylistVideoFilename, 0, strlen($PlaylistVideoFilename)-3) . $MajorCountryAbbr . '.txt';
			}
			
			if (!file_exists($filename)) {
				echo '<div>Warning: '.$PlaylistVideoTitle.' filename does not exist.</div>';
				continue;
			}
			$VideoFilenameContents = file_get_contents($filename);												// returns a string of the contents of the tx file
			$VideoConvertContents = explode("\n", $VideoFilenameContents);										// create array separate by new line
			$VideoConvertWithTab = [];
			$i = 0;
			$BibleStory = 0;
			$VideoName = '';
			$tempArray = [];
			
			// split the 2nd line of txt file
			$tempArray = explode("\t", $VideoConvertContents[1]);												// split the 2nd line of txt file
			if (array_key_exists('3', $tempArray) && stripos($tempArray[3], 'http', 0) === 0) {					// returns 0 means 'http' starts at column 0. === needs to be this way (and not ==) because jPlayer wont work.
				continue;
			}
			if ($PLVideo == 'jesusfilm' || $PLVideo == 'lukevideo' || $PLVideo == 'magdalena' || $PLVideo == 'scriptureanim' || substr($PlaylistVideoFilename, 0, strlen('scripture-videos')) == 'scripture-videos') {																		// first word of the first line (The Jesus Film, etc.) of txt file
				if (preg_match("/\t(.*) — /", $VideoConvertContents[0], $match)) {
					$PlaylistVideoTitle = $match[1];
				}
				else if (preg_match("/\t(.*)\t.*\timages/", $VideoConvertContents[0], $match)) {
					$PlaylistVideoTitle = $match[1];
				}
				else {
					// $PlaylistVideoTitle
				}
			}
			
			for (; $i < count($VideoConvertContents); $i++) {													// iterate through $VideoConvertContents looking for a 0 and 1 or a letter (the very first line of the txt file).
				if (strstr($VideoConvertContents[$i], "\t", true) == '0') {										// true = before the first occurance (\t); the title
					$BibleStory = $i;																			// Bible Story = $i
					$VideoNames = explode("\t", $VideoConvertContents[0]);
					$VideoName = $VideoNames[1];
					continue;
				}
				if (strstr($VideoConvertContents[$i], "\t", true) == '1') {										// true = before the first occurance (\t); the 1st new testament entry
					break;
				}
			}
			if ($st == 'spa') {
				if ($PlaylistVideoTitle == 'Luke Video') $PlaylistVideoTitle = 'video de San Lucas';
				if ($PlaylistVideoTitle == 'Genesis Video') $PlaylistVideoTitle = 'video de Genesis';
				if ($PlaylistVideoTitle == 'Acts Video') $PlaylistVideoTitle = 'video de Hechos';
				if ($PlaylistVideoTitle == 'the Luke Video') $PlaylistVideoTitle = 'el video de San Lucas';
				if ($PlaylistVideoTitle == 'the Genesis Video') $PlaylistVideoTitle = 'el video de Genesis';
				if ($PlaylistVideoTitle == 'the Acts Video') $PlaylistVideoTitle = 'el video de Hechos';
			}
			// $i = the number of rows beginning with the number 1 in the first column
			?>
			<script>
				var orgVideoPixels_<?php echo $z; ?> = 0;
			</script>
			<tr>
				<td style='width: 45px; '>
					<?php
					echo "<div class='linePointer' onclick='PlaylistVideo(orgVideoPixels_$z, \"PlaylistVideoDownload_$z\", $mobile)'><img class='iconActions' src='../images/MP4-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' /></div>";
					?>
				</td>
				<td>
					<?php
					echo "<div class='linePointer' title='".translate('Download', $st, 'sys')." $PlaylistVideoTitle' onclick='PlaylistVideo(orgVideoPixels_$z, \"PlaylistVideoDownload_$z\", $mobile)'>".translate('Download', $st, 'sys') . ' ' . $PlaylistVideoTitle . '</div>';
					// Get and display Playlist Download
					?>
				</td>
			</tr>
			<?php // The extra styles are for the mobile Android to work! (6/17/17) ?>
			<tr id="PlaylistVideoDownload_<?php echo $z; ?>" style="display: none; overflow: hidden; float: left; line-height: 0px; ">
				<?php
				$TotalZipFile = 0;
				if ($BibleStory != 0) {
					$VideoConvertContents[$BibleStory] = str_replace("\r", "", $VideoConvertContents[$BibleStory]);	// Windows text files have a carrage return at the end.
					$VideoConvertWithTab = explode("\t", $VideoConvertContents[$BibleStory]);						// split the line up by tabs
					// $VideoConvertWithTab[0] = number; $VideoConvertWithTab[1] = text; $VideoConvertWithTab[2] = data filename for png or jpg; $VideoConvertWithTab[3] = URL
					if ($st == 'spa') {
						if ($VideoName == 'Luke Video') $VideoName = 'Video de San Lucas';
						if ($VideoName == 'Genesis Video') $VideoName = 'Video de Genesis';
						if ($VideoName == 'Acts Video') $VideoName = 'Video de Hechos';
						if ($VideoName == 'the Luke Video') $VideoName = 'el video de San Lucas';
						if ($VideoName == 'the Genesis Video') $VideoName = 'el video de Genesis';
						if ($VideoName == 'the Acts Video') $VideoName = 'el video de Hechos';
					}
					$z++;
				}
				else {
					$SEVideoPlaylist++;
					$SEVideoPlaylistArray[$SEVideoPlaylistIndex] = $VideoConvertWithTab[1]. '|' . $VideoConvertWithTab[2] . '|' . $VideoConvertWithTab[3];
					$SEVideoPlaylistIndex++;
				}
				?>
				<td colspan="2" width="100%" style='margin-bottom: -50px; '>
					<form>
					<table id='PlaylistVideoDownloadTable_<?php echo $z; ?>' style='margin-bottom: 15px; width: 100%; '>
						<tr>
							<td colspan='4' style='width: 100% '>
								<div style="float: right; margin-top: 0; margin-left: 30px; margin-bottom: 0; ">
									<?php
									echo "<input type='button' id='AllOrNoneVideo_${z}' style='font-size: 11pt; font-weight: bold; ' value='".translate('Select all', $st, 'sys')."' onclick='DownloadAllVideoPlaylistClick(\"$z\", \"".translate('Select all', $st, 'sys')."\", \"".translate('Unselect all', $st, 'sys')."\")' />";
									?>
								</div>
								<div style="float: right; margin-top: 0; margin-right: 15px; margin-bottom: 0; ">
									<?php
									echo "<input type='button' style='margin-top: 0px; margin-right: 20px; font-size: 11pt; font-weight: bold; ' value='".translate('Download selected videos', $st, 'sys')."' onclick='DownloadVideoPlaylistZip(\"$st\", \"$ISO\", \"$PlaylistVideoFilename\", \"$z\", \"$mobile\", \"".translate('Please wait!<br />Creating the ZIP file<br />which will take a while.', $st, 'sys')."\")' />";
									?>
								</div>
								<div id='PlaylistVideoDownload_MB_<?php echo $z; ?>' style='float: right; vertical-align: bottom; margin-top: 6px; font-size: 11pt; '></div>
							</td>
						</tr>
						<?php
						$media_index = 4;
						$num_array_col = "25%";
						$col_span = 1;
						if (isset($mobile) && $mobile == 1) {
							$media_index = 2;
							$num_array_col = "50%";
							$col_span = 2;
						}
						$j = 0;
						$video_download_index = 0;
						?>
						<tr>
							<?php
							for (; $i < count($VideoConvertContents); $i++) {										// continue using $i to iterate through $VideoConvertContents
								if (trim($VideoConvertContents[$i]) == "") {										// test for blank line at the end of the txt file
									continue;
								}
								if ($j == $media_index) {
									?>
									</tr>
									<tr>
									<?php
									$j = 0;
								}
								?>
								<td colspan='<?php echo $col_span; ?>' style='padding-top: 10pt; vertical-align: top; width: <?php echo $num_array_col; ?>; '>
									<?php
									$ZipFile = 0;
									$VideoConvertContents[$i] = str_replace("\r", "", $VideoConvertContents[$i]);	// Windows text files have a carrage return at the end.
									$VideoConvertWithTab = explode("\t", $VideoConvertContents[$i]);				// split the line up by tabs for txt file
									if (isset($VideoConvertWithTab[3])) {
										$VideoPlaylist_Download_Filename = $VideoConvertWithTab[3];					// = mp4 file names
										if (file_exists($VideoPlaylist_Download_Filename)) {
											$temp = filesize($VideoPlaylist_Download_Filename);
											$temp = intval($temp/1024);			// MB
											$ZipFile += round($temp/1024, 2);
											$ZipFile = round($ZipFile, 1);
											$TotalZipFile += $ZipFile;
											echo "<input type='checkbox' id='DVideoPlaylist_${z}_${video_download_index}' name='DownloadVideoPlaylist_$z' value='${video_download_index}' onclick='DownloadVideoPlaylistClick(\"${video_download_index}\", $ZipFile, $z)' />&nbsp;&nbsp;<span style='font-size: 12pt; '>$VideoConvertWithTab[1]</span><span style='font-size: 11pt; font-weight: normal; '> (~$ZipFile MB)</span>";
											$video_download_index++;												// video count
										}
										else {
											echo "&nbsp;&nbsp;<span style='font-size: 10pt; background-color: yellow; '>$VideoConvertWithTab[1] mp4 filename is misspelled?</span>";
										}
									}
									else {
										echo "&nbsp;&nbsp;<span style='font-size: 10pt; background-color: yellow; '>$VideoConvertWithTab[1] mp4 filename does not exist. Maybe it is in line the $PlaylistVideoFilename file?</span>";
									}
									?>
								</td>
								<?php
								$j++;																				// column count
							}
							for (; $j <= $media_index; $j++) {
								?>
								<td style='width: <?php echo $num_array_col; ?>; ' colspan='<?php echo $col_span; ?>'>&nbsp;</td>
								<?php
							}
							?>
						</tr>
					</table>
					<input type="hidden" id="TotalZipFile_<?php echo $z; ?>" name="TotalZipFile_<?php echo $z; ?>" value="<?php echo $TotalZipFile; ?>" />
					</form>
				</td>
			</tr>
			<?php
		}
	}

/*
	*************************************************************************************************************
		disabled: Is thw PDF downloadable?
	*************************************************************************************************************
*/

/*
	*************************************************************************************************************
		Is it Bible.is? (if "Text with audio" exists here and if not then above)
	*************************************************************************************************************
*/
	if ($Internet && $BibleIs && $SAB) {
		$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND NOT BibleIs = 0";
		$result2=$db->query($query);
		$num=$result2->num_rows;
		if ($result2 && $num > 0) {
			while ($r_links=$result2->fetch_array(MYSQLI_ASSOC)) {
				$URL=trim($r_links['URL']);
				if (preg_match('/^(.*\/)[a-zA-Z0-9][a-zA-Z]{2}\/[0-9]+$/', $URL, $matches)) {		// remove e.g. Mat/1
					$URL=$matches[1];
				}
				$BibleIsVersion=trim($r_links['company_title']);
				$BibleIsLink=$r_links['BibleIs'];
				$BibleIsIcon = '';
				$BibleIsActText = '';
				switch ($BibleIsLink) {
					case 1:
						$BibleIsIcon = 'BibleIs-icon.jpg';
						$BibleIsActText = translate('Read and Listen', $st, 'sys');
						break;
					case 2:
						$BibleIsIcon = 'BibleIs-icon.jpg';
						$BibleIsActText = translate('Read', $st, 'sys');
						break;			
					case 3:
						$BibleIsIcon = 'BibleIsAudio.jpg';
						$BibleIsActText = translate('Read and Listen', $st, 'sys');
						break;			
					case 4:
						$BibleIsIcon = 'BibleIsVideo.jpg';
						$BibleIsActText = translate('Read, Listen, and View', $st, 'sys');
						break;			
					default:
						break;
				}
				?>
				<tr>
					<td>
						<?php
						echo "<div class='linePointer' onclick='LinkedCounter(\"BibleIs_".$counterName."_".$GetName."_".$ISO."\", \"".$URL."\")'><img class='iconActions' src='../images/".$BibleIsIcon."' alt='".$BibleIsActText."' title='".$BibleIsActText."' /></div>";
					echo "</td>";
					echo "<td>";
						echo "<div class='linePointer' onclick='LinkedCounter(\"BibleIs_".$counterName."_".$GetName."_".$ISO."\", \"".$URL."\")' title='".translate('Read/Listen/View from Bible.is', $st, 'sys')."'>" . $BibleIsActText . " ";
						//if (stripos($URL, '/Gen/') !== false)
						/*if ($BibleIsLink == 1)
							echo translate('to the New Testament', $st, 'sys');
						else if ($BibleIsLink == 2)
							echo translate('to the Old Testament', $st, 'sys');
						else	// $BibleIs == 3
							echo translate('to the Bible', $st, 'sys');*/
						echo translate('on Bible.is', $st, 'sys');
						if ($BibleIsVersion!='') {
							echo ' ' . $BibleIsVersion;
						}
						echo "</div>";
						?>
					</td>
				</tr>
				<?php
			}
		}
	}

/*
	*************************************************************************************************************
		Is it YouVersion?
	*************************************************************************************************************
*/
	if ($YouVersion && $Internet) {
		$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND YouVersion = 1";
		$result2=$db->query($query);
		if ($result2) {
			if ($Internet) {
				//$num=mysql_num_rows($result2);
				$text='';
				$text1='';
				$text2='';
				$match=array();
				while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
					$URL=trim($r2['URL']);
					$organization=trim($r2['company_title']);
					$text = trim($r2['company']);
					if (strpos($text, ' on') !== false) {
						preg_match("/([^ ]*)( .*)/", $text, $match);
						$text1 = $match[1];
						$text2 = $match[2];
						if (preg_match("/ on/", $text2)) {
							$matchTemp=array();
							preg_match("/(.*) on/", $text2, $matchTemp);
							$text2 = $matchTemp[1];
						}
						$text2 = trim($text2);
					}
					else {
						$text1 = $text;
						$text2='';
					}
					?>
					<tr>
						<td style='width: 45px; '>
							<?php
							echo "<div class='linePointer' onclick=\"window.open('$URL')\"><img class='iconActions' src='../images/YouVersion-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' /></div>";
						echo "</td>";
						echo "<td>";
							echo "<div class='linePointer' onclick=\"window.open('$URL')\"  title='".translate('Read from YouVersion (Bible.com)', $st, 'sys')."'>" . translate($text1, $st, 'sys') . ' ' . translate($text2, $st, 'sys') . ': ' . $organization . '</div>';
							?>
						</td>
					</tr>
				<?php
				}
			}
		}
	}

/*
	*************************************************************************************************************
		Is it Bibles.org?
	*************************************************************************************************************
*/
	if ($Biblesorg && $Internet) {
		$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND `Bibles_org` = 1";
		$result2=$db->query($query);
		if ($result2) {
			if ($Internet) {
				//$num=mysql_num_rows($result2);
				$text='';
				$text1='';
				$text2='';
				$match=array();
				while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
					$URL=trim($r2['URL']);
					if ($MajorCountryAbbr == 'es' || $MajorCountryAbbr == 'pt' || $MajorCountryAbbr == 'fr') {
						$URL = preg_replace('/(.*\/?\/?).*(bibles\.org.*)/', '$1' . ($MajorCountryAbbr == 'pt' ? 'pt-br.' : $MajorCountryAbbr.'.') . '$2', $URL);
					}
					$organization=trim($r2['company_title']);
					$text = trim($r2['company']);
					if (strpos($text, ' on') !== false) {
						preg_match("/([^ ]*)( .*)/", $text, $match);
						$text1 = $match[1];
						$text2 = $match[2];
						if (preg_match("/ on/", $text2)) {
							$matchTemp=array();
							preg_match("/(.*) on/", $text2, $matchTemp);
							$text2 = $matchTemp[1];
						}
						$text2 = trim($text2);
					}
					else {
						$text1 = $text;
						$text2='';
					}
					?>
					<tr>
						<td style='width: 45px; '>
							<?php
							echo "<div class='linePointer' onclick=\"window.open('$URL')\"><img class='iconActions' src='../images/BibleSearch-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' /></div>";
						echo "</td>";
						echo "<td>";
							echo "<div class='linePointer' onclick=\"window.open('$URL')\">" . translate($text1, $st, 'sys') . ' ' . translate($text2, $st, 'sys') . ': ' . $organization . '</div>';
							?>
						</td>
					</tr>
					<?php
				}
			}
		}
	}

/*
	*************************************************************************************************************
		disabled: Can it be viewed? (if (Bible.is || YouVersion || Bibles.org || "Text with audio") then here otherwise below)
	*************************************************************************************************************
*/

/*
	*************************************************************************************************************
		Is it a cell phone module (apart from the Android App and iOS Asset Package)?
	*************************************************************************************************************
*/
	/*
		GoBible (Java)
		MySword (Android)
		iPhone		// only one "iPhone" record in the table (6/27/2022)
		Windows
		Blackberry
		Standard Cell Phone
		Android App (apk) (not here but up above)
		iOS Asset Package (not here but up above)
	*/
	if ($CellPhone) {
		$query="SELECT * FROM CellPhone WHERE ISO_ROD_index = '$ISO_ROD_index' AND (Cell_Phone_Title <> 'Android App' AND Cell_Phone_Title <> 'iOS Asset Package')";
		$result2=$db->query($query);
		if ($result2) {
			while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
				$Cell_Phone_Title=$r2['Cell_Phone_Title'];
				$Cell_Phone_File=trim($r2['Cell_Phone_File']);
				$optional=trim($r2['optional']);
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						if ($Cell_Phone_Title == 'MySword (Android)')
							echo "<div class='linePointer' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><img class='iconActions' src='../images/mysword-icon.jpg' alt='".translate('Cell Phone', $st, 'sys')."' title='".translate('Cell Phone', $st, 'sys')."' />";
						elseif ($Cell_Phone_Title == 'iPhone') {		// only one "iPhone" record in the table (6/27/2022)
							echo "<div class='linePointer' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><img class='iconActions' src='../images/iOS_App.jpg' alt='".translate('Cell Phone', $st, 'sys')."' title='".translate('Cell Phone', $st, 'sys')."' />";
						}
						else {
							echo "<div class='linePointer' $Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><img class='iconActions' src='../images/CellPhoneIcon.png' alt='".translate('Cell Phone', $st, 'sys')."' title='".translate('Cell Phone', $st, 'sys')."' />";
						}
						echo '</div>';
					echo '</td>';
					echo '<td>';
						if ($Cell_Phone_Title == 'MySword (Android)')
							if ($Internet)
								echo "<div class='linePointer' title='" . translate('Download the cell phone module for', $st, 'sys') . " $Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'>" . translate('Download', $st, 'sys') . " " . translate('the cell phone module for', $st, 'sys') . "</div> <a href='https://www.mysword.info/' title='" . translate('Download the cell phone module for', $st, 'sys') . "' target='_blank'><span class='lineAction'>$Cell_Phone_Title</span></a>";
							else
								echo "<div class='linePointer' title='" . translate('Download the cell phone module for', $st, 'sys') . " $Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'>" . translate('Download', $st, 'sys') . " " . translate('the cell phone module for', $st, 'sys') . ' ' . $Cell_Phone_Title . '</div>';
						else
							echo "<div class='linePointer' title='" . translate('Download the app for', $st, 'sys') . " $Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'>" . translate('Download', $st, 'sys') . " " . translate('the app for', $st, 'sys') . ' ' . $Cell_Phone_Title . '</div>';
						echo ' ' . $optional;
						?>
					</td>
				</tr>
				<?php
			}
		}
	}

/*
	*************************************************************************************************************
		It is GRN? (table links)
	*************************************************************************************************************
*/
	if ($GRN && $Internet) {
		$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND GRN = 1";
		$result2=$db->query($query);
		if ($result2) {
			if ($Internet) {
				//$num=mysql_num_rows($result2);
				$text='';
				$text1='';
				$text2='';
				$match=array();
				$deaf = 0;
				$query="SELECT LN_English FROM LN_English WHERE ISO_ROD_index = '$ISO_ROD_index'";
				$result3=$db->query($query);
				if ($result3->num_rows > 0) {
					$r_LN = $result3->fetch_array(MYSQLI_ASSOC);
					if (str_contains($r_LN['LN_English'], 'Sign Language')) {						// The only way I know how to see if the language is a sign language
						$deaf = 1;
					}
				}
				while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
					$URL=trim($r2['URL']);
					if ($st == 'spa' && strpos($URL, 'globalrecordings.net') !== false) {							// if $URL is GRN
						$URL = str_replace('/en/', '/es/', $URL);
					}
					$organization=trim($r2['company']);												// usually 'Global Recordings Network'
					$text = trim($r2['company_title']);												// usually 'Audio recordings'
					if ($deaf) {																	// if $dead
						$text = str_replace('Audio recordings', 'View', $text);
					}
					if (strpos($text, ' on') !== false) {
						preg_match("/([^ ]*)( .*)/", $text, $match);
						$text1 = $match[1];
						$text2 = $match[2];
						if (preg_match("/ on/", $text2)) {
							$matchTemp=array();
							preg_match("/(.*) on/", $text2, $matchTemp);
							$text2 = $matchTemp[1];
						}
						$text2 = trim($text2);
					}
					else {
						$text1 = $text;
						$text2='';
					}
					?>
					<tr>
						<td style='width: 45px; '>
							<?php
							echo "<div class='linePointer' onclick=\"window.open('$URL')\"><img class='iconActions' src='../images/GRN-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' /></div>";
						echo "</td>";
						echo "<td>";
							echo "<div class='linePointer' onclick=\"window.open('$URL')\" title='Global Recordings Network'>" . translate($text1, $st, 'sys') . ' ' . translate($text2, $st, 'sys') . ': ' . $organization . '</div>';
							?>
						</td>
					</tr>
				<?php
				}
			}
		}
	}

/*
	*************************************************************************************************************
		Can it be watched?
	*************************************************************************************************************
*/
	if ($watch && $Internet) {
		$query="SELECT * FROM watch WHERE ISO_ROD_index = '$ISO_ROD_index'";
		$result2=$db->query($query);
		if ($result2) {
			while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
				$organization=trim($r2['organization']);
				$watch_what=trim($r2['watch_what']);
				$URL=trim($r2['URL']);
				$JesusFilm=trim($r2['JesusFilm']);							// booleon
				$YouTube=trim($r2['YouTube']);								// booleon
				if ($st == 'spa' && $watch_what == 'The Story of Jesus for Children') $watch_what = 'la historia de Jesús para niños';
				if ($st == 'eng' && $watch_what == 'la historia de Jesús para niños') $watch_what = 'The Story of Jesus for Children';
				?>
				<tr>
				<td style='width: 45px; '>
					
					<?php
					if ($JesusFilm) {
						// JESUS Film
						if (substr($URL, 0, strlen("http://api.arclight.org/videoPlayerUrl")) == "http://api.arclight.org/videoPlayerUrl") {
							?>
								<div class='linePointer' onclick="window.open('JESUSFilmView.php?<?php echo $URL ?>','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=890,height=690,top=300,left=300'); return false;" title="<?php echo $LN ?>">
								<img class='iconActions' src='../images/JESUS-icon.jpg' alt="<?php echo translate('View', $st, 'sys') ?>" title="<?php echo translate('View', $st, 'sys') ?>" />
						</div>
							<?php
						}
						else {
							?>
								<div class='linePointer' onclick="window.open('<?php echo $URL ?>','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=380,top=200,left=300'); return false;" title="<?php echo $LN ?>">
								<img class='iconActions' src='../images/JESUS-icon.jpg' alt="<?php echo translate('View', $st, 'sys') ?>" title="<?php echo translate('View', $st, 'sys') ?>" />
						</div>
							<?php
						}
					}
					elseif ($YouTube) {
						// YouTube
						//     href="#" onclick="w=screen.availWidth; h=screen.availHeight; window.open('<?php echo $URL ? >','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width='+w+',height='+h+',top=0,left=0'); return false;" title="< ?php echo $LN ? >">
						?>
							<div class='linePointer' onclick="window.open('<?php echo $URL ?>')">
							<img class='iconActions' src='../images/youtube-icon.jpg'  alt="<?php echo translate('View', $st, 'sys') ?>" title="<?php echo translate('View', $st, 'sys') ?>" />
							</div>
						<?php
					}
					else {
						?>
							<div class='linePointer' onclick="window.open('<?php echo $URL ?>')">
							<img class='iconActions' src='../images/watch-icon.jpg'  alt="<?php echo translate('View', $st, 'sys') ?>" title="<?php echo translate('View', $st, 'sys') ?>" />
							</div>
						<?php
					}
					?>
				</td>
				<td>
					<?php
					if ($JesusFilm) {
						// JESUS Film
						if (substr($URL, 0, strlen("http://api.arclight.org/videoPlayerUrl")) == "http://api.arclight.org/videoPlayerUrl") {
								echo "<div class='linePointer' onclick='window.open(\"JESUSFilmView.php?$URL\",\"clip\",\"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=890,height=690,top=300,left=300\"); return false;' title='$LN'>";
						}
						else {
							echo "<div class='linePointer' onclick='window.open(\"$URL\",\"clip\",\"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=380,top=200,left=300\"); return false;' title='$LN'>";
						}
					}
					elseif ($YouTube) {
						// YouTube
						//    href="#" onclick="w=screen.availWidth; h=screen.availHeight; window.open('<?php echo $URL ? >','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width='+w+',height='+h+',top=0,left=0'); return false;" title="<?php echo $LN ? >">
						echo "<div class='linePointer' onclick=\"window.open('$URL')\" title='$LN'>";
					}
					else {
						echo "<div class='linePointer' onclick=\"window.open('$URL')\" title='translate(\"View\", $st, \"sys\")'>";
					}

					if ($JesusFilm) {
						// JESUS Film
						//echo $watch_what;
						echo translate('View the JESUS Film', $st, 'sys');
					}
					else if ($YouTube) {
						// YouTube
						echo translate('View', $st, 'sys').' (YouTube)'."&nbsp;: $organization $watch_what";
					}
					else {
						//echo translate('View', $st, 'sys')."</span> ".translate('by', $st, 'sys')." $organization:&nbsp;$watch_what";
						echo translate('View', $st, 'sys')." $organization:&nbsp;$watch_what";
					}
					?>
					</div>
				</td>
				</tr>
				<?php
			}
		}
	}

/*
	*************************************************************************************************************
		Is MP4 downloadable (other table)?
	*************************************************************************************************************
*/
	$query="SELECT other, other_title, download_video FROM other_titles WHERE ISO_ROD_index = '$ISO_ROD_index' AND (download_video IS NOT NULL AND trim(download_video) <> '')";
	$result_DV=$db->query($query);
	$DV_num=$result_DV->num_rows;
	if ($DV_num > 0) {
		while ($r_DV = $result_DV->fetch_array(MYSQLI_ASSOC)) {
			$other = $r_DV['other'];
			$other_title = $r_DV['other_title'];
			$download_video = $r_DV['download_video'];
			?>
			<tr style='margin-top: -2px; '>
			<td style='width: 45px; '>
			<?php
			echo "<div class='linePointer' onclick=\"window.open('./data/".$ISO.'/video/'.$download_video."')\" download><img class='iconActions' src='../images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' /></div>";
			echo "</td>";
			echo "<td>";
			echo "<div class='linePointer' onclick=\"window.open('./data/".$ISO.'/video/'.$download_video."')\" title='".translate('Download the video.', $st, 'sys')."' download>".translate('Download', $st, 'sys').' '.$other. ' ' . $other_title . ' ' .translate('video', $st, 'sys').' (MP4)</div>';
			?>
			</td>
			</tr>
			<?php
		}
	}

/*
	*************************************************************************************************************
		Is it playlist audio?
	*************************************************************************************************************
*/
	if ($PlaylistAudio && $Internet) {
		$homepage = '';
		$query="SELECT PlaylistAudioTitle, PlaylistAudioFilename FROM PlaylistAudio WHERE ISO_ROD_index = '$ISO_ROD_index'";
		$result_Playlist=$db->query($query);
		$num3=$result_Playlist->num_rows;
		$z=1;
		while ($r_Playlist = $result_Playlist->fetch_array(MYSQLI_ASSOC)) {
			$PlaylistAudioTitle = $r_Playlist['PlaylistAudioTitle'];
			$PlaylistAudioFilename = $r_Playlist['PlaylistAudioFilename'];
			$ArrayPlaylistAudio = [];
			?>
			<tr>
				<td style='width: 45px; '>
					<?php
					echo "<div class='linePointer' onclick='PlaylistAudio_$z($z, $num3)'><img class='iconActions' src='../images/listen-icon.jpg' alt='".translate('Listen', $st, 'sys')."' title='".translate('Listen', $st, 'sys')."' /></div>";
					?>
				</td>
				<td>
					<?php
					echo "<div class='linePointer' title='".translate('Listen', $st, 'sys')." $PlaylistAudioTitle' onclick='PlaylistAudio_$z($z, $num3)'>".translate('Listen', $st, 'sys').": $PlaylistAudioTitle</div>";
					// Get and display Playlist
					?>
					<div id="PlaylistAudioListenNow_<?php echo $z; ?>" class='ourPlaylistAudioNow' style='margin-top: 0px; '>
						<script>
							$(document).ready(function(){
								new jPlayerPlaylist({
									jPlayer: "#jquery_jplayer_playlist_<?php echo $z; ?>",
									cssSelectorAncestor: "#jp_container_playlist_<?php echo $z; ?>"
								}, [
									<?php
									$filename = './data/'.$ISO.'/audio/'.$PlaylistAudioFilename;
									if (file_exists($filename)) {
										// the data on a remote server
										$lines1 = @file($filename);
										foreach ($lines1 as $line1) {
											$ArrayPlaylistAudio[] = $line1;
										}
										// the data on the same server as the PHP script
										$homepage = file_get_contents($filename);						// returns a string
										echo $homepage;												// this is required because it is accessed later on in the audio download
									} else {
										echo "The text file $filename does not exist.";
									}
									?>
								], {
									swfPath: "_js",
									supplied: "mp3",
									wmode: "window",
									smoothPlayBar: true,
									keyEnabled: true
								});
							});

							var orgPixels_<?php echo $z; ?> = 0;
							function PlaylistAudio_<?php echo $z; ?>(futureNumber, totalNumber) {
								var divHeight = 0;
								var currentNumber = 0;
								for (var a=1; a <= totalNumber; a++) {
									if (document.getElementById('PlaylistAudioListenNow_'+a).style.display == "block") {
										$("#jquery_jplayer_playlist_"+a).jPlayer("stop");
										document.getElementById('PlaylistAudioListenNow_'+a).style.display = "none";
										currentNumber = a;
									}
								}
								$(document).ready(function() {
									if (currentNumber != futureNumber) {
										document.getElementById('PlaylistAudioListenNow_'+futureNumber).style.display = "block";
									}
								});
							}
						</script>
		
						<div id="jquery_jplayer_playlist_<?php echo $z; ?>" class="jp-jplayer"></div>
						<div id="jp_container_playlist_<?php echo $z; ?>" class="jp-audio-playlist">
							<div class="jp-type-playlist">
								<div class="jp-gui jp-interface" style="background-color: #ddd; ">
									<ul class="jp-controls" style="background: none; ">
										<li><a href="#" class="jp-previous" tabindex="1">previous</a></li>
										<li><a href="#" class="jp-play" tabindex="1">play</a></li>
										<li><a href="#" class="jp-pause" tabindex="1">pause</a></li>
										<li><a href="#" class="jp-next" tabindex="1">next</a></li>
										<li><a href="#" class="jp-stop" tabindex="1">stop</a></li>
										<li><a href="#" class="jp-mute" tabindex="1" title="mute">mute</a></li>
										<li><a href="#" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
										<li><a href="#" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
									</ul>
									<div class="jp-progress">
										<div class="jp-seek-bar">
											<div class="jp-play-bar"></div>
										</div>
									</div>
									<div class="jp-volume-bar">
										<div class="jp-volume-bar-value"></div>
									</div>
									<div class="jp-current-time" style="margin-top: -4px; "></div>
									<div class="jp-duration" style="margin-top: -4px; "></div>
								</div>
								<div class="jp-playlist" style="background-color: #999; ">
									<ul>
										<li></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td style='width: 45px; '>
					<?php
					echo "<div class='linePointer' onclick='PlaylistTableClick_$z()'><img class='iconActions' src='../images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' /></div>";
				echo "</td>";
				echo "<td>";
					echo "<div class='linePointer' title='".translate('Download', $st, 'sys')." $PlaylistAudioTitle' onclick='PlaylistTableClick_$z()'>".translate('Download', $st, 'sys').": ". $PlaylistAudioTitle ."</div>";
					//$homepage = preg_replace('/{\s*title:\s*[\'"]([^\'"]+)[\'"],\s*mp3:\s*[\'"]([^\'"]+)[\'"]\s*},?\s*/', '$1^$2|', $homepage);
					$homepage = '';
					foreach ($ArrayPlaylistAudio as $APLA) {
						$homepage .= preg_replace('/{\s*title:\s*[\'"]([^\'"]+)[\'"],\s*mp3:\s*[\'"]([^\'"]+)[\'"]\s*},?\s*/', '$1^$2|', $APLA);
					}
					$homepage = rtrim($homepage, "|");																// delete last '|' of the array
					$arr = array_map(function($val) { return explode('^', $val); }, explode('|', $homepage));		// put the string into 2D array
					if (isset($mobile) && $mobile == 1) {
						$howManyCol = 1;
						$tableWidth = 310;
						$width = $tableWidth/$howManyCol;
						$DivIndent = 100 - (($howManyCol) * 14);
					}
					else {
						$howManyCol = 2;
						$tableWidth = 750;
						$width = $tableWidth/$howManyCol;
						$DivIndent = 100 - (($howManyCol) * 7);
					}
					$MB_Total_Amount = 0;
					?>
					<form>
					<table id="DLPlaylistAudio_<?php echo $z; ?>" style="width: <?php echo $tableWidth; ?>px; margin-top: 5px; margin-right: 10px; font-weight: bold; font-size: 11pt; ">
						<tr>
							<td colspan="<?php echo $howManyCol; ?>" style='width: 100%; '>
								<div style="float: right; margin-top: 0; margin-left: 30px; margin-bottom: 4px; ">
									<?php
									$CountArr = count($arr);
									echo "<input id='AllOrNone_${z}' style='font-size: 1em; font-weight: bold; font-family: Arial, Helvetica, sans-serif; ' type='button' value='".translate('Select all', $st, 'sys')."' onclick='PlaylistAllAudioZip(\"$z\", $CountArr, \"".translate('Select all', $st, 'sys')."\", \"".translate('Unselect all', $st, 'sys')."\")' />";
									?>
								</div>
								<div style="float: right; margin-top: 0; margin-right: 15px; margin-bottom: 4px; ">
									<?php
									$CountArr = count($arr);
									echo "<input style='font-size: 1em; font-weight: bold; font-family: Arial, Helvetica, sans-serif; ' type='button' value='".translate('Download Audio Playlist', $st, 'sys')."' onclick='PlaylistAudioZip(\"$st\", \"$ISO\", \"$ROD_Code\", \"$z\", $CountArr, \"$mobile\", \"".translate('Please wait!<br />Creating the ZIP file<br />which will take a while.', $st, 'sys')."\")' />";
									?>
								</div>
								<div id="Playlist_Download_MB_<?php echo $z; ?>" style='float: right; display: inline-block; margin-top: 6px; margin-right: 8px; margin-bottom: 2px; '></div>
							</td>
						</tr>
						<?php
						$i = 0;
						$j = 0;
						foreach ($arr as $single) {
							if ($i == 0) {
								echo "<tr style='vertical-align: top; '>";
							}
							echo "<td style='width: ${width}px; vertical-align: top; '>";
								// $single[0] = text name
								// $single[1] = filename
								$temp = filesize($single[1]);
								$temp = intval($temp/1024);			// MB
								$ZipFile = round($temp/1024, 2);
								$ZipFile = round($ZipFile, 1);
								$MB_Total_Amount += $ZipFile;
								$j++;
								//echo "<input type='checkbox' id='Playlist_audio_${z}_$j' name='Playlist_audio_${z}_$j' onclick='PlaylistAudioClick_$z(\"$z\", $j, $ZipFile)' />";
								echo "<input type='checkbox' id='Playlist_audio_${z}_$j' name='Playlist_audio_${z}_$j' value='$single[1]' onclick='PlaylistAudioClick_$z(\"$z\", $j, $ZipFile)' />";
								echo "<div style='display: inline; float: right; width: ${DivIndent}%; margin-right: 20px; '>$single[0]<span style='font-size: .9em; font-weight: normal; '> (~$ZipFile MB)</style></div>";
							echo "</td>";
							$i++;
							if ($i == $howManyCol) {
								echo "</tr>";
							}
							if ($i == $howManyCol) $i = 0;
						}
						if ($i != 0) {
							while ($i < $howManyCol) {
								echo "<td style='width: ${width}px; '>";
									echo "&nbsp;";
								echo "</td>";
								$i++;
							}
							echo "</tr>";
						}
						?>
					</table>
					</form>
					<div id='MB_<?php echo $z; ?>' style='display: none; '><?php echo $MB_Total_Amount; ?></div>
					<script type="text/javascript" language="javascript">
						document.getElementById("DLPlaylistAudio_<?php echo $z; ?>").style.display = 'none';
						document.getElementById("Playlist_Download_MB_<?php echo $z; ?>").style.display = 'none';
						var ZipFilesPlaylist_<?php echo $z; ?> = 0;
						function PlaylistAudioClick_<?php echo $z; ?>(PlaylistGroupIndex, PlaylistIndex, ZipFileSize) {		// check box name, the book
							if (document.getElementById("Playlist_audio_"+PlaylistGroupIndex+"_"+PlaylistIndex).checked) {
								ZipFilesPlaylist_<?php echo $z; ?> += ZipFileSize;
							}
							else {
								ZipFilesPlaylist_<?php echo $z; ?> -= ZipFileSize;
							}
							ZipFilesPlaylist_<?php echo $z; ?> = Math.round(ZipFilesPlaylist_<?php echo $z; ?>*100)/100;		// rounded just does integers!
							if (ZipFilesPlaylist_<?php echo $z; ?> <= 0.049) {
								document.getElementById("Playlist_Download_MB_<?php echo $z; ?>").style.display = 'none';
							}
							else {
								document.getElementById("Playlist_Download_MB_<?php echo $z; ?>").style.display = 'block';
								document.getElementById("Playlist_Download_MB_<?php echo $z; ?>").innerHTML = "~"+ZipFilesPlaylist_<?php echo $z; ?> + " MB&nbsp;";
							}
						}

						var PlaylistTableVisible_<?php echo $z; ?> = 0;
						var divHeight_<?php echo $z; ?> = 0;
						function PlaylistTableClick_<?php echo $z; ?>() {
							$(document).ready(function() {
								if (PlaylistTableVisible_<?php echo $z; ?> == 0) {
									document.getElementById("DLPlaylistAudio_<?php echo $z; ?>").style.display = "block";
									PlaylistTableVisible_<?php echo $z; ?> = 1;
								}
								else {
									document.getElementById("DLPlaylistAudio_<?php echo $z; ?>").style.display = "none";
									PlaylistTableVisible_<?php echo $z; ?> = 0;
								}
							});
						}
					</script>
				</td>
			</tr>
			<?php
			$z++;
		}
	}

/*
	*************************************************************************************************************
		Can it be boughten?
	*************************************************************************************************************
*/
	if ($buy && $Internet) {
		$query="SELECT * FROM buy WHERE ISO_ROD_index = '$ISO_ROD_index'";
		$result2=$db->query($query);
		// mysql_num_rows($result2) >= 1
		while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
			$buy_what=trim($r2['buy_what']);
			$organization=trim($r2['organization']);
			$URL=trim($r2['URL']);
			?>
			<tr>
				<td style='width: 45px; '>
					<?php
					echo "<div class='linePointer' onclick=\"window.open('$URL')\"><img class='iconActions' src='../images/buy-icon.jpg' alt='".translate('Buy', $st, 'sys')."' title='".translate('Buy', $st, 'sys')."' /></div>";
				echo '</td>';
				echo '<td>';
					echo "<div class='linePointer' onclick=\"window.open('$URL')\" title='".translate('Buy from organization.', $st, 'sys')."'>".translate('Buy from', $st, 'sys')." $organization: $buy_what</div>";
					?>
				</td>
			</tr>
			<?php
		}
		$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND buy = 1";
		$result2=$db->query($query);
		//$num=mysql_num_rows($result2);
		while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
			$company_title=trim($r2['company_title']);
			$company=trim($r2['company']);
			$URL=trim($r2['URL']);
			?>
			<tr>
				<td style='width: 45px; '>
					<?php
					echo "<div class='linePointer' onclick=\"window.open('$URL')\"><img class='iconActions' src='../images/buy-icon.jpg' alt='".translate('Buy', $st, 'sys')."' title='".translate('Buy', $st, 'sys')."' /></div>";
				echo '</td>';
				echo '<td>';
					echo "<div class='linePointer' onclick=\"window.open('$URL')\" title='".translate('Buy from organization.', $st, 'sys')."'>".translate('Buy from', $st, 'sys')." ".translate('to', $st, 'sys')." ";
					if ($company_title != '' && $company_title != NULL) {
						echo "$company_title: ";
					}
					echo "$company</div>";
					?>
				</td>
			</tr>
			<?php
		}
	}

/*
	*************************************************************************************************************
		Can it be studied?
	*************************************************************************************************************
*/
	if ($study) {
		$query="SELECT * FROM study WHERE ISO_ROD_index = '$ISO_ROD_index'";
		$result2=$db->query($query);
		if ($result2) {
			while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
				$Testament=$r2['Testament'];
				$alphabet=$r2['alphabet'];
				$ScriptureURL=trim($r2['ScriptureURL']);
				$othersiteURL=trim($r2['othersiteURL']);
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						// I have to use a table, float: left or display: inline-block.
						// Using table is "old fashioned".
						// Using float: left you can't have vertical-align: middle.
						// However, if you use display: inline-block you are faced with a whitespace problem.
						// See http://designshack.net/articles/css/whats-the-deal-with-display-inline-block/
						// In an HTML file you must use a /p followed immediatly with another p (or /li with a li)
						// to make up for the extra whitespace.
						// In a PHP file it doesn't seem to matter as long as it is in PHP.
						// $ScriptureDescription
						//echo "<a href='#' style='font-size: .9em; ' title='".translate('Download the module.', $st, 'sys')."' onclick='Study(\"$ISO\", \"ROD_Code\", \"$ScriptureURL\")'><img class='iconActions' src='../images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' />&nbsp;&nbsp;<span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the New Testament', $st, 'sys')."</a><span style='font-size: .85em; '>&nbsp;";
						//echo "<a href='#' style='font-size: .9em; ' title='".translate('Download the module.', $st, 'sys')."' onclick='Study(\"$ISO\", \"ROD_Code\", \"$ScriptureURL\")'><img class='iconActions' src='../images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' />&nbsp;&nbsp;<span class='lineAction'>".translate('Download', $st, 'sys')."</span> ";
						echo "<div class='linePointer' onclick='Study(\"$st\", \"$ISO\", \"ROD_Code\", \"$ScriptureURL\")'>";
						echo "<img class='iconActions' style='margin-top: 4px; ' src='../images/TheWord-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' />";
						echo "</div>";
					echo "</td>";
					echo "<td>";
						echo "<div class='linePointer' title='$LN: ".translate('Download the module for The Word.', $st, 'sys')."' onclick='Study(\"$st\", \"$ISO\", \"ROD_Code\", \"$ScriptureURL\")'>";
						echo translate('Download', $st, 'sys')." ";
						switch ($Testament) {
							case "New Testament":				// NT
								echo translate('the New Testament', $st, 'sys');
								break;
							case "Old Testament":				// OT
								echo translate('the Old Testament', $st, 'sys');
								break;
							case "Bible":						// Bible
								echo translate('the Bible', $st, 'sys');
								break;
							default:							// ?????
								echo translate('what Testament?', $st, 'sys');
								break;
						}
						switch ($alphabet) {
							case "Standard alphabet":			// standard alphabet
								break;
							case "Traditional alphabet":		// traditional alphabet
								echo " <span style='font-size: .8em; '>" . translate('(traditional alphabet)', $st, 'sys') . '</span>';
								break;
							case "New alphabet":				// new alphabet
								echo " <span style='font-size: .8em; '>" . translate('(new alphabet)', $st, 'sys') . '</span>';
								break;
							default:							// ?????
								echo " <span style='font-size: .8em; '>" . translate('(what alphabet?)', $st, 'sys') . '</span>';
								break;
						}					
						echo "</div><span style='font-size: 1em; '>&nbsp;";
						// $statement
						echo translate('for use with the Bible study software', $st, 'sys');
						// $othersiteDescription
						// “ and ” wont work under 00i-SpecificLanguage.php!
						if ($Internet) {
							echo "&nbsp;</span><a href='$othersiteURL' style='font-size: 1em; ' title='The Word Windows software' target='_blank'><span class='lineAction'>&ldquo;The Word&rdquo;</span></a>";
						}
						else {
							echo "&nbsp;</span>&ldquo;The Word&rdquo;";
						}
						?>
						</td>
					</tr>
				<?php
			}
		}
	}

/*
	*************************************************************************************************************
		Are the any other books?
	*************************************************************************************************************
*/
	if ($other_titles) {
		$query="SELECT * FROM other_titles WHERE ISO_ROD_index = '$ISO_ROD_index' AND (download_video IS NULL OR trim(download_video) = '')";
		$result2=$db->query($query);
		if ($result2) {
			while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
				$other=trim($r2['other']);
				$other_title=trim($r2['other_title']);
				$other_PDF=trim($r2['other_PDF']);
				$other_audio=trim($r2['other_audio']);
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						if (!empty($other_PDF)) {
							echo "<div class='linePointer' onclick=\"window.open('./data/$ISO/PDF/$other_PDF')\"><img class='iconActions' src='../images/read-icon.jpg' alt='".translate('Books', $st, 'sys')."' title='".translate('Books', $st, 'sys')."' /></div>";
							echo "</td>";
							echo "<td>";
							echo "<div class='linePointer' onclick=\"window.open('./data/$ISO/PDF/$other_PDF')\" title='".translate('Read this title.', $st, 'sys')."'>".translate('Read', $st, 'sys');
						}
						else {
							echo "<div class='linePointer' onclick=\"window.open('./data/$ISO/audio/$other_audio')\"><img class='iconActions' src='../images/listen-icon.jpg' alt='".translate('Books', $st, 'sys')."' title='".translate('Books', $st, 'sys')."' /></div>";
							echo "</td>";
							echo "<td>";
							echo "<div class='linePointer' onclick=\"window.open('./data/$ISO/audio/$other_audio')\" title='".translate('Listen this title.', $st, 'sys')."'>".translate('Listen', $st, 'sys');
						}
						echo "&nbsp;$other:&nbsp;$other_title</div>";
						?>
					</td>
				</tr>
				<?php
			}
		}
	}

/*
	***********************************************************************************************************
		Does it have any more links? (table links)
	*************************************************************************************************************
*/
	if ($links && $Internet) {
		// This takes care of all of the rest of the links.
		$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND email = 0 AND map = 0 AND buy = 0 AND BibleIs = 0 AND YouVersion = 0 AND `Bibles_org` = 0 AND `GooglePlay` = 0 AND `GRN` = 0 ORDER BY URL";
		$result2=$db->query($query);
		if ($result2) {
			if ($Internet) {
				while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
					$company_title=trim($r2['company_title']);
					$company=trim($r2['company']);
					$URL=trim($r2['URL']);
					?>
					<tr>
						<td style='width: 45px; '>
							<?php
							if (preg_match('/onestory/i', $URL)) {
								echo "<div class='linePointer' onclick=\"window.open('$URL')\"><img class='iconActions' src='../images/onestory-icon.jpg' alt='OneStory' title='OneStory' />";
							}
							elseif (preg_match('/itunes/i', $URL) || preg_match('/\.apple\./i', $URL)) {
								echo "<div class='linePointer' onclick=\"window.open('$URL')\"><img class='iconActions' src='../images/iTunes-icon.jpg' alt='iTunes' title='iTunes' />";
							}
							elseif (preg_match('/\.facebook\./i', $URL)) {
								echo "<div class='linePointer' onclick=\"window.open('$URL')\"><img class='iconActions' src='../images/facebook-icon.jpg' alt='Facebook' title='Facebook' />";
							}
							elseif (preg_match('/\bdeaf\.?bible\./i', $URL)) {
								echo "<div class='linePointer' onclick=\"window.open('$URL')\"><img class='iconActions' src='../images/deaf_bible_icon.png' alt='Deaf Bible' title='Deaf Bible' />";
							}
							else {
								echo "<div class='linePointer' onclick=\"window.open('$URL')\"><img class='iconActions' src='../images/links-icon.jpg' alt='".translate('Link', $st, 'sys')."' title='".translate('Link', $st, 'sys')."' />";
							}
							echo "</div>";
						echo "</td>";
						echo "<td>";
							echo "<div class='linePointer' onclick=\"window.open('$URL')\" title='".translate('Link to the organization.', $st, 'sys')."'>".translate('Link', $st, 'sys')." : ";
							if ($company_title != "" && $company_title != NULL) {
								echo "$company_title: ";
							}
							echo "$company</div>";
							?>
						</td>
					</tr>
					<?php
				}
			}
		}
	}

/*
	*************************************************************************************************************
		Can it be displayed by eBible?
	*************************************************************************************************************
*/
	if ($eBible && $Internet) {
		$query="SELECT homeDomain, translationId FROM eBible_list WHERE ISO_ROD_index = '$ISO_ROD_index'";			// used to have vernacularTitle!
		$result2=$db->query($query);
		if ($result2->num_rows > 0) {
			$r2 = $result2->fetch_array(MYSQLI_ASSOC);
			$homeDomain=trim($r2['homeDomain']);
			$translationId=trim($r2['translationId']);
			if ($homeDomain == 'inscript.org') {
				//$translationTraditionalAbbreviation=trim($r2['translationTraditionalAbbreviation']);
				$publicationURL='eBible.org/find/details.php?id=' . $translationId;
			}
			else {
				$publicationURL=$homeDomain . '/' . $translationId;
			}
			
			/***************************************************************************
				using curl to test if the URL file exists
			***************************************************************************/
			$curl = curl_init("https://$publicationURL");															// get the curl status
			// don't fetch the actual page, you only want to check the connection is ok
			curl_setopt($curl, CURLOPT_NOBODY, true);
			// do request
			$curlResult = curl_exec($curl);
			// if request did not fail
			if ($curlResult !== false) {
				// if request was ok, check response code
				$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
				// If 404 is returned, then file is not found.
				if (strcmp($statusCode,"404") == 1) {
					//echo  'eBible: ' . $publicationURL . ' not found.';
				}
				else {
					//$vernacularTitle = trim($r2['vernacularTitle']);
					$PDFline = '';
					?>
					<tr>
						<td style='width: 45px; '>
							<?php
							echo "<div class='linePointer' onclick='eBibleClick()'><img class='iconActions' src='../images/eBible-icon.jpg' alt='".translate('Scripture Resources from eBible.org', $st, 'sys')."' title='".translate('Scripture Resources from eBible.org', $st, 'sys')."' /></div>";
						echo "</td>";
						echo "<td>";
							echo "<div class='linePointer' title='".translate('Scripture Resources from eBible.org', $st, 'sys')."' onclick='eBibleClick()'>".translate('Scripture Resources from eBible.org', $st, 'sys').'</div><br />';
							echo '<div id="eBibleClick">';
							echo '<br />';
							// start of eBible AJAX
							echo '<script>';
							echo 'eBibleShow("'.$publicationURL.'","'.$st.'","'.$mobile.'")';
							echo '</script>';
							echo '<div id="vernacularTitle" style="text-align: center; "></div>';
							echo '<div id="eBibleItems"></div>';
							echo '</div>';
							?>
						</td>
					</tr>
					<?php
				}
			}
		}
	}

/*
	*************************************************************************************************************
		Does it have an SIL link?
	*************************************************************************************************************
*/
	if ($SILlink && $Internet) {
		$URL = 'https://www.sil.org/resources/search/code/'.$ISO.'?sort_order=DESC&sort_by=field_reap_sortdate';
		?>
		<tr>
			<td style='width: 45px; '>
				<?php
				echo "<div class='linePointer' onclick=\"window.open('$URL')\"><img class='iconActions' src='../images/sil-icon.jpg' alt='".translate('Check SIL.org', $st, 'sys')."' title='".translate('Check SIL.org', $st, 'sys')."' /></div>";
			echo "</td>";
			echo "<td>";
				echo "<div class='linePointer' onclick=\"window.open('$URL')\" title='".translate('Check SIL.org', $st, 'sys')."'>".translate('Check SIL.org', $st, 'sys')." ".translate('for language and culture resources in this language.', $st, 'sys')."</div>";
				//if ($company_title != "" && $company_title != NULL) {
				//   echo "$company_title: ";
				//}
				//echo "SIL</a>";
				?>
			</td>
		</tr>
		<?php
	}

/*
	*************************************************************************************************************
		Does it have a map? (table links)
	*************************************************************************************************************
*/
	if ($links && $Internet) {
		$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND map = 1";
		$result2=$db->query($query);
		if ($result2) {
			while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
				$company_title=stripslashes(trim($r2['company_title']));
				$company=trim($r2['company']);
				$URL=trim($r2['URL']);
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						echo "<div class='linePointer' onclick=\"window.open('$URL')\"><img class='iconActions' src='../images/globe-icon.png' alt='".translate('Map', $st, 'sys')."' title='".translate('Map', $st, 'sys')."' /></div>";
					echo "</td>";
					echo "<td>";
						echo "<div class='linePointer' onclick=\"window.open('$URL')\" title='".translate('Link to the organization.', $st, 'sys')."'>".translate('Link', $st, 'sys')." : ";
						if ($company_title != "" && $company_title != NULL) {
							if ($company_title == 'language map') {
								echo translate('language map', $st, 'sys').': ';
							}
							else {
								echo "$company_title: ";
							}
						}
						echo "$company</div>";
						?>
					</td>
				</tr>
				<?php
			}
		}
	}

/*
	*************************************************************************************************************
		Does it have an email link? (table links)
	*************************************************************************************************************
*/
	if ($links && $Internet) {
		$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND email = 1";
		$result2=$db->query($query);
		if ($result2) {
			while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
				$company_title=stripslashes(trim($r2['company_title']));
				//$company=trim($r2['company']);
				$URL=trim($r2['URL']);
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						echo "<img class='iconActions' src='../images/email-icon.jpg' alt='Email' title='Email' />";
					echo "</td>";
					echo "<td>";
						echo "<div title='Email'>$company_title : <a style='text-decoration: none; border-bottom: 2px solid red; ' href='mailto:$URL'>$URL</a></div>";
						?>
					</td>
				</tr>
				<?php
			}
		}
	}

?>
</table>
<br />
</div>
</div>

<script>
	function iOSAssetPackage(URL) {
		if (URL.startsWith("http://") || URL.startsWith("https://")) {
			if (URL.endsWith(".zip")) {
				const link = document.createElement("a");
				link.href = URL;
				link.download = URL.substr(URL.lastIndexOf('/')+1);
				link.click();
			}
			else {
				window.open(URL, '_blank');
			}
		}
		else if (URL.startsWith("asset://")) {
			//			URL = URL.replace("asset://", "https://");
			const link = document.createElement("a");
			link.href = URL;
			link.download = URL.substr(URL.lastIndexOf('/')+1);
			link.click();
		}
		else {
			alert('This isnt suppose to happen! (iOSAssetPackage)');
		}
	}
    function SAB_OT() {
        var x = document.getElementById("OT_SAB_Book").selectedIndex;
        var y = document.getElementById("OT_SAB_Book").options;
        if (y[x].text != "<?php echo translate('Choose One...', $st, 'sys'); ?>") {
        	//GoSAB("< ?php echo $ISO; ?>", document.form_OT_SAB_Books.OT_SAB_Book.value);
        	GoSAB("<?php echo $ISO; ?>", y[x].value);
		}
    }
	function SAB_NT() {
        var x = document.getElementById("NT_SAB_Book").selectedIndex;
        var y = document.getElementById("NT_SAB_Book").options;
        if (y[x].text != "<?php echo translate('Choose One...', $st, 'sys'); ?>") {
            //GoSAB("< ?php echo $ISO; ?>", document.form_NT_SAB_Books.NT_SAB_Book.value);
            GoSAB("<?php echo $ISO; ?>", y[x].value);
        }
    }
    function SAB_Scriptoria_Index(subfolder) {
        //var x = document.getElementById("SAB_Book"+index).selectedIndex;
        //var y = document.getElementById("SAB_Book"+index).options;
		//if (y[x].text != "< ?php echo translate('Choose One...', $st, 'sys'); ?>") {
			window.open("./data/<?php echo $ISO; ?>/" + subfolder + "index.html", "SABPage");
		//}
	}
    function SAB_Scriptoria_Other(url) {
		window.open(url, "SABLink");
	}
    function SAB_Scriptoria(index, subfolder) {
        var x = document.getElementById("SAB_Book_"+index).selectedIndex;
        var y = document.getElementById("SAB_Book_"+index).options;
        if (y[x].text != "<?php echo translate('Choose One...', $st, 'sys'); ?>") {
			//GoSAB_subfolder("< ?php echo $ISO; ?>", document.form_SAB_Books.SAB_Book.value, subfolder);
			GoSAB_subfolder("<?php echo $ISO; ?>", y[x].value, subfolder);
		}
		else {
			window.open("./data/<?php echo $ISO; ?>/" + subfolder + "index.html", "SABPage");
		}
	}
</script>

<script type='text/javascript' language='javascript' src='_js/user_events.js'></script>
