<?php

class Converter
{
    private string $fileOne;
    private string $fileTwo;

    private $fileOneArray;
    private $fileTwoArray;

    public $newFileArray;

    public function __construct(string $fileOne, string $fileTwo)
    {
        $this->fileOne = $fileOne;
        $this->fileTwo = $fileTwo;
        $this->CheckIsSet();
    }

    private function CheckIsSet()
    {
        $fileOneExplode = explode("/", $this->fileOne);
        $fileTwoExplode = explode("/", $this->fileTwo);
        if (count($fileOneExplode) >= 1 && count($fileTwoExplode) >= 1) {
            $fileOneParts = pathinfo($this->fileOne);
            $fileTwoParts = pathinfo($this->fileTwo);
            
            if ($fileOneParts['extension'] == 'json' && $fileTwoParts['extension'] == 'json') {
                $this->OpenFiles();
            } else {
                print "Błędne rozrzeżenie plików wybierz pliki .json";
            }
        }
    }
    
   

    private function OpenFiles()
    {
        if (($json = file_get_contents($this->fileTwo)) == false) {
            die('Error reading json file...');
        }
        $this->fileTwoArray = json_decode($json, true);

        if (($json = file_get_contents($this->fileOne)) == false) {
            die('Error reading json file...');
        }
        $this->fileOneArray = json_decode($json, true);
        
        $arr = $this->flattenArray($this->fileTwoArray);
        $myfile = fopen("newFile.json", "w") or die("Unable to open file!");
            fwrite($myfile, json_encode($arr));
            fclose($myfile);
    }

    public function flattenArray($array)
    {
        static $flattened = [];
        static $data = []; 
        if (is_array($array) && count($array) > 0) {
            foreach ($array as $member) {
                if (!is_array($member)) {
                    foreach ($this->fileOneArray as $key2 => $value) {
                        if ($member == $value['category_id']) {
                            $data = ['id' => $value['category_id'], 'name' => $value['translations']['pl_PL']['name'] , 'children'=> []];
                            print_r($data);
                            $flattened[] = $data;
                        }
                    }
                    
                   
                } else {
                    $this->flattenArray($member);
                }
            }
        }

        return $flattened;
    }
}

if (isset($argv[1]) || isset($argv[2])) {
    new Converter($argv[1], $argv[2]);
} else {
    print "Brak podanej lokalizacji plików.";
}
