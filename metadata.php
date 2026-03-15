<?php

$stream = "http://streaming.live365.com/a82574";

$opts = array(
 "http" => array(
  "method" => "GET",
  "header" => "Icy-MetaData: 1\r\n"
 )
);

$context = stream_context_create($opts);
$fp = fopen($stream, "r", false, $context);

$metaInterval = 0;

foreach ($http_response_header as $header) {
 if (stripos($header, "icy-metaint") !== false) {
  $metaInterval = (int)substr($header,15);
 }
}

if ($metaInterval) {

 fread($fp,$metaInterval);
 $length = ord(fread($fp,1))*16;
 $metadata = fread($fp,$length);

 if (preg_match("/StreamTitle='([^']*)'/",$metadata,$matches)) {
  $song = $matches[1];
 } else {
  $song = "";
 }

} else {
 $song = "";
}

fclose($fp);

$artist="";
$title=$song;

if(strpos($song," - ")!==false){
 $parts=explode(" - ",$song);
 $artist=$parts[0];
 $title=$parts[1];
}

header("Content-Type: application/json");

echo json_encode([
 "artist"=>$artist,
 "title"=>$title
]);

?>
