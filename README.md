# phpISTSOSClient / PHP-ISTSOS-Client


This is improved release of PHP based client for IST Sensor Observation Service [(ISTSOS)].
Sensor Observation Service [SOS] standards are provided by Open Geospatial Consortium [OGC].  
The details about its use and functionality are described in section A and B. 
The client installation and configurations for Windows and Linux described in section C, D and E respectively.

### Installation

### 1. Prerequisites:

#### A. The most important prerequisites are;

  a. Installed one of these "WAMPP / XAMPP / LAMPP" apache http server. The installation of database is not required. 

  b. PHP XMLReader class / module (Refer: http://www.php.net/manual/en/book.xmlreader.php)

### 2. Capabilities: 

#### A. What this client is capable of ?

  1. This is basic PHP implementation for accessing istSOS server through http get requests.

  2. It is tested on Windows platform; it may also work on Linux platform.

  3. The client is divided into four components and one configuration section (directory name is in this bracket):
    a. Service information (info): it provides information about selected service. 
    b. Describe sensor component (descsen): it describes the selected sensor procedure for selected service.
    c. Get observation component (getobs): it populates the observations for selected procedure. Note: some sections are hard coded.
    d. Dashboard component (dashboard): shows recent data updates in selected sensors. Note: this module is completely hard coded for respective sensing system.
    e. Include configurations and functions (includes): all functions used by above sections (a to d) are stored together. 

#### B. What this client can't do?

  1. Presently it is not interoperable with [52°North] SOS, as some of the xml formats and GET / POST components described by [ISTSOS] and [52°North] differ at few places.

  
#### C. How to install in Windows-Apache environment?

  1. Create directory on web server

  2. Paste / extract all files and dirs. Note: do not change the client folder names 

  3. Restart the web server	

  4. In favorite internet browser type "http://server_name_or_ip_addr:port_if_any/client_folder_name/" 


#### D. How to install in Linux-Apache environment?

  1. Create directory on web server

  2. Paste / extract all files and dirs. Note: do not change the client folder names 

  3. Add directory permissions for execution on web server 

  4. Restart the web server

  5. In favorite internet browser type "http://server_name_or_ip_addr:port_if_any/client_folder_name/" 


#### E. How to configure PHP_istSOS_Client

  1. Modify settings.php file from includes directory
  
  2. Modify variable content of section 1. "Overall service configurations"     


### Version
1.0.0

### Tested on

Ubuntu 12.04 with PHP-5.5, Windows xp / 7 / 10 using [ms4w]


### Other References

- [OGC] Open Geospatial Consortium
- OGC [SOS] Specifications
- Python based SOS [ISTSOS]
- National Data Buoy Center SOS [NDBC]
- Get Capability request for [ISTSOS-Demo] SOS
- Get Capability request for [NDBC-SOS] SOS

### Other SOS Clients

- Refer [Python-SOS-client] Python based client for Sensor Observation Service (SOS).
- Refer [sos4R] sos4R is an extension for the R environment for statistical computing and visualization. Designed by [52°North].
- Refer [sos-js] a JavaScript library to browse, visualise, and access, data from an Open Geospatial Consortium (OGC) Sensor Observation Service (SOS). Designed by [52°North].


### Snapshots

#### Snapshot of service information module

![Snapshot 1](https://raw.githubusercontent.com/suryakant54321/php_istSOS_client/master/snapshots/serviceInfo.PNG)

#### Snapshot of describe sensor module

![Snapshot 2](https://raw.githubusercontent.com/suryakant54321/php_istSOS_client/master/snapshots/descSensor.PNG)

#### Snapshot of GetObservation module

![Snapshot 3](https://raw.githubusercontent.com/suryakant54321/php_istSOS_client/master/snapshots/getObs1.PNG)


![Snapshot 4](https://raw.githubusercontent.com/suryakant54321/php_istSOS_client/master/snapshots/getObs2.PNG)

#### Snapshot of dashboard module

![Snapshot 5](https://raw.githubusercontent.com/suryakant54321/php_istSOS_client/master/snapshots/dashboard.PNG)

#### Snapshot of map visualization module

![Snapshot 6](https://raw.githubusercontent.com/suryakant54321/php_istSOS_client/master/snapshots/sensorMap.PNG)


### TODO

1. Write detailed documentation using [Sphinix].


[(ISTSOS)]: <http://istsos.org/>
[ISTSOS]: <http://istsos.org/>
[ISTSOS-Demo]: <http://istsos.org/istsos/demo?request=getCapabilities&section=contents&service=SOS>
[Python-SOS-Client]:<https://github.com/suryakant54321/Python-SOS-Client>
[ms4w]:<http://ms4w.com/>
[NDBC]: <http://sdf.ndbc.noaa.gov/sos/>
[NDBC-SOS]: <http://sdf.ndbc.noaa.gov/sos/server.php?request=GetCapabilities&service=SOS>
[OGC]: <http://www.opengeospatial.org/>
[SOS]: <http://www.opengeospatial.org/standards/sos>
[PHP-istSOS-client]: <https://github.com/suryakant54321/php_istSOS_client>
[sos4R]: <https://github.com/52North/sos4R>
[sos-js]: <https://github.com/52North/sos-js>
[52°North]: <http://52north.org/>
[Sphinix]: <http://www.sphinx-doc.org/en/stable/>
