<?php

error_reporting(E_ALL); // reports all errors
ini_set("display_errors", "1"); // shows all errors
ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");

	$inData = getRequestInfo();
	
	$id = 0;
	$name = "";

	$servername = "localhost";
	$username = "dev";
	$password = "knights";
	$database = "Contacts";
	
	// Create connection
	$conn = new mysqli($servername, $username, $password, $database);	
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{

			//$conn->query("SET @in_username='walt';");
			$conn->query("SET @in_username=$inData[login];");
			//$conn->query("SET @in_password='walt';");
			$conn->query("SET @in_password=$inData[password];");

			$stmt = $conn->query("CALL `Authenticate`(@in_username, @in_password);");
			
			if( $row = $stmt->fetch_assoc() )
			{
				returnWithInfo($row['name'], $row['id']);
			}
			else
			{
				returnWithError("No Records Found");
			}
	}

	function returnWithInfo( $name, $id )
	{
		$retValue = '{"id":' . $id . ',"name":"' . $name . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}

	function returnWithError( $err )
	{
		$retValue = '{"id":0,"name":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}
	
?>
