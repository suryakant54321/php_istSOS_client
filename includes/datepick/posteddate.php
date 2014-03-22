<html>
<head>

</head>
<body>
<?php
	if(isset($_POST['SelectedDate'])) {
		echo "<p>First date : ".date("Y-m-d", strtotime($_POST['SelectedDate']))." </p>";
		echo "<p>Second date: ".date("Y-m-d", strtotime($_POST['SelectedDate2']))." </p>";
	}else{
		echo "something required <br/>";
	}
?>
</body>
</html>