<?php
function WhichAudioPlayer($ISO_ROD_index, $ISO, $ROD_Code) {
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
	$queryFCBH="SELECT DAM_ID, StockNo FROM FCBHLanguageList WHERE ISO_ROD_index = '$ISO_ROD_index' ORDER BY DAM_ID DESC, StockNo DESC";		// N = New Testament; C = Complete Bible
	$resultFCBH=mysql_query($queryFCBH);
	$numFCBH=mysql_num_rows($resultFCBH);
	if (!$numFCBH) {
		$queryFCBH="SELECT DAM_ID, StockNo FROM FCBHLanguageList WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code' ORDER BY DAM_ID DESC, StockNo DESC";		// N = New Testament; C = Complete Bible
		$resultFCBH=mysql_query($queryFCBH);
		$numFCBH=mysql_num_rows($resultFCBH);
	}
	if ($numFCBH > 0) {
		$Bible_ID = mysql_result($resultFCBH,0,'DAM_ID');			// 1 = non-drama; 2 = drama
		$StockNo = mysql_result($resultFCBH,0,'StockNo');			// 1 = non-drama; 2 = drama
		if ($Bible_ID) {
			return $Bible_ID;
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
$Biblesorg=mysql_result($result,0,'scripture_main.Bibles_org');			// boolean

$ISO_Country=mysql_result($result,0,'countries.ISO_Country');

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

/*
	*************************************************************************************************************
		Is it PDF and listening?
	*************************************************************************************************************
*/
$BibleIs=mysql_result($result,0,"scripture_main.BibleIs");			// boolean
if ($BibleIs) {
	$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND BibleIs = 1";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			$URL=trim(mysql_result($result2,0,"URL"));
			?>
			<div class='lineCategories'>
			<p class='lineItem'>
            <?php
			echo "<a href='$URL' target='_blank'><img class='iconActions' align='middle' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' width='45' height='40' />";
			echo "&nbsp;&nbsp;<span class='lineAction'>" . translate('Read and Listen', $st, 'sys') . "</span> ";
			if (stripos($URL, '/Gen/') !== false)
				echo translate('to the Old Testament', $st, 'sys');
			else
				echo translate('to the New Testament', $st, 'sys');
			echo "</a>";
			?>
            </p>
			</div>
            <?php
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
		?>
		<div class='lineCategories'>
        <?php
		if ($SB_PDF > 0) {
			$i_SB=0;
			while ($i_SB < $SB_PDF) {
				?>
				<p class='lineItem'>
                <?php
				$Item = mysql_result($result_SB,$i_SB,'Item');
				if ($Item == 'B') {
					$whole_Bible=trim(mysql_result($result_SB,$i_SB,"Scripture_Bible_Filename"));
					echo "<a href='data/$ISO/PDF/$whole_Bible' title='".translate('Read the Bible.', $st, 'sys')."' target='_blank'><img class='iconActions' align='middle' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' width='45' height='40' />";
					echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('the Bible', $st, 'sys')."</a>";
				}
				else {
					$complete_Scripture=trim(mysql_result($result_SB,$i_SB,"Scripture_Bible_Filename"));
					echo "<a href='data/$ISO/PDF/$complete_Scripture' title='".translate('Read a Scripture portion.', $st, 'sys')."' target='_blank'><img class='iconActions' align='middle' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' width='45' height='40' />";
					echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('a Scripture portion', $st, 'sys')."</a>";
				}
				?>
				</p>
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
                <p class='lineItem'>
                <?php
				echo "<a href='data/$ISO/PDF/$OT_PDF_Filename' title='".translate('Read the Old Testament.', $st, 'sys')."' target='_blank'><img class='iconActions' align='middle' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' width='45' height='40' />";
				echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('the Old Testament', $st, 'sys')."</a>";
				?>
                </p>
                <?php
			}
			$query="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF != 'OT'";		// check if there is any other book but the OT
			$result1=mysql_query($query);
			$num=mysql_num_rows($result1);
			if ($num > 0) {
				$i=0;
				$a_index = 0;
				?>
                <form name='PDF_OT'>
				<p class='lineItem'>
                <?php
				echo "<img class='iconActions' align='middle' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' width='45' height='40' />";
				echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('a book from the Old Testament:', $st, 'sys')." ";
				echo "<select style='color: navy; font-weight: bold; font-size: 11pt; ' name='OT_PDF' onchange='if (this.options[this.selectedIndex].text != \"".translate('Choose One...', $st, 'sys')."\") { window.open(this.options[this.selectedIndex].value, \"_blank\"); }'>";
				echo "<option style='color: navy; font-weight: bold; font-size: 11pt; '>".translate('Choose One...', $st, 'sys')."</option>";
				foreach ($OT_array[OT_EngBook] as $a) {															// there is/are book(s)
					$query_array="SELECT * FROM OT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND OT_PDF = '$a_index'";
					$result_array=mysql_query($query_array);
					$num=mysql_num_rows($result_array);
					if ($num > 0) {
						$OT_PDF_Filename = trim(mysql_result($result_array,0,"OT_PDF_Filename"));
						$a = str_replace(" ", "&nbsp;", $a);
						if (!empty($OT_PDF_Filename)) {
							echo "<option style='color: navy; font-weight: bold; font-size: 11pt; ' value='data/$ISO/PDF/$OT_PDF_Filename'>$a</option>";
						}
					}
					$a_index++;
				}
				echo "</select>";
				?>
				</p>
				</form>
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
				<p class='lineItem'>
                <?php
				echo "<a href='data/$ISO/PDF/$NT_PDF_Filename' title='".translate('Read the New Testament.', $st, 'sys')."' target='_blank'><img class='iconActions' align='middle' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' width='45' height='40' />";
				echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('the New Testament', $st, 'sys')."</a>";
				?>
                </p>
                <?php
			}
			$query="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF != 'NT'";				// check if there is any other book but the NT
			$result1=mysql_query($query);
			$num=mysql_num_rows($result1);
			if ($num > 0) {
				$i=0;
				$a_index = 0;
				?>
				<form name='PDF_NT'>
				<p class='lineItem'>
                <?php
				echo "<img class='iconActions' align='middle' src='images/read-icon.jpg' alt='".translate('Read', $st, 'sys')."' title='".translate('Read', $st, 'sys')."' width='45' height='40' />";
				echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Read', $st, 'sys')."</span> ".translate('a book from the New Testament:', $st, 'sys')." ";
				echo "<select style='color: navy; font-weight: bold; font-size: 11pt; ' name='NT_PDF' onchange='if (this.options[this.selectedIndex].text != \"".translate('Choose One...', $st, 'sys')."\") { window.open(this.options[this.selectedIndex].value, \"_blank\"); }'>";
				echo "<option style='color: navy; font-weight: bold; font-size: 11pt; '>".translate('Choose One...', $st, 'sys')."</option>";
				foreach ($NT_array[NT_EngBook] as $a) {																	// there is/are book(s)
					$query_array="SELECT * FROM NT_PDF_Media WHERE ISO_ROD_index = '$ISO_ROD_index' AND NT_PDF = '$a_index'";
					$result_array=mysql_query($query_array);
					$num=mysql_num_rows($result_array);
					if ($num > 0) {
						$NT_PDF_Filename = trim(mysql_result($result_array,0,"NT_PDF_Filename"));
						$a = str_replace(" ", "&nbsp;", $a);
						if (!empty($NT_PDF_Filename)) {
							echo "<option style='color: navy; font-weight: bold; font-size: 11pt; ' value='data/$ISO/PDF/$NT_PDF_Filename'>$a</option>";
						}
					}
					$a_index++;
				}
				echo "</select>";
				?>
				</p>
				</form>
                <?php
			}
		}
		?>
		</div>
        <?php
	}
//}

/*
	*************************************************************************************************************
		Is it audio?
	*************************************************************************************************************
*/
if (!$BibleIs) {
	$OT_Audio=mysql_result($result,0,"scripture_main.OT_Audio");		// boolean
	$NT_Audio=mysql_result($result,0,"scripture_main.NT_Audio");		// boolean
	$FCBH=mysql_result($result,0,"scripture_main.FCBH");				// boolean
	if ($NT_Audio > 0 || $OT_Audio > 0 || $FCBH > 0) {					// if the boolean is 1
		/*
			*************************************************************************************************************
				Is it FCBH? If it is the script needs the Bible ID or stock number.
			*************************************************************************************************************
		*/
		if ($FCBH > 0) {
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
			echo "<a href='#' title='".translate('Listen with Faith Comes By Hearing widget.', $st, 'sys')."' onclick='FCBHClick(o.value)'><img style='margin-top: 0px; margin-bottom: 2px; margin-left: 10px; border-style: none; ' align='middle' src='images/listen-icon.jpg' alt='".translate('Listen', $st, 'sys')."' title='".translate('Listen', $st, 'sys')."' width='45' height='40' />";
			echo "&nbsp;&nbsp;<span class='lineAction' style='font-weight: bold; '>".translate('Listen', $st, 'sys')."</span></a>";
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
			// Keep the br lines! The widget is the cause!
			?>
            </div>
			<br style='clear: right; line-height: 1px; ' />
			<br style='line-height: 2px; ' />
            <?php
		}
		else {
			/*
				*************************************************************************************************************
					It is not FCBH but it is audio.
				*************************************************************************************************************
			*/
			if ($OT_Audio > 0) {
				$query="SELECT * FROM OT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code'";	// ISO_ROD_index = '$ISO_ROD_index'";
				$result2=mysql_query($query);
				$num2=mysql_num_rows($result2);
				?>
				<div style='margin-left: 6px; '>
                <?php
				if ($num2 > 0) {
					$OT_Book = array();
					$OT_Book_Chapter = array();
					echo "<div class='OTAudio'>";
					$a_index = 0;
					echo "<a href='#' title='".translate('Listen to the Old Testament.', $st, 'sys')."' onclick='ListenAudio(document.form_OT_Chapters_mp3.OT_Chapters_mp3, true, \"OTListenNow\", $OT_Audio + $NT_Audio)'><img style='margin-top: -9px; margin-bottom: 4px; margin-left: 4px; border-style: none; ' align='middle' src='images/listen-icon.jpg' alt='".translate('Listen', $st, 'sys')."' title='".translate('Listen', $st, 'sys')."' width='45' height='40' />";
					echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Listen', $st, 'sys')."</span></a> ".translate('to the audio Old Testament:', $st, 'sys')." ";
					?>
					<div id='OTAudioSelects' style='display: inline; '>
                    <?php
					// Get and display Books
					echo "<select id='OT_Book_mp3' name='OT_Book_mp3' style='color: navy; font-weight: bold; font-size: 11pt; ' onchange='AudioChangeChapters(\"OT\", \"$ISO\", \"$ROD_Code\", this.options[this.selectedIndex].value); ListenAudio(document.form_OT_Chapters_mp3.OT_Chapters_mp3, true, \"OTListenNow\", $OT_Audio + $NT_Audio)'>";
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
							echo "<option id='OT_Book_$a_index' name='OT_Book_$a_index' style='color: navy; font-weight: bold; font-size: 11pt; ' value='$j'>$a</option>";
						}
						$a_index++;
					}
					echo "</select>";
					// Get and display chapters
					?>
					<form name='form_OT_Chapters_mp3' id='form_OT_Chapters_mp3' style='display: inline; color: navy; font-weight: bold; font-size: 12pt; '>
					<select name='OT_Chapters_mp3' id='OT_Chapters_mp3' style='color: navy; font-weight: bold; font-size: 11pt; ' onchange='ListenAudio(this, true, "OTListenNow", <?php echo $OT_Audio + $NT_Audio; ?>)'>
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
									echo "<option id='OT_Audio_Chapters_$i' name='OT_Audio_Chapters_$i' style='color: navy; font-weight: bold; font-size: 11pt; ' value='$a^data/$ISO/audio/$OT_Audio_Filename'>$OT_Audio_Chapter</option>";
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
                        <div class='ourFlashPlayer' >
                            <span id='OTBookChapter' style='vertical-align: top; '> listenBook " " listenChapter </span> &nbsp;&nbsp; 
                            <div id="jquery_jplayer_<?php echo ( $NT_Audio > 0 ? '2' : '1' ) ?>" class="jp-jplayer"></div>
                            <div id="jp_container_<?php echo ( $NT_Audio > 0 ? '2' : '1' ) ?>" class="jp-audio">
                                <div class="jp-type-single">
                                    <div class="jp-gui jp-interface">
                                        <ul class="jp-controls">
                                            <li><a href="#" class="jp-play" tabindex="1">play</a></li>
                                            <li><a href="#" class="jp-pause" tabindex="1">pause</a></li>
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
                                        <div class="jp-time-holder">
                                            <div class="jp-current-time"></div>
                                            <div class="jp-duration"></div>
                                            <!--ul class="jp-toggles">
                                                <li><a href="#" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
                                                <li><a href="#" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
                                            </ul-->
                                        </div>
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
                        </div>
                    </div>
                    <?php
				}
				?>
				</div>
                <?php
			}
		
			if ($NT_Audio > 0) {
				$query="SELECT * FROM NT_Audio_Media WHERE ISO = '$ISO' AND ROD_Code = '$ROD_Code'";	// ISO_ROD_index = '$ISO_ROD_index'";
				$result2=mysql_query($query);
				$num2=mysql_num_rows($result2);
				?>
				<div style='margin-left: 6px; '>
                <?php
				if ($num2 > 0) {
					$NT_Book = array();
					$NT_Book_Chapter = array();
					?>
					<div class='NTAudio'>
                    <?php
					$a_index = 0;
					echo "<a href='#' title='".translate('Listen to the New Testament.', $st, 'sys')."' onclick='ListenAudio(document.form_NT_Chapters_mp3.NT_Chapters_mp3, true, \"NTListenNow\", $OT_Audio + $NT_Audio)'><img style='margin-top: -9px; margin-bottom: 4px; margin-left: 4px; border-style: none; ' align='middle' src='images/listen-icon.jpg' alt='".translate('Listen', $st, 'sys')."' title='".translate('Listen', $st, 'sys')."' width='45' height='40' />";
					echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Listen', $st, 'sys')."</span></a> ".translate('to the audio New Testament:', $st, 'sys')." ";
					?>
                    <div id='NTAudioSelects' style='display: inline; '>
                    <?php
					// Get and display Books
					echo "<select id='NT_Book_mp3' name='NT_Book_mp3' style='color: navy; font-weight: bold; font-size: 11pt; ' onchange='AudioChangeChapters(\"NT\", \"$ISO\", \"$ROD_Code\", this.options[this.selectedIndex].value); ListenAudio(document.form_NT_Chapters_mp3.NT_Chapters_mp3, true, \"NTListenNow\", $OT_Audio + $NT_Audio)'>";
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
							echo "<option id='NT_Book_$a_index' name='NT_Book_$a_index' style='color: navy; font-weight: bold; font-size: 11pt; ' value='$j'>$a</option>";
						}
						$a_index++;
					}
					echo "</select>";
					// Get and display chapters
					?>
					<form name='form_NT_Chapters_mp3' id='form_NT_Chapters_mp3' style='display: inline; color: navy; font-weight: bold; font-size: 11pt; '>
					<select name='NT_Chapters_mp3' id='NT_Chapters_mp3' style='color: navy; font-weight: bold; font-size: 11pt; ' onchange='ListenAudio(this, true, "NTListenNow", <?php echo $OT_Audio + $NT_Audio; ?>)'>
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
									echo "<option id='NT_Audio_Chapters_$i' name='NT_Audio_Chapters_$i' style='color: navy; font-weight: bold; font-size: 11pt; ' value='$a^data/$ISO/audio/$NT_Audio_Filename'>$NT_Audio_Chapter</option>";
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
                        <div class='ourFlashPlayer'>
                            <span id='NTBookChapter' style='vertical-align: top; '> listenBook " " listenChapter </span> &nbsp;&nbsp; 
                            <div id="jquery_jplayer_1" class="jp-jplayer"></div>
                            <div id="jp_container_1" class="jp-audio">
                                <div class="jp-type-single">
                                    <div class="jp-gui jp-interface">
                                        <ul class="jp-controls">
                                            <li><a href="#" class="jp-play" tabindex="1">play</a></li>
                                            <li><a href="#" class="jp-pause" tabindex="1">pause</a></li>
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
                                        <div class="jp-time-holder">
                                            <div class="jp-current-time"></div>
                                            <div class="jp-duration"></div>
                                            <!--ul class="jp-toggles">
                                                <li><a href="#" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
                                                <li><a href="#" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
                                            </ul-->
                                        </div>
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
                        </div>
                    </div>
                    <?php
				}
				?>
				</div>
                <?php
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
			<div class='lineCategories' style='margin-top: -2px; '>
			<p class='lineItem'>
			<?php
			if ($Item == 'B') {
				$whole_Bible=trim(mysql_result($result_SB,$i_SB,"Scripture_Bible_Filename"));
				echo "<a href='#' title='".translate('Download the Bible.', $st, 'sys')."' onclick='BPDF(\"$st\", \"$ISO\", \"$ROD_Code\", \"$whole_Bible\")'><img class='iconActions' align='middle' src='images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' width='45' height='40' />";
				echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the Bible', $st, 'sys')."</a> (PDF)";
			}
			else {
				$complete_Scripture=trim(mysql_result($result_SB,$i_SB,"Scripture_Bible_Filename"));
				echo "<a href='#' title='".translate('Download Scripture text.', $st, 'sys')."' onclick='SPDF(\"$st\", \"$ISO\",\"$ROD_Code\", \"$complete_Scripture\")'><img class='iconActions' align='middle' src='images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' width='45' height='40' />";
				echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('Scripture text', $st, 'sys')."</a> (PDF)";
			}
			?>
			</p>
			</div>
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
			<div class='lineCategories' style='margin-top: -2px; '>
				<p class='lineItem'>
					<?php
					echo "<a href='#' title='".translate('Download the PDF Old Testament.', $st, 'sys')."' onclick='OTPDF(\"$st\", \"$ISO\",\"$ROD_Code\", \"$OT_PDF_Filename\")'><img class='iconActions' align='middle' src='images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' width='45' height='40' />";
					echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the Old Testament (PDF)', $st, 'sys')."</a>";
					?>
				</p>
			</div>
		</form>
        <?php
	}
	if ($OT_Audio > 0) {
		?>
		<form>
			<div id='otaudiofiles' class='otaudiofiles' style='display: block; '>
				<div class='lineCategories' style='margin-top: -2px; '>
					<p class='lineItem'>
						<?php
						echo "<a href='#' title='".translate('Download the audio Old Testament files.', $st, 'sys')."' onclick='OTTableClick()'><img class='iconActions' align='middle' src='images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' width='45' height='40' />";
						echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the Old Testament audio files (MP3)', $st, 'sys')."</a>";
						?>
					</p>
				</div>
				<table id='OTTable' style='clear: both; width: 775px; margin: 5px auto 0 auto; font-weight: bold; font-size: 11pt; '>
					<tr>
						<td colspan='5' width='100%'>
							<?php
							echo "<input style='float: right; margin-top: 0px; margin-right: 20px; font-size: 11pt; font-weight: bold; ' type='button' value='".translate('Download Selected OT Audio', $st, 'sys')."' onclick='OTAudio(\"$st\", \"$ISO\", \"$ROD_Code\")' />";
							?>
							<div id='OT_Download_MB' style='float: right; vertical-align: bottom; margin-top: 6px; '></div>
						</td>
					</tr>
					<?php
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
								if ($j == 5) {
									?>
									</tr>
									<tr>
									<?php
									$j = 0;
								}
								?>
								<td width='20%'>
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
						for (; $j <= 5; $j++) {
							?>
							<td width='20%'>&nbsp;</td>
							<?php
						}
						?>
					</tr>
				</table>
			</div>
		</form>
        <?php
	}
	if (($PDF_NT > 0 || $NT_Audio > 0) && ($PDF_OT > 0 || $OT_Audio > 0)) {
	}
	if ($PDF_NT > 0) {
		$NT_PDF_Filename = trim(mysql_result($result_NT,0,"NT_PDF_Filename"));				// there is a NT
		?>
		<form>
			<div class='lineCategories' style='margin-top: -2px; '>
				<p class='lineItem'>
					<?php
					echo "<a href='#' title='".translate('Download the New Testament.', $st, 'sys')."' onclick='NTPDF(\"$st\", \"$ISO\", \"$ROD_Code\", \"$NT_PDF_Filename\")'><img class='iconActions' align='middle' src='images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' width='45' height='40' />";
					echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the New Testament (PDF)', $st, 'sys')."</a>";
					?>
				</p>
			</div>
		</form>
        <?php
	}
	if ($NT_Audio > 0) {
		?>
		<form>
			<div id='ntaudiofiles' class='ntaudiofiles' style='display: block; '>
				<div class='lineCategories' style='margin-top: -2px; '>
					<p class='lineItem'>
						<?php
						echo "<a href='#' title='".translate('Download the audio New Testament files.', $st, 'sys')."' onclick='NTTableClick()'><img class='iconActions' align='middle' src='images/download-icon.jpg' alt='".translate('Download', $st, 'sys')."' title='".translate('Download', $st, 'sys')."' width='45' height='40' />";
						echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the New Testament audio files (MP3)', $st, 'sys')."</a>";
						?>
					</p>
				</div>
				<table id='NTTable' style='clear: both; width: 775px; margin: 5px auto 0 auto; font-weight: bold; font-size: 11pt; '>
					<tr>
						<td colspan='5' width='100%'>
							<?php
							echo "<input style='float: right; margin-top: 0px; margin-right: 20px; font-size: 11pt; font-weight: bold; ' type='button' value='".translate('Download Selected NT Audio', $st, 'sys')."' onclick='NTAudio(\"$st\", \"$ISO\", \"$ROD_Code\")' />";
							?>
							<div id='NT_Download_MB' style='float: right; vertical-align: bottom; margin-top: 6px; '></div>
						</td>
					</tr>
					<?php
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
								if ($j == 5) {
									?>
									</tr>
									<tr>
									<?php
									$j = 0;
								}
								?>
								<td>
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
						for (; $j <= 5; $j++) {
							?>
							<td width='20%'>&nbsp;</td>
							<?php
						}
						?>
					</tr>
				</table>
			</div>
		</form>
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
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			$URL=trim(mysql_result($result2,0,"URL"));
			?>
			<div class='lineCategories'>
			<p class='lineItem'>
            <?php
			echo "<a href='$URL' target='_blank'><img class='iconActions' align='middle' src='images/CellPhoneIcon.png' alt='".translate('Cell Phone', $st, 'sys')."' title='".translate('Cell Phone', $st, 'sys')."' width='45' height='40' />";
			echo "&nbsp;&nbsp;<span class='lineAction'>" . translate('Read', $st, 'sys') . "</span> " . translate('on YouVersion.com', $st, 'sys') . "</a>";
			?>
            </p>
			</div>
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
			?>
			<div class='lineCategories'>
            <?php
			$i=0;
			while ($i < $num) {
				$Cell_Phone_Title=trim(mysql_result($result2,$i,"Cell_Phone_Title"));
				$Cell_Phone_File=trim(mysql_result($result2,$i,"Cell_Phone_File"));
				?>
				<p class='lineItem'>
                <?php
				echo "<a href='#' alt='" . translate('Download the cell phone module for', $st, 'sys') . "$Cell_Phone_Title' onclick='CellPhoneModule(\"$st\", \"$ISO\", \"$ROD_Code\", \"$Cell_Phone_File\")'><img class='iconActions' align='middle' src='images/CellPhoneIcon.png' alt='".translate('Cell Phone', $st, 'sys')."' title='".translate('Cell Phone', $st, 'sys')."' width='45' height='40' />";
				if ($Cell_Phone_Title == 'MySword (Android)')
					echo "&nbsp;&nbsp;<span class='lineAction'>" . translate('Download', $st, 'sys') . "</span></a> " . translate('the cell phone module for', $st, 'sys') . " <a href='http://www.mysword.info/' target='_blank'><span class='lineAction'>$Cell_Phone_Title</span></a>";
				else
					echo "&nbsp;&nbsp;<span class='lineAction'>" . translate('Download', $st, 'sys') . "</span> " . translate('the cell phone module for', $st, 'sys') . " $Cell_Phone_Title</a>";
				?>
				</p>
                <?php
				$i++;
			}
			?>
            </div>
            <?php
		}
	}
}

/*
	*************************************************************************************************************
		Can it be watched?
	*************************************************************************************************************
*/
$watch=trim(mysql_result($result,0,"scripture_main.watch"));
if ($watch) {
	$query="SELECT * FROM watch WHERE ISO_ROD_index = '$ISO_ROD_index'";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			?>
			<div class='lineCategories'>
            <?php
			$i=0;
			while ($i < $num) {
				$organization=trim(mysql_result($result2,$i,"organization"));
				$watch_what=trim(mysql_result($result2,$i,"watch_what"));
				$URL=trim(mysql_result($result2,$i,"URL"));
				$JesusFilm=trim(mysql_result($result2,$i,"JesusFilm"));
				$YouTube=trim(mysql_result($result2,$i,"YouTube"));
				?>
				<p class='lineItem'>
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
				echo "<img class='iconActions' align='middle' src='images/watch-icon.jpg' alt='".translate('Watch', $st, 'sys')."' title='".translate('Watch', $st, 'sys')."' width='45' height='40' />&nbsp;&nbsp;<span class='lineAction'>";
				if ($JesusFilm || $YouTube) {
					// JESUS Film or YouTube
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
				</p>
                <?php
				$i++;
			}
			?>
            </div>
            <?php
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
if ($Biblesorg) {
	$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND `Bibles_org` = 1";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			$URL=trim(mysql_result($result2,0,"URL"));
			?>
			<div class='lineCategories'>
			<p class='lineItem'>
            <?php
			echo "<a href='$URL' target='_blank'><img class='iconActions' align='middle' src='images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' width='45' height='40' />";
			echo "&nbsp;&nbsp;<span class='lineAction'>" . translate('Study', $st, 'sys') . "</span> " . translate('on Bibles.org', $st, 'sys') . "</a>";
			?>
            </p>
			</div>
            <?php
		}
	}
}
if ($viewer && (!$YouVersion && !$Biblesorg)) {
	$query="SELECT viewer_ROD_Variant, rtl FROM viewer WHERE ISO_ROD_index = '$ISO_ROD_index' AND Variant_Code = '$Variant_Code'";						// check if there is a viewer
	$result_viewer=mysql_query($query);
	$num_viewer=mysql_num_rows($result_viewer);
	$ROD_Var='';
	$rtl = 0;
	if ($num_viewer > 0) {
		$ROD_Var=trim(mysql_result($result_viewer,0,"viewer_ROD_Variant"));
		$rtl=trim(mysql_result($result_viewer,0,"rtl"));
	}
	?>
	<div class='lineCategories'>
	<p class='lineItem'>
	<?php
	echo "<a href='./viewer/views.php?iso=$ISO&ROD_Code=$ROD_Code&Variant_Code=$Variant_Code&ROD_Var=$ROD_Var&rtl=$rtl&st=$st' title='".translate('Viewer for $LN', $st, 'sys')."' target='_blank'><img class='iconActions' align='middle' src='images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' width='45' height='40' />";
	echo "&nbsp;&nbsp;<span class='lineAction'>".translate('Go to', $st, 'sys')."</span> ".translate('the online viewer', $st, 'sys')."</a>";
	?>
	</p>
	</div>
	<?php
}
if ($study) {
	$query="SELECT * FROM study WHERE ISO_ROD_index = '$ISO_ROD_index'";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			?>
			<div class='lineCategories'>
			<?php
			$i=0;
			while ($i < $num) {
				$Testament=mysql_result($result2,$i,"Testament");
				$alphabet=mysql_result($result2,$i,"alphabet");
				$ScriptureURL=trim(mysql_result($result2,$i,"ScriptureURL"));
				$othersiteURL=trim(mysql_result($result2,$i,"othersiteURL"));
				?>
				<div class='lineItem' style='margin-top: -19px; margin-bottom: -16px; '>
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
					//echo "<a href='#' style='font-size: .9em; ' title='".translate('Download $LN module.', $st, 'sys')."' onclick='Study(\"$ISO\", \"ROD_Code\", \"$ScriptureURL\")'><img class='iconActions' align='middle' src='images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' width='45' height='40' />&nbsp;&nbsp;<span class='lineAction'>".translate('Download', $st, 'sys')."</span> ".translate('the New Testament', $st, 'sys')."</a><span style='font-size: .85em; '>&nbsp;";
					//echo "<a href='#' style='font-size: .9em; ' title='".translate('Download $LN module.', $st, 'sys')."' onclick='Study(\"$ISO\", \"ROD_Code\", \"$ScriptureURL\")'><img class='iconActions' align='middle' src='images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' width='45' height='40' />&nbsp;&nbsp;<span class='lineAction'>".translate('Download', $st, 'sys')."</span> ";
					?>
					<p style='display: inline-block; vertical-align: middle; margin-right: 10px; '>
					<?php
					echo "<a href='#' title='$LN: ".translate('Download module.', $st, 'sys')."' onclick='Study(\"$st\", \"$ISO\", \"ROD_Code\", \"$ScriptureURL\")'>";
					echo "<img class='iconActions' style='margin-top: 4px; ' src='images/study-icon.jpg' alt='".translate('Study', $st, 'sys')."' title='".translate('Study', $st, 'sys')."' width='45' height='40' />";
					echo "</a>";
					?>
					<!-- THIS IS IMPORTANT! The </p> HAS TO FOLLOWED IMMIDEALY BY <p> in order for display: inline-block to work! -->
					</p><p class='TheWordText'>				
					<?php
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
					echo "&nbsp;</span><a href='$othersiteURL' style='font-size: 1em; ' target='_blank'><span class='lineAction'>&ldquo;The Word&rdquo;</span>";
					echo "</a>";
					?>
					</p>
					</div>
				<?php
				$i++;
			}
			?>
			</div>
			<?php
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
			?>
            <div class='lineCategories'>
            <?php
			$num=mysql_num_rows($result2);
			$i=0;
			while ($i < $num) {
				$other=trim(mysql_result($result2,$i,"other"));
				$other_title=trim(mysql_result($result2,$i,"other_title"));
				$other_PDF=trim(mysql_result($result2,$i,"other_PDF"));
				$other_audio=trim(mysql_result($result2,$i,"other_audio"));
				?>
                <p class='lineItem'>
                <?php
				if (!empty($other_PDF))
					echo "<a href='data/$ISO/PDF/$other_PDF' title='".translate('Read $other_title.', $st, 'sys')."' target='_blank'><img class='iconActions' align='middle' src='images/read-icon.jpg' alt='".translate('Books', $st, 'sys')."' title='".translate('Books', $st, 'sys')."' width='45' height='40' />&nbsp;&nbsp;<span class='lineAction'>".translate('Read', $st, 'sys')."</span>";
				else
					echo "<a href='data/$ISO/audio/$other_audio' title='".translate('Listen to $other_title.', $st, 'sys')."' target='_blank'><img class='iconActions' align='middle' src='images/read-icon.jpg' alt='".translate('Books', $st, 'sys')."' title='".translate('Books', $st, 'sys')."' width='45' height='40' />&nbsp;&nbsp;<span class='lineAction'>".translate('Listen', $st, 'sys')."</span>";
				echo "&nbsp;$other:&nbsp;$other_title</a>";
				?>
                </p>
                <?php
				$i++;
			}
			?>
            </div>
            <?php
		}
	}
}

/*
	*************************************************************************************************************
		Can it be boughten?
	*************************************************************************************************************
*/
$buy=trim(mysql_result($result,0,"scripture_main.buy"));
if ($buy) {
	$query="SELECT * FROM buy WHERE ISO_ROD_index = '$ISO_ROD_index'";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			?>
            <div class='lineCategories'>
            <?php
			$i=0;
			while ($i < $num) {
				$buy_what=trim(mysql_result($result2,$i,"buy_what"));
				$organization=trim(mysql_result($result2,$i,"organization"));
				$URL=trim(mysql_result($result2,$i,"URL"));
				?>
                <p class='lineItem'>
                <?php
				echo "<a href='$URL' title='".translate('Buy from $organization.', $st, 'sys')."' target='_blank'><img class='iconActions' align='middle' src='images/buy-icon.jpg' alt='".translate('Buy', $st, 'sys')."' title='".translate('Buy', $st, 'sys')."' width='45' height='40' />&nbsp;&nbsp;<span class='lineAction'>".translate('Buy', $st, 'sys')."</span> ".translate('from', $st, 'sys')." $organization: $buy_what</a>";
				?>
                </p>
                <?php
				$i++;
			}
			?>
            </div>
            <?php
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
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			?>
            <div class='lineCategories'>
            <?php
			$i=0;
			while ($i < $num) {
				$company_title=trim(mysql_result($result2,$i,"company_title"));
				$company=trim(mysql_result($result2,$i,"company"));
				$URL=trim(mysql_result($result2,$i,"URL"));
				?>
                <p class='lineItem'>
                <?php
				echo "<a href='$URL' title='".translate('Link to $company.', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/buy-icon.jpg' alt='".translate('Buy', $st, 'sys')."' title='".translate('Buy', $st, 'sys')."' width='45' height='40' />&nbsp;&nbsp;<span class='lineAction'>".translate('Link', $st, 'sys')."</span> ".translate('to', $st, 'sys')." ";
				if ($company_title != "" && $company_title != NULL) {
					echo "$company_title: ";
				}
				echo "$company</a>";
				?>
                </p>
                <?php
				$i++;
			}
			?>
            </div>
            <?php
		}
	}
	// This takes care of all of the rest of the links.
	$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND map = 0 AND buy = 0 AND BibleIs = 0 AND YouVersion = 0 AND `Bibles_org` = 0";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			?>
            <div class='lineCategories'>
            <?php
			$i=0;
			while ($i < $num) {
				$company_title=trim(mysql_result($result2,$i,"company_title"));
				$company=trim(mysql_result($result2,$i,"company"));
				$URL=trim(mysql_result($result2,$i,"URL"));
				?>
                <p class='lineItem'>
                <?php
				echo "<a href='$URL' title='".translate('Link to $company.', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/links-icon.jpg' alt='".translate('Link', $st, 'sys')."' title='".translate('Link', $st, 'sys')."' width='45' height='40' />&nbsp;&nbsp;<span class='lineAction'>".translate('Link', $st, 'sys')."</span> ".translate('to', $st, 'sys')." ";
				if ($company_title != "" && $company_title != NULL) {
					echo "$company_title: ";
				}
				echo "$company</a>";
				?>
                </p>
                <?php
				$i++;
			}
			?>
            </div>
            <?php
		}
	}
	$query="SELECT * FROM links WHERE ISO_ROD_index = '$ISO_ROD_index' AND map = 1";
	$result2=mysql_query($query);
	if (isset($result2)) {
		if (mysql_num_rows($result2) >= 1) {
			$num=mysql_num_rows($result2);
			?>
            <div class='lineCategories'>
            <?php
			$i=0;
			while ($i < $num) {
				$company_title=trim(mysql_result($result2,$i,"company_title"));
				$company=trim(mysql_result($result2,$i,"company"));
				$URL=trim(mysql_result($result2,$i,"URL"));
				?>
                <p class='lineItem'>
                <?php
				echo "<a href='$URL' title='".translate('Link to $company.', $st, 'sys')."' target='_blank'><img class='iconActions' src='images/globe-icon.png' alt='".translate('Map', $st, 'sys')."' title='".translate('Map', $st, 'sys')."' width='45' height='40' />&nbsp;&nbsp;<span class='lineAction'>".translate('Link', $st, 'sys')."</span> ".translate('to', $st, 'sys')." ";
				if ($company_title != "" && $company_title != NULL) {
					echo "$company_title: ";
				}
				echo "$company</a>";
				?>
                </p>
                <?php
				$i++;
			}
			?>
            </div>
            <?php
		}
	}
}
?>
</div>
