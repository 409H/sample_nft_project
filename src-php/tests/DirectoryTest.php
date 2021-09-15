<?php
declare(strict_Types=1);
use PHPUnit\Framework\TestCase;

final class DirectoryTest extends TestCase
{
    /**
     * @test
     * Checks to ensure the raw image dir exist
     * Files live in ../images_raw/*
     */
    public function testRawImageDirExists(): void 
    {
        $filename = __DIR__ ."/../../images_raw";
        $this->assertDirectoryExists(
            $filename,
            "directory ./images_raw does not exist in proj root"
        );
    }

    /**
     * @test
     * Checks to ensure the raw images directory is readable 
     * Files live in ../images_raw/*
     */
    public function testRawImageDirIsReadable(): void 
    {
        $dirname = __DIR__ ."/../../images_raw";
        $this->assertDirectoryIsReadable(
            $dirname
        );
    }

    /**
     * @test
     * Checks to ensure the processed image dir exist
     * Files live in ../images_processed/*
     */
    public function testProcessedImageDirExists(): void 
    {
        $filename = __DIR__ ."/../../images_processed";
        $this->assertDirectoryExists(
            $filename,
            "directory ./images_processed does not exist in proj root"
        );
    }

    /**
     * @test
     * Checks to ensure the processed images directory is writeable 
     * Files live in ../images_processed/*
     */
    public function testProcessedImageDirIsWriteable(): void 
    {
        $dirname = __DIR__ ."/../../images_processed";
        $this->assertDirectoryIsWritable(
            $dirname
        );
    }
}