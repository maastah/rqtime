<!DOCTYPE html>
<html>
<body>
<?php
// include 
include("simple_html_dom.php"); //for crawler

// variables and arrays
$crawled_urls=array();
$found_urls=array();
$surl=array();


// START crawler (thanks to http://subinsb.com/how-to-create-a-simple-web-crawler-in-php)
function rel2abs($rel, $base){
 if (parse_url($rel, PHP_URL_SCHEME) != ''){
  return $rel;
 }
 if ($rel[0]=='#' || $rel[0]=='?'){
  return $base.$rel;
 }
 extract(parse_url($base));
 $path = preg_replace('#/[^/]*$#', '', $path);
 if ($rel[0] == '/'){
  $path = '';
 }
 $abs = "$host$path/$rel";
 $re = array('#(/.?/)#', '#/(?!..)[^/]+/../#');
 for($n=1; $n>0;$abs=preg_replace($re,'/', $abs,-1,$n)){}
 $abs=str_replace("../","",$abs);
 return $scheme.'://'.$abs;
}

function perfect_url($u,$b){
 $bp=parse_url($b);
 if(($bp['path']!="/" && $bp['path']!="") || $bp['path']==''){
  if($bp['scheme']==""){
   $scheme="http";
  }else{
   $scheme=$bp['scheme'];
  }
  $b=$scheme."://".$bp['host']."/";
 }
 if(substr($u,0,2)=="//"){
  $u="http:".$u;
 }
 if(substr($u,0,4)!="http"){
  $u=rel2abs($u,$b);
 }
 return $u;
}
$aurl=array();
function crawl_site($u){
 global $crawled_urls, $found_urls, $user_url, $actdata, $surl, $aurl;
 $uen=urlencode($u);
 if((array_key_exists($uen,$crawled_urls)==0 || $crawled_urls[$uen] < date("YmdHis",strtotime('-25 seconds', time())))){
  $html = file_get_html($u);
  $crawled_urls[$uen]=date("YmdHis");
  foreach($html->find("a") as $li){
   $url=perfect_url($li->href,$u);
   $enurl=urlencode($url);
   if($url!='' && substr($url,0,4)!="mail" && substr($url,0,4)!="java" && array_key_exists($enurl,$found_urls)==0){
    $found_urls[$enurl]=1;
	$aurl[] = $url;
   }
   
  }
 }
 return $aurl;
}
// END crawler

// DB input

include 'dbconnect.php'; //for db con

$con = new mysqli('localhost', $user, $pass, $dbname) or die("Unable to connect");

// table sites
$sql0 = "INSERT INTO sites (url) VALUES ('$user_url')";

if ($con->query($sql0) === TRUE) {
    //echo "Update sites successfully";
} else {
    echo "Error: " . $sql0 . "<br>" . $con->error;
}

// get id of actual $user_url

$idn;
$sqlidtofor = "SELECT id FROM sites WHERE url = '$user_url'";
$takeurlid = $con->query($sqlidtofor);
if ($takeurlid->num_rows > 0) {
	
	while($row = $takeurlid->fetch_assoc()) {
		$idn = $row["id"];
		
		}
} else {
		echo "0 results";
	}



// table site_urls

$surl = crawl_site($user_url);
	
// Curl
for($i=0;$i<count($surl);++$i) {
	$actualurl = $surl[$i];
	
	

	


	$ch = curl_init($actualurl); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	
	
	if(curl_exec($ch))
	{
	$info = curl_getinfo($ch);
	$rt = $info['total_time'];
	}
	
    curl_close($ch);
	
	
	$sql1 = "INSERT INTO site_urls (url, site_id, response_time) VALUES ('$actualurl', '$idn', '$rt') ON DUPLICATE KEY UPDATE url = '$actualurl', site_id = '$idn', response_time = '$rt'";
	if ($con->query($sql1) === TRUE) {
    //echo "Update site_urls successfully<br/>";
} else {
    echo "Error: " . $sql1 . "<br>" . $con->error;
}
}

// variables and arrays
$grapha = array();
$graphb = array();
$graphc = array();
$histl = array();
$histd = array();

if ($con->connect_error) {
	die ("Connection failder: " . $con->connect_error);
}

$sqlshow = "SELECT url, response_time FROM site_urls WHERE site_id = '$idn' ORDER BY response_time ASC";
$sqlhist = "SELECT url, execution_time FROM sites";

$result = $con->query($sqlshow);

if ($result->num_rows > 0) {
	
	while($row = $result->fetch_assoc()) {
		//echo "url: " . $row["url"]. " - Request time: " . $row["response_time"] . "<br />";
		
		array_push($grapha, $row["url"]);
		array_push($graphb, $row["response_time"]);
		
		}
} else {
		echo "0 results";
	}

	
$resulthist = $con ->query($sqlhist);
if ($resulthist->num_rows > 0) {
	
	while($row = $resulthist->fetch_assoc()) {
		array_push($histl, $row["url"]);
		array_push($histd, $row["execution_time"]);
		}
} else {
		echo "0 results";
	}

$con->close();
// DB con close

$aa = $grapha;
$ab = $graphb;
$ht = array_combine($aa, $ab);
$ah = array_combine($histl, $histd); // use in html table


asort($ht); //low-high sort of rqtime to html table

// HTML table
$html_table = '<table border="1 cellspacing="10" cellpadding="10""><tr><th>url:</th><th>request time</th></tr><tr>';
$nr_cols = 1; 
$n = 0; 
foreach($ht as $key => $val){
	$html_table .= '<td>' .$key. '</td>'. '<td>' . $val. '</td>'; 
	$n++;
	
	$col_to_add = $n % $nr_cols;
    if($col_to_add == 0) { $html_table .= '</tr><tr>'; }
}
	
	if($col_to_add != 0) $html_table .= '<td colspan="'. ($nr_cols - $col_to_add). '">&nbsp;</td>';

	$html_table .= '</tr></table>';         // ends the last row, and the table
	// delete posible empty row (<tr></tr>) which cand be created after last column
	$html_table = str_replace('<tr></tr>', '', $html_table);

// END HTML table

// Arrays to json (for charts)
$jsarraya = json_encode($aa);
$jsarrayb = json_encode($ab);




?>

 
</body>
</html>