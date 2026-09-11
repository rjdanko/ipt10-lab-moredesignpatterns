<?php

// ---------------------------------------------
// Product
// ---------------------------------------------

class CallingCard
{
    private $image;
    private $width;
    private $height;

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->image = imagecreatetruecolor($width, $height);
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function save(string $directory): string
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = $directory . '/calling-card-' . uniqid() . '.png';

        if (imagepng($this->image, $filename)) {
            echo "Calling card generated successfully.\n";
            echo "File: $filename\n";
        } else {
            echo "ERROR: Could not save image.\n";
        }

        return $filename;
    }

    public function destroy(): void
    {
        imagedestroy($this->image);
    }
}


// ---------------------------------------------
// Builder interface
// ---------------------------------------------

interface CallingCardBuilder
{
    public function reset(int $width, int $height): void;
    public function allocateColors(): void;
    public function drawBackground(): void;
    public function drawCardPanel(): void;
    public function drawAccentBar(): void;
    public function drawBusinessName(string $businessName): void;
    public function drawPersonName(string $name): void;
    public function drawPosition(string $position): void;
    public function drawDivider(): void;
    public function drawContactInfo(string $email, string $phone, string $address): void;
    public function drawWebsite(string $url): void;
    public function getCard(): CallingCard;
}


// ---------------------------------------------
// Concrete Builder
// ---------------------------------------------

class StandardCallingCardBuilder implements CallingCardBuilder
{
    private CallingCard $card;

    private $background;
    private $white;
    private $black;
    private $gray;
    private $blue;

    public function reset(int $width, int $height): void
    {
        $this->card = new CallingCard($width, $height);
    }

    public function allocateColors(): void
    {
        $image = $this->card->getImage();

        $this->background = imagecolorallocate($image, 245, 247, 250);
        $this->white      = imagecolorallocate($image, 255, 255, 255);
        $this->black      = imagecolorallocate($image, 30, 30, 30);
        $this->gray       = imagecolorallocate($image, 100, 100, 100);
        $this->blue       = imagecolorallocate($image, 40, 100, 200);
    }

    public function drawBackground(): void
    {
        imagefill($this->card->getImage(), 0, 0, $this->background);
    }

    public function drawCardPanel(): void
    {
        imagefilledrectangle(
            $this->card->getImage(),
            50, 50,
            950, 550,
            $this->white
        );
    }

    public function drawAccentBar(): void
    {
        imagefilledrectangle(
            $this->card->getImage(),
            50, 50,
            75, 550,
            $this->blue
        );
    }

    public function drawBusinessName(string $businessName): void
    {
        imagestring(
            $this->card->getImage(),
            5,
            120,
            100,
            strtoupper($businessName),
            $this->blue
        );
    }

    public function drawPersonName(string $name): void
    {
        imagestring(
            $this->card->getImage(),
            5,
            120,
            170,
            $name,
            $this->black
        );
    }

    public function drawPosition(string $position): void
    {
        imagestring(
            $this->card->getImage(),
            4,
            120,
            210,
            $position,
            $this->blue
        );
    }

    public function drawDivider(): void
    {
        imageline(
            $this->card->getImage(),
            120,
            260,
            880,
            260,
            $this->gray
        );
    }

    public function drawContactInfo(string $email, string $phone, string $address): void
    {
        // Email
        imagestring(
            $this->card->getImage(),
            4,
            120,
            310,
            'Email: ' . $email,
            $this->black
        );

        // Phone
        imagestring(
            $this->card->getImage(),
            4,
            120,
            365,
            'Phone: ' . $phone,
            $this->black
        );

        // Address
        imagestring(
            $this->card->getImage(),
            4,
            120,
            420,
            'Address: ' . $address,
            $this->black
        );
    }

    public function drawWebsite(string $url): void
    {
        imagestring(
            $this->card->getImage(),
            3,
            120,
            485,
            $url,
            $this->gray
        );
    }

    public function getCard(): CallingCard
    {
        return $this->card;
    }
}


// ---------------------------------------------
// Director
// ---------------------------------------------

class CallingCardDirector
{
    private CallingCardBuilder $builder;

    public function __construct(CallingCardBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function buildStandardCard(array $data): CallingCard
    {
        $name = "{$data['firstName']} {$data['lastName']}";

        $email = strtolower(
            "{$data['lastName']}.{$data['firstName']}@auf.edu.ph"
        );

        $address = "{$data['street']}, {$data['city']}";

        $this->builder->reset(1000, 600);
        $this->builder->allocateColors();
        $this->builder->drawBackground();
        $this->builder->drawCardPanel();
        $this->builder->drawAccentBar();
        $this->builder->drawBusinessName($data['businessName']);
        $this->builder->drawPersonName($name);
        $this->builder->drawPosition($data['position']);
        $this->builder->drawDivider();
        $this->builder->drawContactInfo($email, $data['phone'], $address);
        $this->builder->drawWebsite('www.auf.edu.ph');

        return $this->builder->getCard();
    }
}


// ---------------------------------------------
// Client
// ---------------------------------------------

$data = [
    'firstName'    => 'Riejed Aniko',
    'lastName'     => 'Macatula',
    'businessName' => 'College of Computing Studies',
    'position'     => 'BSIT Student',
    'street'       => 'AUF EYA Building',
    'city'         => 'Angeles City',
    'phone'        => sprintf(
        '+1 (555) %03d-%04d',
        rand(100, 999),
        rand(1000, 9999)
    ),
];

$builder = new StandardCallingCardBuilder();
$director = new CallingCardDirector($builder);

$card = $director->buildStandardCard($data);

$outputDirectory = __DIR__ . '/cards';
$card->save($outputDirectory);
$card->destroy();
