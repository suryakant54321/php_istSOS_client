<html>
<head>
	<meta charset="utf-8">
	<script src="htmlDatePicker.js" type="text/javascript"></script>
	<link href="htmlDatePicker.css" rel="stylesheet">
</head>
<body>
<form method="post"action="posteddate.php">
	<input type="text" placeholder="Click to select date" name="SelectedDate" id="SelectedDate" readonly onClick="GetDate(this);"/>
	<input type="text" placeholder="Click to select date" name="SelectedDate2" id="SelectedDate2" readonly onClick="GetDate(this);"/>
	<br/>
	<input type="submit"/>
</form>
</body>
</html>
