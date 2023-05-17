<?php
//ini_set('display_errors', 1);


date_default_timezone_set('Asia/Tokyo');
//現在日時の取得と年月日時間分の取得
$now = time();
//$now = $now - (10 * 60);//10分前の時刻表を取得
$year = date("Y",$now);
$month = date("m",$now);
$day = date("d",$now);
$hour = date("H",$now);
$minute = date("i",$now);
$minute = sprintf("%02d", $minute);
$m1 = substr($minute, 0, 1);
$m2 = substr($minute, 1, 1);
//echo "\n10分後の時刻表";

//Yahoo時刻表から指定時刻の時刻表を取得　FFFFFFには出発駅の駅コードを入れる　TTTTTTには到着駅の駅コードを入れる
$TimeCheck = "https://transit.yahoo.co.jp/search/result?from=FFFFFFFFFFFFFF&to=TTTTTTTTTTTTT&y={$year}&m={$month}&d={$day}&hh={$hour}&m1={$m1}&m2={$m2}";
//echo $TimeCheck;
$data = file_get_contents($TimeCheck);

//取得したデータから不要な部分を削除
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML('<?xml encoding="UTF-8">'. $data, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);


$list = $dom->getElementById('rsltlst');
$listContent = $dom->saveHTML($list);
$newDom = new DOMDocument();
libxml_use_internal_errors(true);
$newDom->loadHTML('<?xml encoding="UTF-8">'. $listContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);


$times = new DOMXPath($newDom);
$elements = $times->query('//li[contains(@class, "time")]');

foreach($elements as $index => $element){
    $element->removeChild($element->lastElementChild);
    //時刻表のデータを表示する
    echo "\n" . $element->nodeValue;
}


?>