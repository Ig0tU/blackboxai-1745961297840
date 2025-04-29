<?php
use PHPUnit\Framework\TestCase;
use Voila\Mappers\Dictionary;

class DictionaryTest extends TestCase
{
    private $dictionary;

    protected function setUp(): void
    {
        $this->dictionary = new Dictionary();
    }

    public function testGetCommonTermFromWordPress()
    {
        $this->assertEquals('GET_POSTS', $this->dictionary->getCommonTermFromWordPress('wp_get_posts'));
        $this->assertNull($this->dictionary->getCommonTermFromWordPress('non_existent_function'));
    }

    public function testGetJoomlaFunction()
    {
        $this->assertEquals('getJoomlaPosts', $this->dictionary->getJoomlaFunction('GET_POSTS'));
        $this->assertNull($this->dictionary->getJoomlaFunction('NON_EXISTENT_TERM'));
    }
}
