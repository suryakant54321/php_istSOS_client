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
      float:center;
      width:100%;
	  height:25%;
    }
    .bottomContent {
      float:center;
      width:100%;
	  height:58%;
    }	
  </style>
</head>
<body bgcolor='white'>
  <iframe scrolling="no" class="menu" src="index_1.php" frameBorder="0" name="tsec"></iframe>
  <iframe scrolling="no" class="midContent" src="blank.php" frameBorder="0" name="msec"></iframe>
  <iframe class="bottomContent" src="blank.php" frameBorder="0" name="bsec"></iframe>
</body>
</html>
<!-- scrolling="no"-->