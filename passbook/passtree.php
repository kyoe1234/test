<?php
setlocale(LC_MONETARY, 'en_US');
require('PKPass.php');

// Variables
$id = rand(100000,999999) . '-' . rand(100,999) . '-' . rand(100,999); // Every card should have a unique serialNumber
$balance = '$'.rand(0,30).'.'.rand(10,99); // Create random balance
//$name = stripslashes($_POST['name']);
$name = 'passtree';

// Create pass
$pass = new PKPass();

$pass->setCertificate('./certif/ngmsPassTypeId.p12'); // Set the path to your Pass Certificate (.p12 file)
$pass->setCertificatePassword('face1234'); // Set password for certificate
$pass->setWWDRcertPath('./certif/Apple_Worldwide_Developer.pem');
$pass->setJSON('{
    "passTypeIdentifier": "pass.skcc.ngms.pushexam",
    "formatVersion": 1,
    "organizationName": "Pass Tree",
    "teamIdentifier": "NUG3DQ847F",
    "serialNumber": "p69f2J",
    "backgroundColor": "rgb(0,100,0)",
    "logoText": "Pass Tree",
    "authenticationToken" : "vxwxd7J8AlNNFPS8k0a0FfUFtq0ewzFdc",
    "locations" : [
      {
        "longitude" : 126.738142,
        "latitude" : 37.498653
      }
    ],
    "description": "Store card",
    "storeCard" : {
    "auxiliaryFields" : [
      {
        "key" : "name",
        "label" : "NAME",
        "value" : "Park Jaezin"
      },
      {
        "key" : "code",
        "label" : "CODE NUMBER",
        "value" : "1234567890"
      }
    ],
    "backFields" : [
      {
        "key" : "info",
        "label" : "Infomation",
        "value" : "이 패스는 오프라인에서 발급된 패스를 모바일용으로 전환된 것으로, 해당업체에서 정식으로 발급되지 않은 패스입니다. 바코드인식에 문제가 있는 경우 바코드넘버를 입력하여 사용해주세요."
      },
      {
        "key" : "site",
        "label" : "Web Site",
        "value" : "웹사이트에 방문해서 다양한 패스를 등록하세요. http://passtree.net"
      },
      {
        "key" : "app",
        "label" : "Appstore Link",
        "value" : "앱을 다운받아 더욱 손쉽게 패스를 등록하세요."
      },
      {
        "key" : "email",
        "label" : "Contact Us",
        "value" : "help@passtree.net"
      },
    ]
  },
    "barcode": {
        "format": "PKBarcodeFormatPDF417",
        "message": "1234567890",
        "messageEncoding": "iso-8859-1"
    }
    }');

// add files to the PKPass package
$pass->addFile('./first_pass/icon.png');
$pass->addFile('./first_pass/icon@2x.png');
$pass->addFile('./first_pass/logo.png');
$pass->addFile('./first_pass/background.png', './first_pass/strip.png');

if(!$pass->create(true)) { // Create and output the PKPass
    echo 'Error: '.$pass->getError();
}
exit;