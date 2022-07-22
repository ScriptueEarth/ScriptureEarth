<?php
function WhichAudioPlayer($ISO_ROD_index, $ISO, $ROD_Code) {
	/*
		**************************************************************
		The table has to updated every time a new language is added.
		Contact Lori Goldstein at lgoldstein@fcbhmail.org or Alan Hooker at adhooker@fcbhmail.org

		Bible_ID (new field from FCBH)
		ENGESVN2DA -- ENG is the ISO code; ESV is for the version; 2-letter code for type (N, O, C, B, P, or S followed by 1 (non-drama) or 2 (drama)); 2-letter code for media type (DA for 'digital audio')
		The field size for DAM_ID is 10 characters.
		C = Complete Bible
		N = New Testament
		O = Old Testament
		P = Portions
		B = Books
		S = Storying (special recording for a partner organization in Asia)

		StockNo (old field from FCBH)
		N2HUVTBL -- Huave, San Mateo del Mar [N2 below; HUV is the ISO code; TBL is the version]
		The field size for StockNo is 8 characters.
		C2 = complete bible drama
		C1 = complete bible non-drama
		N2 = new testament drama
		N1 = new testament non-drama
		**************************************************************
	*/
	$queryFCBH="SELECT DAM_ID, StockNo FROM FCBHLanguageList WHERE ISO_ROD_index = '$ISO_ROD_index' ORDER BY DAM_ID DESC, StockNo DESC";		// N = New Testament; C = Complete Bible
	$resultFCBH=mysql_query($queryFCBH);
	$numFCBH=mysql_num_rows($resultFCBH);
	if (!$numFCBH) {
		$queryFCBH="SELECT DAM_ID, StockNo FROM FCBHLanguageList WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code' ORDER BY DAM_ID DESC, StockNo DESC";		// N = New Testament; C = Complete Bible
		$resultFCBH=mysql_query($queryFCBH);
		$numFCBH=mysql_num_rows($resultFCBH);
	}
	if ($numFCBH > 0) {
		$DAM_ID = mysql_result($resultFCBH,0,'DAM_ID');				// 1 = non-drama; 2 = drama
		$StockNo = mysql_result($resultFCBH,0,'StockNo');			// 1 = non-drama; 2 = drama
		if ($DAM_ID) {
			return $DAM_ID;
		}
		else {
			return $StockNo;
		}
	}
	return '00000000';
}

/*
	*******************************************************************************************************************
		select the default primary language name to be used by displaying the Countries and indigenous langauge names
	*******************************************************************************************************************
*/
$query = "SELECT DISTINCT scripture_main.*, $SpecificCountry, countries.ISO_Country FROM scripture_main, countries, ISO_countries WHERE countries.ISO_Country = ISO_countries.ISO_countries AND ISO_countries.ISO_ROD_index = scripture_main.ISO_ROD_index AND scripture_main.ISO_ROD_index = '$ISO_ROD_index'";
$result=mysql_query($query) or die (translate('Query failed:', $st, 'sys') . ' ' . mysql_error() . '</body></html>');
if (!$result || (mysql_num_rows($result) < 1)) {
	die ("&ldquo;$ISO&rdquo; " . translate('is not found.', $st, 'sys') . '</body></html>');
}
$YouVersion=mysql_result($result,0,'scripture_main.YouVersion');			// boolean
$Biblesorg=mysql_result($result,0,'scripture_main.Bibles_org');				// boolean

$ISO_Country=mysql_result($result,0,'countries.ISO_Country');

$PlaylistAudio=mysql_result($result,0,'scripture_main.PlaylistAudio');			// boolean
$PlaylistVideo=mysql_result($result,0,'scripture_main.PlaylistVideo');			// boolean

$i=0;			// used in 00-DBLanguageCountryName.inc.php include
include ('./include/00-DBLanguageCountryName.inc.php');

?>

<div id='SpecLang' class='callI'><br />

<?php

/*
	*********************************************************************************************
		Get the alternate language name, if there is any, to display.
	*********************************************************************************************
*/
$query_alt="SELECT alt_lang_name FROM alt_lang_names WHERE ISO_ROD_index = '$ISO_ROD_index'";				// then look to the alt_lang_name table
$result_alt=mysql_query($query_alt);
if (!$result_alt || (mysql_num_rows($result_alt) < 1)) {
}
else {
	$num_alt=mysql_num_rows($result_alt);
	?>
	<div class='alternativeLanguageNames'><?php echo translate('Alternative Language Names:', $st, 'sys'); ?>
        <span class='alternativeLanguageName'>
        <?php
        $i_alt=0;
        while ($i_alt < $num_alt) {
            if ($i_alt != 0) {
                echo ", ";
            }
            $alt_lang_name=trim(mysql_result($result_alt,$i_alt,"alt_lang_name"));
            echo "$alt_lang_name";
            $i_alt++;
        }
        ?>
        </span>
	</div>
    <?php
}

// Get the name(s) of the country(ies)
$query="SELECT $SpecificCountry FROM ISO_countries, countries WHERE ISO_countries.ISO_ROD_index = '$ISO_ROD_index' AND ISO_countries.ISO_countries = countries.ISO_Country";
$result_ISO_countries=mysql_query($query);
$num_ISO_countries=mysql_num_rows($result_ISO_countries);
$country = trim(mysql_result($result_ISO_countries,0,$SpecificCountry));									// name of the country in the language version
for ($i_ISO_countries = 1; $i_ISO_countries < $num_ISO_countries; $i_ISO_countries++) {
	$country = $country.', '.trim(mysql_result($result_ISO_countries,$i_ISO_countries,$SpecificCountry));	// name of the country in the language version
}
// Displays the country and the ISO code
?>

<div class='Country'><?php echo translate('Country:', $st, 'sys'); ?> <span class='Country'><?php echo $country; ?></span></div>
<div class='languageCode'><?php echo translate('Language Code:', $st, 'sys'); ?> <span class='languageCode'><a href='http://www.ethnologue.org/show_language.asp?code=<?php echo $ISO; ?>' target='_blank' title='<?php echo translate('View this language in the Ethnologue.com.', $st, 'sys'); ?>'><?php echo $ISO; ?></a></span></div>
<br />
<img class='BlueBar' src='images/IframeBlueBar.png' />
&nbsp;<br />

<?php
$OT_PDF=0;			// boolean
$NT_PDF=0;			// boolean
$SB_PDF=0;			// boolean
$OT_Audio=0;		// boolean
$NT_Audio=0;		// boolean

?>
<table id='individualLanguage' cellpadding="0" cellspacing="0" border="0">
<?php

	$query="SELECT interest_index FROM interest WHERE ISO_ROD_index = '$ISO_ROD_index' AND NoLang = 1";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) == 1) {
			$query="SELECT Goto_ISO_ROD_index, Goto_ISO, Goto_ROD_Code, Goto_Variant_Code, Percentage FROM GotoInterest WHERE ISO_ROD_index = '$ISO_ROD_index'";
			$result2=mysql_query($query);
			if (isset($result2)) {
				$GotoInterest = mysql_num_rows($result2);
				if ($GotoInterest > 0) {
					$i_GI=0;
					?>
					<tr>
						<td>
							<?php
							echo translate('Speakers of this language may be able to use media in', $st, 'sys');
							while ($i_GI < $GotoInterest) {
								$Goto_ISO_ROD_index=trim(mysql_result($result2,$i_GI,"Goto_ISO_ROD_index"));
								$Goto_ISO=trim(mysql_result($result2,$i_GI,"Goto_ISO"));
								$Goto_ROD_Code=trim(mysql_result($result2,$i_GI,"Goto_ROD_Code"));
								if ($Goto_ROD_Code == "") $Goto_ROD_Code="00000";
								$Goto_Variant_Code=trim(mysql_result($result2,$i_GI,"Goto_Variant_Code"));
								$Percentage=trim(mysql_result($result2,$i_GI,"Percentage"));

								/*
									*********************************************************************************************
										Get the "Goto' language name.
									*********************************************************************************************
								*/
								$query="SELECT scripture_main.* FROM scripture_main WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
								$result3=mysql_query($query);
								$ML_Interest=mysql_result($result3,0,"scripture_main.$MajorLanguage");				// boolean
								$def_LN_Interest=mysql_result($result3,0,'scripture_main.def_LN');					// default langauge (a 2 digit number for the national langauge)
								if (!$ML_Interest) {																// if the country then the major default langauge name
									switch ($def_LN_Interest) {
										case 1:
											$query="SELECT LN_English FROM LN_English WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
											$result_LN=mysql_query($query);
											$LN=trim(mysql_result($result_LN,0,'LN_English'));
											break;
										case 2:
											$query="SELECT LN_Spanish FROM LN_Spanish WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
											$result_LN=mysql_query($query);
											$LN=trim(mysql_result($result_LN,0,'LN_Spanish'));
											break;
										case 3:
											$query="SELECT LN_Portuguese FROM LN_Portuguese WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
											$result_LN=mysql_query($query);
											$LN=trim(mysql_result($result_LN,0,'LN_Portuguese'));
											break;	
										case 4:
											$query="SELECT LN_French FROM LN_French WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
											$result_LN=mysql_query($query);
											$LN=trim(mysql_result($result_LN,0,'LN_French'));
											break;	
										case 5:
											$query="SELECT LN_Dutch FROM LN_Dutch WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
											$result_LN=mysql_query($query);
											$LN=trim(mysql_result($result_LN,0,'LN_Dutch'));
											break; 	
										default:
											echo 'This isn�t supposed to happen! The "Goto" language name isn�t found.';
											break;
									}
								}
								else {
									$query="SELECT $MajorLanguage FROM $MajorLanguage WHERE ISO_ROD_index = '$Goto_ISO_ROD_index'";
									$result_LN=mysql_query($query);
									$LN=trim(mysql_result($result_LN,0,"$MajorLanguage"));
								}

								if ($i_GI > 0) 
									echo ", " . translate('or', $st, 'sys');
								echo " <a href='http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'] . "?sortby=lang&name=".$Goto_ISO."&ROD_Code=".$Goto_ROD_Code."&Variant_Code=".$Goto_Variant_Code."' style='text-decoration: underline; '>" . $LN . "</a> (" . $Percentage . ")";
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
		Is it PDF and listening?
	*************************************************************************************************************
*/
$Internet = 0;		// localhost is 127.0.0.1 but "192.168.x.x" should be not-on-the-Internet because it's URL is part of the stand-alone server.
$BibleIs=mysql_result($result,0,"scripture_main.BibleIs");			// boolean
if ($Internet = substr($_SERVER['REMOTE_ADDR'], 0 , 7) != "192.168") {
	if ($BibleIs) {
		$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND NOT BibleIs = 0";
		$result2=mysql_query($query);
		if (isset($result2)) {
			if (mysql_num_rows($result2) >= 1) {
				$num=mysql_num_rows($result2);
				$i=0;
				while ($i < $num) {
					$URL=trim(mysql_result($result2,$i,"URL"));
					$BibleIsLink=trim(mysql_result($result2,$i,"BibleIs"));
					?>
					<tr>
						<td>
							<?php
							echo "<a href='$URL' target='_blank'><img class='iconActions' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' />";
							echo "</a>";
						echo "</td>";
						echo "<td>";
							echo "<a href='$URL' target='_blank'><span class='lineAction'>" . translate('Read and Listen', $st, 'sys') . "</span> ";
							//if (stripos($URL, '/Gen/') !== false)
							if ($BibleIsLink == 1)
								echo translate('to the New Testament', $st, 'sys');
							else if ($BibleIsLink == 2)
								echo translate('to the Old Testament', $st, 'sys');
							else	// $BibleIs == 3
								echo translate('to the Bible', $st, 'sys');
							echo " " . translate('on Bible.is', $st, 'sys');
							echo "</a>";
							?>
						</td>
					</tr>
					<?php
					$i++;
				}
			}
		}
	}
}

/*
	*************************************************************************************************************
		Is it PDF?
	*************************************************************************************************************
*/
//if (!$BibleIs) {
	$OT_PDF=mysql_result($result,0,"scripture_main.OT_PDF");		// boolean
	$NT_PDF=mysql_result($result,0,"scripture_main.NT_PDF");		// boolean
	$query_SB="SELECT Item, Scripture_Bible_Filename FROM Scripture_and_or_Bible WHERE ISO_ROD_index = '$ISO_ROD_index'";		// then look to the Scripture_and_or_Bible table
	$result_SB=mysql_query($query_SB);
	if (!$result_SB)
		$SB_PDF = 0;
	else
		$SB_PDF=mysql_num_rows($result_SB);
	if ($NT_PDF > 0 || $OT_PDF > 0 || $SB_PDF > 0) {				// if it is 1 then
		if ($SB_PDF > 0) {
			$i_SB=0;
			while ($i_SB < $SB_PDF) {
				?>
        		<tr>
					<td>
						<?php
						$Item = mysql_result($result_SB,$i_SB,'Item');
						if ($Item == 'B') {
							$whole_Bible=trim(mysql_result($result_SB,$i_SB,"Scripture_Bible_Filename"));
							echo "<a href='data/$ISO/PDF/$whole_Bible' title='".translate('Read the Bible.', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')." (PDF)"."' title='".translate('Read', $st, 'sys')."' />";
							echo "</a>";
							echo "</td>";
							echo "<td>";
							echo "<a href='data/$ISO/PDF/$whole_Bible' title='".translate('Read the Bible.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('the Bible', $st, 'sys')." (PDF)"."</a>";
						}
						else {
							$complete_Scripture=trim(mysql_result($result_SB,$i_SB,"Scripture_Bible_Filename"));
							echo "<a href='data/$ISO/PDF/$complete_Scripture' title='".translate('Read a Scripture portion.', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')." (PDF)"."' title='".translate('Read', $st, 'sys')."' />";
							echo "</a>";
							echo "</td>";
							echo "<td>";
							echo "<a href='data/$ISO/PDF/$complete_Scripture' title='".translate('Read a Scripture portion.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('a Scripture portion', $st, 'sys')." (PDF)"."</a>";
						}
						?>
					</td>
				</tr>
                <?php
				$i_SB++;
			}
		}
		if ($OT_PDF > 0) {
			$query="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = 'OT'";		// check if there is a OT
			$result1=mysql_query($query);
			if (!$result1) {
				die(translate('Could not insert the data into the OT_PDF_Media table:', $st, 'sys') . ' ' . mysql_error());
			}
			$num=mysql_num_rows($result1);
			if ($num > 0) {
				$OT_PDF_Filename = trim(mysql_result($result1,0,"OT_PDF_Filename"));							// there is a OT
				?>
                <tr>
					<td>
						<?php
						echo "<a href='data/$ISO/PDF/$OT_PDF_Filename' title='".translate('Read the Old Testament.', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')." (PDF)"."' title='".translate('Read', $st, 'sys')."' />";
						echo "</a>";
					echo "</td>";
					echo "<td>";
						echo "<a href='data/$ISO/PDF/$OT_PDF_Filename' title='".translate('Read the Old Testament.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('the Old Testament', $st, 'sys')." (PDF)"."</a>";
						?>
					</td>
				</tr>
				<?php
			}
			$query="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF != 'OT'";		// check if there is any other book but the OT
			$result1=mysql_query($query);
			$num=mysql_num_rows($result1);
			if ($num > 0) {
				$i=0;
				$a_index = 0;
				?>
				<tr>
					<td>
						<?php
						echo "<img class='iconActions' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' />";
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
						echo "<option class='selectOption'>".translate('Choose One...', $st, 'sys')."</option>";
						foreach ($OT_array[OT_EngBook] as $a) {															// there is/are book(s)
							$query_array="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = '$a_index'";
							$result_array=mysql_query($query_array);
							$num=mysql_num_rows($result_array);
							if ($num > 0) {
								$OT_PDF_Filename = trim(mysql_result($result_array,0,"OT_PDF_Filename"));
								$a = str_replace(" ", "&nbsp;", $a);
								if (!empty($OT_PDF_Filename)) {
									echo "<option class='selectOption' id='OT_PDF_Media_$a' value='data/$ISO/PDF/$OT_PDF_Filename'>$a</option>";
								}
							}
							$a_index++;
						}
						$query_array="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = '100'";	// appendice
						$result_array=mysql_query($query_array);
						$num=mysql_num_rows($result_array);
						if ($num > 0) {
							$OT_PDF_Filename = trim(mysql_result($result_array,0,"OT_PDF_Filename"));
							if (!empty($OT_PDF_Filename)) {
								echo "<option class='selectOption' value='data/$ISO/PDF/$OT_PDF_Filename'>".translate('Appendix', $st, 'sys')."</option>";
							}
						}
						$query_array="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = '101'";	// glossary
						$result_array=mysql_query($query_array);
						$num=mysql_num_rows($result_array);
						if ($num > 0) {
							$OT_PDF_Filename = trim(mysql_result($result_array,0,"OT_PDF_Filename"));
							if (!empty($OT_PDF_Filename)) {
								echo "<option class='selectOption' value='data/$ISO/PDF/$OT_PDF_Filename'>".translate('Glossary', $st, 'sys')."</option>";
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
		if ($NT_PDF > 0) {
			$query="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF = 'NT'";		// check if there is a NT
			$result1=mysql_query($query);
			if (!$result1) {
				die(translate('Could not insert the data into the NT_PDF_media table:', $st, 'sys') . ' ' . mysql_error());
			}
			$num=mysql_num_rows($result1);
			if ($num > 0) {
				$NT_PDF_Filename = trim(mysql_result($result1,0,"NT_PDF_Filename"));							// there is a NT
				?>
                <tr>
					<td>
						<?php
						echo "<a href='data/$ISO/PDF/$NT_PDF_Filename' title='".translate('Read the New Testament.', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')." (PDF)"."' title='".translate('Read', $st, 'sys')."' />";
						echo "</a>";
					echo "</td>";
					echo "<td>";
						echo "<a href='data/$ISO/PDF/$NT_PDF_Filename' title='".translate('Read the New Testament.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('the New Testament', $st, 'sys')." (PDF)"."</a>";
						?>
					</td>
				</tr>
				<?php
			}
			$query="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF != 'NT'";				// check if there is any other book but the NT
			$result1=mysql_query($query);
			$num=mysql_num_rows($result1);
			if ($num > 0) {
				$i=0;
				$a_index = 0;
				?>
                <tr>
					<td>
						<?php
						echo "<img class='iconActions' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' />";
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
						echo "<option class='selectOption'>".translate('Choose One...', $st, 'sys')."</option>";
						foreach ($NT_array[NT_EngBook] as $a) {																	// there is/are book(s)
							$query_array="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF = '$a_index'";
							$result_array=mysql_query($query_array);
							$num=mysql_num_rows($result_array);
							if ($num > 0) {
								$NT_PDF_Filename = trim(mysql_result($result_array,0,"NT_PDF_Filename"));
								$a = str_replace(" ", "&nbsp;", $a);
								if (!empty($NT_PDF_Filename)) {
									echo "<option class='selectOption' value='data/$ISO/PDF/$NT_PDF_Filename'>$a</option>";
								}
							}
							$a_index++;
						}
						$query_array="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF = '200'";	// appendice
						$result_array=mysql_query($query_array);
						$num=mysql_num_rows($result_array);
						if ($num > 0) {
							$NT_PDF_Filename = trim(mysql_result($result_array,0,"NT_PDF_Filename"));
							if (!empty($NT_PDF_Filename)) {
								echo "<option class='selectOption' value='data/$ISO/PDF/$NT_PDF_Filename'>".translate('Appendix', $st, 'sys')."</option>";
							}
						}
						$query_array="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF = '201'";	// glossary
						$result_array=mysql_query($query_array);
						$num=mysql_num_rows($result_array);
						if ($num > 0) {
							$NT_PDF_Filename = trim(mysql_result($result_array,0,"NT_PDF_Filename"));
							if (!empty($NT_PDF_Filename)) {
								echo "<option class='selectOption' value='data/$ISO/PDF/$NT_PDF_Filename'>".translate('Glossary', $st, 'sys')."</option>";
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
//}

/*
	*************************************************************************************************************
		Is it audio?
	*************************************************************************************************************
*/
$OT_Audio=mysql_result($result,0,"scripture_main.OT_Audio");		// boolean
$NT_Audio=mysql_result($result,0,"scripture_main.NT_Audio");		// boolean
$FCBH=mysql_result($result,0,"scripture_main.FCBH");				// boolean
if (!$BibleIs || !$Internet) {
	if ($NT_Audio > 0 || $OT_Audio > 0 || $FCBH > 0) {					// if the boolean is 1
		/*
			*************************************************************************************************************
				Is it FCBH? If it is the script needs the Bible ID or stock number.
			*************************************************************************************************************
		*/
		if ($FCBH > 0 && (!isset($mobile) || $mobile == 0) && $Internet) {			// $FCBH > 0 and $mobile = 0 and $Internet = 1
			/*
				**************************************************************
				The table has to updated every time a new language is added.
				Contact Alan Hooker at adhooker@fcbhmail.org
		
				Bible_ID (new field from FCBH)
				ENGESVN2DA -- ENG is the ISO code; ESV is for the version; 2-letter code for type (N, O, C, B, P, or S followed by 1 (non-drama) or 2 (drama)); 2-letter code for media type (DA for 'digital audio')
				The field size for DAM_ID is 10 characters.
				C = Complete Bible
				N = New Testament
				O = Old Testament
				P = Portions
				B = Books
				S = Storying (special recording for a partner organization in Asia)
		
				StockNo (old field from FCBH)
				N2HUVTBL -- Huave, San Mateo del Mar [N2 below; HUV is the ISO code; TBL is the version]
				The field size for StockNo is 8 characters.
				C2 = complete bible drama
				C1 = complete bible non-drama
				N2 = new testament drama
				N1 = new testament non-drama
				**************************************************************
			*/
			$queryFCBH="SELECT DAM_ID, StockNo FROM FCBHLanguageList WHERE ISO_ROD_index = '$ISO_ROD_index' OR (ISO = '$ISO' AND ROD_Code = '$ROD_Code') ORDER BY DAM_ID DESC, StockNo DESC";		// N = New Testament; C = Complete Bible
			$resultFCBH=mysql_query($queryFCBH);
			$numFCBH=mysql_num_rows($resultFCBH);
			?>
            <script language="javascript" type="application/javascript">
				function myobject() {
					this.value = 0;
				}
				function objectAdd(obj) {
					++obj.value; // runs the method of the object being passed in
				}
				var o = new myobject();
				//objectAdd(o);
			</script>
            <?php
            echo "<tr>";
            echo '<td>';
			echo "<a href='#' title='".translate('Listen with Faith Comes By Hearing widget.', $st, 'sys')."' onclick='FCBHClick(o.value)'><img class='iconActions' src='images/listen-icon.jpg' alt='".translate('Listen', $st, 'sys')."' title='".translate('Listen', $st, 'sys')."' />";
			echo "</a>";
			echo "</td>";
			echo "<td>";
			echo "<a href='#' title='".translate('Listen with Faith Comes By Hearing widget.', $st, 'sys')."' onclick='FCBHClick(o.value)'><span class='lineAction' style='font-weight: bold; '>".translate('Listen', $st, 'sys')."</span></a>";
			?>
			<div class='FCBH' id='FCBHb'>
            <?php
			$FCBHArray = array();
			for ($FCBHIndex = 0; $FCBHIndex < $numFCBH; $FCBHIndex++) {
				$Bible_ID = mysql_result($resultFCBH,$FCBHIndex,"DAM_ID");			// 1 = non-drama; 2 = drama
				$StockNo = mysql_result($resultFCBH,$FCBHIndex,"StockNo");			// 1 = non-drama; 2 = drama
				if ($Bible_ID == NULL || $Bible_ID == "") {
					array_push($FCBHArray, $StockNo);								// add 1 item to the array
				}
				else {
					array_push($FCBHArray, $Bible_ID);			// add 1 item to the array
				}
			}
			$N = 0;
			$O = 0;
			$C = 0;
			$P = 0;
			$B = 0;
			$S = 0;
			$FinalFCBHArray = array();
			foreach ($FCBHArray as $FCBHIndex) {
				if (strpos($FCBHIndex, "2")) {
					preg_match('/[A-Z]2/', $FCBHIndex, $matches);
					switch ($matches[0]) {
						case "N2":
							if ($N == 0) {
								$N = 1;
								array_push($FinalFCBHArray, $FCBHIndex);
							}
							break;
						case "O2":
							if ($O == 0) {
								$O = 1;
								array_push($FinalFCBHArray, $FCBHIndex);
							}
							break;
						case "C2":
							if ($C == 0) {
								$C = 1;
								array_push($FinalFCBHArray, $FCBHIndex);
							}
							break;
						case "P2":
							if ($P == 0) {
								$P = 1;
								array_push($FinalFCBHArray, $FCBHIndex);
							}
							break;
						case "B2":
							if ($B == 0) {
								$B = 1;
								array_push($FinalFCBHArray, $FCBHIndex);
							}
							break;
						case "S2":
							if ($S == 0) {
								$S = 1;
								array_push($FinalFCBHArray, $FCBHIndex);
							}
							break;
						/*default:
							echo "Ouch! Reg Ex on Study doesn't exit!";
							break;*/
					}
				}
				else {
					preg_match('/[A-Z]1/', $FCBHIndex, $matches);
					switch ($matches[0]) {
						case "N1":
							if ($N == 0) {
								$N = 1;
								array_push($FinalFCBHArray, $FCBHIndex);
							}
							break;
						case "O1":
							if ($O == 0) {
								$O = 1;
								array_push($FinalFCBHArray, $FCBHIndex);
							}
							break;
						case "C1":
							if ($C == 0) {
								$C = 1;
								array_push($FinalFCBHArray, $FCBHIndex);
							}
							break;
						case "P1":
							if ($P == 0) {
								$P = 1;
								array_push($FinalFCBHArray, $FCBHIndex);
							}
							break;
						case "B1":
							if ($B == 0) {
								$B = 1;
								array_push($FinalFCBHArray, $FCBHIndex);
							}
							break;
						case "S1":
							if ($S == 0) {
								$S = 1;
								array_push($FinalFCBHArray, $FCBHIndex);
							}
							break;
						/*default:
							echo "Ouch! Reg Ex on Study doesn't exit!";
							break;*/
					}
				}
			}
			foreach ($FinalFCBHArray as $FCBHIndex) {
				?>
				<script language="javascript" type="application/javascript">
					objectAdd(o);
				</script>
                <?php
				$TP = $FCBHIndex;
				if (isset($mobile) && $mobile == 1) {
					?>
					<iframe src="http://www.faithcomesbyhearing.com/projects/streaming_player/widget-iframe.php?bible_id=<?php echo $TP; ?>&size=300X100&c_head_bg=820127&c_head_border_bg=f6b149&c_main_bg=e6e0c7&c_head_txt=ffffff&c_dl_txt=333333&c_dl_bg=f6b149" width="300" height="100" frameborder="0" scrolling="no" style='margin-top: 6px; '>
					<p>
					<?php
					echo translate('Sorry, this content can not be displayed because your browser does not support iframes.', $st, 'sys');
					?>
					</p>
					</iframe>
					<?php
				}
				else {
					?>
					<iframe src='http://www.faithcomesbyhearing.com/projects/streaming_player/widget-iframe.php?bible_id=<?php echo $TP; ?>&size=600X150&c_head_bg=820127&c_head_border_bg=f6b149&c_main_bg=e6e0c7&c_head_txt=ffffff&c_dl_txt=8b8265&c_dl_bg=f6b149' width='600' height='150' frameborder='0' scrolling='no' style='margin: 15px 15px 15px 0; '>
					<p>
					<?php
					echo translate('Sorry, this content can not be displayed because your browser does not support iframes.', $st, 'sys');
					?>
					</p>
					</iframe>
					<?php
				}
			}
			?>
			</div>
			<?php
			// Keep the br lines! The widget is the cause!
			//<br style='clear: right; line-height: 1px; ' />
			//<br style='line-height: 2px; ' />
            echo "</td>";
            echo "</tr>";
		}
		else {
			/*
				*************************************************************************************************************
					It is not FCBH and $Internet = 0 but it is audio.
				*************************************************************************************************************
			*/
			if ($OT_Audio > 0) {
				$query="SELECT * FROM OT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code'";	// ISO_ROD_index = '$ISO_ROD_index'";
				$result2=mysql_query($query);
				$num2=mysql_num_rows($result2);
				//<div style='margin-left: 6px; '>
				if ($num2 > 0) {
					?>
					<tr>
					<td>
					<?php
					$OT_Book = array();
					$OT_Book_Chapter = array();
					$a_index = 0;
					echo "<a href='#' title='".translate('Listen to the Old Testament.', $st, 'sys')."' onclick='ListenAudio(document.form_OT_Chapters_mp3.OT_Chapters_mp3, true, \"OTListenNow\", $OT_Audio + $NT_Audio)'><img  class='iconActions' src='images/listen-icon.jpg' alt='".translate('Listen', $st, 'sys')."' title='".translate('Listen', $st, 'sys')."' />";
					echo "</a>";
					echo "</td>";
					echo "<td>";
					?>
					<div class='OTAudio'>
					<?php
					echo "<a href='#' title='".translate('Listen to the Old Testament.', $st, 'sys')."' onclick='ListenAudio(document.form_OT_Chapters_mp3.OT_Chapters_mp3, true, \"OTListenNow\", $OT_Audio + $NT_Audio)'><span class='lineAction'>".translate('Listen', $st, 'sys')."</span></a> ".translate('to the audio Old Testament:', $st, 'sys');
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
					echo "<select id='OT_Book_mp3' name='OT_Book_mp3' class='selectOption' onchange='AudioChangeChapters(\"OT\", \"$ISO\", \"$ROD_Code\", this.options[this.selectedIndex].value); ListenAudio(document.form_OT_Chapters_mp3.OT_Chapters_mp3, true, \"OTListenNow\", $OT_Audio + $NT_Audio)'>";
					foreach ($OT_array[OT_EngBook] as $a) {											// display Eng. OT books
						$query_array="SELECT * FROM OT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code' AND OT_Audio_Book = '$a_index' AND (OT_Audio_Filename IS NOT NULL AND trim(OT_Audio_Filename) <> '')";		// ISO_ROD_index = '$ISO_ROD_index'
						$result_array=mysql_query($query_array);
						$num_array=mysql_num_rows($result_array);
						if ($num_array > 0) {
							$OT_Book[] = $a_index;
							$i=0;
							$j=(string)$a_index;
							while ($i < $num_array) {												// display the chapters using a drop-down box
								$OT_Audio_Filename = trim(mysql_result($result_array,$i,"OT_Audio_Filename"));
								if (!empty($OT_Audio_Filename)) {
									$OT_Audio_Chapter = trim(mysql_result($result_array,$i,"OT_Audio_Chapter"));
									$OT_Book_Chapter[$a_index][] = $OT_Audio_Chapter;
									$j = $j . "," . $OT_Audio_Chapter . "," . $OT_Audio_Filename;
								}
								$i++;
							}
							echo "<option id='OT_Book_$a_index' name='OT_Book_$a_index' class='selectOption' value='$j'>$a</option>";
						}
						$a_index++;
					}
					echo "</select>";
					// Get and display chapters
					?>
					<form name='form_OT_Chapters_mp3' id='form_OT_Chapters_mp3' style='display: inline; '>
					<select name='OT_Chapters_mp3' id='OT_Chapters_mp3' class='selectOption' onchange='ListenAudio(this, true, "OTListenNow", <?php echo $OT_Audio + $NT_Audio; ?>)'>
					<?php
                    $a_index = 0;
					foreach ($OT_array[OT_EngBook] as $a) {											// display Eng. OT books
						$query_array="SELECT * FROM OT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code' AND OT_Audio_Book = '$a_index' AND (OT_Audio_Filename IS NOT NULL AND trim(OT_Audio_Filename) <> '')";		// ISO_ROD_index = '$ISO_ROD_index'
						$result_array=mysql_query($query_array);
						$num_array=mysql_num_rows($result_array);
						if ($num_array > 0) {
							$i=0;
							while ($i < $num_array) {												// display the chapters using a drop-down box
								$OT_Audio_Filename = trim(mysql_result($result_array,$i,"OT_Audio_Filename"));
								if (!empty($OT_Audio_Filename)) {
									$OT_Audio_Chapter = trim(mysql_result($result_array,$i,"OT_Audio_Chapter"));
									echo "<option id='OT_Audio_Chapters_$i' name='OT_Audio_Chapters_$i' class='selectOption' value='$a^data/$ISO/audio/$OT_Audio_Filename'>$OT_Audio_Chapter</option>";
								}
								$i++;
							}
							break;
						}
						$a_index++;
					}
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
                            <div id="jquery_jplayer_<?php echo ( $NT_Audio > 0 ? '2' : '1' ) ?>" class="jp-jplayer"></div>
                            <div id="jp_container_<?php echo ( $NT_Audio > 0 ? '2' : '1' ) ?>" class="jp-audio">
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
                                            <div class="jp-volume-bar">
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
                                        To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
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
		
			if ($NT_Audio > 0) {
				$query="SELECT * FROM NT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code'";	// ISO_ROD_index = '$ISO_ROD_index'";
				$result2=mysql_query($query);
				$num2=mysql_num_rows($result2);
				if ($num2 > 0) {
					?>
					<tr>
					<td>
					<!--div style='margin-left: 6px; '-->
					<?php
					$NT_Book = array();
					$NT_Book_Chapter = array();
					$a_index = 0;
					echo "<a href='#' title='".translate('Listen to the New Testament.', $st, 'sys')."' onclick='ListenAudio(document.form_NT_Chapters_mp3.NT_Chapters_mp3, true, \"NTListenNow\", $OT_Audio + $NT_Audio)'><img class='iconActions' src='images/listen-icon.jpg' alt='".translate('Listen', $st, 'sys')."' title='".translate('Listen', $st, 'sys')."' />";
					echo "</a>";
					echo "</td>";
					echo "<td>";
					?>
					<div class='NTAudio'>
                    <?php
					echo "<a href='#' title='".translate('Listen to the New Testament.', $st, 'sys')."' onclick='ListenAudio(document.form_NT_Chapters_mp3.NT_Chapters_mp3, true, \"NTListenNow\", $OT_Audio + $NT_Audio)'><span class='lineAction'>".translate('Listen', $st, 'sys')."</span></a> ".translate('to the audio New Testament:', $st, 'sys');
					?>
                    <div id='NTAudioSelects' style='display: inline; '>
                    <?php
					if (isset($mobile) && $mobile == 1) {
						echo "<br />";
					}
					else {
						echo " ";
					}
					// Get and display Books
					echo "<select id='NT_Book_mp3' name='NT_Book_mp3' class='selectOption' onchange='AudioChangeChapters(\"NT\", \"$ISO\", \"$ROD_Code\", this.options[this.selectedIndex].value); ListenAudio(document.form_NT_Chapters_mp3.NT_Chapters_mp3, true, \"NTListenNow\", $OT_Audio + $NT_Audio)'>";
					foreach ($NT_array[NT_EngBook] as $a) {											// display Eng. NT books
						$query_array="SELECT * FROM NT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code' AND NT_Audio_Book = '$a_index' AND (NT_Audio_Filename is not null AND trim(NT_Audio_Filename) <> '')";		// ISO_ROD_index = '$ISO_ROD_index'
						$result_array=mysql_query($query_array);
						$num_array=mysql_num_rows($result_array);
						if ($num_array > 0) {
							$NT_Book[] = $a_index;
							$i=0;
							$j=(string)$a_index;
							while ($i < $num_array) {												// display the chapters
								$NT_Audio_Filename = trim(mysql_result($result_array,$i,"NT_Audio_Filename"));
								if (!empty($NT_Audio_Filename)) {
									$NT_Audio_Chapter = trim(mysql_result($result_array,$i,"NT_Audio_Chapter"));
									$NT_Book_Chapter[$a_index][] = $NT_Audio_Chapter;
									$j = $j . "," . $NT_Audio_Chapter . "," . $NT_Audio_Filename;
								}
								$i++;
							}
							echo "<option id='NT_Book_$a_index' name='NT_Book_$a_index' class='selectOption' value='$j'>$a</option>";
						}
						$a_index++;
					}
					echo "</select>";
					// Get and display chapters
					?>
					<form name='form_NT_Chapters_mp3' id='form_NT_Chapters_mp3' style='display: inline; '>
					<select name='NT_Chapters_mp3' id='NT_Chapters_mp3' class='selectOption' onchange='ListenAudio(this, true, "NTListenNow", <?php echo $OT_Audio + $NT_Audio; ?>)'>
					<?php
                    $a_index = 0;
					foreach ($NT_array[NT_EngBook] as $a) {											// display Eng. NT books
						$query_array="SELECT * FROM NT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code' AND NT_Audio_Book = '$a_index' AND (NT_Audio_Filename is not null AND trim(NT_Audio_Filename) <> '')";		// ISO_ROD_index = '$ISO_ROD_index'
						$result_array=mysql_query($query_array);
						$num_array=mysql_num_rows($result_array);
						if ($num_array > 0) {
							$i=0;
							while ($i < $num_array) {												// display the chapters
								$NT_Audio_Filename = trim(mysql_result($result_array,$i,"NT_Audio_Filename"));
								if (!empty($NT_Audio_Filename)) {
									$NT_Audio_Chapter = trim(mysql_result($result_array,$i,"NT_Audio_Chapter"));
									echo "<option id='NT_Audio_Chapters_$i' name='NT_Audio_Chapters_$i' class='selectOption' value='$a^data/$ISO/audio/$NT_Audio_Filename'>$NT_Audio_Chapter</option>";
								}
								$i++;
							}
							break;
						}
						$a_index++;
					}
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
                                            <div class="jp-volume-bar">
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
                                        To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
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
		Is it downloadable?
	*************************************************************************************************************
*/
$query="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = 'OT'";				// check if there is a OT
$result_OT=mysql_query($query);
$PDF_OT=mysql_num_rows($result_OT);
$query="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF = 'NT'";				// check if there is a NT
$result_NT=mysql_query($query);
$PDF_NT=mysql_num_rows($result_NT);

if ($NT_Audio > 0 || $OT_Audio > 0 || $PDF_NT > 0 || $PDF_OT > 0 || $SB_PDF > 0) {							// if it is a 1 then
	if ($SB_PDF > 0) {
		?>
		<form>
        <?php
		$i_SB=0;
		while ($i_SB < $SB_PDF) {
			$Item = mysql_result($result_SB,$i_SB,'Item');
			?>
			<tr style='margin-top: -2px; '>
			<td style='width: 45px; '>
			<?php
			if ($Item == 'B') {
				$whole_Bible=trim(mysql_result($result_SB,$i_SB,"Scripture_Bible_Filename"));
				echo "<a href='#' title='".translate('Download the Bible.', $st, 'sys')."' onclick='BPDF(\"$st\", \"$ISO\", \"$ROD_Code\", \"$whole_Bible\")'><img class='iconActions' src='images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
				echo "</a>";
				echo "</td>";
				echo "<td>";
				echo "<a href='#' title='".translate('Download the Bible.', $st, 'sys')."' onclick='BPDF(\"$st\", \"$ISO\", \"$ROD_Code\", \"$whole_Bible\")'><span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the Bible', $st, 'sys')."</a> (PDF)";
			}
			else {
				$complete_Scripture=trim(mysql_result($result_SB,$i_SB,"Scripture_Bible_Filename"));
				echo "<a href='#' title='".translate('Download Scripture text.', $st, 'sys')."' onclick='SPDF(\"$st\", \"$ISO\",\"$ROD_Code\", \"$complete_Scripture\")'><img class='iconActions' src='images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
				echo "</a>";
				echo "</td>";
				echo "<td>";
				echo "<a href='#' title='".translate('Download Scripture text.', $st, 'sys')."' onclick='SPDF(\"$st\", \"$ISO\",\"$ROD_Code\", \"$complete_Scripture\")'><span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('Scripture text', $st, 'sys')."</a> (PDF)";
			}
			?>
			</td>
			</tr>
			<?php
			$i_SB++;
		}
		?>
		</form>
        <?php
	}
	if ($PDF_OT > 0) {
		$OT_PDF_Filename = trim(mysql_result($result_OT,0,"OT_PDF_Filename"));								// there is a OT
		?>
		<form>
			<tr style='margin-top: -2px; '>
				<td style='width: 45px; '>
					<?php
					echo "<a href='#' title='".translate('Download the PDF Old Testament.', $st, 'sys')."' onclick='OTPDF(\"$st\", \"$ISO\",\"$ROD_Code\", \"$OT_PDF_Filename\")'><img class='iconActions' src='images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
					echo "</a>";
				echo "</td>";
				echo "<td>";
					echo "<a href='#' title='".translate('Download the PDF Old Testament.', $st, 'sys')."' onclick='OTPDF(\"$st\", \"$ISO\",\"$ROD_Code\", \"$OT_PDF_Filename\")'><span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the Old Testament (PDF)', $st, 'sys')."</a>";
					?>
				</td>
			</tr>
		</form>
        <?php
	}
	if ($OT_Audio > 0) {
		?>
			<div id='otaudiofiles' class='otaudiofiles' style='display: block; '>
			<tr style='margin-top: -2px; '>
				<td style='width: 45px; '>
					<?php
					echo "<a href='#' title='".translate('Download the audio Old Testament files.', $st, 'sys')."' onclick='OTTableClick()'><img class='iconActions' src='images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
					echo "</a>";
				echo "</td>";
				echo "<td>";
					echo "<a href='#' title='".translate('Download the audio Old Testament files.', $st, 'sys')."' onclick='OTTableClick()'><span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the Old Testament audio files (MP3)', $st, 'sys')."</a>";
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
							echo "<input style='float: right; margin-top: 0px; margin-right: 20px; font-size: 11pt; font-weight: bold; ' type='button' value='".translate('Download Selected OT Audio', $st, 'sys')."' onclick='OTAudio(\"$st\", \"$ISO\", \"$ROD_Code\")' />";
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
						foreach ($OT_array[OT_EngBook] as $a) {																// display Eng. NT books
							$query_array="SELECT * FROM OT_Audio_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_Audio_Book = '$a_index' AND (OT_Audio_Filename IS NOT NULL AND trim(OT_Audio_Filename) <> '')";
							$result_array=mysql_query($query_array);
							$num_array=mysql_num_rows($result_array);
							if ($num_array > 0) {
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
								$ii=0;
								while ($ii < $num_array) {													// display the chapters
									$OT_Audio_Filename = trim(mysql_result($result_array,$ii,"OT_Audio_Filename"));
									$temp = filesize("data/$ISO/audio/$OT_Audio_Filename");
									$temp = intval($temp/1024);			// MB
									$ZipFile += round($temp/1024, 2);
									$ZipFile = round($ZipFile, 1);
									$ii++;
								}
								echo "<input type='checkbox' id='OT_audio_$a_index' name='OT_audio_$a_index' onclick='OTAudioClick(\"$a_index\", $ZipFile)' />&nbsp;&nbsp;$a";
								?>
								</td>
								<?php
								$j++;
							}
							$a_index++;
						}
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
	if (($PDF_NT > 0 || $NT_Audio > 0) && ($PDF_OT > 0 || $OT_Audio > 0)) {
	}
	if ($PDF_NT > 0) {
		$NT_PDF_Filename = trim(mysql_result($result_NT,0,"NT_PDF_Filename"));				// there is a NT
		?>
		<form>
			<tr style='margin-top: -2px; '>
				<td style='width: 45px; '>
					<?php
					echo "<a href='#' title='".translate('Download the New Testament.', $st, 'sys')."' onclick='NTPDF(\"$st\", \"$ISO\", \"$ROD_Code\", \"$NT_PDF_Filename\")'><img class='iconActions' src='images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
					echo "</a>";
				echo "</td>";
				echo "<td>";
					echo "<a href='#' title='".translate('Download the New Testament.', $st, 'sys')."' onclick='NTPDF(\"$st\", \"$ISO\", \"$ROD_Code\", \"$NT_PDF_Filename\")'><span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the New Testament (PDF)', $st, 'sys')."</a>";
					?>
				</td>
			</tr>
		</form>
        <?php
	}
	if ($NT_Audio > 0) {
		?>
			<div id='ntaudiofiles' class='ntaudiofiles' style=''>
			<tr>
				<td style='width: 45px; '>
					<?php
					echo "<a href='#' title='".translate('Download the audio New Testament files.', $st, 'sys')."' onclick='NTTableClick()'><img class='iconActions' src='images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
					echo "</a>";
				echo "</td>";
				echo "<td>";
					echo "<a href='#' title='".translate('Download the audio New Testament files.', $st, 'sys')."' onclick='NTTableClick()'><span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the New Testament audio files (MP3)', $st, 'sys')."</a>";
					?>
                </td>
            </tr>
            <tr>
                <td colspan="2" width="100%" style='margin-bottom: -50px; '>
				<form>
				<table id='NTTable' style='margin-bottom: 15px; '>
					<tr>
						<td colspan='4' width='100%'>
							<?php
							echo "<input style='float: right; margin-top: 0px; margin-right: 20px; font-size: 11pt; font-weight: bold; ' type='button' value='".translate('Download Selected NT Audio', $st, 'sys')."' onclick='NTAudio(\"$st\", \"$ISO\", \"$ROD_Code\")' />";
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
					$j = 0;
					?>
					<tr>
						<?php
						foreach ($NT_array[NT_EngBook] as $a) {												// display Eng. NT books
							$query_array="SELECT * FROM NT_Audio_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_Audio_Book = '$a_index' AND (NT_Audio_Filename IS NOT NULL AND trim(NT_Audio_Filename) <> '')";
							$result_array=mysql_query($query_array);
							$num_array=mysql_num_rows($result_array);
							if ($num_array > 0) {
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
								$ii = 0;
								while ($ii < $num_array) {													// display the chapters
									$NT_Audio_Filename = trim(mysql_result($result_array,$ii,"NT_Audio_Filename"));
									$temp = filesize("data/$ISO/audio/$NT_Audio_Filename");
									$temp = intval($temp/1024);			// MB
									$ZipFile += round($temp/1024, 2);
									$ZipFile = round($ZipFile, 1);
									$ii++;
								}
								echo "<input type='checkbox' id='NT_audio_$a_index' name='NT_audio_$a_index' onclick='NTAudioClick(\"$a_index\", $ZipFile)' />&nbsp;&nbsp;$a";
								?>
								</td>
								<?php
								$j++;
							}
							$a_index++;
						}
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
}

/*
	*************************************************************************************************************
		Is it a cell phone module?
	*************************************************************************************************************
*/
if ($YouVersion) {
	$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND YouVersion = 1";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1 && $Internet) {
			$num=mysql_num_rows($result2);
			$URL=trim(mysql_result($result2,0,"URL"));
			?>
			<tr>
				<td style='width: 45px; '>
					<?php
					echo "<a href='$URL' target='_blank'><img class='iconActions' src='images/CellPhoneIcon.png' alt='".translate('Cell Phone', $st, 'sys')."' title='".translate('Cell Phone', $st, 'sys')."' />";
					echo "</a>";
				echo "</td>";
				echo "<td>";
					echo "<a href='$URL' target='_blank'><span class='lineAction'>" . translate('Read', $st, 'sys') . "</span> " . translate('on YouVersion.com', $st, 'sys') . "</a>";
					?>
				</td>
			</tr>
            <?php
		}
	}
}
$CellPhone=trim(mysql_result($result,0,"scripture_main.CellPhone"));
	/*
	GoBible (Java)
	MySword (Android)
	iPhone
	Windows
	Blackberry
	Standard Cell Phone
	*/
if ($CellPhone) {
	$query="SELECT * FROM CellPhone WHERE ISO_ROD_index = '$ISO_ROD_index'";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			$i=0;
			while ($i < $num) {
				$Cell_Phone_Title=trim(mysql_result($result2,$i,"Cell_Phone_Title"));
				$Cell_Phone_File=trim(mysql_result($result2,$i,"Cell_Phone_File"));
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						echo "<a href='#' alt='" . translate('Download the cell phone module for', $st, 'sys') . "$Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><img class='iconActions' src='images/CellPhoneIcon.png' alt='".translate('Cell Phone', $st, 'sys')."' title='".translate('Cell Phone', $st, 'sys')."' />";
						echo "</a>";
					echo "</td>";
					echo "<td>";
						if ($Cell_Phone_Title == 'MySword (Android)')
							if ($Internet)
								echo "<a href='#' alt='" . translate('Download the cell phone module for', $st, 'sys') . "$Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><span class='lineAction'>" . translate('Download', $st, 'sys') . "</span></a> " . translate('the cell phone module for', $st, 'sys') . " <a href='http://www.mysword.info/' target='_blank'><span class='lineAction'>$Cell_Phone_Title</span></a>";
							else
								echo "<a href='#' alt='" . translate('Download the cell phone module for', $st, 'sys') . "$Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><span class='lineAction'>" . translate('Download', $st, 'sys') . "</span></a> " . translate('the cell phone module for', $st, 'sys') . " $Cell_Phone_Title";
						else
							echo "<a href='#' alt='" . translate('Download the cell phone module for', $st, 'sys') . "$Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><span class='lineAction'>" . translate('Download', $st, 'sys') . "</span> " . translate('the cell phone module for', $st, 'sys') . " $Cell_Phone_Title</a>";
						?>
					</td>
				</tr>
                <?php
				$i++;
			}
		}
	}
}

/*
	*************************************************************************************************************
		Can it be watched?
	*************************************************************************************************************
*/
$watch=trim(mysql_result($result,0,"scripture_main.watch"));
if ($watch && $Internet) {
	$query="SELECT * FROM watch WHERE ISO_ROD_index = '$ISO_ROD_index'";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			$i=0;
			while ($i < $num) {
				$organization=trim(mysql_result($result2,$i,"organization"));
				$watch_what=trim(mysql_result($result2,$i,"watch_what"));
				$URL=trim(mysql_result($result2,$i,"URL"));
				$JesusFilm=trim(mysql_result($result2,$i,"JesusFilm"));
				$YouTube=trim(mysql_result($result2,$i,"YouTube"));
				?>
                <tr>
                <td style='width: 45px; '>
                    <a 
                    <?php
                    if ($JesusFilm) {
                        // JESUS Film
                        if (substr($URL, 0, strlen("http://api.arclight.org/videoPlayerUrl")) == "http://api.arclight.org/videoPlayerUrl") {
                            ?>
                                href="#" onclick="window.open('JESUSFilmView.php?<?php echo $URL ?>','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=890,height=690,top=300,left=300'); return false;" title="<?php echo $LN ?>">
                            <?php
                        }
                        else {
                            ?>
                                href="#" onclick="window.open('<?php echo $URL ?>','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=380,top=200,left=300'); return false;" title="<?php echo $LN ?>">
                            <?php
                        }
                    }
                    elseif ($YouTube) {
                        // YouTube
                        ?>
                            href="#" onclick="w=screen.availWidth; h=screen.availHeight; window.open('<?php echo $URL ?>','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width='+w+',height='+h+',top=0,left=0'); return false;" title="<?php echo $LN ?>">
                        <?php
                    }
                    else {
                        ?>
                        href='<?php echo $URL ?>' title='View' target='_blank'>
                        <?php
                    }
                    echo "<img class='iconActions' src='images/watch-icon.jpg' alt='".translate('Watch', $st, 'sys')."' title='".translate('Watch', $st, 'sys')."' />";
                    ?>
                    </a>
                </td>
                <td>
                    <a
                    <?php
                    if ($JesusFilm) {
                        // JESUS Film
                        if (substr($URL, 0, strlen("http://api.arclight.org/videoPlayerUrl")) == "http://api.arclight.org/videoPlayerUrl") {
                            ?>
                                href="#" onclick="window.open('JESUSFilmView.php?<?php echo $URL ?>','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=890,height=690,top=300,left=300'); return false;" title="<?php echo $LN ?>">
                            <?php
                        }
                        else {
                            ?>
                                href="#" onclick="window.open('<?php echo $URL ?>','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=380,top=200,left=300'); return false;" title="<?php echo $LN ?>">
                            <?php
                        }
                    }
                    elseif ($YouTube) {
                        // YouTube
                        ?>
                            href="#" onclick="w=screen.availWidth; h=screen.availHeight; window.open('<?php echo $URL ?>','clip','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width='+w+',height='+h+',top=0,left=0'); return false;" title="<?php echo $LN ?>">
                        <?php
                    }
                    else {
                        ?>
                        href='<?php echo $URL ?>' title='View' target='_blank'>
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
						
                        echo $watch_what;
                        ?>
                        </span>
                        <?php
                    }
                    else {
                        echo translate('View', $st, 'sys')."</span> ".translate('by', $st, 'sys')." $organization:&nbsp;$watch_what";
                    }
                    ?>
                    </a>
				</td>
            	</tr>
                <?php
				$i++;
			}
		}
	}
}

/*
	*************************************************************************************************************
		Can it be studied?
	*************************************************************************************************************
*/
$study=mysql_result($result,0,"scripture_main.study");
$viewer=mysql_result($result,0,"scripture_main.viewer");
if ($Biblesorg && $Internet) {
	$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND `Bibles_org` = 1";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			$URL=trim(mysql_result($result2,0,"URL"));
			?>
			<tr>
				<td style='width: 45px; '>
					<?php
					echo "<a href='$URL' target='_blank'><img class='iconActions' src='images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' />";
					echo "</a>";
				echo "</td>";
				echo "<td>";
					echo "<a href='$URL' target='_blank'><span class='lineAction'>" . translate('Study', $st, 'sys') . "</span> " . translate('on Bibles.org', $st, 'sys') . "</a>";
					?>
				</td>
			</tr>
            <?php
		}
	}
}
if ($viewer && (!$YouVersion && !$Biblesorg) && $Internet) {
	$ROD_Var='';
	$rtl = 0;
	$query="SELECT viewer_ROD_Variant, rtl FROM viewer WHERE ISO_ROD_index = '$ISO_ROD_index' AND Variant_Code = '$Variant_Code'";						// check if there is a viewer
	$resultViewer=mysql_query($query);
	if ($resultViewer) {
		if (mysql_num_rows($resultViewer) > 0) {
			$numViewer=mysql_num_rows($resultViewer);
			if ($numViewer > 0) {
				$ROD_Var=trim(mysql_result($resultViewer,0,"viewer_ROD_Variant"));
				$rtl=trim(mysql_result($resultViewer,0,"rtl"));
			}
		}
	}
	?>
	<tr>
		<td style='width: 45px; '>
			<?php
			echo "<a href='./viewer/views.php?iso=$ISO&ROD_Code=$ROD_Code&Variant_Code=$Variant_Code&ROD_Var=$ROD_Var&rtl=$rtl&st=$st' title='".translate('Viewer for $LN', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' />";
			echo "</a>";
		echo "</td>";
		echo "<td>";
			echo "<a href='./viewer/views.php?iso=$ISO&ROD_Code=$ROD_Code&Variant_Code=$Variant_Code&ROD_Var=$ROD_Var&rtl=$rtl&st=$st' title='".translate('Viewer for $LN', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Go to', $st, 'sys')."</span> ".translate('the online viewer', $st, 'sys')."</a>";
			?>
		</td>
	</tr>
	<?php
}
if ($study) {
	$query="SELECT * FROM study WHERE ISO_ROD_index = '$ISO_ROD_index'";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			$i=0;
			while ($i < $num) {
				$Testament=mysql_result($result2,$i,"Testament");
				$alphabet=mysql_result($result2,$i,"alphabet");
				$ScriptureURL=trim(mysql_result($result2,$i,"ScriptureURL"));
				$othersiteURL=trim(mysql_result($result2,$i,"othersiteURL"));
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
					//echo "<a href='#' style='font-size: .9em; ' title='".translate('Download $LN module.', $st, 'sys')."' onclick='Study(\"$ISO\", \"ROD_Code\", \"$ScriptureURL\")'><img class='iconActions' src='images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' />&nbsp;&nbsp;<span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the New Testament', $st, 'sys')."</a><span style='font-size: .85em; '>&nbsp;";
					//echo "<a href='#' style='font-size: .9em; ' title='".translate('Download $LN module.', $st, 'sys')."' onclick='Study(\"$ISO\", \"ROD_Code\", \"$ScriptureURL\")'><img class='iconActions' src='images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' />&nbsp;&nbsp;<span class='lineAction'>".translate('Download', $st, 'sys')."</span> ";
					echo "<a href='#' title='$LN: ".translate('Download module.', $st, 'sys')."' onclick='Study(\"$st\", \"$ISO\", \"ROD_Code\", \"$ScriptureURL\")'>";
					echo "<img class='iconActions' style='margin-top: 4px; ' src='images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' />";
					echo "</a>";
				echo "</td>";
				echo "<td>";
					echo "<a href='#' title='$LN: ".translate('Download module.', $st, 'sys')."' onclick='Study(\"$st\", \"$ISO\", \"ROD_Code\", \"$ScriptureURL\")'>";
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
					echo "</a><span style='font-size: 1em; '>&nbsp;";
					// $statement
					echo translate('for use with the Bible study software', $st, 'sys');
					// $othersiteDescription
					// � and � wont work under 00i-SpecificLanguage.php!
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
				$i++;
			}
		}
	}
}

/*
	*************************************************************************************************************
		Are the any other books?
	*************************************************************************************************************
*/
$other_titles=mysql_result($result,0,"scripture_main.other_titles");
if ($other_titles) {
	$query="SELECT * FROM other_titles WHERE ISO_ROD_index = '$ISO_ROD_index'";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			$i=0;
			while ($i < $num) {
				$other=trim(mysql_result($result2,$i,"other"));
				$other_title=trim(mysql_result($result2,$i,"other_title"));
				$other_PDF=trim(mysql_result($result2,$i,"other_PDF"));
				$other_audio=trim(mysql_result($result2,$i,"other_audio"));
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						if (!empty($other_PDF)) {
							echo "<a href='data/$ISO/PDF/$other_PDF' title='".translate('Read $other_title.', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/read-icon.jpg' alt='".translate('Books', $st, 'sys')."' title='".translate('Books', $st, 'sys')."' />";
							echo "</a>";
							echo "</td>";
							echo "<td>";
							echo "<a href='data/$ISO/PDF/$other_PDF' title='".translate('Read $other_title.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Read', $st, 'sys')."</span>";
						}
						else {
							echo "<a href='data/$ISO/audio/$other_audio' title='".translate('Listen to $other_title.', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/read-icon.jpg' alt='".translate('Books', $st, 'sys')."' title='".translate('Books', $st, 'sys')."' />";
							echo "</a>";
							echo "</td>";
							echo "<td>";
							echo "<a href='data/$ISO/audio/$other_audio' title='".translate('Listen to $other_title.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Listen', $st, 'sys')."</span>";
						}
						echo "&nbsp;$other:&nbsp;$other_title</a>";
						?>
					</td>
				</tr>
                <?php
				$i++;
			}
		}
	}
}

/*
	*************************************************************************************************************
		Can it be boughten?
	*************************************************************************************************************
*/
$buy=trim(mysql_result($result,0,"scripture_main.buy"));
if ($buy && $Internet) {
	$query="SELECT * FROM buy WHERE ISO_ROD_index = '$ISO_ROD_index'";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			$i=0;
			while ($i < $num) {
				$buy_what=trim(mysql_result($result2,$i,"buy_what"));
				$organization=trim(mysql_result($result2,$i,"organization"));
				$URL=trim(mysql_result($result2,$i,"URL"));
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						echo "<a href='$URL' title='".translate('Buy from $organization.', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/buy-icon.jpg' alt='".translate('Buy', $st, 'sys')."' title='".translate('Buy', $st, 'sys')."' />";
						echo "</a>";
					echo "</td>";
					echo "<td>";
						echo "<a href='$URL' title='".translate('Buy from $organization.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Buy', $st, 'sys')."</span> ".translate('from', $st, 'sys')." $organization: $buy_what</a>";
						?>
					</td>
				</tr>
                <?php
				$i++;
			}
		}
	}
}

/*
	*************************************************************************************************************
		Does it have any more links?
	*************************************************************************************************************
*/
$links=trim(mysql_result($result,0,"scripture_main.links"));
if ($links) {
	$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND buy = 1";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1 && $Internet) {
			$num=mysql_num_rows($result2);
			$i=0;
			while ($i < $num) {
				$company_title=trim(mysql_result($result2,$i,"company_title"));
				$company=trim(mysql_result($result2,$i,"company"));
				$URL=trim(mysql_result($result2,$i,"URL"));
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						echo "<a href='$URL' title='".translate('Link to $company.', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/buy-icon.jpg' alt='".translate('Buy', $st, 'sys')."' title='".translate('Buy', $st, 'sys')."' />";
						echo "</a>";
					echo "</td>";
					echo "<td>";
						echo "<a href='$URL' title='".translate('Link to $company.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Link', $st, 'sys')."</span> ".translate('to', $st, 'sys')." ";
						if ($company_title != "" && $company_title != NULL) {
							echo "$company_title: ";
						}
						echo "$company</a>";
						?>
					</td>
				</tr>
                <?php
				$i++;
			}
		}
	}
	// This takes care of all of the rest of the links.
	$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND map = 0 AND buy = 0 AND BibleIs = 0 AND YouVersion = 0 AND `Bibles_org` = 0";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1 && $Internet) {
			$num=mysql_num_rows($result2);
			$i=0;
			while ($i < $num) {
				$company_title=trim(mysql_result($result2,$i,"company_title"));
				$company=trim(mysql_result($result2,$i,"company"));
				$URL=trim(mysql_result($result2,$i,"URL"));
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						echo "<a href='$URL' title='".translate('Link to $company.', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/links-icon.jpg' alt='".translate('Link', $st, 'sys')."' title='".translate('Link', $st, 'sys')."' />";
						echo "</a>";
					echo "</td>";
					echo "<td>";
						echo "<a href='$URL' title='".translate('Link to $company.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Link', $st, 'sys')."</span> ".translate('to', $st, 'sys')." ";
						if ($company_title != "" && $company_title != NULL) {
							echo "$company_title: ";
						}
						echo "$company</a>";
						?>
					</td>
				</tr>
                <?php
				$i++;
			}
		}
	}
	$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND map = 1";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1 && $Internet) {
			$num=mysql_num_rows($result2);
			$i=0;
			while ($i < $num) {
				$company_title=trim(mysql_result($result2,$i,"company_title"));
				$company=trim(mysql_result($result2,$i,"company"));
				$URL=trim(mysql_result($result2,$i,"URL"));
				?>
				<tr>
					<td style='width: 45px; '>
						<?php
						echo "<a href='$URL' title='".translate('Link to $company.', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/globe-icon.png' alt='".translate('Map', $st, 'sys')."' title='".translate('Map', $st, 'sys')."' />";
						echo "</a>";
					echo "</td>";
					echo "<td>";
						echo "<a href='$URL' title='".translate('Link to $company.', $st, 'sys')."' target='_blank'><span class='lineAction'>".translate('Link', $st, 'sys')."</span> ".translate('to', $st, 'sys')." ";
						if ($company_title != "" && $company_title != NULL) {
							echo "$company_title: ";
						}
						echo "$company</a>";
						?>
					</td>
				</tr>
                <?php
				$i++;
			}
		}
	}
}

/*
	*************************************************************************************************************
		Is it playlist video?
	*************************************************************************************************************
*/
if ($PlaylistVideo && $Internet) {
	$query="SELECT PlaylistVideoTitle, PlaylistVideoFilename FROM PlaylistVideo WHERE ISO_ROD_index = '$ISO_ROD_index'";
	$result_Playlist=mysql_query($query);
	$num3=mysql_num_rows($result_Playlist);
	for ($z=1; $z <= $num3; $z++) {
		$PlaylistVideoTitle = mysql_result($result_Playlist, $z-1, 'PlaylistVideoTitle');
		$PlaylistVideoFilename = mysql_result($result_Playlist, $z-1, 'PlaylistVideoFilename');
		$filename = 'data/'.$ISO.'/video/'.$PlaylistVideoFilename;
		if (!file_exists($filename)) {
			//echo "The text file $filename does not exist.";
			continue;
		}
		$VideoFilenameContents = file_get_contents($filename);			// returns a string of the contents of the file
		$VideoConvertContents = explode("\n", $VideoFilenameContents);	// create array separate by new line
		$i = 0;
		$bs = 0;
		$VideoName = "";
		$BibleStory = "";
		for (; $i < count($VideoConvertContents); $i++) {				// iterate through $VideoConvertContents looking for a 0 and 1.
			if (strstr($VideoConvertContents[$i], "\t", true) == '0') {	// true = before the first occurance (\t); the title
				$bs = $i;	// Bible Story = $i
				$VideoName = substr($VideoConvertContents[0], strrpos($VideoConvertContents[0], "\t") + 1, -1);
				continue;
			}
			if (strstr($VideoConvertContents[$i], "\t", true) == '1') {	// true = before the first occurance (\t); the 1st new testament entry
				break;
			}
		}
		if ($st == "spa") {
			if ($PlaylistVideoTitle == "Luke Video") $PlaylistVideoTitle = "video de San Lucas";
			if ($PlaylistVideoTitle == "Genesis Video") $PlaylistVideoTitle = "video de Genesis";
			if ($PlaylistVideoTitle == "Acts Video") $PlaylistVideoTitle = "video de Hechos";
		}
		// $i = the number of rows beginning with the number 1 in the first column
		?>
		<script>
			var orgVideoPixels_<?php echo $z; ?> = 0;
		</script>
		<tr>
			<td style='width: 45px; '>
				<?php
				echo "<a href='#' title='".translate('View', $st, 'sys')." $PlaylistVideoTitle' onClick='PlaylistVideo(orgVideoPixels_$z, \"PlaylistVideoNow_$z\")'><img class='iconActions' src='images/watch-icon.jpg' alt='".translate('View', $st, 'sys')."' title='".translate('View', $st, 'sys')."' /></a>";
				?>
			</td>
			<td>
				<?php
				echo "<a href='#' title='".translate('View', $st, 'sys')." $PlaylistVideoTitle' onClick='PlaylistVideo(orgVideoPixels_$z, \"PlaylistVideoNow_$z\")'><span class='lineAction'>".translate('View', $st, 'sys')."</span> $PlaylistVideoTitle</a>";
				// Get and display Playlist
				?>
			</td>
		</tr>
        <tr id="PlaylistVideoNow_<?php echo $z; ?>" style="display: none; ">
			<td style='width: 45px; '>&nbsp;
			</td>
            <td>
				<?php
					if ($bs != 0) {
						$VideoConvertContents[$bs] = str_replace("\r", "", $VideoConvertContents[$bs]);		// Windows text files have a carrage return at the end.
						$VideoConvertWithTab = explode("\t", $VideoConvertContents[$bs]);					// split the line up by tabs
						if ($st == "spa") {
							if ($VideoName == "Luke Video") $VideoName = "Video de San Lucas";
							if ($VideoName == "Genesis Video") $VideoName = "Video de Genesis";
							if ($VideoName == "Acts Video") $VideoName = "Video de Hechos";
						}
						echo "<div style='text-align: center; '><a href='#' style='text-align: center; ' title='".$VideoName.' '.translate('from the Bible', $st, 'sys')."' onClick='window.open(\"".$VideoConvertWithTab[3]."\",\"_blank\")'><img src='data/".$ISO."/video/".$VideoConvertWithTab[2]."' alt='".translate('View', $st, 'sys')." ".$VideoName.' '.translate('from the Bible', $st, 'sys')."' title='".translate('View', $st, 'sys')." ".$VideoName.' '.translate('from the Bible', $st, 'sys')."' /></a></div>";
						echo '<div style="text-align: center; font-size: .9em; margin-bottom: 10px; font-weight: normal; ">'.$VideoName.' '.translate('from the Bible', $st, 'sys').'</div>';
						echo '<hr style="color: navy; text-align: center; width: 75%; " />';
					}
				?>
				<table style='width: 100%'>
					<tr style="margin-top: 8px; margin-bottom: 8px; ">
					<?php
						$c = 0;
						for ($a = $i; $i < count($VideoConvertContents); $i++, $c++) {							// continue using $i to iterate through $VideoConvertContents
							if ($c%4 == 0) {
								if ($a != $i) {
									echo '</tr>';
									echo '<tr style="margin-top: 8px; margin-bottom: 8px; ">';
								}
							}
							$VideoConvertContents[$i] = str_replace("\r", "", $VideoConvertContents[$i]);		// Windows text files have a carrage return at the end.
							$VideoConvertWithTab = explode("\t", $VideoConvertContents[$i]);					// split the line up by tabs
							echo '<td style="width: 25%; text-align: center; ">';
								echo "<a href='#' title='".$VideoConvertWithTab[1]."' onClick='window.open(\"".$VideoConvertWithTab[3]."\",\"_blank\")'><img src='data/".$ISO."/video/".$VideoConvertWithTab[2]."' alt='".translate('View', $st, 'sys')." ".$VideoConvertWithTab[1]."' title='".translate('View', $st, 'sys')." ".$VideoConvertWithTab[1]."' /></a>";
								echo '<div style="text-align: center; font-size: .7em; font-weight: normal; ">'.$VideoConvertWithTab[1].'</div>';
							echo '</td>';
						}
						for (; $c%4 != 0; $c++) {
							echo "<td>&nbsp;</td>";
						}
					?>
					</tr>
				</table>
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
	$query="SELECT PlaylistAudioTitle, PlaylistAudioFilename FROM PlaylistAudio WHERE ISO_ROD_index = '$ISO_ROD_index'";
	$result_Playlist=mysql_query($query);
	$num3=mysql_num_rows($result_Playlist);
	for ($z=1; $z <= $num3; $z++) {
		$PlaylistAudioTitle = mysql_result($result_Playlist, $z-1, 'PlaylistAudioTitle');
		$PlaylistAudioFilename = mysql_result($result_Playlist, $z-1, 'PlaylistAudioFilename');
		?>
		<tr>
			<td style='width: 45px; '>
				<?php
				echo "<a href='#' title='".translate('Listen', $st, 'sys')." $PlaylistAudioTitle' onClick='PlaylistAudio_$z($z, $num3)'><img class='iconActions' src='images/listen-icon.jpg' alt='".translate('Listen', $st, 'sys')."' title='".translate('Listen', $st, 'sys')."' /></a>";
				?>
			</td>
			<td>
				<?php
				echo "<a href='#' title='".translate('Listen', $st, 'sys')." $PlaylistAudioTitle' onClick='PlaylistAudio_$z($z, $num3)'><span class='lineAction'>".translate('Listen', $st, 'sys').":</span> $PlaylistAudioTitle</a>";
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
								$filename = 'data/'.$ISO.'/audio/'.$PlaylistAudioFilename;
								if (file_exists($filename)) {
									$homepage = file_get_contents($filename);		// returns a string
									echo $homepage;
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

						// http://ryanve.com/lab/dimensions/ = "Documents" heights
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
									orgPixels_<?php echo $z; ?> = document.body.scrollHeight - 42;
									document.getElementById('PlaylistAudioListenNow_'+futureNumber).style.display = "block";
									divHeight = document.body.scrollHeight - 31;
								}
								else {
									divHeight = orgPixels_<?php echo $z; ?>;
								}
								document.getElementById("container").style.height = divHeight + "px";
								// if the table is long enough IE goes to dark black (blur and opacity). I don't know why.
								$("#container").redrawShadow({left: 5, top: 5, blur: 2, opacity: 0.5, color: "black", swap: false});
							});
						}
					</script>
	
					<div id="jquery_jplayer_playlist_<?php echo $z; ?>" class="jp-jplayer"></div>
					<div id="jp_container_playlist_<?php echo $z; ?>" class="jp-audio-playlist">
						<div class="jp-type-playlist">
							<div class="jp-gui jp-interface">
								<ul class="jp-controls">
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
								<div class="jp-current-time"></div>
								<div class="jp-duration"></div>
							</div>
							<div class="jp-playlist">
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
                echo "<a href='#' title='".translate('Download', $st, 'sys')." $PlaylistAudioTitle' onclick='PlaylistTableClick_$z()'><img class='iconActions' src='images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' />";
                echo "</a>";
            echo "</td>";
            echo "<td>";
                echo "<a href='#' title='".translate('Download', $st, 'sys')." $PlaylistAudioTitle' onclick='PlaylistTableClick_$z()'><span class='lineAction'>".translate('Download', $st, 'sys').":</span> ". $PlaylistAudioTitle ."</a>";
                //$inputPlaylistAudioZip = preg_replace('/.+\/(.+\.mp3)[\'" \}]+,?\s?\x{000a}?/', '$1|', $homepage);
				//$inputPlaylistAudioZip = rtrim($inputPlaylistAudioZip, "|");
				$homepage = preg_replace('/{\s*title:\s*[\'"]([^\'"]+)[\'"],\s*mp3:\s*[\'"]([^\'"]+)[\'"]\s*},?\s*/', '$1^$2|', $homepage);
				$homepage = rtrim($homepage, "|");
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
                ?>
                <form>
                <table id="DLPlaylistAudio_<?php echo $z; ?>" style="width: <?php echo $tableWidth; ?>px; margin-top: 5px; margin-right: 10px; font-weight: bold; font-size: 11pt; ">
                    <tr>
                        <td colspan="<?php echo $howManyCol; ?>" style='width: 100%; '>
							<div style="float: right; margin-top: 0; margin-right: 15px; margin-bottom: 4px; ">
								<?php
								$CountArr = count($arr);
								echo "<input style='font-size: 1em; font-weight: bold; font-family: Arial, Helvetica, sans-serif; ' type='button' value='".translate('Download Audio Playlist', $st, 'sys')."' onclick='PlaylistAudioZip(\"$st\", \"$ISO\", \"$ROD_Code\", \"$z\", $CountArr)' />";
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
							$j++;
                            //echo "<input type='checkbox' id='Playlist_audio_${z}_$j' name='Playlist_audio_${z}_$j' onclick='PlaylistAudioClick_$z(\"$z\", $j, $ZipFile)' />";
							echo "<input type='checkbox' id='Playlist_audio_${z}_$j' name='Playlist_audio_${z}_$j' value='$single[1]' onclick='PlaylistAudioClick_$z(\"$z\", $j, $ZipFile)' />";
							echo "<div style='display: inline; float: right; width: ${DivIndent}%; margin-right: 20px; '>$single[0]</div>";
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
					// http://ryanve.com/lab/dimensions/ = "Documents" heights
					function PlaylistTableClick_<?php echo $z; ?>() {
						$(document).ready(function() {
							if (PlaylistTableVisible_<?php echo $z; ?> == 0) {
								divHeight_<?php echo $z; ?> = document.documentElement.clientHeight;
								document.getElementById("DLPlaylistAudio_<?php echo $z; ?>").style.display = "block";
								PlaylistTableVisible_<?php echo $z; ?> = 1;
								document.getElementById("container").style.height = (document.documentElement.scrollHeight - 32) + "px";
							}
							else {
								document.getElementById("DLPlaylistAudio_<?php echo $z; ?>").style.display = "none";
								PlaylistTableVisible_<?php echo $z; ?> = 0;
								document.getElementById("container").style.height = (divHeight_<?php echo $z; ?> + 204) + "px";
							}
							// if the table is long enough IE goes to dark black (blur and opacity). I don't know why.
							$("#container").redrawShadow({left: 5, top: 5, blur: 2, opacity: 0.5, color: "black", swap: false});
						});
					}
				</script>
            </td>
        </tr>
        <?php
	}
}
?>
</table>
<br />
</div>
