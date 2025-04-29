<?php
namespace Voila\Core;

abstract class Converter
{
    protected $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    abstract public function migrate();
}
