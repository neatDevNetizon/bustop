<?php
$connect=mysqli_connect("localhost", "root", "","d26893_busstops");
if (mysqli_connect_errno())
  {
	header("Location: database_error.php");
  }

$router = $_GET['id'];
switch ($router) {
	case 'getnearby':
		$lat = $_POST['lat'];
		$lon = $_POST['lon'];
		$distance = $_POST['dis'];
		listNearBy($lat, $lon, $distance);
		break;

	case 'getstation':
		$area = $_POST['area'];
		listStation($area);
		break;
		
	case 'getarea':
		listArea();
		break;
	case 'getbuslist':
		$station = $_POST['station'];
		listBuses($station);
		break;

}

function listArea() {
	global $connect;
	$query = "SELECT stop_area FROM stops GROUP BY stop_area";
	$result = mysqli_query($connect, $query);
	$numrows =mysqli_num_rows($result);
	if(!$result) {echo "NO"; return;}
	for($count = 0; $count < $numrows; $count++){
		$ac = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$data[] = $ac;
	}
	print_r(json_encode($data)) ;
	return ;
}

function listNearBy($lat, $long, $dis) {
	global $connect;
	$plusX = $dis*3280/(60*6075);
	$highLat = $plusX+$lat;
	$lowLat = $lat-$plusX;
	$plusY = $dis/(cos($lat*pi()/180)*111.321543);
	$highLong = $long+$plusY;
	$lowLong = $long-$plusY;
	$query = "SELECT stop_area  FROM stops WHERE stop_lat BETWEEN {$lowLat} AND {$highLat} AND stop_lon BETWEEN {$lowLong} AND {$highLong} GROUP BY stop_area";
	$result = mysqli_query($connect, $query);
	$numrows =mysqli_num_rows($result);
	if(!$result) {echo "NO"; return;}
	for($count = 0; $count < $numrows; $count++){
		$ac = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$data[] = $ac;
	}
	print_r(json_encode($data)) ;
	return ;	
}

function listStation($area) {
	global $connect;
	$query = "SELECT stop_name FROM stops WHERE stop_area='{$area}' GROUP BY stop_name";
	$result = mysqli_query($connect, $query);
	$numrows =mysqli_num_rows($result);
	if(!$result) {echo "NO"; return;}
	for($count = 0; $count < $numrows; $count++){
		$ac = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$data[] = $ac;
	}
	print_r(json_encode($data)) ;
	return ;	
}

function listBuses($station) {
	global $connect;
	$query = "SELECT * FROM routes INNER JOIN (SELECT route_id FROM trips INNER JOIN (SELECT trip_id FROM stop_times  INNER JOIN stops ON stops.stop_id=stop_times.stop_id WHERE stops.stop_name='{$station}') AS tt1 ON trips.trip_id=tt1.trip_id) AS tt2 ON tt2.route_id=routes.route_id GROUP BY route_short_name ORDER BY route_short_name ASC";
	$result = mysqli_query($connect, $query);
	$numrows =mysqli_num_rows($result);
	if(!$result) {echo "NO"; return;}
	for($count = 0; $count < $numrows; $count++){
		$ac = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$data[] = $ac;
	}
	print_r(json_encode($data));
	return ;
}

?>