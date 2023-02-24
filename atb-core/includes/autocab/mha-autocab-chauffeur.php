<?php

defined( 'ABSPATH' ) or die;

define( 'MHAAUTOCABCHAUFFEUR___PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MHAAUTOCABCHAUFFEUR___PLUGIN_IMAGES', plugin_dir_url( __FILE__ ).'images/' );

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

$g_strAutocabServerFile = strtolower($_SERVER['SERVER_NAME']);

if ( "www." == substr($g_strAutocabServerFile, 0, 4) ) {
	$g_strAutocabServerFile	=	substr($g_strAutocabServerFile, 4);
}
if ( "airporttaxibooking" == $g_strAutocabServerFile ) {
	define ("MHAAUTOCABCHAUFFEUR_LOCAL_DEV", 1);
} else {
	define ("MHAAUTOCABCHAUFFEUR_LOCAL_DEV", 0);
}


require_once( MHAAUTOCABCHAUFFEUR___PLUGIN_DIR.'api_Cabe.php' );


class CAutocabChauffeur extends WP_Widget
{
	const TITLE				= 'Autocab Chauffeur';
	const SHORTCODE			= 'mha-autocab-chauffeur';
	const PREFIX			= 'mha_autocab_chauffeur_';
	const UNIQUE1_ADMIN		= '8550113C-2584-48D3-B6FE-95F1FB247802';
	const UNIQUE2_CABE		= '22C11505-1E27-42A0-BBF5-A98F05DC846A';
	const DB_VERSION		= 1;

	const TESTING		= 0;
	static $ms_bTestingCabe	= null;
	const SERVER_LIVE	= 'www.airporttaxibooking.co.uk';
	
	const DATEFMT_TIMEZONE	= 'Europe/London';
	const DATEFMT_DATE_HI2LO	= 'Y-m-d';
	const DATEFMT_DATE_YYYY_MM	= 'Y-m';
	const DATEFMT_DATETIME_CHAUF	= 'd/m/Y H:i';
	const DATEFMT_DATETIME_FRIENDLY	= 'j-M-Y H:i';
	const DATEFMT_DATETIME_ISO	= "Y-m-d\TH:i:sT";		// e.g. 2020-01-14T13:01:16+00:00
	
	const SECS_PER_HOUR		=	3600;
	const SECS_PER_DAY		=	86400;
	const SECS_PER_WEEK		=	604800;
	
	const ANALYSIS_REPORT_ALERT_URGENT	=	999;
	const ANALYSIS_REPORT_ALERT_NOTICE	=	888;
	const ANALYSIS_REPORT_ALERT_NONE	=	0;
	const ANALYSIS_REPORT_ALERT_ANTI	=	-1;
	
	const ANALYSIS_REPORT_URGENT_WATERSHED	=	86400;			// Booking is urgent if less than 1 day old
	const ANALYSIS_REPORT_URGENT_FREQUENCY	=	3600-60;		// Urgent report only every hour (less 60 seconds, so not to just-miss an analysis report)
	const ANALYSIS_REPORT_NOTICE_WATERSHED	=	604800;			// Booking is notice if between 1 and 7 days old
	const ANALYSIS_REPORT_NOTICE_FREQUENCY	=	86400-60;		// Notice report only every 24 hours (less 60 seconds, so not to just-miss an analysis report)
	const ANALYSIS_REPORT_ANTI_WATERSHED	=	2073600;		// ANTI-ALERT if older than 4 weeks
	
	const POST_TYPE_BOOKINGS	=	"payment";
	
	const METADATA_BOOKING_DIR_LASTEMAIL	=	"lastemail";
	const METADATA_BOOKING_DIR_BOOKINGDATA	=	"bookingdata";			// cabe sub
	const METADATA_BOOKING_DIR_AUTHREF		=	"authref";				// cabe sub

	const AJAX_ACTION_ANALYSE			= "analyse";
	
	const URLPARAM_FORCE_EMAIL	=	"forceemail";


	private static $ms_arrAttrs = array(
	);
	
	private static $m_strScripts = 
'<script type="text/javascript">
function CAutocabChauffeurFormValidate()
{
	strMessage = "";
	if ( strMessage == "" )
	{
		return true;
	}
	strMessage	+=	"\n\nClick OK to accept.";
	return confirm(strMessage);
}
</script>';

	private static $ms_strScriptsBooking = 
"<script type='text/javascript'>
	var gPlaces;
	var gDirections;
	var gMap;
	var gTravel;
	var qu = [];
	document.addEventListener('DOMContentLoaded', function(event) {
		gPlaces = new google.maps.places.PlacesService(document.createElement('div'));
		if (document.getElementById('idMhaMap') !== null) {
			gMap = new google.maps.Map(document.getElementById('idMhaMap'), {
					mapTypeControl: false,
					streetViewControl: false,
					center: {
						lat: 53.0219186,
						lng: -2.2297829
					},
					zoom: 8
				});
			gDirections = new google.maps.DirectionsService;
			gTravel = new google.maps.DirectionsRenderer;
			gTravel.setMap(gMap);
		}
		qu.push('T');
		aclookup('F');
	});
	function aclookup(part) {
		document.querySelector('#txtMhaAcAddr'+part+'Lat').value = '';
		document.querySelector('#txtMhaAcAddr'+part+'Lng').value = '';
		document.querySelector('#txtMhaAcAddr'+part+'Found').value = '';
		document.querySelector('#txtMhaAcAddr'+part+'Id').value = '';
		var request = {
			query: document.querySelector('#txtMhaAcAddr'+part).value,
			fields: ['formatted_address', 'name', 'geometry', 'place_id'],
		};
		switch (part) {
			case 'F': gPlaces.findPlaceFromQuery(request, accallbackF); break;
			case 'T': gPlaces.findPlaceFromQuery(request, accallbackT); break;
		}
	}
	function accallbackF(results, status) { acanswer('F', results, status);	}
	function accallbackT(results, status) { acanswer('T', results, status);	}

	function acanswer(part, results, status) {
		if (status == google.maps.places.PlacesServiceStatus.OK) {
			for (var i = 0; i < results.length; i++) {
				var place = results[i];
				if ( 0 == i ) {
					document.querySelector('#txtMhaAcAddr'+part+'Lat').value = place.geometry.location.lat();
					document.querySelector('#txtMhaAcAddr'+part+'Lng').value = place.geometry.location.lng();
					document.querySelector('#txtMhaAcAddr'+part+'Id').value = place.place_id;
				}
				document.querySelector('#txtMhaAcAddr'+part+'Found').value += place.formatted_address + ' \\t\\t[ ' + place.geometry.location.lat() + ' , ' + place.geometry.location.lng() + ' ]';
			}
			document.querySelector('#txtMhaAcAddr'+part+'Found').value += '\\n = ' + results.length + ' result(s).';
		}
		else {
			alert('Bad address');
			document.querySelector('#doavail').disabled  = true;
		}
		if ( qu.length > 0 ) {
			aclookup(qu.shift());
		}
		else {
			acredraw();
		}
	}
	function acredraw()	{
		var directions = {
			origin: {},
			destination: {},
			travelMode: 'DRIVING'
		};
		directions.origin['placeId'] = document.querySelector('#txtMhaAcAddrFId').value;
		directions.destination['placeId'] = document.querySelector('#txtMhaAcAddrTId').value;
		if ( directions.origin['placeId'].length == 0 ) {
			directions.origin['placeId'] = directions.destination['placeId'];
		}
		if ( directions.destination['placeId'].length == 0 ) {
			directions.destination['placeId'] = directions.origin['placeId'];
		}
		gDirections.route(directions, function (response, status) {
			if (status === 'OK') {
				gTravel.setDirections(response);
				//computeTotalDistance(gTravel.getDirections());
				document.querySelector('#doavail').disabled  = false;
			} else {
				alert('Directions request failed due to ' + status);
				document.querySelector('#doavail').disabled  = true;
			}
		});
	}
</script>";


	private static $ms_strStyles = 
'<style>
	.mhaacTable {	background-color:white;	}
	.mhaacTable, .mhaacTable tr, .mhaacTable th, .mhaacTable td {	border:solid 1px #888; border-collapse:collapse; padding: 0 7px; }
	.mhaacTableDuo {	width:100% }
	.mhaacTableDuo, .mhaacTableDuo tr, .mhaacTableDuo th, .mhaacTableDuo td	{	border:none; }
	.mhaacTableTrio {	width:100% }
	.mhaacTableTrio, .mhaacTableTrio tr, .mhaacTableTrio th, .mhaacTableTrio td	{	border:none; }
	.mhaacShortCell	{	padding-top: 0 !important;	padding-bottom:0 !important;	}
	.mhaacStatusNA, .mhaacNA		{	background-color:#ddd;	}
	.mhaacStatusGood	{	background-color:#9f9;	}
	.mhaacStatusWarn	{	background-color:#fd3 !important;	}
	.mhaacStatusBad		{	background-color:#fbb !important;	}
	.mhaacStatusOk		{	background-color:#fff;	}
	.mhaacStatusOld		{	background-color:#bbb; color:#666;	}
	.mhaacStatusLine	{	font-size:10pt; display:inline-block; padding-top:5px;	}
	.mhaacStatusLine img	{	vertical-align:text-bottom;	}
	.mhaacListCompact	{	margin-left: 20px !important;	}
	ul.mhaacListCompact	{	list-style:bullet;	}
	.mhaacListCompact li	{	margin-bottom: 0 !important;	}
</style>';

	private static $ms_strHTMLEmailFooter = 
'	<br/>
	This email was generated by the website <a href="[[URL_HOME]]" target="_blank">[[URL_HOME]]</a>
';

	function __construct()
	{
		parent::__construct(false,$name = __(self::TITLE));
	}
	
	public static function InitialiseStatics()
	{
		$config = CAutocabChauffeur::GetConfig();
		CAutocabChauffeur::$ms_bTestingCabe = $config["API-CABE"]["CABE.TESTSERVER"];
		
		self::$ms_strHTMLEmailFooter	=	self::ReplaceAttributes( self::$ms_strHTMLEmailFooter, ['URL_HOME' => home_url()] );
	}
	
	public static function mkdirIfNeeded($pathname, $mode = 0777)
	{
		if ( !is_dir($pathname) )
		{
			return	mkdir($pathname, $mode);
		}
		return	true;
	}
	public static function DoLog( $bTS, $sRegion, $sLine, $sText, $bOutput=false )
	{
		self::DoLogAlt( $bTS, $sRegion, "#".$sLine, $sText, $bOutput, "log");
	}

	public static function DoLogAlt($bTS, $Seg1, $Seg2, $sText, $bOutput=false, $strSubFileName="alt")
	{
		$t	=	time();
		$f	=	$_SERVER['DOCUMENT_ROOT']."/_logs_acbooking/".gmdate(self::DATEFMT_DATE_YYYY_MM, $t);
		self::mkdirIfNeeded($f, 0775);
		
		$strFile	=	$f."/".$strSubFileName."-".gmdate(self::DATEFMT_DATE_HI2LO, $t).".log";
		$prefix = "";
		if ( $bTS )
		{
			$prefix .= gmdate( 'c' );
		}
		if ( !empty($Seg1) )
		{
			$prefix .= "\t".$Seg1;
			if ( !empty($Seg2) )
			{
				$prefix .= $Seg2;
			}
		}
		error_log($prefix."\t".$sText."\n", 3, $strFile);
		if ( $bOutput )
		{
			echo gmdate( 'c' )."\t".htmlentities($sText)."<br/>";
		}
	}
	
	public static function GetTimeZone()
	{
		return new \DateTimeZone(self::DATEFMT_TIMEZONE);
	}
	
	private static function GetConfig( $strSub="" )
	{
		global	$g_strAutocabServerFile;
		$config = [];
		$host	=	strtolower($_SERVER['SERVER_NAME']);
		$config["SERVER"]	=	[
			"HOST"	=>	$g_strAutocabServerFile
		];

// 		if ( self::SERVER_LIVE == $g_strAutocabServerFile )
		if ( 1 )
		{
			if ( 1 )
			{
				$config["API-CABE"]	=	[
					"CABE.TESTSERVER"	=>	false,
					"CABE.URL"		=>	"https://cxs.autocab.net/api/agent",
					"CABE.AGENT"	=>	"72992",
					"CABE.PW"		=>	"bLwg9JJ793gvVCb3UP6HxKpD",
					"CABE.VENDOR"	=>	"20068",
				];
			}
		}
		else
		{
			if ( !MHAAUTOCABCHAUFFEUR_LOCAL_DEV )
			{
			}
		}
		
		if ( !isset( $config["API-CABE"] ) )
		{
			// CABE Shared test environment
			$config["API-CABE"]	=	[
					"CABE.TESTSERVER"	=>	true,
					"CABE.URL"	=>	"https://cxs-staging.autocab.net/api/agent",
					"CABE.AGENT"	=>	"300999",
					"CABE.PW"		=>	"jEHjE5Kv",
					"CABE.VENDOR"	=>	"700999",
			];
		}
		if ( !isset( $config["ADMIN"] ) )
		{
			// CABE Shared test environment
			$config["ADMIN"]	=	[
					"EMAIL.ANALYSIS.FREQUENCY"	=>	3540,
					"EMAIL.ANALYSIS.TO"			=>	'developer@cyberflair.com',
			];
		}

		if ( !empty($strSub) )
		{
			if ( isset($config[$strSub]) )
			{
				return $config[$strSub];
			}
			return [];
		}
		return $config;
	}
	
	private static function GetOptionalDateTime($dt)
	{
		if ( null !== $dt )
		{
			return $dt->format(self::DATEFMT_DATETIME_FRIENDLY);
		}
		return "";
	}
	
	private static function GetOptionalData($data, $strPrefix="", $strPostfix="")
	{
		if ( !empty($data) )
		{
			return $strPrefix.$data.$strPostfix;
		}
		return "";
	}

	private static function GetOptionalVias($data, $strPrefix="", $strPostfix="")
	{
		if ( !empty($data) )
		{
			//$list = explode(' >>> ', $data);
			if(is_array($data) || is_object($data)){
				$data = implode("\n",$data);
			}

			$list = explode(PHP_EOL,$data);
			$ret = "";
			$i = 1;
			foreach ( $list as $via )
			{
				$ret .= "<br/>v".$i.": ".$via;
				$i++;
			}
			return $ret;
		}
		return "";
	}
	
	private static function GetDateClass($dt, $dtNow)
	{
		$di = $dtNow->diff($dt);
		//print_r($di);
		if ( $di->invert ) {
			return "mhaacStatusOld";
		}
		if ( $di->days > 7 ) {
			return "mhaacStatusOk";
		}
		return "mhaacStatusWarn";
	}
	
	private static function GetDateDistance($dt, $dtNow, $bAgo=false)
	{
		$ret = "";
		$di = $dtNow->diff($dt);
		if ( $di->invert xor $bAgo ) {
			$ret	=	"-";
		}
		if ( $di->days > 0 ) {
			$ret .= $di->days." days";
		} else {
			$ret .= $di->h." hours";
		}
		return $ret;
	}

	private static function GetTimeDistance($dt, $dtNow, $bAgo=false)
	{
		$ret = "";
		$di = $dtNow->diff($dt);
		if ( $di->invert xor $bAgo ) {
			$ret	=	"-";
		}
		if ( $di->days > 0 ) {
			$ret .= $di->days." days";
		} else if ( $di->h ) {
			$ret .= $di->h." hours";
		}
		else {
			$ret .= $di->i." mins";
		}
		return $ret;
	}
	
	private static function GetTimeDistanceSeconds($dt, $dtNow, $bAgo=false)
	{
		if ( $bAgo ) {
			return $dtNow->getTimestamp() - $dt->getTimestamp();
		}
		else {
			return $dt->getTimestamp() - $dtNow->getTimestamp();
		}
	}

	private static function GetBookingAlertLevel($iSoon, $iEmailedAgo, &$iAlertLevel)
	{
		// TESTLINE if (MHAAUTOCABCHAUFFEUR_LOCAL_DEV )	{	$iSoon	=	163330;			$iEmailedAgo = 999990;		}			// TEST LINE
		$iMustEmail		=	0;
		$iAlertLevel	=	self::ANALYSIS_REPORT_ALERT_NONE;
		if ( $iSoon <= self::ANALYSIS_REPORT_URGENT_WATERSHED)
		{
			$iAlertLevel	=	self::ANALYSIS_REPORT_ALERT_URGENT;
			if ( $iEmailedAgo >= self::ANALYSIS_REPORT_URGENT_FREQUENCY )
			{
				$iMustEmail		=	1;
			}
		}
		else if ( $iSoon <= self::ANALYSIS_REPORT_NOTICE_WATERSHED)
		{
			$iAlertLevel	=	self::ANALYSIS_REPORT_ALERT_NOTICE;
			if ( $iEmailedAgo >= self::ANALYSIS_REPORT_NOTICE_FREQUENCY )
			{
				$iMustEmail		=	1;
			}
		}
		else if ( $iSoon >= self::ANALYSIS_REPORT_ANTI_WATERSHED )
		{
			$iAlertLevel	=	self::ANALYSIS_REPORT_ALERT_ANTI;
		}
		return $iMustEmail;
	}
	
	private static function GetBookingAlertStyle($iAlertLevel)
	{
		switch ($iAlertLevel)
		{
			case	self::ANALYSIS_REPORT_ALERT_URGENT:
			{
				return "background-color:#ffcccc;";
			}
			case	self::ANALYSIS_REPORT_ALERT_NOTICE:
			{
				return "background-color:#ffff99;";
			}
			case	self::ANALYSIS_REPORT_ALERT_ANTI:
			{
				return "background-color:#cccccc;";
			}
		}
		return "";
	}
	
	private static function ReplaceAttributes( $strHaystack, $arrAttributes )
	{
		foreach ( $arrAttributes as $var => $val )
		{
			$strHaystack	=	str_replace( "[[".$var."]]", $val, $strHaystack );
		}
		return	$strHaystack;
	}
	
	public static function StrRedact( $strInput, $strStartFind='', $strEndFind='', $strSubst="\n...\n" )
	{
		if ( empty($strStartFind) && empty($strEndFind) )
		{
			return $strInput;
		}
		$iStart	=	strpos( $strInput, $strStartFind );
		if ( false !== $iStart )
		{
			$iEnd	=	strpos( $strInput, $strEndFind, $iStart );
			$strOutput	=	substr($strInput, 0, $iStart ).$strSubst;
			if ( false !== $iEnd )
			{
				$strOutput	.=	substr($strInput, $iEnd + strlen($strEndFind) );
			}
			// Check again for same block
			return	self::StrRedact( $strOutput, $strStartFind, $strEndFind, $strSubst );
		}
		return	$strInput;
	}
	

	public static function display_shortcode( $atts )
	{
		$_SESSION[self::SHORTCODE] = null;
		
		if( isset($_POST['nameHidden']) && $_POST['nameHidden'] == 'Y' )
		{
			// POSTING = REMEMBER
			$bDebug	=	false;

			try
			{
			}
			catch (Exception $e)
			{
			}
		
			return;
		}
		
		// SHOWING = INJECT ANY CURRENT VALUES
		

		$arrAttrsBefore = self::$ms_arrAttrs;
		$arrAttrsBefore['SHORTCODE']	=	self::SHORTCODE;
//		$arrAttrsBefore['URL_HOME']		=	home_url();
		$arrAtts = shortcode_atts( $arrAttrsBefore, $atts, self::SHORTCODE );
		
		$strReturn = "";
		$strReturn	.=	self::ReplaceAttributes( self::$m_strScripts, $arrAtts );
		$strReturn	.=	self::$ms_strStyles;
		$strReturn	.=	self::ReplaceAttributes( self::$m_strHTML, $arrAtts );

		return $strReturn;
	}
	
	public static function display_tabs( $arrTabs, $strPageId )
	{
		echo '<h2 class="nav-tab-wrapper">';
		$strSelected = (!empty($_GET['tab']))? esc_attr($_GET['tab']) : '';
		foreach ( $arrTabs as $tab => $title )
		{
			if ( empty($strSelected) )
			{
				$strSelected	=	$tab;
			}
			echo '<a class="nav-tab'.(($strSelected==$tab)?' nav-tab-active':'').'" href="?page='.$strPageId.'&tab='.$tab.'">'.$title.'</a>';
		}
		echo '</h2>';
		return	$strSelected;
	}
	
	public static function DoValidPostsExists()
	{
		$bRet	=	false;
		$args = array(   'name' => self::POST_TYPE_BOOKINGS, );
		$post_types = get_post_types( $args, 'objects' );
		foreach ( $post_types  as $post_type ) {
			if ( "Booking" == $post_type->labels->singular_name ) {
				$bRet	=	true;
				break;
			}
		}
		return $bRet;
	}
	
	private static function ChauffeurUseGoogle($strExtra="")
	{
		// NOTE... From \plugins\chauffeur-shortcodes-post-types\chauffeur-shortcodes-post-types.php
		global $chauffeur_data;
		$GoogleMapApiKey = $chauffeur_data['google-map-api-key'];
		// $GoogleMapApiKey = 'AIzaSyBxXDkCSBPquzn_3-Ddzkm8KeVc11P2YqQ';
		if ( !empty($chauffeur_data['google-map-api-key']) ) {
			
			if ( !empty($chauffeur_data['google-api-language']) ) {
				wp_register_script('googlesearch', 'https://maps.googleapis.com/maps/api/js?key=' . $GoogleMapApiKey . '&libraries=places&mode=driving&language='.$chauffeur_data['google-api-language'].$strExtra);
				wp_enqueue_script('googlesearch');
			} else {
				wp_register_script('googlesearch', 'https://maps.googleapis.com/maps/api/js?key=' . $GoogleMapApiKey . '&libraries=places&mode=driving'.$strExtra);
				wp_enqueue_script('googlesearch');
			}
		}
		// ...NOTE
	}	
	
	private static function cabeGetApi()
	{
		$config = self::GetConfig("API-CABE");
		$apiCabe = new CAutocabChauffeurApiCabe($config);
		unset($config);
		return $apiCabe;
	}
	
	private static function cabeGetMetaKeyPrefix( $bOutbound )
	{
		if ( $bOutbound )
		{
			return 'chauffeur_autocab_cabe_';
		}
		return 'chauffeur_autocab_return_cabe_';
	}
	
	private static function cabeGetBookingAuthRef( $postID, $bOutbound )
	{
		return get_post_meta( $postID, self::cabeGetMetaKeyPrefix($bOutbound) . self::METADATA_BOOKING_DIR_AUTHREF, TRUE);
	}
	
	//
	//	Get CABE Booking data, and update info we cache
	//
	private static function cabeGetAndCacheBooking( &$apiCabe, $postID, $bOutbound )
	{
		$cabe_auth_ref = self::cabeGetBookingAuthRef( $postID, $bOutbound );
		if ( empty($cabe_auth_ref) )
		{
			return null;
		}
		$result = $apiCabe->callBookingStatus( self::SHORTCODE, $cabe_auth_ref );
		
		//echo "<pre>".print_r($result,1)."</pre>";
		if ( !empty($result) )
		{
			// Update cache
			$metaCache	=	self::cabeGetMetaKeyPrefix($bOutbound) . self::METADATA_BOOKING_DIR_BOOKINGDATA;
			update_post_meta( $postID, $metaCache, json_encode($result) );
			//echo "{<pre>".print_r(get_post_meta( $postID, $metaCache, TRUE),1)."</pre>}";
		}
		
		return $result;
	}
	
	private static function display_cabe_status( &$apiCabe, $postID, $bOutbound )
	{
		$ret = [];
		$ret['ok'] = false;
		$ret['class']	=	'mhaacStatusBad';
		$ret['html'] = '<br/>';
		$cabe_auth_ref = self::cabeGetBookingAuthRef( $postID, $bOutbound );
		
		$bBooked = empty($cabe_auth_ref)? false : true;
		$link	=	'?page='.self::UNIQUE2_CABE.'&id='.$postID.'&dir='.($bOutbound? 'out':'ret');
		
		$tsLastEmailed = intVal(get_post_meta($postID, self::cabeGetMetaKeyPrefix($bOutbound) . self::METADATA_BOOKING_DIR_LASTEMAIL, TRUE));
		if ( empty($tsLastEmailed) )
		{
			$ret['html'] .= '<span style="background-color:yellow;" title="First notification email has not been sent">NEW</span>&nbsp;';
		}
		/* echo "<pre>";
		print_r($bBooked);
		echo "</pre>"; die; */
		if ( $bBooked )
		{
			$metaCache = self::cabeGetMetaKeyPrefix($bOutbound) . self::METADATA_BOOKING_DIR_BOOKINGDATA;
			$result = get_post_meta( $postID, $metaCache, TRUE );
			if ( empty($result) )
			{
				// Get now live.
				$result = self::cabeGetAndCacheBooking( $apiCabe, $postID, $bOutbound );
			}
			if ( !empty($result) )
			{
				$result = json_decode($result);
				//echo "<pre>";print_r($result);echo "</pre>";
			}

			if ( empty($result) )
			{
				$ret['ok']		=	false;
				$ret['class']	=	'mhaacStatusBad';
				$ret['html']	.=	'<span class="mhaacStatusLine" title="Reference recorded from CABE is no longer found in CABE.">CABE: <b><img src="'.MHAAUTOCABCHAUFFEUR___PLUGIN_IMAGES.'status_bad.png" /> Reference Broken</b> </span>&nbsp;<a href=\''.$link.'\'" class="button">Query CABE</a>';
			}
			else
			{
				$ret['ok']		=	self::get_cabe_status_details( $result, $strStatusText, $strStatusClass, $bInCabe );
				$ret['class']	=	$strStatusClass;
				$ts = "";
				
				if ( isset($result->Agent->Time) )
				{
					$tz		=	self::GetTimeZone();
					$ret['ts']		=	\DateTime::createFromFormat( self::DATEFMT_DATETIME_ISO, $result->Agent->Time, $tz );
					if ( !empty($ret['ts']) )
					{
						$dtNow	=	new \DateTime("now", $tz);
						$t		=	self::GetTimeDistanceSeconds($ret['ts'], $dtNow, true);
						if ( $t > 4200 ) {
							$ts		=	'<img src="'.MHAAUTOCABCHAUFFEUR___PLUGIN_IMAGES.'clock_bad.png" title="Checked in CABE '.self::GetTimeDistance($ret['ts'], $dtNow, true).' ago." /><b>!</b>';
						} else if ( $t > 300 ) {
							$ts		=	'<img src="'.MHAAUTOCABCHAUFFEUR___PLUGIN_IMAGES.'clock.png" title="Checked in CABE '.self::GetTimeDistance($ret['ts'], $dtNow, true).' ago." />';
						}
					}
				}
				$ret['html']	.=	'<span class="mhaacStatusLine">CABE: <b><img src="'.MHAAUTOCABCHAUFFEUR___PLUGIN_IMAGES.($ret['ok']?'status_good.png':'status_bad.png').'" /> Scheduled - '.$strStatusText.'</b> '.$ts.'</span>&nbsp;<a href=\''.$link.'\'" class="button">Query CABE</a>';
			}
		}
		else
		{
			$ret['ok']		=	false;
			$ret['class']	=	'mhaacStatusBad';
			$ret['html']	.=	'<span class="mhaacStatusLine" title="There is no reference that this journey has been booked in CABE">CABE: <b><img src="'.MHAAUTOCABCHAUFFEUR___PLUGIN_IMAGES.'status_bad.png" /> Not booked</b> </span>&nbsp;<a href=\''.$link.'\'" class="button">Authorise in CABE</a></small>';
		}
		return $ret;
	}
	
	//	return:	TRUE = Good Booking, otherwise FALSE.
	private static function get_cabe_status_details( $bookingresult, &$strStatusText, &$strStatusClass, &$bInCabe )
	{
		$strStatusText = 'Unknown';
		$strStatusClass = 'mhaacStatusBad';
		$bInCabe = false;
		$bGoodBooking = false;
		if ( !empty($bookingresult) )
		{
			$bInCabe = true;
			switch (strtoupper($bookingresult->Status))
			{
				case 'BOOKEDACTIVE':
				case 'DISPATCHED':
				case 'VEHICLEARRIVED':
				case 'PASSENGERONBOARD':
				case 'NOFARE':
				case 'COMPLETED':
				{
					$strStatusClass = 'mhaacStatusGood';
					$bGoodBooking = true;
					break;
				}
				case 'BOOKEDNOTACTIVE':
				{
					$strStatusClass = 'mhaacStatusWarn';
					break;
				}
				case 'UNKNOWN':
				case 'CANCELLED':
				default:
				{
					$strStatusClass = 'mhaacStatusBad';
					break;
				}
			}
			$strStatusText = $bookingresult->Status;
		}
		return $bGoodBooking;
	}
	
	private static function get_active_booking( &$arrBookings, $dtIgnore, &$iTotalBookings )
	{
		$iTotalBookings	=	0;
		$arrBookings	=	[];
		
		$posts = get_posts([
		  'post_type' => 'payment',
		  'post_status' => 'publish',
		  'numberposts' => -1
		  // 'order'    => 'ASC'
		]);
		
		$tz		=	self::GetTimeZone();
		$iTotalBookings	=	count($posts);
		
		foreach ( $posts as $post )
		{
			$id	=	$post->ID;
			
			$get_pickup_date = get_post_meta($id,'chauffeur_payment_pickup_date',TRUE);
			$get_pickup_time = get_post_meta($id,'chauffeur_payment_pickup_time',TRUE);
			$get_return_date = get_post_meta($id,'chauffeur_payment_return_date',TRUE);
			$get_return_time = get_post_meta($id,'chauffeur_payment_return_time',TRUE);
			
			$dtLastDate		=	null;
			$dtEarliestDate	=	null;
			$dtWhenPickup	=	null;
			$dtWhenReturn	=	null;
			
			$last_date = "";
			if ( !empty($get_pickup_date) ) {
				$dtEarliestDate = $dtLastDate	=	$dtWhenPickup	=	\DateTime::createFromFormat( self::DATEFMT_DATETIME_CHAUF, $get_pickup_date." ".$get_pickup_time, $tz );
			}
			if ( !empty($get_return_date) ) {
				$dtLastDate	=	$dtWhenReturn	=	\DateTime::createFromFormat( self::DATEFMT_DATETIME_CHAUF, $get_return_date." ".$get_return_time, $tz );
			}
			if ( empty($dtLastDate) )
			{
				continue;
			}
			if ( $dtLastDate < $dtIgnore )
			{
				continue;
			}
			if ( $dtEarliestDate < $dtIgnore )
			{
				$dtEarliestDate	=	$dtLastDate;
			}
			$arrBookings[]	=	new CAutocabChauffeurBookingSorter($id, $dtEarliestDate, $dtWhenPickup, $dtWhenReturn, $dtLastDate );
			
			//echo "<pre>".print_r($post,1)."</pre>";
			//echo "<pre>".print_r(get_post_meta($id),1)."</pre>";
			
		}

		//print_r($arrBookings);
		usort($arrBookings, "CAutocabChauffeurBookingSorter::cmp");
		//echo "<hr/>";	print_r($arrBookings);
	}

	public static function display_admin( )
	{
		echo self::$ms_strStyles;
		
		$strMessage	=	'';
		
		//if( isset($_POST['nameHiddenSettings']) && $_POST['nameHiddenSettings'] == 'Y' )
		//{
			// Update Settings
		//	$strMessage	=	'<div class="updated"><p><strong>Settings saved.</strong></p></div>';
		//}

		if ( !empty($strMessage ) )
		{
			echo $strMessage;
		}
	
		// Read Settings
		
		echo '<h1>'.self::TITLE.'</h1>';
		if ( self::TESTING ) {
			echo "<big style='color:#d00; font-weight:bold;'>***WARNING***: This plugin is Under Development and is for testing only.</big><br/>";
		}
		if ( self::$ms_bTestingCabe ) {
			echo "<br/><big style='color:#d00; font-weight:bold;'>***IMPORTANT***: Calls to CABE are currently to the SHARED development test site, so submissions of personal information is to be avoided.</big><br/>";
		}
		
		if ( !self::DoValidPostsExists() )
		{
			echo "Could not find custom posts of type ".self::POST_TYPE_BOOKINGS."(payment).";
			die;
		}
		
		$tab = self::display_tabs( array(
			"bookings" => "<span class='wp-menu-image dashicons-before dashicons-controls-repeat'></span> Bookings",
			"settings" => "<span class='wp-menu-image dashicons-before dashicons-admin-settings'></span> Settings",
			"tests" => "<span class='wp-menu-image dashicons-before dashicons-hammer'></span> Tests",
			"usage" => "<span class='wp-menu-image dashicons-before dashicons-editor-help'></span> Usage",
		), self::UNIQUE1_ADMIN );
		
		switch ( $tab )
		{
			case "bookings":
			{
				echo '<br/><form name="formSettings" method="post" action="">
				<input type="hidden" name="nameHiddenSettings" value="Y">';
				
				echo '<h2 class="title">Bookings</h2>';
				/*
				<table class="form-table">
					<tr><th><label for="txtMhajtEmailTo">To Addresses</label></th><td><input type="text" name="txtMhajtEmailTo" name="txtMhajtEmailTo" value="'.$arrForm['txtMhajtEmailTo'].'" size="80">
						<span class="wp-menu-image dashicons-before dashicons-editor-help" title="Separate additional emails with commas." style="cursor:default;"></span><br/>
					<tr><th><label for="txtMhajtEmailCc">CC Addresses</label></th><td><input type="text" name="txtMhajtEmailCc" name="txtMhajtEmailCc" value="'.$arrForm['txtMhajtEmailCc'].'" size="80">
						<span class="wp-menu-image dashicons-before dashicons-editor-help" title="Separate additional emails with commas." style="cursor:default;"></span><br/>
					</td></tr>
				</table>
				*/
				
				//echo "<pre>".print_r(get_post_types(),1)."</pre>";
				//echo "<pre>".print_r(get_post_types("payment"),1)."</pre>";
				//get_post_types("payment");
				
				$tz		=	self::GetTimeZone();
				$dtNow		=	new \DateTime("now", $tz);
				$dtIgnore	=	clone $dtNow;
				$dtIgnore->modify("-12 hours");			// KevFix
				
				$iTotalBookings	=	0;
				$arrBookings	=	[];
				self::get_active_booking( $arrBookings, $dtIgnore, $iTotalBookings );

				echo "Analysis time: <b>".$dtNow->format(self::DATEFMT_DATETIME_FRIENDLY)."</b>. Ignoring prior to time: <b>".$dtIgnore->format(self::DATEFMT_DATETIME_FRIENDLY)."</b>. Analysed <b>".$iTotalBookings."</b> bookings.<br/><br/>";
				
				$apiCabe = self::cabeGetApi();
				
				$bDisplayedBound1	=	false;
				$bDisplayedBound2	=	false;
				
				echo "<table class='mhaacTable'><tr><th>#</th><th>Outbound</th><th>Return</th></tr>";
				/* echo "<pre>";
				print_r($arrBookings); die; */
 				foreach ( $arrBookings  as $booking ) {
					$iDistSecs	=	self::GetTimeDistanceSeconds($booking->dtEarliestOfInterest, $dtNow);
					if ( !$bDisplayedBound1 && ( $iDistSecs > self::SECS_PER_DAY ) )
					{
						echo '<tr><td colspan="3" class="mhaacNA" align="center">Over 1 day away...</td></tr>';
						$bDisplayedBound1	=	true;
					}
					if ( !$bDisplayedBound2 && ( $iDistSecs > self::SECS_PER_DAY * 7 ) )
					{
						echo '<tr><td colspan="3" class="mhaacNA" align="center">Over 7 days away...</td></tr>';
						$bDisplayedBound2	=	true;
					}

					$id	=	$booking->ID;
					echo "<tr><td valign='top'><a href='".get_edit_post_link($id)."' target='_blank'>".$id."</a></td>";
					
					
					$dist	=	self::GetDateDistance($booking->dtWhenPickup, $dtNow);
					$cabe	=	self::display_cabe_status( $apiCabe, $id, TRUE );
					$class	=	empty($cabe['class'])? self::GetDateClass($booking->dtWhenPickup, $dtNow) : $cabe['class'];
					echo "<td valign='top' class='".$class."'><table class='mhaacTableTrio dd'><tr><th>".$dist."</th><th align='center'>".self::GetOptionalDateTime($booking->dtWhenPickup)."</th><th align='right'>".get_post_meta($id,'chauffeur_payment_trip_distance',TRUE)."</th></tr></table>";
					echo "<small>F: ".get_post_meta($id,'chauffeur_payment_pickup_address',TRUE);
					echo self::GetOptionalVias(get_post_meta($id,'chauffeur_payment_pickup_via',TRUE), "<br/>V: ");
					echo "<br/>T: ".get_post_meta($id,'chauffeur_payment_dropoff_address',TRUE)."</small>";
					echo $cabe['html'];
					echo "</td>";
					if ( !empty($booking->dtWhenReturn) )
					{
						$dist	=	self::GetDateDistance($booking->dtWhenReturn, $dtNow);
						$cabe	=	self::display_cabe_status( $apiCabe, $id, FALSE );
						$class	=	empty($cabe['class'])? self::GetDateClass($booking->dtWhenReturn, $dtNow) : $cabe['class'];
						echo "<td valign='top' class='".$class."'><table class='mhaacTableTrio'><tr><th>".$dist."</th><th align='center'>".self::GetOptionalDateTime($booking->dtWhenReturn)."</th><th align='right'>".get_post_meta($id,'chauffeur_payment_return_trip_distance',TRUE)."</th></tr></table>";
						echo "<small>F: ".get_post_meta($id,'chauffeur_payment_return_address',TRUE);
						echo self::GetOptionalVias(get_post_meta($id,'chauffeur_payment_return_pickup_via',TRUE), "<br/>V: ");
						echo "<br/>T: ".get_post_meta($id,'chauffeur_payment_return_dropoff',TRUE)."</small>";
						echo $cabe['html'];
						echo "</td>";
					}
					else
					{
						echo "<td></td>";
					}
//					echo "<td valign='top'><pre>".print_r($post,1)."</pre></td>";
//					echo "<td valign='top'><pre>".print_r(get_post_meta($id),1)."</pre></td>";
					echo "</tr>";
				}				
				echo "</table>";

				break;
			}
			case "settings":
			{
				echo '<h2 class="title">Settings</h2><form><table class="mhaacTable">';
				$temp = self::GetConfig();
				echo '
					<tr><th colspan="2">CABe</th></tr>
					<tr><td>URL:</td><td>'.$temp['API-CABE']['CABE.URL'].'</td></tr>
					<tr><td>Agent ID:</td><td>'.$temp['API-CABE']['CABE.AGENT'].'</td></tr>
					<tr><th colspan="2">Email</th></tr>
					<tr><td>Analysis Max Email Frequency:</td><td>'.($temp['ADMIN']['EMAIL.ANALYSIS.FREQUENCY'] / 60).' minutes</td></tr>
					<tr><td>Analysis Send To:</td><td>'.$temp['ADMIN']['EMAIL.ANALYSIS.TO'].'</td></tr>
					';
				echo '</table><p class="submit"><input type="submit" name="Submit" class="button-primary" value="Save Changes" /></p>';
				echo '</form>';
				unset($temp);
				break;
			}
			case "tests":
			{
				echo '<h2 class="title">Tests</h2>';
				
				$strTestAddr = "New Building, Normacot Road, Longton, ST3 1PL";
				echo '
				<table class="form-table">
					<tr><th><label for="txtMhaAcAddr">Test Address</label></th>
						<td><input type="text" name="txtMhaAcAddrF" id="txtMhaAcAddrF" value="'.htmlentities($strTestAddr).'" style="width:100%;" /><br/>
							<textarea name="txtMhaAcAddrFFound" id="txtMhaAcAddrFFound" value="" rows="4" readonly style="width:100%; font-size:9pt;"></textarea></td>
						<td valign="top" style="vertical-align:top;"><button onclick="javascript:aclookup(\'F\');">Lookup</button></td>
					</td></tr>
					<tr><th><label for="">Test Address Coordinates</label></th>
						<td>lat=<input type="text" name="txtMhaAcAddrFLat" id="txtMhaAcAddrFLat" value="" readonly />, 
						lng=<input type="text" name="txtMhaAcAddrFLng" id="txtMhaAcAddrFLng" value="" readonly />,
						id=<input type="text" name="txtMhaAcAddrFId" id="txtMhaAcAddrFId" value="" readonly />
					</td></tr>
				</table>
				';

				
				self::ChauffeurUseGoogle();
				
				//fields: ['photos', 'formatted_address', 'name', 'rating', 'opening_hours', 'geometry'],
				echo self::$ms_strScriptsBooking;
				
				$apiCabe = self::cabeGetApi();
				$strTestAuthorizationRef = "700999-300999-21165";			// AuthorizationReference
				$strCancelAuthorizationRef = $strTestAuthorizationRef;
				
				if ( 1 ) {
					echo "<hr>";
					$result = $apiCabe->callBookingStatus( "TESTCALLREF1", $strTestAuthorizationRef );
					if ( empty($result) ) {
						echo "Error:<pre>".$apiCabe->GetLastError()."</pre>";
					} else {
						echo "Result:<pre>".print_r($result,1)."</pre>";
					}
				}
				
				if ( 0 ) {
					echo "<hr>";
					$result = $apiCabe->callBookingAvailability(
						"TESTCALLREF2",
						new DateTime("now", self::GetTimeZone() ),
						"MediaHeads Test", "000", "kevin@mediaheads.co.uk",
						2, 1, "Estate",
						"New Building, Normacot Road, Longton, ST3 1PL", "53.00266799999999", "-2.179403999999977",
						"New Building, Normacot Road, Longton, ST3 1PL", "53.00266799999999", "-2.179403999999977"
					);
					if ( empty($result) ) {
						echo "Error:<pre>".$apiCabe->GetLastError()."</pre>";
					} else {
						echo "Result:<pre>".print_r($result,1)."</pre>";
					}
				}
				
				if ( 0 ) {
					echo "<hr>";
					$result = $apiCabe->callBookingAuthorization(
						"TESTCALLREF3",
						$apiCabe->GetLastAvailabilityBookingRef(),
						"MyUniqueReference",
						"MediaHeads Test", "000", "kevin@mediaheads.co.uk",
						"Test Note to Driver",
						"",
						"None"
					);
					if ( empty($result) ) {
						echo "Error:<pre>".$apiCabe->GetLastError()."</pre>";
					} else {
						echo "Result:<pre>".print_r($result,1)."</pre>";
					}
				}
				
				if ( isset($strCancelAuthorizationRef) ) {
					echo "<hr>";
					$result = $apiCabe->callBookingCancellation( "TESTCALLREF4", $strCancelAuthorizationRef );
					if ( empty($result) ) {
						echo "Error:<pre>".$apiCabe->GetLastError()."</pre>";
					} else {
						echo "Result:<pre>".print_r($result,1)."</pre>";
					}
				}

				echo "<hr/>";
				break;
			}
			case "usage":
			{
				echo '<h2 class="title">Usage</h2>';
				$url =	admin_url( 'admin-ajax.php' ).'?action='.self::PREFIX . self::AJAX_ACTION_ANALYSE;
				echo '<h4>Analysis</h4>
<p>To perform athe analyses of booking, call:<br/>
<code>'.$url.'</code><br/><a href="'.$url.'" target="_blank">Analyse Now...</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
<a href="'.$url.'&'.self::URLPARAM_FORCE_EMAIL.'=1" target="_blank">Analyse Now (Force sending email)...</a></p>

				';
				
				//self::ajax_callback_analyse();
				
				break;
			}
		}

		echo '</div>';
	}
	
	public static function display_cabe( )
	{
		echo self::$ms_strStyles;
		$strMessage	=	'';
		$strAbortButton = '';
		$apiCabe	=	null;
		
		echo '<h1>'.self::TITLE.' CABE Booking</h1>';
		if ( self::TESTING ) {
			echo "<big style='color:#d00; font-weight:bold;'>***WARNING***: This plugin is Under Development and is for testing only.</big><br/>";
		}
		if ( self::$ms_bTestingCabe ) {
			echo "<br/><big style='color:#d00; font-weight:bold;'>***IMPORTANT***: Calls to CABE are currently to the SHARED development test site, so submissions of personal information is to be avoided.</big><br/>";
		}

		if ( isset($_POST['id']) )
		{
			$postID = isset($_POST['id'])? intVal($_POST['id']) : null;
			$bOutbound = isset($_POST['dir'])? (intVal($_POST['dir'])) : 1;
			if ( !empty($postID) )
			{
				//print_r($_POST);
				if ( isset($_POST['docancel']) )		// Cancel CABE Booking
				{
					$cabe_auth_ref = self::cabeGetBookingAuthRef( $postID, $bOutbound );		
					if ( $cabe_auth_ref == $_POST['authref'] )
					{
						$apiCabe = self::cabeGetApi();
						$result = $apiCabe->callBookingCancellation( self::SHORTCODE, $cabe_auth_ref );
					}
				}
				else if ( isset($_POST['doforget']) )	// Forget authref
				{
					$cabe_auth_ref = self::cabeGetBookingAuthRef( $postID, $bOutbound );
					if ( $cabe_auth_ref == $_POST['authref'] )
					{
						delete_post_meta($postID, self::cabeGetMetaKeyPrefix($bOutbound) . self::METADATA_BOOKING_DIR_AUTHREF );
						$strMessage	=	'<div class="updated"><p><strong>CABE booking cancelled.</strong></p></div>';
					}
				}
				else if ( isset($_POST['doavail']))
				{
					//print_r($_POST);
					$apiCabe = self::cabeGetApi();
					$result = $apiCabe->callBookingAvailability(
						self::SHORTCODE,
						new DateTime("now", self::GetTimeZone() ),
						$_POST['txtMhaAcName'], $_POST['txtMhaAcPhone'], "",
						intVal($_POST['txtMhaAcPassNum']), intVal($_POST['txtMhaAcBagNum']), $_POST['txtMhaAcVehicle'],
						$_POST['txtMhaAcAddrF'], $_POST['txtMhaAcAddrFLat'], $_POST['txtMhaAcAddrFLng'],
						//"","",""		// error destination
						$_POST['txtMhaAcAddrT'], $_POST['txtMhaAcAddrTLat'], $_POST['txtMhaAcAddrTLng']
					);
					if ( empty($result) ) {
						//echo "Error:<pre>".$apiCabe->GetLastError()."</pre>";
						$strMessage	=	'<div class="error"><p><strong>ERROR : '.htmlentities(self::StrRedact($apiCabe->GetLastError(),"<Password>","</Password>","<REDACTED></REDACTED>")).'</strong></p></div>';
					} else {
						// Setup confirm form
						echo '<div class="notice notice-warning"><p><strong>Availability confirmed, you have 15 seconds to authorise!</strong></p></div>';
						echo '<form name="formCABE"  method="post">';
						echo '<input type="hidden" name="id" value="'.$_POST['id'].'" />';
						echo '<input type="hidden" name="dir" value="'.$_POST['dir'].'" />';
						echo '<input type="hidden" name="authref" value="'.$_POST['authref'].'" />';
						echo '<table class="form-table">';
						echo '<tr><th class="mhaacShortCell">Availability Reference:</th><td class="mhaacShortCell"><input type="text" name="cabebookref" value="'.$result->AvailabilityReference.'" readonly style="width:100%;" /></td></tr>';
						echo '<tr><th class="mhaacShortCell">Contact Name:</th><td class="mhaacShortCell"><input type="text" name="txtMhaAcName" value="'.$_POST['txtMhaAcName'].'" readonly style="width:100%;" /></td></tr>';
						echo '<tr><th class="mhaacShortCell">Contact Phone:</th><td class="mhaacShortCell"><input type="text" name="txtMhaAcPhone" value="'.$_POST['txtMhaAcPhone'].'" readonly style="width:20em;" /></td></tr>';
						echo '<tr><th class="mhaacShortCell">Driver Notes</th><td class="mhaacShortCell"><textarea name="txtMhaAcAddrFFound" id="txtMhaAcAddrFFound" value="" rows="2" readonly style="width:100%;"></textarea></td></tr>';
						echo '<tr><th>Estimated Distance:<br/>Estimated Duration:</th><td>'.number_format(intVal($result->EstimatedJourney->Distance)/1609.34,1).' miles (without vias)<br/>'.$result->EstimatedJourney->Duration.' minutes (without vias)</td></tr>';
						echo '</table>
							<input type="submit" id="doauthorization" name="doauthorization" value="Authorise CABE Booking" class="button" />
							 &nbsp;&nbsp;&nbsp;&nbsp; 
							<input type="button" id="" name="" value="Cancel" class="button" onClick="window.location.reload();" />
							 </td></tr>';
						echo '</form>';
						//echo "Result:<pre>".print_r($result,1)."</pre>";
						return;
					}					
				}
				else if ( isset($_POST['doauthorization']))
				{
					//print_r($_POST);
					$apiCabe = self::cabeGetApi();
					$result = $apiCabe->callBookingAuthorization(
						self::SHORTCODE,
						$_POST['cabebookref'],
						$_POST['id']."-".(empty($_POST['dir'])?"R":"O")."-".self::SHORTCODE,
						$_POST['txtMhaAcName'], $_POST['txtMhaAcPhone'], "",
						"",
						"",
						"None"
					);
					if ( empty($result) ) {
						//echo "Error:<pre>".$apiCabe->GetLastError()."</pre>";
						$strMessage	=	'<div class="error"><p><strong>ERROR : '.htmlentities(self::StrRedact($apiCabe->GetLastError(),"<Password>","</Password>","<REDACTED></REDACTED>")).'</strong></p></div>';
					} else {
						if ( isset($result->Result->Success) ) {
							if (( "true" == strtolower($result->Result->Success) ) && (!empty($result->AuthorizationReference))) {
								$strMessage	=	'<div class="updated"><p><strong>Success, the booking has been authorised in CABE.</strong></p></div>';
								update_post_meta( $postID, self::cabeGetMetaKeyPrefix($bOutbound) . self::METADATA_BOOKING_DIR_AUTHREF, (string)$result->AuthorizationReference );
							} else {
								$strMessage	=	'<div class="error"><p><strong>ERROR : '.htmlentities($result->Result->FailureReason).'</strong> ('.htmlentities($result->Result->FailureCode).')</p></div>';
								$strAbortButton = 'OK';
							}
						} else {
							$strMessage	=	'<div class="error"><p><strong>ERROR : CABE did not specify a reason for the failure.</strong></p></div>';
							$strAbortButton = 'OK';
						}
						//echo "Result:<pre>".print_r($result,1)."</pre>";
					}
				}
				else if ( isset($_POST['dosetref']))
				{
					update_post_meta( $postID, self::cabeGetMetaKeyPrefix($bOutbound) . self::METADATA_BOOKING_DIR_AUTHREF, "700999-300999-21165" );
				}
			}
		}

		if ( !empty($strMessage ) )
		{
			echo $strMessage;
		}

		if ( !empty($strAbortButton) )
		{
			echo '<br/><input type="button" id="" name="" value="'.$strAbortButton.'" class="button" onClick="window.location.reload();" />';
			return;
		}
		
		$link_back = '?page='.self::UNIQUE1_ADMIN.'&tab=bookings';
		echo '<h3><a href="'.$link_back.'">&laquo; Back to Bookings</a></h3>';
		
		$postID = isset($_GET['id'])? intVal($_GET['id']) : null;
		$bOutbound = isset($_GET['dir'])? (("out" == $_GET['dir'])? 1 : 0) : 1;
		
		if ( empty($postID) )
		{
			echo 'ERROR: Incorrect call to this page.';
			return;
		}
		
		$cabe_auth_ref = self::cabeGetBookingAuthRef( $postID, $bOutbound );
		
		if ( !isset($apiCabe) ) {
			$apiCabe = self::cabeGetApi();
		}
		
		echo '<form name="formCABE"  method="post">';
		echo '<input type="hidden" name="id" value="'.$postID.'" />';
		echo '<input type="hidden" name="dir" value="'.$bOutbound.'" />';
		echo '<input type="hidden" name="authref" value="'.$cabe_auth_ref.'" />';
		
		$strChaufName	=	htmlentities(trim(get_post_meta($postID,'chauffeur_payment_first_name',TRUE)." ".get_post_meta($postID,'chauffeur_payment_last_name',TRUE)));
		$strChaufPhone	=	htmlentities(get_post_meta($postID,'chauffeur_payment_phone_num',TRUE));
		$strChaufEmail	=	htmlentities(get_post_meta($postID,'chauffeur_payment_email',TRUE));
		$strChaufFrom	=	htmlentities($bOutbound? get_post_meta($postID,'chauffeur_payment_pickup_address',TRUE) : get_post_meta($postID,'chauffeur_payment_return_address',TRUE));
		$strChaufVias	=	$bOutbound? get_post_meta($postID,'chauffeur_payment_pickup_via',TRUE) : get_post_meta($postID,'chauffeur_payment_return_pickup_via',TRUE);
		$strChaufTo		=	htmlentities($bOutbound? get_post_meta($postID,'chauffeur_payment_dropoff_address',TRUE) : get_post_meta($postID,'chauffeur_payment_return_dropoff',TRUE));
		$strChaufPassNum=	htmlentities(get_post_meta($postID,'chauffeur_payment_num_passengers',TRUE));
		$strChaufBagNum	=	htmlentities(get_post_meta($postID,'chauffeur_payment_num_bags',TRUE));
		$strChaufVehicle=	htmlentities(get_post_meta($postID,'chauffeur_payment_item_name',TRUE));
		
		$iVias	=	0;
		if ( !empty($strChaufVias) ) {
			$strChaufVias	=	str_replace(' >>> ', "\n", $strChaufVias, $iVias);
			$iVias++;
			$strChaufVias	=	'<ul class="mhaacListCompact"><li>'.str_replace("\n", "</li><li>", htmlentities($strChaufVias)).'</li></ul>';
		}
		
		if ( empty($cabe_auth_ref) )
		{
			// Booking not made
			self::ChauffeurUseGoogle();		// "&callback=acinitmap"
			
			echo self::$ms_strScriptsBooking;
			
			$strChaufEmail	=	empty($strChaufEmail)? "Not supplied." : "Supplied as '<b>".$strChaufEmail."</b>', but not being sent to CABE.";
			$strChaufVias	=	empty($iVias)? "None." : "<b>".$iVias." vias</b>, but not being sent to CABE as not supported by API.".$strChaufVias;
			
			echo '<table class="form-table">';
			//echo '<tr><td></td><th>Booking Details</th></td></tr>';

			echo '<tr><th class="mhaacShortCell"><label for="txtMhaAcName">Contact Name*:</label></th><td class="mhaacShortCell"><input type="text" name="txtMhaAcName" id="txtMhaAcName" value="'.$strChaufName.'" style="width:100%;" required /></td></tr>';
			echo '<tr><th class="mhaacShortCell"><label for="txtMhaAcPhone">Contact Phone*:</label></th><td class="mhaacShortCell"><input type="text" name="txtMhaAcPhone" id="txtMhaAcPhone" value="'.$strChaufPhone.'" style="width:20em" required /></td></tr>';
			
			echo '<tr><th><label>Contact Email:</label></th><td>'.$strChaufEmail.'</td></tr>';

			echo '<tr><th class="mhaacShortCell"><label for="txtMhaAcPassNum">Number of Passengers:</label></th><td class="mhaacShortCell"><input type="text" name="txtMhaAcPassNum" id="txtMhaAcPassNum" value="'.$strChaufPassNum.'" style="width:5em;" /></td></tr>';
			echo '<tr><th class="mhaacShortCell"><label for="txtMhaAcBagNum">Number of Bags:</label></th><td class="mhaacShortCell"><input type="text" name="txtMhaAcBagNum" id="txtMhaAcBagNum" value="'.$strChaufBagNum.'" style="width:5em;" /></td></tr>';
			echo '<tr><th class="mhaacShortCell"><label for="txtMhaAcVehicle">Vehicle Type:</label></th><td class="mhaacShortCell"><input type="text" name="txtMhaAcVehicle" id="txtMhaAcVehicle" value="'.$strChaufVehicle.'" style="width:15em;" /></td></tr>';

			echo '<tr><th><label for="txtMhaAcAddrF">Pickup From*:</label></th>
					<td><input type="text" name="txtMhaAcAddrF" id="txtMhaAcAddrF" value="'.$strChaufFrom.'" style="width:100%;" required />
						<textarea name="txtMhaAcAddrFFound" id="txtMhaAcAddrFFound" value="" rows="2" readonly style="width:100%; font-size:9pt;"></textarea>
						<br/><input type="text" name="txtMhaAcAddrFLat" id="txtMhaAcAddrFLat" value="" readonly /><input type="text" name="txtMhaAcAddrFLng" id="txtMhaAcAddrFLng" value="" readonly /><input type="text" name="txtMhaAcAddrFId" id="txtMhaAcAddrFId" value="" readonly /></td>
					<td valign="top" style="vertical-align:top;"><a href="javascript:void(0)" onclick="javascript:aclookup(\'F\');" class="button">Lookup</a></td></tr>';

			echo '<tr><th class="mhaacShortCell"><label>Vias:</label></th><td class="mhaacShortCell">'.$strChaufVias.'</td></tr>';

			echo '<tr><th><label for="txtMhaAcAddrT">Drop-off To*:</label></th>
					<td><input type="text" name="txtMhaAcAddrT" id="txtMhaAcAddrT" value="'.$strChaufTo.'" style="width:100%;" required />
						<textarea name="txtMhaAcAddrTFound" id="txtMhaAcAddrTFound" value="" rows="2" readonly style="width:100%; font-size:9pt;"></textarea>
						<br/><input type="text" name="txtMhaAcAddrTLat" id="txtMhaAcAddrTLat" value="" readonly /><input type="text" name="txtMhaAcAddrTLng" id="txtMhaAcAddrTLng" value="" readonly /><input type="text" name="txtMhaAcAddrTId" id="txtMhaAcAddrTId" value="" readonly /></td>
					<td valign="top" style="vertical-align:top;"><a href="javascript:void(0)" onclick="javascript:aclookup(\'T\');" class="button">Lookup</a></td></tr>';

			echo '<tr><th><label>Journey Map:</label></th>
					<td><div id="idMhaMap" width="100%" style="height:260px;"></div></td></tr>';
			// <td valign="top" style="vertical-align:top;"><a href="javascript:void(0)" onclick="javascript:acredraw();" class="button">Redraw</a></td></tr>';

			echo '</table><br/>';
			echo '<input type="button" value="&laquo; Back to Bookings" class="button" onclick="javascript:window.location.href=\''.$link_back.'\';" /> &nbsp;&nbsp;&nbsp;&nbsp; ';
			echo '<input type="submit" id="doavail" name="doavail" value="Send to CABE" class="button" disabled="disabled" />';
			//echo ' &nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" name="dosetref" value="Assign Test CABE Booking" class="button" onclick=\'javascript:return confirm("Assign to test booking?");\' />';
			
			//update_post_meta($postID, 'chauffeur_payment_pickup_via', "ST5".' >>> '."ST3");
		}
		else
		{
			// Booking made as we have a reference
			$result = self::cabeGetAndCacheBooking( $apiCabe, $postID, $bOutbound );
			self::get_cabe_status_details( $result, $strStatusText, $strStatusClass, $bInCabe );
			$strStatusText = '<b>'.$strStatusText.'</b>'.($bInCabe? ' &nbsp; <small>(in CABE)</small>' : '');
			
			echo '<table class="mhaacTable">';
			echo '<tr><td class="mhaacNA"></td><th>CABE Details</th><th>Chauffeur Details</th></td></tr>';
			echo '<tr><td><br/>Status<br/>&nbsp;</td><td align="center" valign="middle" class="'.$strStatusClass.'">'.$strStatusText.'</td><td align="center" valign="middle">Booked</td></tr>';
			echo '<tr><td>Contact Name</td><td class="mhaacNA">'.'</td><td>'.$strChaufName.'</td></tr>';
			echo '<tr><td>Contact Phone</td><td class="mhaacNA">'.'</td><td>'.$strChaufPhone.'</td></tr>';
			echo '<tr><td>Contact Email</td><td class="mhaacNA">'.'</td><td>'.$strChaufEmail.'</td></tr>';
			echo '<tr><td># of Passengers</td><td class="mhaacNA">'.'</td><td>'.$strChaufPassNum.'</td></tr>';
			echo '<tr><td># of Bags</td><td class="mhaacNA">'.'</td><td>'.$strChaufBagNum.'</td></tr>';
			echo '<tr><td>Vehicle Type</td><td class="mhaacNA">'.'</td><td>'.$strChaufVehicle.'</td></tr>';
			echo '<tr><td>Booking Ref</td><td>'.$result->BookingReference.'</td><td><a href="'.get_edit_post_link($postID).'" target="_blank">'.$postID.'</a></td></tr>';
			echo '<tr><td>From</td><td>'.$result->JourneyDetails->From->Data.'</td><td>'.$strChaufFrom.'</td></tr>';
			if (empty($result->JourneyDetails->Vias))
			{
				echo '<tr><td>Vias</td><td></td><td>'.$strChaufVias.'</td></tr>';
			}
			else
			{
				echo '<tr><td>Vias</td><td>'.count($result->JourneyDetails->Vias).'</td><td>'.strChaufVias.'</td></tr>';
			}
			echo '<tr><td>To</td><td>'.$result->JourneyDetails->To->Data.'</td><td>'.$strChaufTo.'</td></tr>';
			echo '<tr><td><small>Auth Ref</small></td><td><small>'.$cabe_auth_ref.'</small></td><td class="mhaacNA"></td></tr>';
			
			
			if ( 0 ) {
				if ( empty($result) ) {
					echo "<tr><td></td><td><small>Error:<pre>".$apiCabe->GetLastError()."</pre></small></td></tr>";
				} else {
					echo "<tr><td></td><td><small>Result:<pre>".print_r($result,1)."</pre></small></td></tr>";
				}
			}
			
			echo '</table><br/>';
			echo '<input type="button" value="&laquo; Back to Bookings" class="button" onclick="javascript:window.location.href=\''.$link_back.'\';" /> &nbsp;&nbsp;&nbsp;&nbsp; ';
			if ( "Cancelled" == $result->Status ) {
				echo '<input type="submit" name="doforget" value="Forget CABE Booking" class="button mhaacStatusBad" onclick=\'javascript:return confirm("Are you sure you wish to forget this booking?\n\nThe booking '.$result->BookingReference.' will still exist in CABE, but won&apos;t be associated with this Chauffeur booking.");\' />';
			} else {
				echo '<input type="submit" name="docancel" value="Cancel CABE Booking" class="button mhaacStatusBad" onclick="javascript:return confirm(\'Are you sure you wish to cancel this booking within CABE?\');" />';
			}
		}

		echo '</form>';
		
	}	
	
	public static function ajax_callback_analyse()
	{
		echo '<html><body><h1>Analysis</h1><pre>';
		
		try
		{
			$tz		=	self::GetTimeZone();
			$dtNow		=	new \DateTime("now", $tz);
			$tsNow		=	time();
			$dtIgnore	=	clone $dtNow;
			$dtIgnore->modify("-12 hours");			// KevFix

			$iTotalBookings	=	0;				
			$arrBookings	=	[];
			
			self::get_active_booking( $arrBookings, $dtIgnore, $iTotalBookings );
			self::DoLog( true, __METHOD__, __LINE__, "Ignored prior to time: ".$dtIgnore->format(self::DATEFMT_DATETIME_FRIENDLY).". Bookings: ".$iTotalBookings.". Active: ".count($arrBookings), 1);
			
			$apiCabe = self::cabeGetApi();
			
			$strStatusText = $strStatusClass = "";
			$bInCabe = false;
			
			$arrAttention = [];
			$arrNew = [];
			$iAttentionOutgoing = $iAttentionReturn = 0;
			$iAlertLevel	=	0;
			$iMustEmail 	= 0;
			
			foreach ( $arrBookings as $booking )
			{
				$postID = $booking->ID;
				
				if ( !empty($booking->dtWhenPickup) )
				{
					$dtWhen = $booking->dtWhenPickup;
					$bOutbound	=	true;
					$tsLastEmailed = intVal(get_post_meta($postID, self::cabeGetMetaKeyPrefix($bOutbound) . self::METADATA_BOOKING_DIR_LASTEMAIL, TRUE));
					$iSoon	=	self::GetTimeDistanceSeconds($booking->dtWhenPickup, $dtNow);
					self::DoLog( true, __METHOD__, __LINE__, "#".$postID." OUTBOUND @ ".$dtWhen->format(self::DATEFMT_DATETIME_FRIENDLY)." = ".intval($iSoon / self::SECS_PER_DAY)." days", 1 );
					$cabe_auth_ref = self::cabeGetBookingAuthRef( $postID, $bOutbound );
					if (empty($cabe_auth_ref)) {
						$bOK = false;
						$strStatusText = "Not booked";
						$bInCabe = false;
					} else {
						$result = self::cabeGetAndCacheBooking( $apiCabe, $postID, $bOutbound );
						$bOK = self::get_cabe_status_details( $result, $strStatusText, $strStatusClass, $bInCabe );
					}
					$status	=	$strStatusText.($bInCabe? " (in CABE)":"");
					$iEmailedAgo = $tsNow - $tsLastEmailed;
					$iMustEmail += self::GetBookingAlertLevel($iSoon, $iEmailedAgo, $iAlertLevel);
					self::DoLog( true, __METHOD__, __LINE__, "#".$postID." status = '".$status."', email ago=".$iEmailedAgo.", alert=".$iAlertLevel, 1 );
					if ( !$bOK )
					{
						$arrAttention[]	=	[ "direction"=>"Outbound", "dir"=>"out", "bdir"=>$bOutbound, "when"=>$booking->dtWhenPickup, "id"=>$postID, "status"=>$status, "alert"=>$iAlertLevel ];
						$iAttentionOutgoing++;
					}
					if ( !$tsLastEmailed )
					{
						$arrNew[]	=	[ "direction"=>"Outbound", "dir"=>"out", "bdir"=>$bOutbound, "when"=>$booking->dtWhenPickup, "id"=>$postID, "status"=>$status, "alert"=>$iAlertLevel ];
					}
				}
				if ( !empty($booking->dtWhenReturn) )
				{
					$dtWhen = $booking->dtWhenReturn;
					$bOutbound	=	false;
					$tsLastEmailed = intVal(get_post_meta($postID, self::cabeGetMetaKeyPrefix($bOutbound) . self::METADATA_BOOKING_DIR_LASTEMAIL, TRUE));
					$tsEmailedAgo = $tsNow - $tsLastEmailed;
					self::DoLog( true, __METHOD__, __LINE__, "#".$postID." RETURN @ ".$dtWhen->format(self::DATEFMT_DATETIME_FRIENDLY)." = ".intval($iSoon / self::SECS_PER_DAY)." days", 1 );
					$cabe_auth_ref = self::cabeGetBookingAuthRef( $postID, $bOutbound );
					if (empty($cabe_auth_ref)) {
						$bOK = false;
						$strStatusText = "Not booked";
						$bInCabe = false;
					} else {
						$result = self::cabeGetAndCacheBooking( $apiCabe, $postID, $bOutbound );
						$bOK = self::get_cabe_status_details( $result, $strStatusText, $strStatusClass, $bInCabe );
					}
					$status	=	$strStatusText.($bInCabe? " (in CABE)":"");
					$iEmailedAgo = $tsNow - $tsLastEmailed;
					$iMustEmail += self::GetBookingAlertLevel($iSoon, $iEmailedAgo, $iAlertLevel);
					self::DoLog( true, __METHOD__, __LINE__, "#".$postID." status = '".$status."', email ago=".$iEmailedAgo.", alert=".$iAlertLevel, 1 );
					if ( !$bOK )
					{
						$arrAttention[]	=	[ "direction"=>"Return", "dir"=>"ret", "bdir"=>$bOutbound, "when"=>$booking->dtWhenPickup, "id"=>$postID, "status"=>$status, "alert"=>$iAlertLevel ];
						$iAttentionReturn++;
					}
					if ( !$tsLastEmailed )
					{
						$arrNew[]	=	[ "direction"=>"Return", "dir"=>"ret", "bdir"=>$bOutbound, "when"=>$booking->dtWhenPickup, "id"=>$postID, "status"=>$status, "alert"=>$iAlertLevel ];
					}
				}
			}
			
			$bForceEmail	=	isset($_GET[self::URLPARAM_FORCE_EMAIL]) ? true : false;
			if ( $bForceEmail )
			{
				self::DoLog( true, __METHOD__, __LINE__, "Analysis called with 'force email' flag...", 1 );
				delete_transient( self::PREFIX . 'analysis_at' );
			}
			echo '</pre><h2>Summary</h2><pre>';
			
			$iAttentionNew	=	count($arrNew);
			self::DoLog( true, __METHOD__, __LINE__, "There are ".$iAttentionOutgoing." outgoing bookings and ".$iAttentionReturn." return bookings requiring attention.", 1 );
			self::DoLog( true, __METHOD__, __LINE__, "There are ".$iMustEmail." attentions needing to be emailed.", 1 );
			self::DoLog( true, __METHOD__, __LINE__, "There are ".$iAttentionNew." newly placed journey bookings since the last email.", 1 );

			if (( $iMustEmail > 0 ) || ( $iAttentionNew > 0 ) || $bForceEmail )
			{
				$config = self::GetConfig( "ADMIN" );
/*				if ( (0 == $iAttentionNew) && (intVal(get_transient( self::PREFIX . 'analysis_at' )) + $config['EMAIL.ANALYSIS.FREQUENCY'] > time() ) )
				{
					self::DoLog( true, __METHOD__, __LINE__, "Skipping admin email (too soon)...", 1 );
				}
				else*/
				{
					self::DoLog( true, __METHOD__, __LINE__, "Sending admin email...", 1 );
					
					$body = '<html><body style="background-color:#ffffff; color:#000000;">
	<p>Hi Admins,</p>';
					if ( self::TESTING )
					{
						$body .= '<span style="color:red;"><b>*** THIS IS AN EARLY PREVIEW OF THE EMAIL NOTICES FROM '.home_url().' ***</b></span>'."\n";
					}
	
					if ( $bForceEmail )
					{
						$body .= '<p>This email was requested generated as analysis was requested with the &apos;force email&apos; flag.</p>';
					}
					$body .= '
	<p><b>There are '.$iAttentionOutgoing.' outgoing bookings and '.$iAttentionReturn.' return bookings requiring attention.</b></p>
	<p><b>There are '.$iAttentionNew.' newly placed journey bookings since the last email.</b></p>
	<p>These are the results from analysis performed at '.$dtNow->format(self::DATEFMT_DATETIME_FRIENDLY).'</p>
';
					$i = 0;
					if ( count($arrAttention) )
					{
						$body .= '	<h3>Attention Journeys</h3>
	<table cellpadding="3" cellspacing="0" border="1">
		<tr><th>#</th><th>Direction</th><th>When</th><th>Soon</th><th>Status</th><th>Booking Link</th><th>Status Link</th></tr>
';
						foreach ( $arrAttention as $item )
						{
							$i++;
							$postID	=	$item["id"];
							$link1	=	get_edit_post_link($postID);
							$link2	=	admin_url( 'admin.php' ).'?page='.self::UNIQUE2_CABE.'&id='.$postID.'&dir='.$item['dir'];
							$style	=	'style="'.self::GetBookingAlertStyle($item['alert']).'"';
							$body .= '		<tr><td>'.$postID.'-'.substr($item['direction'],0,1).'</td><td>'.$item['direction'].'</td><td '.$style.'>'.$item['when']->format(self::DATEFMT_DATETIME_FRIENDLY).'</td><td '.$style.'>'.self::GetTimeDistance($item['when'], $dtNow).'</td><td>'.$item['status'].'</td><td><a href="'.$link1.'" target="_blank"/>View Booking #'.$postID.'...</a></td><td><a href="'.$link2.'" target="_blank"/>View Status...</a></td></tr>'."\n";
						}
						$body .= '	</table>'."\n";
					}
					
					$i = 0;
					if ( count($arrNew) )
					{
						$body .= '	<h3>New Journey Bookings</h3>
	<table cellpadding="3" cellspacing="0" border="1">
		<tr><th>#</th><th>Direction</th><th>When</th><th>Soon</th><th>Status</th><th>Booking Link</th><th>Status Link</th></tr>
';
						foreach ( $arrNew as $item )
						{
							$i++;
							$postID	=	$item["id"];
							$link1	=	get_edit_post_link($postID);
							$link2	=	admin_url( 'admin.php' ).'?page='.self::UNIQUE2_CABE.'&id='.$postID.'&dir='.$item['dir'];
							$style	=	'style="'.self::GetBookingAlertStyle($item['alert']).'"';
							$body .= '		<tr><td>'.$postID.'-'.substr($item['direction'],0,1).'</td><td>'.$item['direction'].'</td><td '.$style.'>'.$item['when']->format(self::DATEFMT_DATETIME_FRIENDLY).'</td><td '.$style.'>'.self::GetTimeDistance($item['when'], $dtNow).'</td><td>'.$item['status'].'</td><td><a href="'.$link1.'" target="_blank"/>View Booking #'.$postID.'...</a></td><td><a href="'.$link2.'" target="_blank"/>View Status...</a></td></tr>'."\n";
						}
						$body .= '	</table>'."\n";
					}
					$body .= self::$ms_strHTMLEmailFooter.'</body></html>';
					
					$arrHeaders	=	array();
					$strTo	=	$config["EMAIL.ANALYSIS.TO"];
					if ( self::TESTING )
					{
						$arrHeaders[]	=	"Cc: kevin@mediaheads.co.uk";
					}
					
					$bSent	=	false;
					if ( 1 )
					{
						//add_filter( 'wp_mail_from_name', 'autocab_chauffeur_set_mail_from_name' );
						//add_filter( 'wp_mail_from', 'autocab_chauffeur_set_mail_from' );
						add_filter( 'wp_mail_content_type', 'autocab_chauffeur_set_html_mail_content_type' );
						if ( wp_mail( $strTo, self::TITLE." Activity Report", $body, $arrHeaders ) )
						{
							self::DoLog( true, __METHOD__, __LINE__, "Admin email sent successfully.", 1 );
							$bSent	=	true;
						}
						else
						{
							self::DoLog( true, __METHOD__, __LINE__, "FAILED to send admin email.", 1 );
							self::DoLog( true, __METHOD__, __LINE__, "to=".$strTo." , body=\n".$body );
						}
						remove_filter( 'wp_mail_content_type', 'autocab_chauffeur_set_html_mail_content_type' );
						//remove_filter( 'wp_mail_from', 'autocab_chauffeur_set_mail_from' );
						//remove_filter( 'wp_mail_from_name', 'autocab_chauffeur_set_mail_from_name' );
					}
					else
					{
						echo "<br/><hr/></pre>".$body."<pre><hr/>";
//						$bSent	=	true;
					}
					
					if ( $bSent )
					{
						// Mark that analysis was successfully processed on this run.
						self::DoLog( true, __METHOD__, __LINE__, "Updating bookings was successfully processed.", 1 );
						set_transient( self::PREFIX . 'analysis_at', $tsNow );
						$arrAll	=	array_merge( $arrAttention, $arrNew );
						foreach ( $arrAll as $item )
						{
							self::DoLog( true, __METHOD__, __LINE__, "Marking as processed #".$item["id"]."-".$item["dir"] );
							update_post_meta($item["id"], self::cabeGetMetaKeyPrefix($item["bdir"]) . self::METADATA_BOOKING_DIR_LASTEMAIL, $tsNow);
//TEST							delete_post_meta($item["id"], self::cabeGetMetaKeyPrefix($item["bdir"]) . self::METADATA_BOOKING_DIR_LASTEMAIL);
						}
					}
					
				}
			}
			else
			{
				self::DoLog( true, __METHOD__, __LINE__, "No admin email required.", 1 );
			}
		}
		catch (Exception $e)
		{
			print_r($e);
		}
	}
	
	public static function ajax_callback()
	{
		global $wpdb;
		
		$action		=	htmlentities($_GET["action"]);
		$bAccept	=	false;
		switch ( $action )
		{
			case self::PREFIX . self::AJAX_ACTION_ANALYSE:
			{
				self::DoLog( true, __METHOD__, __LINE__, ">>> ".$action." - Analysis" );
				$bAccept	=	true;
				self::ajax_callback_analyse();
				break;
			}
		}
		if ($bAccept)
		{
			self::DoLog( true, __METHOD__, __LINE__, "<<< ".$action );
			echo '</pre><br/>end.</body></html>';
		}
	}	
	
}

// 
// ---------------- Wrappers ----------------
//
function autocab_chauffeur_display_install( $atts )
{
/*
	global $wpdb;
	
	$table_name_appts = $wpdb->prefix . CAutocabChauffeur::PREFIX . CAutocabChauffeur::TABLE_APPOINTMENT;
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "
		CREATE TABLE $table_name_appts (
			appointment_id bigint NOT NULL AUTO_INCREMENT,
			appointment_created_when datetime NOT NULL,
			UNIQUE KEY job_id (job_id)
		) $charset_collate;
	";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	add_option( CAutocabChauffeur::PREFIX . "db_version" );
*/
}


function autocab_chauffeur_display_shortcode( $atts )
{
	return	CAutocabChauffeur::display_shortcode( $atts );
}

function autocab_chauffeur_display_admin()
{
	if ( !current_user_can( 'manage_options' ) )
	{
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	CAutocabChauffeur::display_admin();
}
function autocab_chauffeur_display_cabe()
{
	if ( !current_user_can( 'manage_options' ) )
	{
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	CAutocabChauffeur::display_cabe();
}



function autocab_chauffeur_plugin_action_links( $links, $file )
{
	if ( plugin_basename(__FILE__) == $file )
	{
		$settings_link = '<a href="'.menu_page_url( CAutocabChauffeur::UNIQUE1_ADMIN, false ).'">Settings</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}

function autocab_chauffeur_ajax_callback()
{
	CAutocabChauffeur::ajax_callback();
	wp_die(); // this is required to terminate immediately and return a proper response
}

function autocab_chauffeur_set_html_mail_content_type()
{
    return 'text/html';
}
function autocab_chauffeur_set_mail_from_name()
{
	return CAutocabChauffeur::TITLE;
}
function autocab_chauffeur_set_mail_from()
{
	global $g_strAutocabServerFile;
	return "support@".$g_strAutocabServerFile;
}


//
// ---------------- Registration ----------------
//
register_activation_hook( __FILE__, 'autocab_chauffeur_display_install' );
add_shortcode( CAutocabChauffeur::SHORTCODE, "autocab_chauffeur_display_shortcode" );
add_action( "admin_menu", function()
{
	//add_options_page( CAutocabChauffeur::TITLE." Settings", CAutocabChauffeur::TITLE, "manage_options", CAutocabChauffeur::UNIQUE1_ADMIN, "autocab_chauffeur_display_admin" );
	add_menu_page( CAutocabChauffeur::TITLE, CAutocabChauffeur::TITLE, "administrator", CAutocabChauffeur::UNIQUE1_ADMIN, "autocab_chauffeur_display_admin", "dashicons-controls-repeat", 2 );
	add_submenu_page( null, CAutocabChauffeur::TITLE." CABe", CAutocabChauffeur::TITLE." CABe","administrator", CAutocabChauffeur::UNIQUE2_CABE, "autocab_chauffeur_display_cabe" );
});

add_filter( 'plugin_action_links', 'autocab_chauffeur_plugin_action_links', 10, 2 );

// Admin and non-admin
add_action( 'wp_ajax_'.CAutocabChauffeur::PREFIX . CAutocabChauffeur::AJAX_ACTION_ANALYSE, 'autocab_chauffeur_ajax_callback' );
add_action( 'wp_ajax_nopriv_'.CAutocabChauffeur::PREFIX . CAutocabChauffeur::AJAX_ACTION_ANALYSE, 'autocab_chauffeur_ajax_callback' );

//---------------------------------------------------------------------------

CAutocabChauffeur::InitialiseStatics();


class CAutocabChauffeurBookingSorter
{
	public $ID = 0;
	public $dtEarliestOfInterest = null;
	public $dtWhenPickup = null;
	public $dtWhenReturn = null;
	public $dtLastDate = null;

	function __construct($_ID, $_dtEarliestOfInterest, $_dtWhenPickup, $_dtWhenReturn, $_dtLastDate)
	{
		$this->ID	=	$_ID;
		$this->dtEarliestOfInterest	=	$_dtEarliestOfInterest;
		$this->dtWhenPickup	=	$_dtWhenPickup;
		$this->dtWhenReturn	=	$_dtWhenReturn;
		$this->dtLastDate	=	$_dtLastDate;
	}
	
	static function cmp($a, $b)
	{
		return ($a->dtEarliestOfInterest > $b->dtEarliestOfInterest);
	}
}