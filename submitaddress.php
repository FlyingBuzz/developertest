<?php
// Developer Test Code
// Developer: David Lee
// Phone: 909-437-3556
// Date: 07/23/2015
// File: submitaddress.php


// Stores the variables that is accessed
namespace dataStorage {
	class addressData{
		private $validated_address1, $validated_city, $validated_state, $validated_zip, $status;

		function getCity() {
			return $this->validated_city;
		}
		function getState() {
			return $this->validated_state;
		}
		function getStreet() {
			return $this->validated_address1;
		}
		function getZip() {
			return $this->validated_zip;
		}
		function getStatus() {
			return $this->status;
		}
		
		function saveCity($city) {
			$this->validated_city = $city;
		}
		function saveState($state) {
			$this->validated_state = $state;
		}
		function saveStreet($street) {
			$this->validated_address1 = $street;
		}
		function saveZip($zip) {
			$this->validated_zip = $zip;
		}		
		function saveStatus($status) {
			$this->status = $status;
		}			

		function getPostedCity() {
			return $_POST['city'];
		}
		function getPostedState() {
			return $_POST['state'];
		}
		function getPostedStreet() {
			return $_POST['street'];
		}		
	}
}

// Setup and control the mysql database
namespace mysqlControl {
	class mysqlAccess{
		private $mysqli;

		// Store data into mysql
		function saveData($searched_address1, $searched_city, $searched_state, $validated_address1, $validated_city, $validated_state, $validated_zip, $status) {
			$address = $this->mysqli->real_escape_string(strtoupper($searched_address1));
			$city = $this->mysqli->real_escape_string(strtoupper($searched_city));
			$state = $this->mysqli->real_escape_string(strtoupper($searched_state));
			$val_address = $this->mysqli->real_escape_string($validated_address1);
			$val_city = $this->mysqli->real_escape_string($validated_city);
			$val_state = $this->mysqli->real_escape_string($validated_state);
			$val_zip = $this->mysqli->real_escape_string($validated_zip);
			$val_status = $this->mysqli->real_escape_string($status);
			
			$query = "INSERT INTO addresscache (searched_address1,searched_city,searched_state,validated_address1,validated_city,validated_state,validated_zip,error_status)
			VALUES ('".$address."','".$city."','".$state."','".$val_address."','".$val_city."','".$val_state."','".$val_zip."','".$val_status."')";
			$this->mysqli->query($query);
		}
		
		// Get Data from mysql
		function callData($searched_address1, $searched_city, $searched_state) {
			$address = $this->mysqli->real_escape_string(strtoupper($searched_address1));
			$city = $this->mysqli->real_escape_string(strtoupper($searched_city));
			$state = $this->mysqli->real_escape_string(strtoupper($searched_state));
			
			$query = "SELECT * FROM `addresscache` WHERE `searched_address1` = '".$address."' AND `searched_city` = '".$city."' AND `searched_state` = '".$state."' LIMIT 1";
			$result = $this->mysqli->query($query);
			
			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				return $row;
			} else {
				return null;				
			}
			
		}
		
		// Setup the mysql connection
		function __construct() {
			$this->mysqli = new \mysqli("localhost", "c1dlee", "testdlee", "c1developertest");
			if ($this->mysqli->connect_errno) {
				echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			}
		}

		// Destroy the mysql connection
		function __destruct() {
			$this->mysqli->close();
		}
	}
}


// Looks up the address from local cache database first and queue if address was never stored before.
namespace addressInformation{
	class GatherInformation{
		private $addressData, $mysql;
		
		// Look up data from cache
		function doCacheCall() {
			$thedata = $this->mysql->callData($this->addressData->getPostedStreet(),$this->addressData->getPostedCity(),$this->addressData->getPostedState());
			
			// Check if cache is empty and do api call
			if ($thedata == null) {
				$this->doApiCall();
				$this->mysql->saveData($this->addressData->getPostedStreet(),$this->addressData->getPostedCity(),$this->addressData->getPostedState(),$this->addressData->getStreet(),$this->addressData->getCity(),$this->addressData->getState(),$this->addressData->getZip(),$this->addressData->getStatus());
			} else {
				$this->addressData->saveStreet($thedata["validated_address1"]);
				$this->addressData->saveCity($thedata["validated_city"]);
				$this->addressData->saveState($thedata["validated_state"]);
				$this->addressData->saveZip($thedata["validated_zip"]);				
				$this->addressData->saveStatus($thedata["error_status"]);
			}
		}
				
		// Calls google api to look up address information
		function doApiCall() {
 
			$address = $this->addressData->getPostedStreet() . ', ' . $this->addressData->getPostedCity() . ', ' . $this->addressData->getPostedState();
			$details_url = "http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $details_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = json_decode(curl_exec($ch), true);

			$this->addressData->saveStatus($response['status']);
			if ($response['status'] != 'OK') {
				return;
			}

			foreach ($response['results'][0]['address_components'] as $component) {

				switch ($component['types']) {
					case in_array('street_number', $component['types']):
						$street_number = $component['short_name'];
						break;
					case in_array('route', $component['types']):
						$street = $component['short_name'];
						break;
					case in_array('locality', $component['types']):
						$city = $component['short_name'];
						break;
					case in_array('administrative_area_level_1', $component['types']):
						$state = $component['short_name'];
						break;
					case in_array('postal_code', $component['types']):
						$zip = $component['short_name'];
						break;
				}

			}
			
			// Check to see if address return was valid or makes the status invalid
			if (isset($street_number) && isset($street) && isset($city) && isset($state) && isset($zip)) {
			
				$this->addressData->saveStreet($street_number . " " . $street);
				$this->addressData->saveCity($city);
				$this->addressData->saveState($state);
				$this->addressData->saveZip($zip);

			} else {
				$this->addressData->saveStatus("ERROR");
			}
			
	
		}

		// Creates the json data to be returned
		function generateResponse() {
			$data = array();
			$data['validated_address1'] = $this->addressData->getStreet();
			$data['validated_city'] = $this->addressData->getCity();
			$data['validated_state'] = $this->addressData->getState();
			$data['validated_zip'] = $this->addressData->getZip();
			$data['status'] = $this->addressData->getStatus();
			header('Content-Type: application/json');
			echo json_encode($data);
		}		
		
		// Makes the class auto load once called
		function __construct() {
			// Create local storage of variables
			$this->addressData = new \dataStorage\addressData();
			
			// Create mysql connector
			$this->mysql = new \mysqlControl\mysqlAccess();

			// Lookup address from cache
			$this->doCacheCall();
			
			// Create JSON of the returned data
			$this->generateResponse();
		}			
	}
	
}



namespace{
	// Calls the class that will generate the result
	new addressInformation\GatherInformation();
}

?>
