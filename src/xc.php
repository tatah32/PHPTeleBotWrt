<?php
function seeURL($url){
    $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
}

function MyXL($number){
    $data = seeURL("http://beta-test.cloudaccess.host/cek.php?nomor=$number");
    return $data;
}

function Proxies(){
// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'http://192.168.1.1:9090/providers/proxies');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

$headers = array();
$headers[] = 'Accept: */*';
$headers[] = 'Accept-Language: en-US,en;q=0.9';
$headers[] = 'Authorization: Bearer reyre';
$headers[] = 'Connection: keep-alive';
$headers[] = 'Content-Type: application/json';
$headers[] = 'Cookie: filemanager=ee057d392316be9bec05f297f2037536';
$headers[] = 'Referer: http://192.168.1.1:9090/ui/yacd/?hostname=192.168.1.1&port=9090&secret=reyre';
$headers[] = 'Sec-Gpc: 1';
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$data = json_decode($result,true);

$data = $data['providers']['default']['proxies'];
$final = "Type | Name | Ping\n";

    foreach ($data as $key => $value) {
        $name = $value['name'];
        $delay = $value['history'][-0]['delay'];
        $type = $value['type'];
        $final .= "$type | $name | $delay ms \n";
    }
return $final;
}

function Rules(){
// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'http://192.168.1.1:9090/rules');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

$headers = array();
$headers[] = 'Accept: */*';
$headers[] = 'Accept-Language: en-US,en;q=0.9';
$headers[] = 'Authorization: Bearer reyre';
$headers[] = 'Connection: keep-alive';
$headers[] = 'Content-Type: application/json';
$headers[] = 'Cookie: filemanager=ee057d392316be9bec05f297f2037536';
$headers[] = 'Referer: http://192.168.1.1:9090/ui/yacd/?hostname=192.168.1.1&port=9090&secret=reyre';
$headers[] = 'Sec-Gpc: 1';
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$data = json_decode($result,true);

$data = $data['rules'];
$final = "Type | Payload | Proxy\n";

    foreach ($data as $key => $value) {
        $proxy = $value['proxy'];
        $payload = $value['payload'];
        $type = $value['type'];
        $final .= "$type | $payload | $proxy \n";
    }
return $final;
}

function myip(){
    $data = json_decode(seeURL("http://ip-api.com/json/"),true);
    $country = $data['country'];
    $countryCode = $data['countryCode'];
    $region = $data['regionName'];
    $city = $data['city'];
    $isp = $data['isp'];
    $timezone = $data['timezone'];
    $as = $data['as'];
    $ip = $data['query'];
    $result = "ISP : $isp\n↳ Address : $as \n↳ IP : $ip \n↳ Region | City : $region | $city \n↳ Timezone : $timezone \n↳ Country : $country | $countryCode \n↳ XppaiWRT";
    return $result;
}

function Speedtest(){

$result = shell_exec('speedtest > result && cat result');
return $result;

}
