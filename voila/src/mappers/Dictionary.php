<?php
namespace Voila\Mappers;

class Dictionary
{
    private $commonTerms = [];
    private $wpToCommon = [];
    private $joomlaToCommon = [];

    public function __construct()
    {
        // Load mappings from JSON files or define here
        $this->wpToCommon = json_decode(file_get_contents(__DIR__ . '/../config/wp-functions.json'), true);
        $this->joomlaToCommon = json_decode(file_get_contents(__DIR__ . '/../config/joomla-functions.json'), true);

        // Build common terms dictionary
        foreach ($this->wpToCommon as $wpFunc => $commonTerm) {
            $this->commonTerms[$commonTerm]['wordpress'] = $wpFunc;
        }
        foreach ($this->joomlaToCommon as $joomlaFunc => $commonTerm) {
            $this->commonTerms[$commonTerm]['joomla'] = $joomlaFunc;
        }
    }

    public function getCommonTermFromWordPress($wpFunc)
    {
        return $this->wpToCommon[$wpFunc] ?? null;
    }

    public function getCommonTermFromJoomla($joomlaFunc)
    {
        return $this->joomlaToCommon[$joomlaFunc] ?? null;
    }

    public function getWordPressFunction($commonTerm)
    {
        return $this->commonTerms[$commonTerm]['wordpress'] ?? null;
    }

    public function getJoomlaFunction($commonTerm)
    {
        return $this->commonTerms[$commonTerm]['joomla'] ?? null;
    }
}
