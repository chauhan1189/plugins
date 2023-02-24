<?php

// Dev Notes:
// 		new SimpleXMLElement($result);		e.g. $movies->movie[0]->plot;

class CAutocabChauffeurApiCabe
{
	private $m_config 		=	[];
	private $m_strLastError	=	"";
	private $m_strLastException	=	"";
	private $m_strLastAvailabilityCallRef		=	"";		// Reference when requesting Availability
	private $m_strLastAvailabilityBookingRef	=	"";		// Reference returned from Availability request
	
	function __construct( $config )
	{
		$this->m_config = $config;
	}
	
	private function getPayloadAgent( $strCallRef )
	{
		$now = new DateTime(null, new DateTimeZone('Europe/London'));
		return 
"	<Agent Id='".$this->m_config["CABE.AGENT"]."'>
		<Password>".$this->m_config["CABE.PW"]."</Password>
		<Reference>".self::toSafeXml($strCallRef)."</Reference>
		<Time>".$now->format("c")."</Time>
	</Agent>
	<Vendor Id='".$this->m_config["CABE.VENDOR"]."' />";
	}
	
	public static function toSafeXml( $str )
	{
		return htmlspecialchars( $str, ENT_XML1 | ENT_QUOTES );
	}
	
	public function ClearLastResult()
	{
		$this->m_strLastError		=	"";
		$this->m_strLastException	=	"";
		$this->m_strLastAvailabilityCallRef		=	"";		// Reference when requesting Availability
		$this->m_strLastAvailabilityBookingRef	=	"";		// Reference returned from Availability request
	}
	public function GetLastError($bFirstLineOnly=false)
	{
		if ( !$bFirstLineOnly ) {
			return $this->m_strLastError;
		}
		// Return first line only.
		$lines = explode("\n", trim($this->m_strLastError), 2);
		return $lines[0];
	}
	public function GetLastException()
	{
		return $this->m_strLastException;
	}
	public function GetLastAvailabilityCallRef()		// Reference when requesting Availability
	{
		return $this->m_strLastAvailabilityCallRef;
	}
	public function GetLastAvailabilityBookingRef()		// Reference returned from Availability request
	{
		return $this->m_strLastAvailabilityBookingRef;
	}
	
	private function call( $payload )
	{
		try
		{
			$this->ClearLastResult();
			
			//echo "<hr/>Data:<pre>".htmlentities($payload)."</pre>";
			
			CAutocabChauffeur::DoLog( true, __METHOD__, __LINE__, ">>>PAYLOAD=\n".print_r($payload,1) );
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->m_config["CABE.URL"]);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: text/xml',
				'Content-Length: ' . strlen($payload))
			);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			$result = curl_exec($ch);
			curl_close($ch);
			
			if (!empty($result))
			{
				$xml = new SimpleXMLElement($result, LIBXML_NOERROR | LIBXML_NOWARNING);
				if ( !empty($xml) )
				{
					CAutocabChauffeur::DoLog( true, __METHOD__, __LINE__, "<<<RESULT=\n".print_r($xml,1) );
					return $xml;
				}
			}
		}
		catch (Exception $e)
		{
			if ( !empty($result) )
			{
				$this->m_strLastError = $result;
			}
			else
			{
				$this->m_strLastError = trim(print_r($e->getMessage(),1));
			}
			$this->m_strLastException	=	$e;
			CAutocabChauffeur::DoLog( true, __METHOD__, __LINE__, "<<<EXCEPTION=\n".print_r($e,1) );
		}
		return [];
	}

	//
	//	call API AgentBookingStatusRequest
	//
	public function callBookingStatus( $strCallRef, $strAuthorizationRef )
	{
		try
		{
			$this->ClearLastResult();
			$payload = 
"<AgentBookingStatusRequest>
".self::getPayloadAgent($strCallRef)."
	<AuthorizationReference>".self::toSafeXml($strAuthorizationRef)."</AuthorizationReference>
</AgentBookingStatusRequest>
	";
			//echo "<hr/>Data:<pre>".htmlentities($payload)."</pre>";
			return self::call( $payload );
		}
		catch (Exception $e)
		{
		}
		return [];
	}
	
	//
	//	call API AgentBookingAvailabilityRequest
	//
	public function callBookingAvailability(	$strCallRef,
												$dtWhen,
												$strContactName, $strContactPhone, $strContactEmail, 
												$iRidePassengers, $iRideBags, $strRideVehicle, 
												$strFromAddress, $strFromLat, $strFromLong,
												$strToAddress, $strToLat, $strToLong
	)
	{
		try
		{
			$this->ClearLastResult();
			$strWhen = $dtWhen->format("c");
			if ( empty($strWhen) )
			{
				$this->m_strLastError = "Invalid booking travel time";
				return [];
			}
			
			$payload = 
"<AgentBookingAvailabilityRequest>
".self::getPayloadAgent($strCallRef)."
	<BookingParameters>
		<Source>".self::toSafeXml(CAutocabChauffeur::TITLE)."</Source>
		<BookingTime>".$strWhen."</BookingTime>
		<PassengerDetails>
			<Name>".self::toSafeXml($strContactName)."</Name>
			<TelephoneNumber>".self::toSafeXml($strContactPhone)."</TelephoneNumber>
			<EmailAddress>".self::toSafeXml($strContactEmail)."</EmailAddress>
		</PassengerDetails>
		<Ride Type='Passenger'>
			<Count>".intVal($iRidePassengers)."</Count>
			<Luggage>".intVal($iRideBags)."</Luggage>
			<Facilities></Facilities>
			<DriverType>Any</DriverType>
			<VehicleType>".self::toSafeXml($strRideVehicle)."</VehicleType>
		</Ride>
		<Journey>
			<From>
				<Type>Address</Type>
				<Data>".self::toSafeXml($strFromAddress)."</Data>
				<Coordinate>
					<Latitude>".self::toSafeXml($strFromLat)."</Latitude>
					<Longitude>".self::toSafeXml($strFromLong)."</Longitude>
				</Coordinate>
			</From>
			<To>
				<Type>Address</Type>
				<Data>".self::toSafeXml($strToAddress)."</Data>
				<Coordinate>
					<Latitude>".self::toSafeXml($strToLat)."</Latitude>
					<Longitude>".self::toSafeXml($strToLong)."</Longitude>
				</Coordinate>
			</To>
		</Journey>
	</BookingParameters>
</AgentBookingAvailabilityRequest>
	";
			//echo "<hr/>Data:<pre>".htmlentities($payload)."</pre>";
			$ret	=	self::call( $payload );
			if (( !empty($ret) ) && isset($this->m_strLastAvailabilityBookingRef) )
			{
				$this->m_strLastAvailabilityCallRef		=	$strCallRef;		// Reference when requesting Availability
				$this->m_strLastAvailabilityBookingRef	=	(string)$ret->AvailabilityReference;		// Reference returned from Availability request
				//echo "<hr/>THIS:<pre>".print_r($this,1)."</pre>";
			}
			return $ret;
		}
		catch (Exception $e)
		{
		}
		return [];
	}
	
	//
	//	call API AgentBookingAuthorizationRequest
	//
	/*
		$strNotifyAlertEvents -  The booking events which the Vendor will notify the end user about, usually via SMS.
		Can be any combination of the following separated by a space:
			- BookingDispatched
			- LocationUpdate
			- VehicleArrived
			- PassengerOnBoard
			- NoFare
			- BookingCompleted
			- BookingCancelled

		$strNotifyAlertMethod -  The method of alerting the end user.
		Can be one of the following
			- None
			- Ringback
			- Textback	
	*/
	public function callBookingAuthorization(	$strCallRef,
												$strAvailabilityRef,
												$strAgentBookingRef,
												$strContactName, $strContactPhone, $strContactEmail, 
												$strDriverNote="",
												$strNotifyAlertEvents="",
												$strNotifyAlertMethod="None"
	)
	{
		try
		{
			$this->ClearLastResult();
			
			$payload = 
"<AgentBookingAuthorizationRequest>
".self::getPayloadAgent($strCallRef)."
	<AvailabilityReference>".self::toSafeXml($strAvailabilityRef)."</AvailabilityReference>
	<AgentBookingReference>".self::toSafeXml($strAgentBookingRef)."</AgentBookingReference>
	<Passengers>
		<PassengerDetails IsLead='true'>
			<Name>".self::toSafeXml($strContactName)."</Name>
			<TelephoneNumber>".self::toSafeXml($strContactPhone)."</TelephoneNumber>
			<EmailAddress>".self::toSafeXml($strContactEmail)."</EmailAddress>
		</PassengerDetails>
	</Passengers>
	<DriverNote>".self::toSafeXml($strDriverNote)."</DriverNote>
	<Notifications>
		<VendorEvents>".self::toSafeXml($strNotifyAlertEvents)."</VendorEvents>
		<AlertMethod>".self::toSafeXml($strNotifyAlertMethod)."</AlertMethod>
	</Notifications>
</AgentBookingAuthorizationRequest>
	";
			//echo "<hr/>Data:<pre>".htmlentities($payload)."</pre>";
			return self::call( $payload );
		}
		catch (Exception $e)
		{
		}
		return [];
	}
	
	//
	//	call API AgentBookingCancellationRequest
	//
	public function callBookingCancellation( $strCallRef, $strAuthorizationRef )
	{
		try
		{
			$this->ClearLastResult();
			$payload = 
"<AgentBookingCancellationRequest>
".self::getPayloadAgent($strCallRef)."
	<AuthorizationReference>".self::toSafeXml($strAuthorizationRef)."</AuthorizationReference>
</AgentBookingCancellationRequest>
	";
			//echo "<hr/>Data:<pre>".htmlentities($payload)."</pre>";
			return self::call( $payload );
		}
		catch (Exception $e)
		{
		}
		return [];
	}	

}