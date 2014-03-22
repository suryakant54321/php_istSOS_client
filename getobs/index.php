<html>
<head>
  <title>Frames Test</title>
  <style>
   .menu {
      float:top;
      width:100%;
      height:16%; 
    }
    .midContent {
      width:100%;
	  height:46%;
    }
    .bottomContent {
      width:100%;
	  height:58%;
    }	
  </style>
</head>
<body bgcolor='white'>
  <iframe scrolling="no" frameBorder="0" class="menu" src="index_1.php"  name="tsec"></iframe>
  <iframe scrolling="no" frameBorder="0" class="midContent" src="blank.php"  name="msec"></iframe>
  <iframe class="bottomContent" frameBorder="0" src="blank.php" name="bsec"></iframe>
</body>
</html>
<!-- scrolling="no"
frameBorder="0"
-->