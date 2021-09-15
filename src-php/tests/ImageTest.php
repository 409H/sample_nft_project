<?php
declare(strict_Types=1);
use PHPUnit\Framework\TestCase;

final class ImageTest extends TestCase
{
    /**
     * @test
     * Checks to ensure the raw images are named correctly
     * Files live in ../images_raw/*
     */
    public function testRawImageFileNamedCorrectly(): void 
    {
        $files = glob(__DIR__ ."/../../images_raw/*/*.*");
        foreach($files as $file) {
            $filename = basename($file);
            $this->assertMatchesRegularExpression('/^\w+\_\w+\_\w+\.png$/', $filename);
        }
    }
}