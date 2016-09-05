<?php 
//----------------------------------------------------------
// Author: Suryakant Sawant
// Date of last update: 05 Sept. 2016 
// Objective: Map Visualization index page.
//----------------------------------------------------------
?>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>Google Maps Example</title>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript">
    //<![CDATA[

    var customIcons = {
      user: {
        icon: '../images/mm_20_blue.png',
        shadow: '../images/mm_20_shadow.png'
      },
      expert: {
        icon: '../images/mm_20_red.png',
        shadow: '../images/mm_20_shadow.png'
      },
	  admin: {
        icon: '../images/mm_20_red.png',
        shadow: '../images/mm_20_shadow.png'
      }
    };

    function load() {
      var map = new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(21.439328, 78.151588),
        zoom: 14,
		//roadmap or satellite or hybrid
        mapTypeId: 'hybrid'
		
      });
      var infoWindow = new google.maps.InfoWindow;

      // Change this depending on the name of your PHP file
      downloadUrl("getSensorsLL.php", function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");
        for (var i = 0; i < markers.length; i++) {
          var sensor1 = markers[i].getAttribute("sensor1");
          var sensor2 = markers[i].getAttribute("sensor2");
          var sensor3 = markers[i].getAttribute("sensor3");
          var type = markers[i].getAttribute("type");
          var point = new google.maps.LatLng(
              parseFloat(markers[i].getAttribute("lat")),
              parseFloat(markers[i].getAttribute("lon")));
          var html = "<a href='getSensorsLL.php'/> Details : </a><table border=1><tr><td>Sensor 1 </td><td>" + sensor1 + "</td></tr><tr><td>Sensor 2 </td><td>" + sensor2 + "</td></tr><tr><td>Sensor 3</td><td>" + sensor3+"</td></tr></table>";
          var icon = customIcons[type] || {};
          var marker = new google.maps.Marker({
            map: map,
            position: point,
            icon: icon.icon,
            shadow: icon.shadow
          });
          bindInfoWindow(marker, map, infoWindow, html);
        }
      });
    }

    function bindInfoWindow(marker, map, infoWindow, html) {
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
    }

    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }

    function doNothing() {}

    //]]>

  </script>

  </head>
  <body onload="load()">
	<div align="center">
		<br/><h1>Sensor Map</h1><br/>
		<h3>Click on map markers to know more</h3><br/>
		<div id="map" style="width: 1024px; height: 500px"></div>
	</div>
  </body>

</html>
