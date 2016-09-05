<?php
//----------------------------------------------------------
// Author: Suryakant Sawant
// Date of last update: 05 Sept. 2016 
// Objective: Service information index page.
//----------------------------------------------------------
echo "<html>";
echo "<body>";
echo "<div align='center'>";
// URL can be dynamic i.e. entered by user
include_once('../includes/settings.php');
// load functions
include_once('../includes/func_generic.php');
include_once('../includes/func_post_data.php');
include_once('../includes/func_parse_cap.php');
include_once('../includes/func_parse_describeSense.php');
include_once('../includes/func_parse_getobservation.php');
include_once('../includes/func_gen_urls.php');
include_once('../includes/all_html.php');
//
echo "<form method='post' action='".$phpSelf."'>
		<select name='kurl' >
			<option  value='".$ServiceURL."' >".$SOS_NAME_1."</option>
			<option  value='".$OtherService."' >".$SOS_NAME_2."</option>			
		</select>
		<input type='submit' value='Get URL'/>
		</form>";
if (isset($_POST['kurl'])&&(strlen($_POST['kurl'])>10)){
	//echo $_POST['kurl'];
	//echo strlen($_POST['kurl']);
	echo "<form method='post' action='serv_info.php' target='msec'>
		<input type='text' name='userurl' value='".$_POST['kurl']."'/>
		<input type='submit' value='Submit URL'/>
		</form>";	
}
else if(isset($_POST['kurl'])){
	echo $error;
}

echo "</div></body>";
echo"</html>";
?>