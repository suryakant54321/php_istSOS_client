<?php
//----------------------------------------------------------
// Author: Suryakant Sawant
// Date of last update: 05 Sept. 2016 
// Objective: Dashboard index page.
//----------------------------------------------------------
echo "<html>";
echo "<head>
	<meta charset='utf-8'>
	<script src='../includes/datepick/htmlDatePicker.js' type='text/javascript'></script>
	<script>function goBack(){window.history.back()}</script>
	<link href='../includes/datepick/htmlDatePicker.css' rel='stylesheet'>";
echo "<body>";
echo "<div align='center'>";
// tried for istsos same way as 52 north sos but failed
// may be http_get / http_post are not supported by IST SOS
// URL can be dynamic i.e. entered by user
//
include_once('../includes/settings.php');
// load functions
include_once('../includes/func_generic.php');
include_once('../includes/func_post_data.php');
include_once('../includes/func_parse_cap.php');
include_once('../includes/func_parse_describeSense.php');
include_once('../includes/func_parse_getobservation.php');
include_once('../includes/func_gen_urls.php');
include_once('../includes/all_html.php');
include_once('../includes/func_plt_gauge.php');
// 1. 
echo "<form method='post' action='".$phpSelf."' class='modURL'>
		<select name='kurl' >
			<option  value='".$ServiceURL."' >".$SOS_NAME_1."</option>
			<option  value='".$OtherService."' >".$SOS_NAME_2."</option>			
		</select>
		<input type='submit' value='Get URL'/>
		</form>";
echo "<div id='modurl' class='modURL'>";		
if (isset($_POST['kurl'])){
	//echo $_POST['kurl'];
	echo "<form method='post' action='dboard_content.php' target='msec'>
		<input type='text' name='userurl' value='".$_POST['kurl']."'/>
		<input type='submit' value='Submit URL'/>
		</form>";
}
echo "</div>";
echo "</div></body>";
echo"</html>";
?>
