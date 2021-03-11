<?php

namespace Templado\Cli;

use PHPUnit\Framework\TestCase;

/**
 * Class RequestTest
 * @package Templado\Cli
 *
 * @covers \Templado\Cli\Directory
 */
class DirectoryTest extends TestCase
{
    public function testCanBeCreated()
    {
        $this->assertInstanceOf(
            Directory::class,
            new Directory(__DIR__ . '/_data')
        );
    }

    public function testCanBeRetrieved()
    {
        $directory = new Directory(__DIR__ . '/_data');
        $this->assertEquals(__DIR__ . '/_data', $directory->asString());
    }

    public function testCantBeCreatedOnEmptyArgument()
    {
        $this->expectException(DirectoryException::class);
        new Directory('');
    }

    public function testCantBeCreatedOnNonExistingDirectory()
    {
        $this->expectException(DirectoryException::class);
        new Directory(__DIR__ . '/DirectoryTest.php');
    }

    public function testCanGetIterator()
    {
        $directory = new Directory(__DIR__ . '/_data');
        $this->assertInstanceOf(\Traversable::class, $directory->getIterator());
    }

    public function testCanClear()
    {
        $tempDir = __DIR__ . '/_fixture/deleteableFiles';

        //just to be sure we have a valid fixtuer setup
        if (!file_exists($tempDir . '/somefile.txt'))
            touch($tempDir . '/somefile.txt');
        $this->assertEquals(1, count(scandir($tempDir)) - 2);

        $directory = new Directory($tempDir);
        $directory->clear();

        $this->assertEquals(0, count(scandir($tempDir)) - 2);
    }
}
