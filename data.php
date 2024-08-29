<?php
// Include the connect.php file
include ('connect.php');



$url = parse_url($_SERVER["HTTP_REFERER"] ?? '', PHP_URL_QUERY);  


//if parent window var isn't set, get the iframe window var
if (!empty($_GET['pub'])) {
	$xhw = $_GET['pub'];
//get the iframe parent window url, and use the ?var= if it is there
} elseif ( strpos($url,"=") ){
	parse_str($url);
	$xhw = $hw;
	//echo  "<h4>else if parent is set " . $xhw . "</h4>\n";
} else {
	$xhw = "OFR-686dm";
}
//echo "<br>stuff here: ".$xhw."<br>";


// Connect to the database
$database = "publications";
$mysqli = new mysqli($hostname, $username, $password, $database);
/* check connection */
if (mysqli_connect_errno())
	{
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}


$where = " WHERE series_id='".$xhw."'";







	// PRELIMINARY SQL REQUEST. FROM ATTACHED-DATA TABLE
	

	//$query = "SELECT series_id, pub_name, pub_year, pub_author, pub_sec_author, pub_url, pub_scale, pub_thumb, pubPrevLink, bookstore_url, full_citation, pubPrevLink FROM UGSpubs" . $where;
	$query = "SELECT series_id, pub_year, pub_name, pub_author, pub_sec_author, pub_url, pub_scale, notes, keywords, bookstore_url, servName, cam_offset, lat, longitude, popupFL, pubPrevLink, pubPrevLink2, pubPrevLink3, series, pub_thumb, ISBN, series, pages, full_citation, related_pubs FROM UGSpubs" . $where;
	//$query3 = "SELECT series_id, pub_year, pub_name, pub_author, pub_sec_author, pub_url, pub_scale, notes, keywords, bookstore_url, servName, cam_offset, lat, longitude, popupFL, pubPrevLink, pubPrevLink2, pubPrevLink3, series FROM UGSpubs" . $where; //I'm not sure this line does anything regardless of being commented or not
	$result = $mysqli->prepare($query);
	$result->execute();
	//$result->bind_result($series_id, $pub_title, $pub_year, $pub_author, $pub_sec_author, $pub_url, $pub_scale, $pub_thumb, $pub_preview, $bookstore_url, $full_citation, $pubPrevLink);
	$result->bind_result($SeriesID, $PubYear, $PubName, $PubAuthor, $PubSecAuthor, $PubURL, $PubScale, $PubNotes, $KeyWords, $BookstoreURL, $ServiceName, $CameraOffset, $Latitude, $Longitude, $PopupFeatureLayer, $PubPrevLink, $pubPrevLink2, $pubPrevLink3, $Series, $pub_thumb, $PubIsbn, $PubSeries, $PubPages, $full_citation, $related_pubs);


	// loop through result and store into temporary array
	while ($result->fetch()) {

		


			//add comma between primary and secondary authors if any
			if ( empty($PubSecAuthor) ||  is_null($PubSecAuthor) || $PubSecAuthor === null || $PubSecAuthor === 'undefined' || $PubSecAuthor === ' ' ) {
					$PubSecAuthorString = " ";
					//echo "trouble at: ".$SeriesID."AHH!  ";
			} else {
					$PubSecAuthorString = ', '. $PubSecAuthor;
			};

			//add download urls if any
			/*if ( empty($PubURL) ||  is_null($PubURL) || $PubURL === null || $PubURL === 'undefined' || $PubURL === ' ' ) {
					$PubURLString = $string;
					//echo "trouble at: ".$SeriesID."AHH!  ";
			} else {
					//$PubURLString = " <a href='".$PubURL."' target='_blank' download>Report</a>" . $string;
			};*/


			//add bookstore purchase urls if any
			if ( empty($BookstoreURL) ||  is_null($BookstoreURL) || $BookstoreURL === null || $BookstoreURL === 'undefined' || $BookstoreURL === ' ' ) {
					$BookstoreURLString = "<something></something>";
			} else {
					$BookstoreURLString = " - <strong><a href='https://utahmapstore.com/products/".$SeriesID."' target='_blank'>Purchase Hard Copy</a></strong>";
			};

			//add Image Service preview map url if any
			/*if ($ServiceName == 'MD_24K') {
					$PreviewMapURL = " <a target='_blank' href='map.html?servName=".$ServiceName."&mobLat=".$Latitude."&lat=".$CameraOffset."&long=".$Longitude."' target='_blank'>Preview Map</a>";
			} else {
					$PreviewMapURL = "<something></something>";
			};*/

			//add Vector Service or Image Service preview map url if any
			/*if ($ServiceName == '30x60_Quads' || $ServiceName == '7_5_Quads' || $ServiceName == 'Other_Quads' || $ServiceName == 'FigureMaps') {
					$PreviewMapURL = " <a target='_blank' href='map.html?servName=".$ServiceName."&mobLat=".$Latitude."&popupFL=".$PopupFeatureLayer."&lat=".$CameraOffset."&long=".$Longitude."&seriesID=".$SeriesID."&xsection=".$xsection."&lithcolumn=".$lithcolumn."'>Map</a>";
			} else if ($ServiceName == 'MD_24K' && $PopupFeatureLayer != null){
					$PreviewMapURL = " <a target='_blank' href='map.html?servName=".$ServiceName."&mobLat=".$Latitude."&popupFL=".$PopupFeatureLayer."&lat=".$CameraOffset."&long=".$Longitude."&seriesID=".$SeriesID."&xsection=".$xsection."&lithcolumn=".$lithcolumn."'>Map</a>";
			} else if ($ServiceName == 'MD_24K'){
					$PreviewMapURL = " <a target='_blank' href='map.html?servName=".$ServiceName."&mobLat=".$Latitude."&lat=".$CameraOffset."&long=".$Longitude."&seriesID=".$SeriesID."' target='_blank'>Map</a>";
			} else {
					$PreviewMapURL = " ";
			};*/
			
			//add Vector Service or Image Service preview map url if any
			if ($ServiceName == '30x60_Quads' || $ServiceName == 'Other_Quads' || $ServiceName == 'FigureMaps') {
				$PreviewMapURL = " <a href='https://geology.utah.gov/apps/intgeomap/index.html?sid=".$SeriesID."&layers=100k' target='_blank'>Interactive Map</a>";
			} else if ($ServiceName == '7_5_Quads' || $ServiceName == 'MD_24K') {
				$PreviewMapURL = " <a href='https://geology.utah.gov/apps/intgeomap/index.html?sid=".$SeriesID."&layers=24k' target='_blank'>Interactive Map</a>";
			} else if ($ServiceName == '500k_Statewide') {
				$PreviewMapURL = " <a href='https://geology.utah.gov/apps/intgeomap/index.html?sid=".$SeriesID."&layers=500k' target='_blank'>Interactive Map</a>";
			} else {
				$PreviewMapURL = "";
			};


			if ($PubPrevLink != null && $PubURL != null && preg_match("/iPhone|iPad|iPod|webOS/", $_SERVER['HTTP_USER_AGENT'])) {
				$PubPrevLinkURL = " <a target='_blank' href='".$PubPrevLink."'>Publication</a>";
			} else if ($pubPrevLink3 != null){
				$PubPrevLinkURL = " <a target='_blank' href='".$PubPrevLink."'>Booklet</a><br> <a target='_blank' href='".$pubPrevLink2."'>Plate 2</a><br><a target='_blank' href='".$pubPrevLink3."'>Plate 3</a>";
			} else if ($pubPrevLink2 != null && $pubPrevLink2 != "https://geology.utah.gov/apps/hazards"){
				$PubPrevLinkURL = " <a target='_blank' href='".$PubPrevLink."'>Booklet</a><br> <a target='_blank' href='".$pubPrevLink2."'>Plate 2</a>";
			} else if ($pubPrevLink2 == "https://geology.utah.gov/apps/hazards"){
				$PubPrevLinkURL = " <a target='_blank' href='".$PubPrevLink."'>Booklet</a><br> <a target='_blank' href='".$pubPrevLink2."'>Geologic Hazards Portal</a>";
			} else if ($PubPrevLink != null && $PubURL != null){
				$PubPrevLinkURL = " <a target='_blank' href='".$PubPrevLink."'>Publication</a>";
			} else if ($PubPrevLink == null && $PubURL != null && preg_match("/iPhone|iPad|iPod|webOS/", $_SERVER['HTTP_USER_AGENT'])) {
				$PubPrevLinkURL = " <a href='".$PubURL."' target='_blank'>Publication</a>";
			} else if ($PubPrevLink == null && $PubURL != null){
				$PubPrevLinkURL = " <a target='_blank' href='".$PubURL."'>Publication</a>";
			} else if ($PubPrevLink == null && $PubURL == null) {
				$PubPrevLinkURL = "null";
			};


		// ASIGN SQL DATA TO PHP VARIABLES AND PUT IN ARRAY TO SEND TO HTML PAGE
		$alldata[0] = array(
			'series_id' => $SeriesID,
			//'pub_string' => $PubName . $PreviewMapURL . $PubURLString . $BookstoreURLString . $PubPrevLinkURL,
			'pub_year' => $PubYear,
			'pub_name' => $PubName,
			'pub_author' => $PubAuthor . $PubSecAuthorString,
			'pub_scale' => $PubScale,
		    'notes' => $PubNotes,
			'keywords' => $KeyWords,
			'series' => $Series,
			'pub_thumb' => $pub_thumb,
			'pub_name' => $PubName,
			'bookstore_url' => $BookstoreURL,
			'pdf' => $PubURL,
			'isbn' => $PubIsbn,
			'series' => $PubSeries,
			'pages' => $PubPages,
			'full_citation' => $full_citation,
			'related_pubs' => $related_pubs
			
			
			/*,
			'sLayer' => $ServiceLayer,
			'servName' => $ServiceName,
			'cam_offset' => $Latitude,
			'long' => $Longitude,
			'popupFL' => $PopupFeatureLayer*/
		);

		$alldata['downloads']['Report'] = $PubURL;


		$alldata['preview'] = array(
			'0' => $PubPrevLinkURL,
			'preview_map' => $PreviewMapURL
		);


		/*
		$urls[] = array(
			'series_id' => $series_id,
			'pub_name' => $pub_title,
			'pub_year' => $pub_year,
			'pub_author' => $PubAuthor . $PubSecAuthorString,
			'pub_url' => $pub_url,
			'pub_scale' => $pub_scale,
			'pub_thumb' => $pub_thumb,
			'pubPrevLink' => $pub_preview,
			'bookstore_url' => $bookstore_url,
			'full_citation' => $full_citation,
			'pubPrevLink' => $pubPrevLink	// add pubPrevLink2 & pubPrevLink3 here, then split them later in js
		);
		*/

	}


	
// PRELIMINARY SQL REQUEST. FROM ATTACHED-DATA TABLE
$query2 = "SELECT series_id, extra_data, pub_url FROM AttachedData WHERE series_id='".$xhw."'";
$result2 = $mysqli->prepare($query2);
$result2->execute();
$result2->bind_result($series_id, $extra_data, $url2);


// loop through result and store into temporary array
while ($result2->fetch()) {
	if ((strpos($url2, 'http') !== false)){
		$column = $extra_data;  // since the name of the extra_data type changes, we'll make it the name of the key pair
		$alldata['downloads'][$column] = $url2;
	} else {
		$column = $extra_data;  // since the name of the extra_data type changes, we'll make it the name of the key pair
		$alldata['downloads'][$column] = "https://ugspub.nr.utah.gov/publications/" . $url2;
	}	
}
echo json_encode($alldata);
//print "<pre>";
//print_r($alldata);
//print "</pre>";
$result2->close();



/* close statement */
$result->close();
/* close connection */
$mysqli->close();
?>
