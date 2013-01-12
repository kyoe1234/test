<?php
require('PKPass.php');

$pass = new PKPass();

$pass->setCertificate('./certif/ngmsPassTypeId.p12');  // 2. Set the path to your Pass Certificate (.p12 file)
//$pass->setCertificatePassword('test123');     // 2. Set password for certificate
$pass->setCertificatePassword('face1234');
$pass->setWWDRcertPath('./certif/Apple_Worldwide_Developer.pem'); // 3. Set the path to your WWDR Intermediate certificate (.pem file)

// Top-Level Keys http://developer.apple.com/library/ios/#documentation/userexperience/Reference/PassKit_Bundle/Chapters/TopLevel.html
$standardKeys         = array(
    'description'        => 'Demo pass',
    'formatVersion'      => 1,
    'organizationName'   => 'Kyoe Express',
    'passTypeIdentifier' => 'pass.skcc.ngms.pushexam', // 4. Set to yours
    'serialNumber'       => '123456',
    'teamIdentifier'     => 'NUG3DQ847F'           // 4. Set to yours
);
$associatedAppKeys    = array();
$relevanceKeys        = array();
$styleKeys            = array(
    'boardingPass' => array(
        'primaryFields' => array(
            array(
                'key'   => 'origin',
                'label' => 'miya home',
                'value' => '신림'
            ),
            array(
                'key'   => 'destination',
                'label' => 'kyoe home',
                'value' => '사당'
            )
        ),
        'secondaryFields' => array(
            array(
                'key'   => 'gate',
                'label' => 'Gate',
                'value' => 'F12'
            ),
            array(
                'key'   => 'date',
                'label' => 'Departure date',
                'value' => '01/12/2013 12:00'
            )
        ),
        'backFields' => array(
            array(
                'key'   => 'passenger-name',
                'label' => 'Passenger',
                'value' => 'John Appleseed'
            )
        ),
        'transitType' => 'PKTransitTypeAir'
    )
);
$visualAppearanceKeys = array(
    'barcode'         => array(
        'format'          => 'PKBarcodeFormatQR',
        'message'         => 'Flight-GateF12-ID6643679AH7B',
        'messageEncoding' => 'iso-8859-1'
    ),
    'backgroundColor' => 'rgb(107,156,196)',
    'logoText'        => 'Flight info'
);
$webServiceKeys       = array();

// Merge all pass data and set JSON for $pass object
$passData = array_merge(
    $standardKeys,
    $associatedAppKeys,
    $relevanceKeys,
    $styleKeys,
    $visualAppearanceKeys,
    $webServiceKeys
);

$pass->setJSON(json_encode($passData));

// Add files to the PKPass package
$pass->addFile('images/icon.png');
$pass->addFile('images/icon@2x.png');
$pass->addFile('images/logo.png');

if(!$pass->create(true)) { // Create and output the PKPass
    echo 'Error: '.$pass->getError();
}