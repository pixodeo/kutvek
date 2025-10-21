<?php
namespace Core\Interfaces;

interface OptionInterface
{
    public function getOption(string $option, $product, $geoloc): object;
}