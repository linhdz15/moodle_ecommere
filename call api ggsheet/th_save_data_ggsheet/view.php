<?php

require_once '../../config.php';

$appId = 'f59d243f-c14e-4cd9-89f3-944197a3eb39';
$accessKey = 'V2-sj2Sk-OP8s4-GbDvf-TI1u7-n5Zqa-Mkfb3-BiXrA-RGIfq';
$tablename = 'Hopdong';
$tablename = 'Phieuchi';
$tablename = 'Phieuthu';

// $url = 'https://api.appsheet.com/api/v2/apps/' . $appId . '/tables';
$url = 'https://api.appsheet.com/api/v2/apps/' . $appId . '/tables/' . $tablename . '/Action';

$ch = curl_init($url);

// Convert the request body to JSON format

//Lấy tất cả dữ liệu
$requestBodyJson = '{
    "Action": "Find",
    "Properties": {
       "Locale": "en-US",
       "Location": "47.623098, -122.330184",
       "Timezone": "Pacific Standard Time",
       "UserSettings": {
          "Option 1": "value1",
          "Option 2": "value2"
       }
    },
    "Rows": [
    ]
}';

//Lấy dữ liệu có sophieuthu = THU00001
$requestBodyJson = '{
    "Action": "Find",
    "Properties": {
       "Locale": "en-US",
       "Location": "47.623098, -122.330184",
       "Timezone": "Pacific Standard Time",
       "UserSettings": {
          "Option 1": "value1",
          "Option 2": "value2"
       }
    },
    "Rows": [
        {
            "Sophieuthu": "THU00001"
        }
    ]
}';

//Them du lieu moi
$requestBodyJson = '{
    "Action": "Add",
    "Properties": {
       "Locale": "en-US",
       "Location": "47.623098, -122.330184",
       "Timezone": "Pacific Standard Time",
       "UserSettings": {
          "Option 1": "value1",
          "Option 2": "value2"
       }
    },
    "Rows": [
        {
            "Sophieuthu": "THU10000",
            "Loaiphieuthu": "Tiền hợp đồng",
            "Diengiai": "Diễn giải 1",
            "SoHD": "HD1",
            "Nguoinop": "Tuan Linh",
            "Sotien": "1176263",
            "Nguoithu": "NV001",
            "TGthu": "01/08/2022",
        }
    ]
}';

//Cap nhat du lieu moi
$requestBodyJson = '{
    "Action": "Edit",
    "Properties": {
       "Locale": "en-US",
       "Location": "47.623098, -122.330184",
       "Timezone": "Pacific Standard Time",
       "UserSettings": {
          "Option 1": "value1",
          "Option 2": "value2"
       }
    },
    "Rows": [
        {
            "_RowNumber": "522",
            "Sophieuthu": "THU10000",
            "Loaiphieuthu": "Tiền hợp đồng",
            "Diengiai": "Diễn giải 1",
            "SoHD": "HD1",
            "Nguoinop": "Tuan Linh Nguyen",
            "Sotien": "1176263",
            "Nguoithu": "NV001",
            "TGthu": "01/08/2022",
        }
    ]
}';

//Xoa du lieu
$requestBodyJson = '{
    "Action": "Delete",
    "Properties": {
       "Locale": "en-US",
       "Location": "47.623098, -122.330184",
       "Timezone": "Pacific Standard Time",
       "UserSettings": {
          "Option 1": "value1",
          "Option 2": "value2"
       }
    },
    "Rows": [
        {
            "_RowNumber": "522",
            "Sophieuthu": "THU10000",
        }
    ]
}';

// Cài đặt các tùy chọn của curl
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBodyJson);

// Cài đặt các tùy chọn của curl
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'ApplicationAccessKey: ' . $accessKey,
));

// Cài đặt để nhận kết quả trả về
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Gọi API và lấy dữ liệu
$response = curl_exec($ch);

// Kiểm tra và xử lý kết quả
if ($response) {
    $responseData = json_decode($response, true);
    print_object($responseData);
    exit;
} else {
    echo 'Lỗi khi lấy dữ liệu từ API';
}

// Đóng kết nối curl
curl_close($ch);

?>
