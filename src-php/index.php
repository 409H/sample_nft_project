<?php
require_once __DIR__ ."/../vendor/autoload.php";
use TdTrung\Chalk\Chalk;
use Treinetic\ImageArtist\lib\Image;

const OUTPUT_DIR = __DIR__ ."/../images_processed";

$chalk = new Chalk();

// Greetz the user
echo $chalk->color69(base64_decode("LmRQIlk4ICAgIGRiICAgIDhiICAgIGQ4IDg4IiJZYiA4OCAgICAgODg4ODg4ICAgICA4OGIgODggODg4ODg4IDg4ODg4OCAgICAgODgiIlliIDg4IiJZYiAgZFAiWWIgICA4ODg4OCA4ODg4ODggIGRQIiJiOCA4ODg4ODggCmBZYm8uIiAgIGRQWWIgICA4OGIgIGQ4OCA4OF9fZFAgODggICAgIDg4X18gICAgICAgODhZYjg4IDg4X18gICAgIDg4ICAgICAgIDg4X19kUCA4OF9fZFAgZFAgICBZYiAgICAgODggODhfXyAgIGRQICAgYCIgICA4OCAgIApvLmBZOGIgIGRQX19ZYiAgODhZYmRQODggODgiIiIgIDg4ICAubyA4OCIiICAgICAgIDg4IFk4OCA4OCIiICAgICA4OCAgICAgICA4OCIiIiAgODgiWWIgIFliICAgZFAgby4gIDg4IDg4IiIgICBZYiAgICAgICAgODggICAKOGJvZFAnIGRQIiIiIlliIDg4IFlZIDg4IDg4ICAgICA4OG9vZDggODg4ODg4ICAgICA4OCAgWTggODggICAgICAgODggICAgICAgODggICAgIDg4ICBZYiAgWWJvZFAgICJib2RQJyA4ODg4ODggIFlib29kUCAgIDg4ICAg")) . PHP_EOL . PHP_EOL;

// Ask for the collection name
$defaultCollectionName = "Sample NFT Project";
echo $chalk->blue("What do you want to name this collection?", $chalk->yellow(implode("", ["[", $defaultCollectionName, "]"])));
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
$inputCollectionName = strlen(trim($line)) > 0 ? trim($line) : $defaultCollectionName;

// Ask where the collection data is held
$defaultCollectionEndpoint = "https://harrydenley.com/assets/nft/samplenftproject/";
echo $chalk->blue("Where are you storing the metadata?", $chalk->yellow(implode("", ["[", $defaultCollectionEndpoint, "]"])));
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
$defaultCollectionEndpoint = strlen(trim($line)) > 0 ? trim($line) : $defaultCollectionEndpoint;

// Do some directory hygiene
if(count(scandir(OUTPUT_DIR)) > 0) {
    echo PHP_EOL . $chalk->lightRed("[!] Output directory is not empty.", PHP_EOL, $chalk->white("Do you want to delete everything in here beforehand?\r\n If no, application will NOT overwrite files with the same file names", $chalk->lightYellow("[no]")));

    $handle = fopen ("php://stdin","r");
    $line = fgets($handle);
    $input = strtolower(trim($line));

    if(in_array($input, ["y", "yes"])) {
        $files = glob(OUTPUT_DIR ."/*");
        foreach($files as $file){  
            if(is_file($file)) {
                unlink($file); 
            }
        }
    }
}

// Fetch all the layers and detail everything is correct
echo PHP_EOL . $chalk->color10("[*] Fetching and parsing layers") . PHP_EOL;
$files = glob(__DIR__ ."/../images_raw/*/*.png");
$table = [];
foreach($files as $file) {
    $filename = basename($file);
    list($trait_name, $trait_value1, $trait_value2) = preg_split("/\_|\./", $filename);
    $table[$trait_name][] = [
        "trait_shape" => $trait_value1, 
        "trait_color" => $trait_value2, 
        "_filename" => $filename,
        "_abs_path" => $file
    ];
}

// Creating the images
echo PHP_EOL . $chalk->color10("[*] Creating images") . PHP_EOL;

$defaultIterations = 1;
echo $chalk->blue("How many random iterations do you want?", $chalk->yellow(implode("", ["[", $defaultIterations, "]"])));
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
$input = (trim($line) > 0 ? trim($line) : $defaultIterations) + 1;
$fileIndex = 0;

for($i=1;$i<=$input;$i++) {
    foreach($table["head"] as $key => $headLayer) {
        $attr = [];

        $head = new Image($headLayer["_abs_path"]);
        $attr[] = [
            "trait_type" => "Head Color",
            "value" => $headLayer["trait_color"]
        ];
        $attr[] = [
            "trait_type" => "Head Shape",
            "value" => $headLayer["trait_shape"]
        ];

        $random_eye_index = mt_rand(0, count($table["eyes"]) - 1);
        $eyeLayer = $table["eyes"][$random_eye_index];
        $eyes = new Image($eyeLayer["_abs_path"]);
        $head->merge($eyes, 0, 0);
        $attr[] = [
            "trait_type" => "Eye Color",
            "value" => $eyeLayer["trait_color"]
        ];
        $attr[] = [
            "trait_type" => "Eye Shape",
            "value" => $eyeLayer["trait_shape"]
        ];
        
        $random_mouth_index = mt_rand(0, count($table["mouth"]) - 1);
        $mouthLayer = $table["mouth"][$random_mouth_index];
        $mouth = new Image($mouthLayer["_abs_path"]);
        $head->merge($mouth, 0, 0);
        $attr[] = [
            "trait_type" => "Mouth Color",
            "value" => $mouthLayer["trait_color"]
        ];
        $attr[] = [
            "trait_type" => "Mouth Shape",
            "value" => $mouthLayer["trait_shape"]
        ];
        
        if(file_exists(implode("/", [OUTPUT_DIR, $fileIndex.".png"])) === false) {
            $head->save(implode("/", [OUTPUT_DIR, $fileIndex.".png"]), IMAGETYPE_PNG);
            echo PHP_EOL . $chalk->color77("\t[+]Image ${fileIndex} has been created!", PHP_EOL, $chalk->white("\t".json_encode($attr))) . PHP_EOL;

            // Build the trait file for the graphic
            $traits = [
                "attributes" => $attr,
                "descriptions" => "",
                "image" => implode("", [$defaultCollectionEndpoint, $fileIndex, ".png"]),
                "name" => implode(" ", [$inputCollectionName, "#". $fileIndex])
            ];
            file_put_contents(implode("/", [OUTPUT_DIR, $fileIndex]), json_encode($traits));
        } else {
            echo PHP_EOL . $chalk->color74("\t[+]Image ${fileIndex} already exists - not overwritten!", PHP_EOL);
        }
        $fileIndex++;
    }
}