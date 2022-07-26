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
</style>

<?php
// Created by Scott Starker

$mobile=0;

/*
	*******************************************************************************************************************
		select the default primary language name to be used by displaying the Countries and indigenous langauge names
	*******************************************************************************************************************
*/
$query = "SELECT DISTINCT scripture_main.*, $SpecificCountry, countries.ISO_Country FROM scripture_main, countries, ISO_countries WHERE countries.ISO_Country = ISO_countries.ISO_countries AND ISO_countries.ISO_ROD_index = scripture_main.ISO_ROD_index AND scripture_main.ISO_ROD_index = '$ISO_ROD_index'";
$result=$db->query($query) or die (translate('Query failed:', $st, 'sys') . ' ' . $db->error . '</body></html>');
if (!$result) {
	die ("&ldquo;$ISO&rdquo; " . translate('is not found.', $st, 'sys') . '</body></html>');
}
$rowSM = $result->fetch_array(MYSQLI_ASSOC);
//$ISO=$rowSM['ISO'];
//$ROD_Code=$rowSM['ROD_Code'];
//$Variant_Code=$rowSM['Variant_Code'];
$ISO_Country=$rowSM['ISO_Country'];				// oountry = ZZ
$GetName = $ISO_Country;
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

$i=0;											// used in 00-DBLanguageCountryName.inc.php include
//include ('./00-DBLanguageCountryName.inc.php');
?>

<!--link href="jQuery.jPlayer/jPlayer-2.9.2/dist/skin/blue.monday/css/jplayer.blue.monday.min.css" rel="stylesheet" type="text/css" /-->
<!--link href="_css/css/jplayer.pink.flag.min.css" rel="stylesheet" type="text/css" /-->
<style type="text/css">
	/*option.speaker { background-image: url(../images/SAB-speaker-icon.jpg); background-repeat: no-repeat; }*/
</style>

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
    <div style='margin: 0 auto 8px auto; width: 80%; '>
        <div class='alternativeLanguageNames' style='width: 100%; '><?php echo translate('Alternative Language Names:', $st, 'sys'); ?>
            <span class='alternativeLanguageName'>
            <?php
            $i_alt=0;
            while ($r_alt = $result_alt->fetch_array(MYSQLI_ASSOC)) {
                if ($i_alt != 0) {
                    echo ', ';
                }
                $alt_lang_name=trim($r_alt['alt_lang_name']);
                $alt_lang_name = htmlspecialchars($alt_lang_name, ENT_QUOTES, 'UTF-8');
                echo $alt_lang_name;
                $i_alt++;
            }
            ?>
			</span>
        </div>
    </div>
    <?php
}

/*
	*************************************************************************************************************
		Get the name(s) of the country(ies).
	*************************************************************************************************************
*/
$query="SELECT $SpecificCountry, ISO_countries FROM ISO_countries, countries WHERE ISO_countries.ISO_ROD_index = '$ISO_ROD_index' AND ISO_countries.ISO_countries = countries.ISO_Country";
$result_ISO_countries=$db->query($query);
//$num_ISO_countries=mysql_num_rows($result_ISO_countries);
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

<div style='margin: 0 auto; width: 80%; '>
    <div style='width: 100%; '>
        <div class='Country' style='margin-bottom: 8px; '><?php echo translate('Country:', $st, 'sys'); ?>&nbsp;<span class='Country'><?php echo $country; ?></span></div>
        <div class='languageCode'><?php echo translate('Language Code:', $st, 'sys'); ?>&nbsp;<?php echo $ISO; ?></div>
    </div>
</div>
<br />
&nbsp;<br />

<?php
$Internet = 0;		// localhost is 127.0.0.1 but "192.168.x.x" should be not-on-the-Internet because it's URL is part of the stand-alone server.
$Internet = (substr($_SERVER['REMOTE_ADDR'], 0, 7) != "192.168" ? 1 : 0);
?>
<table id='individualLanguage'>
<?php
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
								switch ($def_LN_Interest) {
									case 1:
										$query="SELECT LN_English FROM LN_English WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
										$result_LN=$db->query($query);
										$row_LN = $result_LN->fetch_array(MYSQLI_ASSOC);
										$LN=trim($row_LN['LN_English']);
										break;
									case 2:
										$query="SELECT LN_Spanish FROM LN_Spanish WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
										$result_LN=$db->query($query);
										$row_LN = $result_LN->fetch_array(MYSQLI_ASSOC);
										$LN=trim($row_LN['LN_Spanish']);
										break;
									case 3:
										$query="SELECT LN_Portuguese FROM LN_Portuguese WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
										$result_LN=$db->query($query);
										$row_LN = $result_LN->fetch_array(MYSQLI_ASSOC);
										$LN=trim($row_LN['LN_Portuguese']);
										break;	
									case 4:
										$query="SELECT LN_French FROM LN_French WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
										$result_LN=$db->query($query);
										$row_LN = $result_LN->fetch_array(MYSQLI_ASSOC);
										$LN=trim($row_LN['LN_French']);
										break;	
									case 5:
										$query="SELECT LN_Dutch FROM LN_Dutch WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
										$result_LN=$db->query($query);
										$row_LN = $result_LN->fetch_array(MYSQLI_ASSOC);
										$LN=trim($row_LN['LN_Dutch']);
										break; 	
									case 6:
										$query="SELECT LN_German FROM LN_German WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
										$result_LN=$db->query($query);
										$row_LN = $result_LN->fetch_array(MYSQLI_ASSOC);
										$LN=trim($row_LN['LN_German']);
										break; 	
									default:
										echo 'This isn’t supposed to happen! The "Goto" language name isn’t found.';
										break;
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
	
	$temp_Book_Chapter_HTML = '';
	$subfolder = '';
	$query="SELECT Book_Chapter_HTML FROM SAB WHERE ISO_ROD_index = '$ISO_ROD_index' LIMIT 1";	// test to see if OT is there
	$result2=$db->query($query);
	$num=$result2->num_rows;
	if ($result2 && $num > 0) {
		$r_array = $result2->fetch_array(MYSQLI_ASSOC);
		$Book_Chapter_HTML = trim($r_array['Book_Chapter_HTML']);
		$temp_Book_Chapter_HTML = preg_replace('/^([a-z]{3}[A-Z0-9]*-).*/', '$1' , $Book_Chapter_HTML);		// gives ISO ( + year date or LETTERS)
		if ($temp_Book_Chapter_HTML == $Book_Chapter_HTML) $temp_Book_Chapter_HTML = '';
	}

	$SAB_OT_lists = [];
	$SAB_NT_lists = [];
	
	if (strlen($temp_Book_Chapter_HTML) > 3) {
		$subfolder = rtrim($temp_Book_Chapter_HTML, '-');
		if (is_dir("./data/$ISO/sab/$subfolder") == 0) {
			$subfolder = '';
		}
	}
	
	if ($temp_Book_Chapter_HTML != '' && file_exists("./data/$ISO/sab/js/".$temp_Book_Chapter_HTML."book-names.js")) {	// not on the PHP server but my office/home oomputer
		$SAB_Read = file("./data/$ISO/sab/js/".$temp_Book_Chapter_HTML."book-names.js");	// read the '[ISO][SABnum]-book-names.js' file
		foreach ($SAB_Read as $line_num => $line) {										// read through the lines
			$l = '';
			$l = preg_replace('/.*-([0-9]{2}-[A-Z0-9][A-Z]{2})-.*/', "$1", $line);		// set $l to e.g. '01-GEN'
			if ($l == $line) continue;													// if not $ln = $line
			$ln = (int)substr($l, 0, 2);												// get the number of the book"
			if ($ln > 0 && $ln <= 39) {							// OT books				// if the book from the OT
				$SAB_OT_lists[] = $ln;
			}
			elseif ($ln >= 41 && $ln <= 66) {					// NT books				// if the book from the NT
				$SAB_NT_lists[] = $ln;
			}
			else {
				// this line perminently left empty
			}
		}
	}
	
	if ($subfolder == '') {
		/*
			 OT Scripture App Builder (SAB) HTML
		*/
		$query="SELECT ISO_ROD_index FROM SAB WHERE ISO_ROD_index = '$ISO_ROD_index' AND SAB_Book <= 39 LIMIT 1";	// test to see if OT is there
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
					echo "<a href='#' onclick='GoSAB(\"$ISO\", document.form_OT_SAB_Books.OT_SAB_Book.value, \"$subfolder\")'><img class='iconActions' ";
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
					echo "/></a>";
				echo "</td>";
				echo "<td>";
					?>
					<div class='SABReadListen'>
						<?php
						echo "<a href='#' onclick='GoSAB(\"$ISO\", document.form_OT_SAB_Books.OT_SAB_Book.value, \"$subfolder\")'><span ";
						if ($SAB & 2) {
							echo "class='lineAction'>" . translate('Text with audio', $st, 'sys') . "</span></a>:";
						}
						else if ($SAB & 8) {
							echo "class='lineAction'>" . translate('Text (with audio where available)', $st, 'sys') . "</span></a>:";
						}
						else {		// $SAB & 32
							echo "class='lineAction'>" . translate('View text', $st, 'sys') . "</span></a>:";
						}
						?>
						<div id='OTSABSelects' style='display: inline; '>
							<?php
							// Get and display Books
							$query_array="SELECT * FROM SAB WHERE ISO_ROD_index = '$ISO_ROD_index' AND SAB_Book = ? AND (Book_Chapter_HTML IS NOT null AND trim(Book_Chapter_HTML) <> '') ORDER BY Book_Chapter_HTML ASC";
							$stmt = $db->prepare($query_array);										// create a prepared statement
							echo "<form name='form_OT_SAB_Books' id='form_OT_SAB_Books' style='display: inline; '>";
							echo "<select id='OT_SAB_Book' name='OT_SAB_Book' class='selectOption' onchange='GoSAB(\"$ISO\", this.options[this.selectedIndex].value, \"$subfolder\")'>";
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
									$r_array = $result_array->fetch_array(MYSQLI_ASSOC);						// now you can fetch the results into an array for 'for' - NICE (as oppossed to bind_result)
									$OT_Book_Chapter_HTML = trim($r_array['Book_Chapter_HTML']);	// 1st chapter
									$SAB_Audio = $r_array['SAB_Audio'];								// is there audio in the 1st chapter?
									echo "<option id='OT_SAB_Book_${OT_SAB_a_index}' name='OT_SAB_Book_${OT_SAB_a_index}' class='speaker' value='${OT_Book_Chapter_HTML}'>".($SAB_Audio ? '&#128266; ' : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').$a."</option>";
								}
								$OT_SAB_a_index++;
							}
							echo "</select>";
							echo "</form>";
							$stmt->close();															// close statement
							?>
						</div>
					</div>
				</td>
			 </tr>
			<?php
		}
		
		/*
			 NT Scripture App Builder (SAB) HTML
		*/
		$query="SELECT ISO_ROD_index FROM SAB WHERE ISO_ROD_index = '$ISO_ROD_index' AND SAB_Book >= 41 LIMIT 1";	// test to see if NT is there
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
					echo "<a href='#' onclick='GoSAB(\"$ISO\", document.form_NT_SAB_Books.NT_SAB_Book.value, \"$subfolder\")'><img class='iconActions' ";
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
					echo "/></a>";
				echo "</td>";
				echo "<td>";
					?>
					<div class='SABReadListen'>
						<?php
						echo "<a href='#' onclick='GoSAB(\"$ISO\", document.form_NT_SAB_Books.NT_SAB_Book.value, \"$subfolder\")'><span ";
						if ($SAB & 1) {
							echo "class='lineAction'>" . translate('Text with audio', $st, 'sys') . "</span></a>:";
						}
						else if ($SAB & 4) {
							echo "class='lineAction'>" . translate('Text (with audio where available)', $st, 'sys') . "</span></a>:";
						}
						else {		// $SAB & 16
							echo "class='lineAction'>" . translate('View text', $st, 'sys') . "</span></a>:";
						}
						?>
						<div id='NTSABSelects' style='display: inline; '>
							<?php
							$query_array="SELECT * FROM SAB WHERE ISO_ROD_index = '$ISO_ROD_index' AND SAB_Book = ? AND (Book_Chapter_HTML IS NOT null AND trim(Book_Chapter_HTML) <> '') ORDER BY Book_Chapter_HTML ASC";
							$stmt = $db->prepare($query_array);										// create a prepared statement
							echo "<form name='form_NT_SAB_Books' id='form_NT_SAB_Books' style='display: inline; '>";
							echo "<select id='NT_SAB_Book' name='NT_SAB_Book' class='selectOption' onchange='GoSAB(\"$ISO\", this.options[this.selectedIndex].value, \"$subfolder\")'>";
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
							$stmt->close();																// close statement
							?>
						</div>
					</div>
				</td>
			</tr>
			<?php
		}
	}
	else {
        echo '<tr>';
            echo '<td>';
                echo "<a href='#' onclick='window.open(\"data/$ISO/sab/$subfolder/index.html\", \"SABPage\");'><img class='iconActions' ";
                echo "src='../images/SAB-readListen-icon.png' alt='".translate('Text (with audio and video where available)', $st, 'sys')."' title='".translate('Text (with audio and video where available)', $st, 'sys')."'";
                echo "/></a>";
            echo '</td>';
            echo '<td>';
				echo "<a href='#' onclick='window.open(\"data/$ISO/sab/$subfolder/index.html\", \"SABPage\");'><span ";
				echo "class='lineAction'>" . translate('Text (with audio and video where available)', $st, 'sys') . "</span></a>";
			echo '</td>';
		echo '</tr>';
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
			//$BibleIsLink=trim($r_links['BibleIs']);
			$BibleIsVersion=trim($r_links['company_title']);
			$temp_RLW = translate('Read and Listen', $st, 'sys');
			//if (
			?>
			<tr>
				<td>
					<?php
					echo "<a href='#' onclick='LinkedCounter(\"BibleIs_".$counterName."_".$GetName."_".$ISO."\", \"".$URL."\")'><img class='iconActions' src='../images/BibleIs-icon.jpg' alt='".translate('Read and Listen', $st, 'sys')."' title='".translate('Read and Listen', $st, 'sys')."' />";
					echo "</a>";
				echo "</td>";
				echo "<td>";
					echo "<a href='#' onclick='LinkedCounter(\"BibleIs_".$counterName."_".$GetName."_".$ISO."\", \"".$URL."\")'><span class='lineAction'>" . translate('Read and Listen', $st, 'sys') . "</span> ";
					//if (stripos($URL, '/Gen/') !== false)
					/*if ($BibleIsLink == 1)
						echo translate('to the New Testament', $st, 'sys');
					else if ($BibleIsLink == 2)
						echo translate('to the Old Testament', $st, 'sys');
					else	// $BibleIs == 3
						echo translate('to the Bible', $st, 'sys');*/
					echo translate('on Bible.is', $st, 'sys');
					echo "</a>";
					if ($BibleIsVersion!='') {
						echo ' ' . $BibleIsVersion;
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
		Can it be viewed? (if not (Bible.is && YouVersion && Bibles.org && "Text with audio") then do not display "viewer")
	*************************************************************************************************************
*/
if ($viewer && !($YouVersion && $Biblesorg && $SAB && $BibleIs) && $Internet) {
	$ROD_Var='';
	$rtl = 0;
	$query="SELECT viewer_ROD_Variant, rtl FROM viewer WHERE ISO_ROD_index = '$ISO_ROD_index' AND Variant_Code = '$Variant_Code'";						// check if there is a viewer
	$resultViewer=$db->query($query);
	if ($resultViewer) {
		//if (mysql_num_rows($resultViewer) > 0) {
			//$numViewer=mysql_num_rows($resultViewer);
			//if ($numViewer > 0) {
				$r_Viewer = $resultViewer->fetch_array(MYSQLI_ASSOC);
				$ROD_Var=trim($r_Viewer['viewer_ROD_Variant']);
				$rtl=trim($r_Viewer['rtl']);
			//}
		//}
	}
	?>
	<tr>
		<td style='width: 45px; '>
			<?php
			echo "<a href='./viewer/views.php?iso=$ISO&ROD_Code=$ROD_Code&Variant_Code=$Variant_Code&ROD_Var=$ROD_Var&rtl=$rtl&st=$st' title='".translate('Viewer for the Language Name', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' />";
			echo "</a>";
		echo "</td>";
		echo "<td>";
			echo "<a href='./viewer/views.php?iso=$ISO&ROD_Code=$ROD_Code&Variant_Code=$Variant_Code&ROD_Var=$ROD_Var&rtl=$rtl&st=$st' title='".translate('Viewer for the Language Name', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Go to', $st, 'sys')."</span> ".translate('the online viewer', $st, 'sys')."</a>";
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
//if (!$BibleIs) {
	$SB_PDF=0;														// boolean
	$query_SB="SELECT Item, Scripture_Bible_Filename FROM Scripture_and_or_Bible WHERE ISO_ROD_index = '$ISO_ROD_index'";		// then look to the Scripture_and_or_Bible table
	$result_SB=$db->query($query_SB);
	if (!$result_SB)
		$SB_PDF = 0;
	else
		$SB_PDF=$result_SB->num_rows;
	if ($NT_PDF > 0 || $OT_PDF > 0 || $SB_PDF > 0) {				// if it is 1 then
		if ($SB_PDF > 0) {
			//$i_SB=0;
			while ($r_SB = $result_SB->fetch_array(MYSQLI_ASSOC)) {
				?>
				<tr>
					<td>
						<?php
						$Item = $r_SB['Item'];
						if ($Item == 'B') {
							$whole_Bible=trim($r_SB['Scripture_Bible_Filename']);
							echo "<a href='./data/$ISO/PDF/$whole_Bible' title='".translate('Read the Bible.', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')." (PDF)"."' title='".translate('Read', $st, 'sys')."' />";
							echo "</a>";
							echo "</td>";
							echo "<td>";
							echo "<a href='./data/$ISO/PDF/$whole_Bible' title='".translate('Read the Bible.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('the Bible', $st, 'sys')." (PDF)"."</a>";
						}
						else {
							$complete_Scripture=trim($r_SB['Scripture_Bible_Filename']);
							echo "<a href='./data/$ISO/PDF/$complete_Scripture' title='".translate('Read a Scripture portion.', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/read-icon.jpg' alt='".translate('Read', $st, 'sys')." (PDF)"."' title='".translate('Read', $st, 'sys')."' />";
							echo "</a>";
							echo "</td>";
							echo "<td>";
							echo "<a href='./data/$ISO/PDF/$complete_Scripture' title='".translate('Read a Scripture portion.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('a Scripture portion', $st, 'sys')." (PDF)"."</a>";
						}
						?>
					</td>
				</tr>
				<?php
				//$i_SB++;
			}
		}
		if ($OT_PDF) {
			$query="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = 'OT'";		// check if there is a OT
			$result1=$db->query($query);
			$num=$result1->num_rows;
			if ($result1 && $num > 0) {
				if ($r1 = $result1->fetch_array(MYSQLI_ASSOC)) {
					$OT_PDF_Filename = trim($r1['OT_PDF_Filename']);									// there is a OT
					?>
					<tr>
						<td>
							<?php
							echo "<a href='./data/$ISO/PDF/$OT_PDF_Filename' title='".translate('Read the Old Testament.', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/read-icon.jpg' alt='".translate('Read', $st, 'sys')." (PDF)"."' title='".translate('Read', $st, 'sys')."' />";
							echo "</a>";
						echo "</td>";
						echo "<td>";
							echo "<a href='./data/$ISO/PDF/$OT_PDF_Filename' title='".translate('Read the Old Testament.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('the Old Testament', $st, 'sys')." (PDF)"."</a>";
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
						//$i=0;
						$a_index = 0;
						?>
						<tr>
							<td>
								<?php
								echo "<img class='iconActions' src='../images/read-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' />";
							echo "</td>";
							echo "<td>";
								echo "<form name='PDF_OT' id='PDF_OT'>";
								echo "<span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('a book from the Old Testament:', $st, 'sys');
								if (isset($mobile) && $mobile == 1) {
									echo "<br />";
								}
								else {
									echo " ";
								}
								//echo "<select class='selectOption' name='OT_PDF' id='OT_PDF' onchange='OT_PDF_Change()'>";
								echo "<select class='selectOption' name='OT_PDF' onchange='if (this.options[this.selectedIndex].text != \"".translate('Choose One...', $st, 'sys')."\") { window.open(this.options[this.selectedIndex].value, \"_blank\"); }'>";
								echo "<option>".translate('Choose One...', $st, 'sys')."</option>";
								$query_array="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = ?";
								$stmt = $db->prepare($query_array);										// create a prepared statement
								foreach ($OT_array[OT_EngBook] as $a) {									// there is/are book(s)
									//$query_array="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = '$a_index'";
									//$result_array=$db->query($query_array);
									$stmt->bind_param("i", $a_index);									// bind parameters for markers								// 
									$stmt->execute();													// execute query
									$result_array = $stmt->get_result();								// instead of bind_result (used for only 1 record):
									//$num=mysql_num_rows($result_array);
									if ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {
										$OT_PDF = $r_array['OT_PDF'];
										while ($OT_PDF !== null && !is_numeric($OT_PDF)) {									// important: not 'int' but 'numeric'! Any number and except not 'OT' ('OT' which is 0 in bind_param("i", $a_index)), etc.
											$r_array = $result_array->fetch_array(MYSQLI_ASSOC);
											$OT_PDF = $r_array['OT_PDF'];
										}
										$OT_PDF_Filename = trim($r_array['OT_PDF_Filename']);
										$a = str_replace(" ", "&nbsp;", $a);
										if (!empty($OT_PDF_Filename)) {
											echo "<option id='OT_PDF_Media_$a' value='./data/$ISO/PDF/$OT_PDF_Filename'>$a</option>";
										}
									}
									$a_index++;
								}
								$stmt->close();															// close statement
								$query_array="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = '100'";	// appendice
								$result_array=$db->query($query_array);
								//$num=mysql_num_rows($result_array);
								if ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {
									$OT_PDF_Filename = trim($r_array['OT_PDF_Filename']);
									if (!empty($OT_PDF_Filename)) {
										echo "<option value='./data/$ISO/PDF/$OT_PDF_Filename'>".translate('Appendix', $st, 'sys')."</option>";
									}
								}
								$query_array="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = '101'";	// glossary
								$result_array=$db->query($query_array);
								//$num=mysql_num_rows($result_array);
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
					$NT_PDF_Filename = trim($r_NT['NT_PDF_Filename']);								// there is a NT
					?>
					<tr>
						<td>
							<?php
							echo "<a href='./data/$ISO/PDF/$NT_PDF_Filename' title='".translate('Read the New Testament.', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/read-icon.jpg' alt='".translate('Read', $st, 'sys')." (PDF)"."' title='".translate('Read', $st, 'sys')."' />";
							echo "</a>";
						echo "</td>";
						echo "<td>";
							echo "<a href='./data/$ISO/PDF/$NT_PDF_Filename' title='".translate('Read the New Testament.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('the New Testament', $st, 'sys')." (PDF)"."</a>";
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
						//$i=0;
						$a_index = 0;
						?>
						<tr>
							<td>
								<?php
								echo "<img class='iconActions' src='../images/read-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' />";
							echo "</td>";
							echo "<td>";
								echo "<form name='PDF_NT'>";
								echo "<span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('a book from the New Testament:', $st, 'sys');
								if (isset($mobile) && $mobile == 1) {
									echo "<br />";
								}
								else {
									echo " ";
								}
								echo "<select class='selectOption' name='NT_PDF' onchange='if (this.options[this.selectedIndex].text != \"".translate('Choose One...', $st, 'sys')."\") { window.open(this.options[this.selectedIndex].value, \"_blank\"); }'>";
								echo "<option>".translate('Choose One...', $st, 'sys')."</option>";
								$query_array="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF = ?";
								$stmt = $db->prepare($query_array);										// create a prepared statement
								foreach ($NT_array[NT_EngBook] as $a) {									// there is/are book(s) (from NT_Books.php)
									//$query_array="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF = '$a_index'";
									//$result_array=$db->query($query_array);
									$stmt->bind_param("i", $a_index);									// bind parameters for markers								// 
									$stmt->execute();													// execute query
									$result_array = $stmt->get_result();								// instead of bind_result (used for only 1 record):
									//$num=mysql_num_rows($result_array);
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
								//$num=mysql_num_rows($result_array);
								if ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {
									$NT_PDF_Filename = trim($r_array['NT_PDF_Filename']);
									if (!empty($NT_PDF_Filename)) {
										echo "<option value='../data/$ISO/PDF/$NT_PDF_Filename'>".translate('Appendix', $st, 'sys')."</option>";
									}
								}
								$query_array="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF = '201'";	// glossary
								$result_array=$db->query($query_array);
								//$num=mysql_num_rows($result_array);
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
//}

/*
	*************************************************************************************************************
		Is it GooglePlay? (table links)
	*************************************************************************************************************
*/
if ($links) {
	$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND GooglePlay = 1";
	$result2=$db->query($query);
	if ($result2) {
		//$num=mysql_num_rows($result2);
		//$i=0;
		while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
			$company_title=trim($r2['company_title']);
			$company=trim($r2['company']);
			$URL=trim($r2['URL']);
			?>
			<tr>
				<td style='width: 45px; '>
					<?php
					echo "<a href='$URL' title='".translate('Link to organization.', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/Google_Play-icon.jpg' alt='".translate('Google Play', $st, 'sys')."' title='".translate('Google Play', $st, 'sys')."' />";
					echo "</a>";
				echo "</td>";
				echo "<td>";
					echo "<a href='$URL' title='".translate('Link to organization.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Link', $st, 'sys')."</span> : ";
					if ($company_title != "" && $company_title != NULL) {
						echo "$company_title: ";
					}
					echo "$company</a>";
					?>
				</td>
			</tr>
			<?php
			//$i++;
		}
	}
}

/*
	*************************************************************************************************************
		Is it a cell phone module = Android App (apk)?
	*************************************************************************************************************
*/
	/*
		GoBible (Java)
		MySword (Android)
		iPhone
		Windows
		Blackberry
		Standard Cell Phone
		Android App (apk)
	*/
if ($CellPhone) {
	$query="SELECT * FROM CellPhone WHERE ISO_ROD_index = '$ISO_ROD_index' AND Cell_Phone_Title = 'Android App'";
	$result2=$db->query($query);
	if ($result2) {
$c = 0;
		while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
			$Cell_Phone_Title=$r2['Cell_Phone_Title'];
			$Cell_Phone_File=trim($r2['Cell_Phone_File']);
			$optional=trim($r2['optional']);
			$pos = strpos($Cell_Phone_File, '://');															// check to see if "://" is present (https;//zzzzz)
$c++;
//echo $c . ') pos: ' . $pos . '#<br />';
			if ($pos === false) {
//echo $c . ') file_exists: ' . file_exists('./data/' . $ISO . '/study/' . $Cell_Phone_File) . '#<br />';
				if (!file_exists('./data/' . $ISO . '/study/' . $Cell_Phone_File)) {
					$matches = [];
					preg_match('/(.*-)[0-9.]+\.apk/', $Cell_Phone_File, $matches);							// SE (keep track of everything but the number)
//echo $c . ') Cell_Phone_File: ' . $Cell_Phone_File . '; apk: ' . $matches[1] . '#<br />';
					//echo $matches[1] . '<br />
					$list = [];
					$list = glob('./data/' . $ISO . '/study/' . $matches[1] . '*.apk');						// server (glob = find a file based on wildcards)
					if (empty($list)) {
						echo 'WARNING: Android App (apk) downloadable cell phone file is not there!';
					}
					else {
						//echo $list[0] . '<br />';
						$matches = [];
						preg_match('/.*\/(.*\.apk)/', $list[0], $matches);									// server
//echo 'count: ' . count($list) . '<br />';
//for ($d=0; $d < count($list); $d++) {
//	echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$d.') '.$list[$d]. '<br />';
//}
						//echo $matches[1] . '<br />';
						if (empty($matches)) {
							echo 'WARNING: Android App (apk) downloadable cell phone file is not there!';
						}
						else {
							$Cell_Phone_File = $matches[1];
							if (file_exists('./data/' . $ISO . '/study/' . $Cell_Phone_File)) {
								//$db->query("UPDATE CellPhone SET Cell_Phone_File = '$Cell_Phone_File' WHERE ISO_ROD_index = '$ISO_ROD_index' AND Cell_Phone_Title = 'Android App'");
echo $c . ') would have UPDATE CellPhone<br />';
							}
							else {
								echo 'WARNING: Android App (apk) downloadable cell phone file is not there!';
							}
						}
					}
				}
				else {
					// file exists so don't do anything
				}
			}
			?>
			<tr>
				<td style='width: 45px; '>
					<?php
					echo "<div class='linePointer' title='" . translate('Download the app for', $st, 'sys') . " $Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><img class='iconActions' src='../images/android_module-icon.jpg' alt='".translate('Cell Phone', $st, 'sys')."' title='".translate('Cell Phone', $st, 'sys')."' />";
				echo "</td>";
				echo "<td>";
					echo "<div class='linePointer' title='" . translate('Download the app for', $st, 'sys') . " $Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><span class='lineAction'>" . translate('Download', $st, 'sys') . "</span> " . translate('the app for', $st, 'sys') . ' ' . ($Cell_Phone_Title == 'Android App' ? 'Android' : $Cell_Phone_Title);
				echo ' ' . $optional . '</div>';
					?>
				</td>
			</tr>
			<?php
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
				//$num2=mysql_num_rows($result2);
				//<div style='margin-left: 6px; '>
				if ($result2) {
					$OTNT = $NT_Audio + $OT_Audio;
					?>
					<tr>
					<td>
						<?php
						$OT_Book = array();
						$OT_Book_Chapter = array();
						$a_index = 0;
						echo "<div class='linePointer' title='".translate('Listen to the Old Testament.', $st, 'sys')."' onclick='ListenAudio(document.form_OT_Chapters_mp3.OT_Chapters_mp3, true, \"OTListenNow\", $OTNT)'><img  class='iconActions' src='../images/listen-icon.jpg' alt='".translate('Listen', $st, 'sys')."' title='".translate('Listen', $st, 'sys')."' />";
						echo '</div>';
					echo "</td>";
					echo "<td>";
					?>
					<div class='OTAudio'>
					<?php
					echo "<div class='linePointer' title='".translate('Listen to the Old Testament.', $st, 'sys')."' onclick='ListenAudio(document.form_OT_Chapters_mp3.OT_Chapters_mp3, true, \"OTListenNow\", $OTNT)'><span class='lineAction'>".translate('Listen', $st, 'sys')."</span></a> ".translate('to the audio Old Testament:', $st, 'sys');
					echo '</div>';
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
						//$query_array="SELECT * FROM OT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code' AND OT_Audio_Book = '$a_index' AND (OT_Audio_Filename IS NOT NULL AND trim(OT_Audio_Filename) <> '')";		// ISO_ROD_index = '$ISO_ROD_index'
						//$result_array=$db->query($query_array);
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
						//$query_array="SELECT * FROM OT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code' AND OT_Audio_Book = '$a_index' AND (OT_Audio_Filename IS NOT NULL AND trim(OT_Audio_Filename) <> '')";		// ISO_ROD_index = '$ISO_ROD_index'
						//$result_array=$db->query($query_array);
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
                                    <!--div class="jp-title">
                                        <ul>
                                            <li>NT - John 1</li>
                                        </ul>
                                    </div-->
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
		
			if ($NT_Audio) {
				$query="SELECT * FROM NT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code'";	// ISO_ROD_index = '$ISO_ROD_index'";
				$result2=$db->query($query);
				//$num2=mysql_num_rows($result2);
				if ($result2) {
					$OTNT = $NT_Audio + $OT_Audio;
					?>
					<tr>
					<td>
					<!--div style='margin-left: 6px; '-->
					<?php
					$NT_Book = array();
					$NT_Book_Chapter = array();
					$a_index = 0;
					echo "<div class='linePointer'' title='".translate('Listen to the New Testament.', $st, 'sys')."' onclick='ListenAudio(document.form_NT_Chapters_mp3.NT_Chapters_mp3, true, \"NTListenNow\", $OTNT)'><img class='iconActions' src='../images/listen-icon.jpg' alt='".translate('Listen', $st, 'sys')."' title='".translate('Listen', $st, 'sys')."' />";
					echo "</div>";
					echo "</td>";
					echo "<td>";
					?>
					<div class='NTAudio'>
					<?php
					echo "<div class='linePointer' title='".translate('Listen to the New Testament.', $st, 'sys')."' onclick='ListenAudio(document.form_NT_Chapters_mp3.NT_Chapters_mp3, true, \"NTListenNow\", $OTNT)'><span class='lineAction'>".translate('Listen', $st, 'sys')."</span></a> ".translate('to the audio New Testament:', $st, 'sys');
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
						//$query_array="SELECT * FROM NT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code' AND NT_Audio_Book = '$a_index' AND (NT_Audio_Filename is not null AND trim(NT_Audio_Filename) <> '')";		// ISO_ROD_index = '$ISO_ROD_index'
						//$result_array=$db->query($query_array);
						$stmt->bind_param("i", $a_index);											// bind parameters for markers								// 
						$stmt->execute();															// execute query
						$result_array = $stmt->get_result();										// instead of bind_result (used for only 1 record):
						$num_array=$result_array->num_rows;
						if ($result_array && $num_array > 0) {
							$NT_Book[] = $a_index;
							//$i=0;
							$j=(string)$a_index;
							while ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {						// display the chapters
								$NT_Audio_Filename = trim($r_array['NT_Audio_Filename']);
								if (!empty($NT_Audio_Filename)) {
									$NT_Audio_Chapter = trim($r_array['NT_Audio_Chapter']);
									$NT_Book_Chapter[$a_index][] = $NT_Audio_Chapter;
									$j = $j . "," . $NT_Audio_Chapter . "," . $NT_Audio_Filename;
								}
								//$i++;
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
						//$query_array="SELECT * FROM NT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code' AND NT_Audio_Book = '$a_index' AND (NT_Audio_Filename is not null AND trim(NT_Audio_Filename) <> '')";		// ISO_ROD_index = '$ISO_ROD_index'
						//$result_array=$db->query($query_array);
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
                                    <!--div class="jp-title">
                                        <ul>
                                            <li>NT - John 1</li>
                                        </ul>
                                    </div-->
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
					echo "<div class='linePointer' title='".translate('Download the audio Old Testament files.', $st, 'sys')."' onclick='OTTableClick()'><img class='iconActions' src='../images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
					echo "</div>";
				echo "</td>";
				echo "<td>";
					echo "<div class='linePointer' title='".translate('Download the audio Old Testament files.', $st, 'sys')."' onclick='OTTableClick()'><span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the Old Testament audio files (MP3)', $st, 'sys')."</div>";
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
						foreach ($OT_array[OT_EngBook] as $a) {																// display Eng. NT books
							//$query_array="SELECT * FROM OT_Audio_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_Audio_Book = '$a_index' AND (OT_Audio_Filename IS NOT NULL AND trim(OT_Audio_Filename) <> '')";
							//$result_array=$db->query($query_array);
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
								//$ii=0;
								while ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {						// display the chapters
									$OT_Audio_Filename = trim($r_array['OT_Audio_Filename']);
									if (file_exists("./data/$ISO/audio/$OT_Audio_Filename")) {
										$temp = filesize("./data/$ISO/audio/$OT_Audio_Filename");
										$temp = intval($temp/1024);			// MB
										$ZipFile += round($temp/1024, 2);
										$ZipFile = round($ZipFile, 1);
									}
									//$ii++;
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
			<div id='ntaudiofiles' class='ntaudiofiles' style=''>
			<tr>
				<td style='width: 45px; '>
					<?php
					echo "<div class='linePointer' title='".translate('Download the audio New Testament files.', $st, 'sys')."' onclick='NTTableClick()'><img class='iconActions' src='../images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
					echo "</div>";
				echo "</td>";
				echo "<td>";
					echo "<div class='linePointer' title='".translate('Download the audio New Testament files.', $st, 'sys')."' onclick='NTTableClick()'><span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the New Testament audio files (MP3)', $st, 'sys')."</div>";
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
							//$query_array="SELECT * FROM NT_Audio_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_Audio_Book = '$a_index' AND (NT_Audio_Filename IS NOT NULL AND trim(NT_Audio_Filename) <> '')";
							//$result_array=$db->query($query_array);
							$stmt->bind_param("i", $a_index);											// bind parameters for markers								// 
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
								//$ii = 0;
								while ($r_array = $result_array->fetch_array(MYSQLI_ASSOC)) {						// display the chapters
									$NT_Audio_Filename = trim($r_array['NT_Audio_Filename']);
									if (file_exists("./data/$ISO/audio/$NT_Audio_Filename")) {
										$temp = filesize("./data/$ISO/audio/$NT_Audio_Filename");
										$temp = intval($temp/1024);			// MB
										$ZipFile += round($temp/1024, 2);
										$ZipFile = round($ZipFile, 1);
									}
									//$ii++;
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
	//$num3=mysql_num_rows($result_Playlist);
	$z=0;
	$SEVideoPlaylist=10;
	while ($r_Playlist = $result_Playlist->fetch_array(MYSQLI_ASSOC)) {
		$PlaylistVideoTitle = $r_Playlist['PlaylistVideoTitle'];
		$PlaylistVideoFilename = $r_Playlist['PlaylistVideoFilename'];
		$SEVideoPlaylistIndex=0;
		$SEVideoPlaylistArray = [];
		
		//preg_match('/^([a-zA-Z]+)-/i', $PlaylistVideoFilename, $matches);		// get the left most letters before the '-'
		//$PLVideo = strtolower($matches[1]);									// to lower case
		$PLVideo = $PlaylistVideoFilename;
		$ISO_dialect = $ISO;
		//echo 'ISO = ' . $ISO . '<br />';

		/*******************************************************************************************************************
				set $PLVideo in order to set $PlaylistVideoTitle for the 'tool tip' (see about 100 lines below)
		********************************************************************************************************************/
// JESUSFilm-bzs.txt, etc.
		preg_match('/^[a-zA-Z]+-('.$ISO.'[a-zA-Z0-9]*)(-|\.)/', $PlaylistVideoFilename, $matches);	// get the ISO code and if there is anything attached before the '-'
		if (isset($matches[1])) {
			$ISO_dialect = $matches[1];
			//echo '1 ISO_dialect #' . $ISO_dialect . '#<br />';
			preg_match('/^([a-zA-Z]+)-/i', $PlaylistVideoFilename, $matches);	// get the left most letters before the '-'
			$PLVideo = strtolower($matches[1]);									// to lower case
			//echo '#' . $PLVideo . '#<br />';
		}
// bzj-ScriptureAnim.txt, etc.
		preg_match('/^('.$ISO.'[a-zA-Z0-9]*)(-|\.)/', $PlaylistVideoFilename, $matches);	// get the ISO code and if there is anything attached before the '-'
		if (isset($matches[1])) {
			$ISO_dialect = $matches[1];
			//echo '2 ISO_dialect #' . $ISO_dialect . '#<br />';
			preg_match('/-([a-zA-Z]+)\./i', $PlaylistVideoFilename, $matches);	// get the left most letters before the '-'
			$PLVideo = strtolower($matches[1]);									// to lower case
			//echo '#' . $PLVideo . '#<br />';
		}
		
		/*******************************************************************************************************************
				set $filename
		********************************************************************************************************************/
		$filename = './data/'.$ISO.'/video/' . substr($PlaylistVideoFilename, 0, strlen($PlaylistVideoFilename)-3) . $MajorCountryAbbr . '.txt';
		if (!file_exists($filename)) {
			//echo "The text file $filename does not exist.";
			//continue;
			$filename = './data/'.$ISO.'/video/' . $PlaylistVideoFilename;
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
		$BibleStory = '';
		$image = 0;																// just the ISO
		$bookName = '';
		$tempArray = [];
		
		$tempArray = explode("\t", $VideoConvertContents[1]);					// $Internet test
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
			//echo '#' . $PLVideo . '#<br />';
			$VT = '';																				// get everything after "-" from $PlaylistVideoTitle
			if (preg_match("/- *(.*)/", $PlaylistVideoTitle, $match)) {
				$VT = $match[1];
			}
			if (preg_match("/\t(.*) — /", $VideoConvertContents[0], $match)) {
				//echo '#' . $PLVideo . '#<br />';
				//echo '#' . $PlaylistVideoTitle . '#<br />';
				$PlaylistVideoTitle = $match[1];
				//echo '#' . $PlaylistVideoTitle . '#<br />';
			}
			else if (preg_match("/\t(.*)\t.*\timages/", $VideoConvertContents[0], $match)) {
				$PlaylistVideoTitle = $match[1];
			}
			else {
				// just $PlaylistVideoTitle
			}
			if ($VT != '') {																		// set everything after "-" to $PlaylistVideoTitle
				$PlaylistVideoTitle = $PlaylistVideoTitle . ' - ' . $VT;
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
				echo "<div class='linePointer' title='".translate('View', $st, 'sys')." $PlaylistVideoTitle' onclick='PlaylistVideo(orgVideoPixels_$z, \"PlaylistVideoNow_$z\", $mobile)'><img class='iconActions' src='../images/youtube-icon.jpg' alt='".translate('View', $st, 'sys')."' title='".translate('View', $st, 'sys')."' /></div>";
				?>
			</td>
			<td>
				<?php
				echo "<div class='linePointer' title='".translate('View', $st, 'sys')." $PlaylistVideoTitle' onclick='PlaylistVideo(orgVideoPixels_$z, \"PlaylistVideoNow_$z\", $mobile)'><span class='lineAction'>".translate('View', $st, 'sys')."</span> $PlaylistVideoTitle</div>";
				// Get and display Playlist
				?>
			</td>
		</tr>
		<tr id="PlaylistVideoNow_<?php echo $z; ?>" style="display: none; overflow: hidden; float: left; line-height: 0px; ">	<?php // The extra styles are for the mobile Andoid to work! (6/17/17) ?>
			<td style='width: 45px; '>&nbsp;
			</td>
			<td>
				<?php
				if ($BibleStory != 0) {
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
                    if (stripos($VideoConvertWithTab[3], 'http', 0) === 0) {									// returns 0 means 'http' starts at column 0. === needs to be this way (and not ==) because jPlayer wont work.
                    	echo "<div style='text-align: center; '><div style='cursor: pointer; display: inline; text-align: center; ' title='".$VideoName."'";
                        echo " onclick='window.open(\"".$VideoConvertWithTab[3]."\",\"_blank\")'>";
					/*
					}
					else {
						$SEVideoPlaylist++;
						$SEVideoPlaylistArray[$SEVideoPlaylistIndex] = $VideoConvertWithTab[1]. '|' . $VideoConvertWithTab[2] . '|' . $VideoConvertWithTab[3];
						echo " id='PV_".$SEVideoPlaylist."_".$SEVideoPlaylistIndex."' onclick='PlaylistVideo_$SEVideoPlaylist($SEVideoPlaylistIndex)'>";
						$SEVideoPlaylistIndex++;
					}*/
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
				}
				else {
					$SEVideoPlaylist++;
					$SEVideoPlaylistArray[$SEVideoPlaylistIndex] = $VideoConvertWithTab[1]. '|' . $VideoConvertWithTab[2] . '|' . $VideoConvertWithTab[3];
					$SEVideoPlaylistIndex++;
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
				//$VideoConvertWithTab = explode("\t", $VideoConvertContents[2]);								// split the line up by tabs
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
								//alert(totalNumber);
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
//		$SEVideoPlaylist++;
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
	//$num3=mysql_num_rows($result_Playlist);
	$z=0;
	$SEVideoPlaylist=0;
	while ($r_Playlist = $result_Playlist->fetch_array(MYSQLI_ASSOC)) {
		$PlaylistVideoTitle = $r_Playlist['PlaylistVideoTitle'];
		$PlaylistVideoFilename = $r_Playlist['PlaylistVideoFilename'];
		$SEVideoPlaylistIndex=0;
		$SEVideoPlaylistArray = [];

		//preg_match('/^([a-zA-Z]+)-/i', $PlaylistVideoFilename, $matches);			// get the left most letters before the '-'
		//$PLVideo = strtolower($matches[1]);										// to lower case
		$PLVideo = $PlaylistVideoFilename;
		$ISO_dialect = $ISO;
//echo 'ISO = ' . $ISO . '<br />';
// JESUSFilm-bzs.txt or
		preg_match('/^[a-zA-Z]+-('.$ISO.'[a-zA-Z0-9]*)(-|\.)/', $PlaylistVideoFilename, $matches);	// get the ISO code and if there is anything attached before the '-'
		if (isset($matches[1])) {
			$ISO_dialect = $matches[1];
//echo '1 ISO_dialect #' . $ISO_dialect . '#<br />';
			preg_match('/^([a-zA-Z]+)-/i', $PlaylistVideoFilename, $matches);		// get the left most letters before the '-'
			$PLVideo = strtolower($matches[1]);										// to lower case
//echo '#' . $PLVideo . '#<br />';
		}
// bzj-ScriptureAnim.txt
		preg_match('/^('.$ISO.'[a-zA-Z0-9]*)(-|\.)/', $PlaylistVideoFilename, $matches);	// get the ISO code and if there is anything attached before the '-'
		if (isset($matches[1])) {
			$ISO_dialect = $matches[1];
//echo '2 ISO_dialect #' . $ISO_dialect . '#<br />';
			preg_match('/-([a-zA-Z]+)(-|\.)/i', $PlaylistVideoFilename, $matches);	// get the left most letters before the '-'
			$PLVideo = strtolower($matches[1]);										// to lower case
//echo '#' . $PLVideo . '#<br />';
		}
		
		$filename = './data/'.$ISO.'/video/' . substr($PlaylistVideoFilename, 0, strlen($PlaylistVideoFilename)-3) . $MajorCountryAbbr . '.txt';
		if (!file_exists($filename)) {
		//$file_headers = @get_headers($filename);									// Fetches all 9 of the headers sent by the server in response to an HTTP request.
		//if ($file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0] == 'HTTP/1.1 302 Moved Temporarily') {
			//echo "The text file $filename does not exist.";
			//continue;
			$filename = './data/'.$ISO.'/video/' . $PlaylistVideoFilename;
		}
		else {
			$PlaylistVideoFilename = substr($PlaylistVideoFilename, 0, strlen($PlaylistVideoFilename)-3) . $MajorCountryAbbr . '.txt';
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
				echo "<div class='linePointer' title='".translate('Download', $st, 'sys')." $PlaylistVideoTitle' onclick='PlaylistVideo(orgVideoPixels_$z, \"PlaylistVideoDownload_$z\", $mobile)'><img class='iconActions' src='../images/MP4-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' /></div>";
				?>
			</td>
			<td>
				<?php
				echo "<div class='linePointer' title='".translate('Download', $st, 'sys')." $PlaylistVideoTitle' onclick='PlaylistVideo(orgVideoPixels_$z, \"PlaylistVideoDownload_$z\", $mobile)'><span class='lineAction'>".translate('Download', $st, 'sys')."</span> $PlaylistVideoTitle</div>";
				// Get and display Playlist Download
				?>
			</td>
		</tr>
		<?php // The extra styles are for the mobile Andoid to work! (6/17/17) ?>
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
							<div style="float: right; margin-top: 0; margin-left: 30px; margin-bottom: 4px; ">
								<?php
								echo "<input type='button' id='AllOrNoneVideo_${z}' style='font-size: 11pt; font-weight: bold; ' value='".translate('Select all', $st, 'sys')."' onclick='DownloadAllVideoPlaylistClick(\"$z\", \"".translate('Select all', $st, 'sys')."\", \"".translate('Unselect all', $st, 'sys')."\")' />";
								?>
							</div>
							<div style="float: right; margin-top: 0; margin-right: 15px; margin-bottom: 4px; ">
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
                            <td colspan='<?php echo $col_span; ?>' style='vertical-align: top; width: <?php echo $num_array_col; ?>; '>
                                <?php
                                $ZipFile = 0;
                                //$ii = 0;
                                $VideoConvertContents[$i] = str_replace("\r", "", $VideoConvertContents[$i]);	// Windows text files have a carrage return at the end.
                                $VideoConvertWithTab = explode("\t", $VideoConvertContents[$i]);				// split the line up by tabs for txt file
                                $VideoPlaylist_Download_Filename = $VideoConvertWithTab[3];						// = mp4 file names
                                if (file_exists($VideoPlaylist_Download_Filename)) {
                                    $temp = filesize($VideoPlaylist_Download_Filename);
                                    $temp = intval($temp/1024);			// MB
                                    $ZipFile += round($temp/1024, 2);
                                    $ZipFile = round($ZipFile, 1);
									$TotalZipFile += $ZipFile;
                                }
                                echo "<input type='checkbox' id='DVideoPlaylist_${z}_${video_download_index}' name='DownloadVideoPlaylist_$z' value='${video_download_index}' onclick='DownloadVideoPlaylistClick(\"${video_download_index}\", $ZipFile, $z)' />&nbsp;&nbsp;<span style='font-size: 12pt; '>$VideoConvertWithTab[1]</span><span style='font-size: 11pt; font-weight: normal; '> (~$ZipFile MB)</span>";
                                ?>
                            </td>
                            <?php
                            $j++;
                            $video_download_index++;
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
$query="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = 'OT'";				// check if there is a OT
$result_OT=$db->query($query);
$PDF_OT=$result_OT->num_rows;
$query="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF = 'NT'";				// check if there is a NT
$result_NT=$db->query($query);
$PDF_NT=$result_NT->num_rows;
if ($PDF_NT > 0 || $PDF_OT > 0 || $SB_PDF > 0) {
	if ($SB_PDF > 0) {
		?>
		<form>
		<?php
		//$i_SB=0;
		$result_SB->data_seek(0);																			// from above
		while ($r_SB = $result_SB->fetch_array(MYSQLI_ASSOC)) {
			$Item = $r_SB['Item'];
			?>
			<tr style='margin-top: -2px; '>
			<td style='width: 45px; '>
			<?php
			if ($Item == 'B') {
				$whole_Bible=trim($r_SB['Scripture_Bible_Filename']);
				echo "<div class='linePointer' title='".translate('Download the Bible.', $st, 'sys')."' onclick='BPDF(\"$st\", \"$ISO\", \"$ROD_Code\", \"$whole_Bible\")'><img class='iconActions' src='../images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
				echo "</div>";
				echo "</td>";
				echo "<td>";
				echo "<div class='linePointer' title='".translate('Download the Bible.', $st, 'sys')."' onclick='BPDF(\"$st\", \"$ISO\", \"$ROD_Code\", \"$whole_Bible\")'><span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the Bible', $st, 'sys')."</div> (PDF)";
			}
			else {
				$complete_Scripture=trim($r_SB['Scripture_Bible_Filename']);
				echo "<div class='linePointer' title='".translate('Download Scripture text.', $st, 'sys')."' onclick='SPDF(\"$st\", \"$ISO\",\"$ROD_Code\", \"$complete_Scripture\")'><img class='iconActions' src='../images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
				echo "</div>";
				echo "</td>";
				echo "<td>";
				echo "<div class='linePointer' title='".translate('Download Scripture text.', $st, 'sys')."' onclick='SPDF(\"$st\", \"$ISO\",\"$ROD_Code\", \"$complete_Scripture\")'><span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('Scripture text', $st, 'sys')."</div> (PDF)";
			}
			?>
			</td>
			</tr>
			<?php
			//$i_SB++;
		}
		?>
		</form>
		<?php
	}
	if ($PDF_OT > 0) {
		$r_OT = $result_OT->fetch_array(MYSQLI_ASSOC);
		$OT_PDF_Filename = trim($r_OT['OT_PDF_Filename']);													// there is a OT
		?>
		<form>
			<tr style='margin-top: -2px; '>
				<td style='width: 45px; '>
					<?php
					echo "<div class='linePointer' title='".translate('Download the PDF Old Testament.', $st, 'sys')."' onclick='OTPDF(\"$st\", \"$ISO\",\"$ROD_Code\", \"$OT_PDF_Filename\")'><img class='iconActions' src='../images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
					echo "</div>";
				echo "</td>";
				echo "<td>";
					echo "<div class='linePointer' title='".translate('Download the PDF Old Testament.', $st, 'sys')."' onclick='OTPDF(\"$st\", \"$ISO\",\"$ROD_Code\", \"$OT_PDF_Filename\")'><span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the Old Testament (PDF)', $st, 'sys')."</div>";
					?>
				</td>
			</tr>
		</form>
		<?php
	}
	if ($PDF_NT > 0) {
		$r_NT = $result_NT->fetch_array(MYSQLI_ASSOC);
		$NT_PDF_Filename = trim($r_NT['NT_PDF_Filename']);													// there is a NT
		?>
		<form>
			<tr style='margin-top: -2px; '>
				<td style='width: 45px; '>
					<?php
					echo "<div class='linePointer' title='".translate('Download the New Testament.', $st, 'sys')."' onclick='NTPDF(\"$st\", \"$ISO\", \"$ROD_Code\", \"$NT_PDF_Filename\")'><img class='iconActions' src='../images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
					echo "</div>";
				echo "</td>";
				echo "<td>";
					echo "<div class='linePointer' title='".translate('Download the New Testament.', $st, 'sys')."' onclick='NTPDF(\"$st\", \"$ISO\", \"$ROD_Code\", \"$NT_PDF_Filename\")'><span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the New Testament (PDF)', $st, 'sys')."</div>";
					?>
				</td>
			</tr>
		</form>
        <?php
	}
}
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
			//$BibleIsLink=trim($r_links['BibleIs']);
			$BibleIsVersion=trim($r_links['company_title']);
			?>
			<tr>
				<td>
					<?php
					echo "<a href='$URL' target='_blank'><img class='iconActions' src='../images/BibleIs-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' />";
					echo "</a>";
				echo "</td>";
				echo "<td>";
					echo "<a href='$URL' target='_blank'><span class='lineAction'>" . translate('Read and Listen', $st, 'sys') . "</span> ";
					//if (stripos($URL, '/Gen/') !== false)
					/*if ($BibleIsLink == 1)
						echo translate('to the New Testament', $st, 'sys');
					else if ($BibleIsLink == 2)
						echo translate('to the Old Testament', $st, 'sys');
					else	// $BibleIs == 3
						echo translate('to the Bible', $st, 'sys');*/
					echo translate('on Bible.is', $st, 'sys');
					echo "</a>";
					if ($BibleIsVersion!='') {
						echo ' ' . $BibleIsVersion;
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
						echo "<a href='$URL' target='_blank'><img class='iconActions' src='../images/YouVersion-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' />";
						echo "</a>";
					echo "</td>";
					echo "<td>";
						echo "<a href='$URL' target='_blank'><span class='lineAction'>" . translate($text1, $st, 'sys') . '</span> ' . translate($text2, $st, 'sys') . ': ' . $organization . '</a>';
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
						echo "<a href='$URL' target='_blank'><img class='iconActions' src='../images/BibleSearch-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' />";
						echo "</a>";
					echo "</td>";
					echo "<td>";
						echo "<a href='$URL' target='_blank'><span class='lineAction'>" . translate($text1, $st, 'sys') . '</span> ' . translate($text2, $st, 'sys') . ': ' . $organization . '</a>';
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
if ($viewer && ($YouVersion || $Biblesorg || $SAB || $BibleIs) && $Internet) {
	$ROD_Var='';
	$rtl = 0;
	$query="SELECT viewer_ROD_Variant, rtl FROM viewer WHERE ISO_ROD_index = '$ISO_ROD_index' AND Variant_Code = '$Variant_Code'";						// check if there is a viewer
	$resultViewer=$db->query($query);
	if ($resultViewer) {
		//if (mysql_num_rows($resultViewer) > 0) {
			//$numViewer=mysql_num_rows($resultViewer);
			//if ($numViewer > 0) {
				$r_Viewer = $resultViewer->fetch_array(MYSQLI_ASSOC);
				$ROD_Var=trim($r_Viewer['viewer_ROD_Variant']);
				$rtl=trim($r_Viewer['rtl']);
			//}
		//}
	}
	?>
	<tr>
		<td style='width: 45px; '>
			<?php
			echo "<a href='./viewer/views.php?iso=$ISO&ROD_Code=$ROD_Code&Variant_Code=$Variant_Code&ROD_Var=$ROD_Var&rtl=$rtl&st=$st' title='".translate('Viewer for the Language Name', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' />";
			echo "</a>";
		echo "</td>";
		echo "<td>";
			echo "<a href='./viewer/views.php?iso=$ISO&ROD_Code=$ROD_Code&Variant_Code=$Variant_Code&ROD_Var=$ROD_Var&rtl=$rtl&st=$st' title='".translate('Viewer for the Language Name', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Go to', $st, 'sys')."</span> ".translate('the online viewer', $st, 'sys')."</a>";
			?>
		</td>
	</tr>
	<?php
}
*/

/*
	*************************************************************************************************************
		Is it a cell phone module?
	*************************************************************************************************************
*/
	/*
		GoBible (Java)
		MySword (Android)
		iPhone
		Windows
		Blackberry
		Standard Cell Phone
		Android App (apk)
	*/
if ($CellPhone) {
	$query="SELECT * FROM CellPhone WHERE ISO_ROD_index = '$ISO_ROD_index' AND Cell_Phone_Title <> 'Android App'";
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
						echo "<div class='linePointer' title='" . translate('Download the cell phone module for', $st, 'sys') . " $Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><img class='iconActions' src='../images/mysword-icon.jpg' alt='".translate('Cell Phone', $st, 'sys')."' title='".translate('Cell Phone', $st, 'sys')."' />";
					else
						echo "<div class='linePointer' title='" . translate('Download the app for', $st, 'sys') . " $Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><img class='iconActions' src='../images/CellPhoneIcon.png' alt='".translate('Cell Phone', $st, 'sys')."' title='".translate('Cell Phone', $st, 'sys')."' />";
					echo "</div>";
				echo "</td>";
				echo "<td>";
					if ($Cell_Phone_Title == 'MySword (Android)')
						if ($Internet)
							echo "<div class='linePointer' title='" . translate('Download the cell phone module for', $st, 'sys') . " $Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><span class='lineAction'>" . translate('Download', $st, 'sys') . "</span></a> " . translate('the cell phone module for', $st, 'sys') . " <a href='https://www.mysword.info/' target='_blank'><span class='lineAction'>$Cell_Phone_Title</span></a>";
						else
							echo "<div class='linePointer' title='" . translate('Download the cell phone module for', $st, 'sys') . " $Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><span class='lineAction'>" . translate('Download', $st, 'sys') . "</span></a> " . translate('the cell phone module for', $st, 'sys') . " $Cell_Phone_Title";
					else
						echo "<div class='linePointer' title='" . translate('Download the app for', $st, 'sys') . " $Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><span class='lineAction'>" . translate('Download', $st, 'sys') . "</span> " . translate('the app for', $st, 'sys') . " $Cell_Phone_Title";
					echo ' ' . $optional . '</div>';
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
			while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
				$URL=trim($r2['URL']);
				$organization=trim($r2['company']);
				$text = trim($r2['company_title']);
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
						echo "<a href='$URL' target='_blank'><img class='iconActions' src='../images/GRN-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' />";
						echo "</a>";
					echo "</td>";
					echo "<td>";
						echo "<a href='$URL' target='_blank'><span class='lineAction'>" . translate($text1, $st, 'sys') . '</span> ' . translate($text2, $st, 'sys') . ': ' . $organization . '</a>';
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
		//if (mysql_num_rows($result2) >= 1) {
			//$num=mysql_num_rows($result2);
			//$i=0;
			while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
				$organization=trim($r2['organization']);
				$watch_what=trim($r2['watch_what']);
				$URL=trim($r2['URL']);
				$JesusFilm=trim($r2['JesusFilm']);
				$YouTube=trim($r2['YouTube']);
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
                                <a href="#" onClick="window.open('JESUSFilmView.php?<?php echo $URL ?>','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=890,height=690,top=300,left=300'); return false;" title="<?php echo $LN ?>">
                            	<img class='iconActions' src='../images/JESUS-icon.jpg' 
							<?php
                        }
                        else {
                            ?>
                                <a href="#" onclick="window.open('<?php echo $URL ?>','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=380,top=200,left=300'); return false;" title="<?php echo $LN ?>">
                            	<img class='iconActions' src='../images/JESUS-icon.jpg' 
							<?php
                        }
                    }
                    elseif ($YouTube) {
                        // YouTube
                        //     href="#" onclick="w=screen.availWidth; h=screen.availHeight; window.open('<?php echo $URL ? >','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width='+w+',height='+h+',top=0,left=0'); return false;" title="< ?php echo $LN ? >">
						?>
                            <a href='<?php echo $URL ?>' title='<?php echo $LN ?>' target='_blank'>
                            <img class='iconActions' src='../images/youtube-icon.jpg' 
						<?php
                    }
                    else {
                        ?>
							<a href='<?php echo $URL ?>' title='<?php echo translate('View', $st, 'sys') ?>' target='_blank'>
                        	<img class='iconActions' src='../images/watch-icon.jpg' 
                        <?php
                    }
                    echo "alt='".translate('View', $st, 'sys')."' title='".translate('View', $st, 'sys')."' />";
                    ?>
                    </a>
                </td>
                <td>
                    <?php
                    if ($JesusFilm) {
                        // JESUS Film
                        if (substr($URL, 0, strlen("http://api.arclight.org/videoPlayerUrl")) == "http://api.arclight.org/videoPlayerUrl") {
                            ?>
                                <a href="#" onclick="window.open('JESUSFilmView.php?<?php echo $URL ?>','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=890,height=690,top=300,left=300'); return false;" title="<?php echo $LN ?>">
                            <?php
                        }
                        else {
                            ?>
								<a href="#" onclick="window.open('<?php echo $URL ?>','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=380,top=200,left=300'); return false;" title="<?php echo $LN ?>">
                            <?php
                        }
                    }
                    elseif ($YouTube) {
                        // YouTube
                        //    href="#" onclick="w=screen.availWidth; h=screen.availHeight; window.open('<?php echo $URL ? >','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width='+w+',height='+h+',top=0,left=0'); return false;" title="<?php echo $LN ? >">
                        ?>
                            <a href='<?php echo $URL ?>' title='<?php echo $LN ?>' target='_blank'>
                        <?php
                    }
                    else {
                        ?>
                        <a href='<?php echo $URL ?>' title='<?php echo translate('View', $st, 'sys') ?>' target='_blank'>
                        <?php
                    }
                    ?>
                    <span class='lineAction'>
                    <?php
                    if ($JesusFilm) {
                        // JESUS Film
                        //echo $watch_what;
						echo translate('View the JESUS Film', $st, 'sys');
                        ?>
                        </span>
                        <?php
                    }
                    else if ($YouTube) {
                        // YouTube
						
                        echo translate('View', $st, 'sys')."</span>&nbsp;$watch_what";
                    }
                    else {
                        //echo translate('View', $st, 'sys')."</span> ".translate('by', $st, 'sys')." $organization:&nbsp;$watch_what";
						echo translate('View', $st, 'sys')."</span> $organization:&nbsp;$watch_what";
                    }
                    ?>
                    </a>
				</td>
            	</tr>
                <?php
				//$i++;
			}
		//}
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
		echo "<a href='./data/".$ISO.'/video/'.$download_video."' title='".translate('Download the video.', $st, 'sys')."' download><img class='iconActions' src='../images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
		echo "</a>";
		echo "</td>";
		echo "<td>";
		echo "<a href='./data/".$ISO.'/video/'.$download_video."' title='".translate('Download the video.', $st, 'sys')."' download><span class='lineAction'>".translate('Download', $st, 'sys').'</span> '.$other. ' ' . $other_title . ' ' .translate('video', $st, 'sys').' (MP4)</a>';
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
				echo "<div class='linePointer' title='".translate('Listen', $st, 'sys')." $PlaylistAudioTitle' onclick='PlaylistAudio_$z($z, $num3)'><img class='iconActions' src='../images/listen-icon.jpg' alt='".translate('Listen', $st, 'sys')."' title='".translate('Listen', $st, 'sys')."' /></div>";
				?>
			</td>
			<td>
				<?php
				echo "<div class='linePointer' title='".translate('Listen', $st, 'sys')." $PlaylistAudioTitle' onclick='PlaylistAudio_$z($z, $num3)'><span class='lineAction'>".translate('Listen', $st, 'sys').":</span> $PlaylistAudioTitle</div>";
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
				echo "<div class='linePointer' title='".translate('Download', $st, 'sys')." $PlaylistAudioTitle' onclick='PlaylistTableClick_$z()'><img class='iconActions' src='../images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
				echo "</div>";
			echo "</td>";
			echo "<td>";
				echo "<div style='cursor: pointer; display: inline; ' title='".translate('Download', $st, 'sys')." $PlaylistAudioTitle' onclick='PlaylistTableClick_$z()'><span class='lineAction'>".translate('Download', $st, 'sys').":</span> ". $PlaylistAudioTitle ."</div>";
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
	if ($result2) {
		//if (mysql_num_rows($result2) >= 1) {
			//$num=mysql_num_rows($result2);
			//$i=0;
			while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
				$buy_what=trim($r2['buy_what']);
				$organization=trim($r2['organization']);
				$URL=trim($r2['URL']);
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						echo "<a href='$URL' title='".translate('Buy from organization.', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/buy-icon.jpg' alt='".translate('Buy', $st, 'sys')."' title='".translate('Buy', $st, 'sys')."' />";
						echo "</a>";
					echo "</td>";
					echo "<td>";
						echo "<a href='$URL' title='".translate('Buy from organization.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Buy from', $st, 'sys')."</span> $organization: $buy_what</a>";
						?>
					</td>
				</tr>
                <?php
				//$i++;
			}
		//}
	}
	$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND buy = 1";
	$result2=$db->query($query);
	if ($result2) {
		if ($Internet) {
			//$num=mysql_num_rows($result2);
			//$i=0;
			while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
				$company_title=trim($r2['company_title']);
				$company=trim($r2['company']);
				$URL=trim($r2['URL']);
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						echo "<a href='$URL' title='".translate('Buy from organization.', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/buy-icon.jpg' alt='".translate('Buy', $st, 'sys')."' title='".translate('Buy', $st, 'sys')."' />";
						echo "</a>";
					echo "</td>";
					echo "<td>";
						echo "<a href='$URL' title='".translate('Buy from organization.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Buy from', $st, 'sys')."</span> ".translate('to', $st, 'sys')." ";
						if ($company_title != "" && $company_title != NULL) {
							echo "$company_title: ";
						}
						echo "$company</a>";
						?>
					</td>
				</tr>
                <?php
				//$i++;
			}
		}
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
		//if (mysql_num_rows($result2) >= 1) {
			//$num=mysql_num_rows($result2);
			//$i=0;
			while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
				$Testament=$r2['Testament'];
				$alphabet=$r2['alphabet'];
				$ScriptureURL=trim($r2['ScriptureURL']);
				$othersiteURL=trim($r2['othersiteURL']);
				?>
				<tr>
					<td style='width: 45px; '>
                    <!--div class='lineItem' style='margin-top: -19px; margin-bottom: -16px; '-->
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
                        echo "<div class='linePointer' title='$LN: ".translate('Download the module.', $st, 'sys')."' onclick='Study(\"$st\", \"$ISO\", \"ROD_Code\", \"$ScriptureURL\")'>";
                        echo "<img class='iconActions' style='margin-top: 4px; ' src='../images/TheWord-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' />";
                        echo "</div>";
                    echo "</td>";
                    echo "<td>";
                        echo "<div class='linePointer' title='$LN: ".translate('Download the module.', $st, 'sys')."' onclick='Study(\"$st\", \"$ISO\", \"ROD_Code\", \"$ScriptureURL\")'>";
                        echo "<span class='lineAction'>";
                        echo translate('Download', $st, 'sys')."</span> ";
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
                            echo "&nbsp;</span><a href='$othersiteURL' style='font-size: 1em; ' target='_blank'><span class='lineAction'>&ldquo;The Word&rdquo;</span>";
                            echo "</a>";
                        }
                        else {
                            echo "&nbsp;</span>&ldquo;The Word&rdquo;";
                        }
                        ?>
                    <!--/div-->
                        </td>
					</tr>
				<?php
				//$i++;
			}
		//}
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
		//if (mysql_num_rows($result2) >= 1) {
			//$num=mysql_num_rows($result2);
			//$i=0;
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
							echo "<a href='./data/$ISO/PDF/$other_PDF' title='".translate('Read from the other title.', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/read-icon.jpg' alt='".translate('Books', $st, 'sys')."' title='".translate('Books', $st, 'sys')."' />";
							echo "</a>";
							echo "</td>";
							echo "<td>";
							echo "<a href='./data/$ISO/PDF/$other_PDF' title='".translate('Read from the other title.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Read', $st, 'sys')."</span>";
						}
						else {
							echo "<a href='./data/$ISO/audio/$other_audio' title='".translate('Listen to the other title.', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/listen-icon.jpg' alt='".translate('Books', $st, 'sys')."' title='".translate('Books', $st, 'sys')."' />";
							echo "</a>";
							echo "</td>";
							echo "<td>";
							echo "<a href='./data/$ISO/audio/$other_audio' title='".translate('Listen to the other title.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Listen', $st, 'sys')."</span>";
						}
						echo "&nbsp;$other:&nbsp;$other_title</a>";
						?>
					</td>
				</tr>
				<?php
				//$i++;
			}
		//}
	}
}

/*
	***********************************************************************************************************
		Does it have any more links? (table links)
	*************************************************************************************************************
*/
if ($links && $Internet) {
	// This takes care of all of the rest of the links.
	$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND map = 0 AND buy = 0 AND BibleIs = 0 AND YouVersion = 0 AND `Bibles_org` = 0 AND `GooglePlay` = 0 AND `GRN` = 0";
	$result2=$db->query($query);
	if ($result2) {
		if ($Internet) {
			//$num=mysql_num_rows($result2);
			//$i=0;
			while ($r2 = $result2->fetch_array(MYSQLI_ASSOC)) {
				$company_title=trim($r2['company_title']);
				$company=trim($r2['company']);
				$URL=trim($r2['URL']);
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						if (preg_match('/onestory/i', $URL)) {
							echo "<a href='$URL' title='".translate('Link to the organization.', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/onestory-icon.jpg' alt='OneStory' title='OneStory' />";
						}
						elseif (preg_match('/itunes/i', $URL) || preg_match('/\.apple\./i', $URL)) {
							echo "<a href='$URL' title='".translate('Link to the organization.', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/iTunes-icon.jpg' alt='iTunes' title='iTunes' />";
						}
						elseif (preg_match('/\.facebook\./i', $URL)){
							echo "<a href='$URL' title='".translate('Link to the organization.', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/facebook-icon.jpg' alt='Facebook' title='Facebook' />";
						}
						else {
							echo "<a href='$URL' title='".translate('Link to the organization.', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/links-icon.jpg' alt='".translate('Link', $st, 'sys')."' title='".translate('Link', $st, 'sys')."' />";
						}
						echo "</a>";
					echo "</td>";
					echo "<td>";
						echo "<a href='$URL' title='".translate('Link to the organization.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Link', $st, 'sys')."</span> : ";
						if ($company_title != "" && $company_title != NULL) {
							echo "$company_title: ";
						}
						echo "$company</a>";
						?>
					</td>
				</tr>
                <?php
				//$i++;
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
	$num2=$result2->num_rows;
	if ($num2 > 0) {
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
		//$vernacularTitle = trim($r2['vernacularTitle']);
		$PDFline = '';
		?>
		<tr>
			<td style='width: 45px; '>
				<?php
				echo "<div class='linePointer' title='".translate('Scripture Resources from eBible.org', $st, 'sys')."' onclick='eBibleClick()'><img class='iconActions' src='../images/eBible-icon.jpg' alt='".translate('Scripture Resources from eBible.org', $st, 'sys')."' title='".translate('Scripture Resources from eBible.org', $st, 'sys')."' />";
				echo "</div>";
			echo "</td>";
			echo "<td>";
				echo "<div class='linePointer' title='".translate('Scripture Resources from eBible.org', $st, 'sys')."' onclick='eBibleClick()'><span class='lineAction'>".translate('Scripture Resources from eBible.org', $st, 'sys').'</span></div><br />';
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
			echo "<a href='$URL' title='".translate('Check SIL.org', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/sil-icon.jpg' alt='".translate('Check SIL.org', $st, 'sys')."' title='".translate('Check SIL.org', $st, 'sys')."' />";
			echo "</a>";
		echo "</td>";
		echo "<td>";
			echo "<a href='$URL' title='".translate('Check SIL.org', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Check SIL.org', $st, 'sys')."</span> ".translate('for resources in this language', $st, 'sys')." ";
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
					echo "<a href='$URL' title='".translate('Link to the organization.', $st, 'sys')."' target='_blank'><img class='iconActions' src='../images/globe-icon.png' alt='".translate('Map', $st, 'sys')."' title='".translate('Map', $st, 'sys')."' />";
					echo "</a>";
				echo "</td>";
				echo "<td>";
					echo "<a href='$URL' title='".translate('Link to the organization.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Link', $st, 'sys')."</span> : ";
					if ($company_title != "" && $company_title != NULL) {
						if ($company_title == 'language map') {
							echo translate('language map', $st, 'sys').': ';
						}
						else {
							echo "$company_title: ";
						}
					}
					echo "$company</a>";
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
