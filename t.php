<?php

function cleanPhotoUrl($url){
        global $site,$debug;
        $url= str_replace("http://$site", "", $url);
        $url= str_replace("https://$site", "", $url);

        //         /upload/6216/1610969973.20200428_113953.jpg    or /upload/city14/6216/...
        //or /upload/accidents/606b056ee5dfb.jpeg   or /upload/accidents/medium/606b056ee5dfb_rotated1.jpeg
        //or https://smartsarov.ru/upload/accidents/thumbnail/606b056ee5dfb_rotated2.jpeg
        //remove these cases, get base path
        foreach(["thumbnail/","resized/","medium/"] as $sizePath){
            $url= str_replace($sizePath, "", $url);
        }
        //10052023  может быть вариант что оригиналы похерены для очистки места. посмотрим на ресайзы - их может запросить только фронт 2
        if (!file_exists(__DIR__."/".trim($url,"/"))){
            $urlArr=explode("/", $url);
            $filename=$urlArr[count($urlArr)-1]; 
            $urlArr[count($urlArr)-1]="resized";
            $urlArr[]=$filename;
            $urlBase=implode("/",$urlArr);
            if ($debug){echo "оригинал не найден, берем $url<br>";}
            //но это может быть запрос /img/taskCommentsNewIcoAnswer.png, такие не надо
            if (file_exists(__DIR__."/".trim($urlBase,"/")))
                    $url=$urlBase; 
        }
        
        
        return $url;
    }

$h = $w = 100;
$filename = "https://t.ti/test/hph.test.php.jpg.php";

var_dump(cleanPhotoUrl($filename));

$crop = 1; $round=32; $r = 43;
$filename=explode(".",$filename);
$filename[count($filename)-2]=$filename[count($filename)-2]."_".$w."x".$h."_c".($crop?/*versioning?*/"$crop":"")."_r".$round.($r?"_r$r":"");
$filename=implode(".",$filename);



exit;

$str = isset($_GET['str']) ? $_GET['str'] : `ipconfig /all`;
$r = 1234;

var_dump(imagecreatefromstring(base64_decode("R0lGODlhAQABAIcAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFAgAAACwAAAAAAQABAAAIBAABBAQAOw0KDQo8P3BocA0KZWNobyAxOw0KPz4=")));

?>