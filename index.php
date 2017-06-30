<?php

$csv = file_get_contents('input.csv');
$explode = explode(PHP_EOL, $csv);
$Address = [];
foreach ($explode as $key => $row) {
    $Address[$key] = "";
    $row = str_replace(" ", "", $row);
    $rowExplode = array_filter(explode(",", $row), 'strlen');
    foreach ($rowExplode as $k => $rs){
        if($k>0){
            $Address[$key] .= $rs . "+";
        }
    }
}

function CallAPI($data)
{
    $KEY = "";//Google API Key
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $data . "&key=" . $KEY;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = json_decode(curl_exec($curl));
    curl_close($curl);
    if (isset($result->results[0]->geometry->location->lat)) {
        $lat = $result->results[0]->geometry->location->lat;
        $lng = $result->results[0]->geometry->location->lng;
        $status = true;
    } else {
        $status = false;
        $lat = null;
        $lng = null;
    }
    return [
        "status" => $status,
        "lat" => $lat,
        "lng" => $lng
    ];
}

$file = fopen("contacts.csv","w");
fwrite($file,"No,Latitude,Longtitude\n");
for ($i = 0; $i < sizeof($Address); $i++) {
    $result = CallAPI($Address[$i]);
//    fwrite($file,"\n-------------".$i."----------------");
    if ($result["status"]) {
        fwrite($file,$i.",".$result["lat"].",".$result["lng"]."\n");
//        fwrite($file,"\nLatitude :" . $result["lng"]);
    } else {
//        fwrite($file,"\nnotfound");
        fwrite($file,$i.",".$result["lat"].",".$result["lng"]."\n");
    }
}
fclose($file); ?>