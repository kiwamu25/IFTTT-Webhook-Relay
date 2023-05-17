<?php
//ini_Set('display_errors', 1);
//時間を日本時間に設定する。 time is set to Japanese time.
date_default_timezone_set('Asia/Tokyo');

//webhookaからJSONデータを取得 webhook to get JSON data
$file = file_get_contents('php://input');
// JSONデータを配列に変換 convert JSON data to array
$data = json_decode($file, true);

// 現在の日付と時間を取得 get current date and time
$date = date('Y/m/d H:i:s');

//ログ用のデータ作成 create data for log
$request_log = $date . " " . $file . "\n";
//ログをWebからアクセスできない場所に保存 save log to a place where it cannot be accessed from the web
file_put_contents('/var/www/html/public_html/file_path***', $request_log, FILE_APPEND);

// データが取得されたかどうかの判定 check if data is obtained
if (isset($data)) {
    // データが取得された場合の処理 process when data is obtained
    // $dataにはJSONデータが配列として格納されます。 JSON data is stored in $data as an array.
} else {
    // データが取得されなかった場合の処理 process when data is not obtained
    http_response_code(400);

    // リダイレクト先のURLを指定 specify the URL of the redirect destination
    $redirect_url = "https://homepage URL****";

    // リダイレクト redirect
    header('Location: '.$redirect_url);

    // 処理終了 end of process
    exit();
}
// データが取得された場合の処理 process when data is obtained
if(isset($data["who"])&&isset($data["where"])&&isset($data["how"])) {
    //各データを変数に格納 store each data in a variable
    $who = $data["who"];
    $where = $data["where"];
    $how = strtolower($data["how"]);
} else {
    // データが取得されなかった場合の処理 process when data is not obtained
    http_response_code(400);
    
    // リダイレクト先のURLを指定 specify the URL of the redirect destination
    $redirect_url = "https://homepage URL****";
    header('Location: '.$redirect_url);
    exit();
}
//$howがenteredだった場合、$howに着いたを代入 if $how is entered, assign arrived to $how
if(strpos($how , "entered") !== false){
    $how = "着いた";
}else{
    $how = "出た";
}


//子供の名前が入っていた場合、電車の時間を取得して$howに追加 if the child's name is included, get the time and add it to $how
//関係ない場合はコメントアウト comment out if not related
if($who == "[子供の名前]" && $how == "出た"){
    //TimeCheck.phpから電車の時刻を取得 get the train time from TimeCheck.php
    $time = file_get_contents("TimeCheck.php");
    //時間を$howに追加 add time to $how
    $how = $how.$time;
}

//IFTTTに送るデータを作成 create data to send to IFTTT
$jdata = array('value1' => $who, 'value2' => $where, 'value3' => $how);
//JSONに変換 convert to JSON
$json_data = json_encode($jdata, JSON_UNESCAPED_UNICODE);

echo $json_data;
//IFTTTのWebhookのURLを指定 specify the URL of IFTTT's Webhook
$url = "https://maker.ifttt.com/trigger/*EventName*/with/key/***************";

//curlでデータを送信 send data with curl
$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

$result = curl_exec($curl);

curl_close($curl);




// レスポンスを返す return response
http_response_code(200);

?>