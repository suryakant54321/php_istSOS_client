phpISTSOSClient:
---------------------------

This is first release of PHP based client for IST Sensor Observation Service (SOS). 
The details about its use and functionality are described in section A and B.
The client installation and configurations for Windows and Linux described in section C, D and E respectively.

1. Prerequisits:
---------------------------
  1. The most important prequisits are;
     a. Installed one of these "WAMPP / XAMPP / LAMPP" apache http server. The installation of database is not required. 
	 b. PHP XMLReader class / module (Refer: http://www.php.net/manual/en/book.xmlreader.php)

2. Capabilities: 
---------------------------
A. What this client is capable of ?
  1. This is basic PHP implementation for accessing istSOS server through http get requests.
  2. It is tested on Windows platform; it may also work on Linux platform.
  3. The client is divided into four components and one configuration section (directory name is in this bracket): 
    a. Service information (info): it provides information about selected service. 
    b. Describe sensor component (descsen): it describes the selected sensor procedure for selected service.
    c. Get observation component (getobs): it populates the observations for selected procedure. Note: some sections are hard coded.
    d. Dashboard component (dashboard): shows recent data updates in selected sensors. Note: this module is completely hard coded for respective sensing system.  
    e. Include configurations and functions (includes): all functions used by above sections (a to d) are stored together. 

B. What this client can't do?
  1. Presently it is not interoperable with 52 North SOS, as some of the xml formats and GET / POST components described by istSOS and 52North differ at few places. In future I will resolve this issue. 
  2. The client cannot show map based sensor observations. This idea / module is under development and may be added in next version of phpISTSOSClient.

3. Installation:
---------------------------
C. How to install in Windows-Apache environment?
  1. Create directory on web server
  2. Paste / extract all files and dirs. Note: do not change the client folder names 
  3. Restart the web server	
  4. In favorite internet browser type "http://server_name_or_ip_addr:port_if_any/client_folder_name/" 

D. How to install in Linux-Apache environment?
  1. Create directory on web server
  2. Paste / extract all files and dirs. Note: do not change the client folder names 
  3. Add directory permissions for execution on web server 
  4. Restart the web server
  5. In favorite internet browser type "http://server_name_or_ip_addr:port_if_any/client_folder_name/" 

E. How to configure PHP_istSOS_Client
  1. Modify settings.php file from includes directory
  2. Modify variable content of section 1. "Overall service configurations"

