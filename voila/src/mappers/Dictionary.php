<?php
namespace Voila\Mappers;

class Dictionary
{
    private $commonTerms = [];

    public function __construct()
    {
        // Load mappings from the common.json file
        $commonFile = __DIR__ . '/../config/common.json';
        $jsonData = file_get_contents($commonFile);
        $elements = json_decode($jsonData, true);

        // Build common terms dictionary from common.json
        foreach ($elements as $element) {
            $name = $element['name'] ?? null;
            if ($name) {
                $this->commonTerms[$name] = $element;
            }
        }
    }

    public function getElementByName($name)
    {
        return $this->commonTerms[$name] ?? null;
    }

    // Additional methods can be added to map between WordPress and Joomla using this common dictionary
}
