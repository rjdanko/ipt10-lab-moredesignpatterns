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

    // PHP's default clone is shallow, so a cloned CallingCard would
    // otherwise still point at the ORIGINAL image resource. Drawing on
    // a "cloned" card would then corrupt the prototype. __clone() gives
    // every clone its own independent GD image.
    public function __clone()
    {
        $copy = imagecreatetruecolor($this->width, $this->height);
        imagecopy($copy, $this->image, 0, 0, 0, 0, $this->width, $this->height);
        $this->image = $copy;
    }
}


// ---------------------------------------------
// Prototype interface
// ---------------------------------------------

interface CallingCardPrototype
{
    public function cloneCard(): CallingCardPrototype;
    public function setPersonName(string $name): void;
    public function setPosition(string $position): void;
    public function setContactInfo(string $email, string $phone, string $address): void;
    public function getCard(): CallingCard;
}


// ---------------------------------------------
// Concrete Prototype
// ---------------------------------------------

class StandardCallingCardPrototype implements CallingCardPrototype
{
    private CallingCard $card;

    private $background;
    private $white;
    private $black;
    private $gray;
    private $blue;

    public function __construct(int $width, int $height, string $businessName, string $url)
    {
        $this->card = new CallingCard($width, $height);

        $this->allocateColors();
        $this->drawBackground();
        $this->drawCardPanel();
        $this->drawAccentBar();
        $this->drawBusinessName($businessName);
        $this->drawDivider();
        $this->drawWebsite($url);
    }

    // Cloning duplicates the already-drawn template (background, panel,
    // accent bar, business name, divider, website) instead of redrawing
    // it from scratch. The caller then only fills in the per-person
    // details on the clone.
    public function cloneCard(): CallingCardPrototype
    {
        return clone $this;
    }

    public function __clone()
    {
        $this->card = clone $this->card;
    }

    private function allocateColors(): void
    {
        $image = $this->card->getImage();

        $this->background = imagecolorallocate($image, 245, 247, 250);
        $this->white      = imagecolorallocate($image, 255, 255, 255);
        $this->black      = imagecolorallocate($image, 30, 30, 30);
        $this->gray       = imagecolorallocate($image, 100, 100, 100);
        $this->blue       = imagecolorallocate($image, 40, 100, 200);
    }

    private function drawBackground(): void
    {
        imagefill($this->card->getImage(), 0, 0, $this->background);
    }

    private function drawCardPanel(): void
    {
        imagefilledrectangle(
            $this->card->getImage(),
            50, 50,
            950, 550,
            $this->white
        );
    }

    private function drawAccentBar(): void
    {
        imagefilledrectangle(
            $this->card->getImage(),
            50, 50,
            75, 550,
            $this->blue
        );
    }

    private function drawBusinessName(string $businessName): void
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

    public function setPersonName(string $name): void
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

    public function setPosition(string $position): void
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

    private function drawDivider(): void
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

    public function setContactInfo(string $email, string $phone, string $address): void
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

    private function drawWebsite(string $url): void
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
// Client
// ---------------------------------------------

$students = [
    [
        'first_name' => 'Felicity',
        'last_name' => 'Hampton',
    ],
    [
        'first_name' => 'Hank',
        'last_name' => 'Rice',
    ],
    [
        'first_name' => 'Ada',
        'last_name' => 'Wilson',
    ],
    [
        'first_name' => 'Daniel',
        'last_name' => 'Salgado',
    ],
    [
        'first_name' => 'Avalynn',
        'last_name' => 'Crane',
    ],
    [
        'first_name' => 'Fox',
        'last_name' => 'Summers',
    ],
    [
        'first_name' => 'Frankie',
        'last_name' => 'Andersen',
    ],
    [
        'first_name' => 'Alistair',
        'last_name' => 'Decker',
    ],
    [
        'first_name' => 'Aleena',
        'last_name' => 'Phillips',
    ],
    [
        'first_name' => 'Andrew',
        'last_name' => 'Marks',
    ],
    [
        'first_name' => 'Monica',
        'last_name' => 'French',
    ],
    [
        'first_name' => 'Corey',
        'last_name' => 'Hess',
    ],
    [
        'first_name' => 'Kaliyah',
        'last_name' => 'Richard',
    ],
    [
        'first_name' => 'Ahmed',
        'last_name' => 'Richardson',
    ],
    [
        'first_name' => 'Allison',
        'last_name' => 'Cortes',
    ],
    [
        'first_name' => 'Banks',
        'last_name' => 'McGee',
    ],
    [
        'first_name' => 'Kayleigh',
        'last_name' => 'Mendoza',
    ],
    [
        'first_name' => 'Dominic',
        'last_name' => 'Atkins',
    ],
    [
        'first_name' => 'Mina',
        'last_name' => 'Beasley',
    ],
    [
        'first_name' => 'Stanley',
        'last_name' => 'Jefferson',
    ],
];

$sharedData = [
    'businessName' => 'College of Computing Studies',
    'position'     => 'BSIT Student',
    'street'       => 'AUF EYA Building',
    'city'         => 'Angeles City',
];

// Build the prototype ONCE. All the shared, expensive-to-draw template
// elements (background, panel, accent bar, business name, divider,
// website) are drawn a single time here.
$prototype = new StandardCallingCardPrototype(
    1000,
    600,
    $sharedData['businessName'],
    'www.auf.edu.ph'
);

$outputDirectory = __DIR__ . '/cards';

foreach ($students as $student) {
    $name = "{$student['first_name']} {$student['last_name']}";

    $email = strtolower(
        "{$student['last_name']}.{$student['first_name']}@auf.edu.ph"
    );

    $phone = sprintf(
        '+1 (555) %03d-%04d',
        rand(100, 999),
        rand(1000, 9999)
    );

    $address = "{$sharedData['street']}, {$sharedData['city']}";

    // Clone the prototype instead of rebuilding the template from
    // scratch, then customize only the per-student details.
    $studentCard = $prototype->cloneCard();
    $studentCard->setPersonName($name);
    $studentCard->setPosition($sharedData['position']);
    $studentCard->setContactInfo($email, $phone, $address);

    $card = $studentCard->getCard();
    $card->save($outputDirectory);
    $card->destroy();
}
