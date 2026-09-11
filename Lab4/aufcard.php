<?php

// ---------------------------------------------
// Canvas
// ---------------------------------------------

$width = 1000;
$height = 600;

$image = imagecreatetruecolor($width, $height);


// ---------------------------------------------
// Colors
// ---------------------------------------------

$background = imagecolorallocate($image, 245, 247, 250);
$white      = imagecolorallocate($image, 255, 255, 255);
$black      = imagecolorallocate($image, 30, 30, 30);
$gray       = imagecolorallocate($image, 100, 100, 100);
$blue       = imagecolorallocate($image, 40, 100, 200);


// ---------------------------------------------
// Background
// ---------------------------------------------

imagefill($image, 0, 0, $background);


// ---------------------------------------------
// Calling Card
// ---------------------------------------------

imagefilledrectangle(
    $image,
    50, 50,
    950, 550,
    $white
);


// Left accent bar
imagefilledrectangle(
    $image,
    50, 50,
    75, 550,
    $blue
);

$firstName = "Juan";
$lastName = "Dela Cruz";

$businessName = "College of Computing Studies";
$position = "BSIT Student";

$street = "AUF CCS Building";
$city = "Angeles City";

$name = "$firstName $lastName";

$email = strtolower(
    "{$lastName}.{$firstName}@auf.edu.ph"
);

$phone = sprintf(
    '+1 (555) %03d-%04d',
    rand(100, 999),
    rand(1000, 9999)
);

$address = "$street, $city";


// ---------------------------------------------
// Draw Text
//
// GD built-in fonts:
// 1 = small
// 2 = medium
// 3 = medium-large
// 4 = large
// 5 = largest
// ---------------------------------------------

// Business name
imagestring(
    $image,
    5,
    120,
    100,
    strtoupper($businessName),
    $blue
);


// Person name
imagestring(
    $image,
    5,
    120,
    170,
    $name,
    $black
);


// Position
imagestring(
    $image,
    4,
    120,
    210,
    $position,
    $blue
);


// Divider
imageline(
    $image,
    120,
    260,
    880,
    260,
    $gray
);


// Email
imagestring(
    $image,
    4,
    120,
    310,
    'Email: ' . $email,
    $black
);


// Phone
imagestring(
    $image,
    4,
    120,
    365,
    'Phone: ' . $phone,
    $black
);


// Address
imagestring(
    $image,
    4,
    120,
    420,
    'Address: ' . $address,
    $black
);


// Website
imagestring(
    $image,
    3,
    120,
    485,
    'www.auf.edu.ph',
    $gray
);


// ---------------------------------------------
// Save Image
// ---------------------------------------------

$outputDirectory = __DIR__ . '/cards';

if (!is_dir($outputDirectory)) {
    mkdir($outputDirectory, 0755, true);
}

$filename =
    $outputDirectory .
    '/calling-card-' .
    uniqid() .
    '.png';


// Save PNG
if (imagepng($image, $filename)) {
    echo "Calling card generated successfully.\n";
    echo "File: $filename\n";
} else {
    echo "ERROR: Could not save image.\n";
}


// ---------------------------------------------
// Clean up
// ---------------------------------------------

imagedestroy($image);
