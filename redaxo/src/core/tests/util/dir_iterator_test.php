<?php

class rex_dir_iterator_test extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    parent::setUp();

    rex_file::put($this->getPath('file1.txt'), '');
    rex_file::put($this->getPath('file2.txt'), '');
    rex_file::put($this->getPath('dir1/file.txt'), '');
    rex_dir::create($this->getPath('dir1/dir'));
    rex_dir::create($this->getPath('dir2'));
  }

  public function tearDown()
  {
    parent::tearDown();

    rex_dir::delete($this->getPath());
  }

  public function getPath($file = '')
  {
    return rex_path::addonData('tests', 'rex_dir_iterator_test/' . $file);
  }

  public function testDefault()
  {
    $iterator = rex_dir::iterator($this->getPath());
    $array = iterator_to_array($iterator, true);
    $this->assertEquals(4, count($array), 'default iterator returns all elements of first level');
    $this->assertArrayHasKey($this->getPath('file1.txt'), $array, 'file1 is in array');
    $this->assertArrayHasKey($this->getPath('file2.txt'), $array, 'file2 is in array');
    $this->assertArrayHasKey($this->getPath('dir1'), $array, 'dir1 is in array');
    $this->assertArrayHasKey($this->getPath('dir2'), $array, 'dir2 is in array');
  }

  public function testIgnoreAllDirs()
  {
    $iterator = rex_dir::iterator($this->getPath())->ignoreDirs();
    $array = iterator_to_array($iterator, true);
    $this->assertEquals(2, count($array), 'ignoreDirs() returns only files');
    $this->assertArrayHasKey($this->getPath('file1.txt'), $array, 'file1 is in array');
    $this->assertArrayHasKey($this->getPath('file2.txt'), $array, 'file2 is in array');
  }

  public function testIgnoreSpecificDirs()
  {

    $iterator = rex_dir::iterator($this->getPath())->ignoreDirs(array('dir1'));
    $array = iterator_to_array($iterator, true);
    $this->assertEquals(3, count($array), 'ignoreDir() with array ignores specific dirs');
    $this->assertArrayHasKey($this->getPath('file1.txt'), $array, 'file1 is in array');
    $this->assertArrayHasKey($this->getPath('file2.txt'), $array, 'file2 is in array');
    $this->assertArrayHasKey($this->getPath('dir2'), $array, 'dir2 is in array');
  }

  public function testIgnoreAllFiles()
  {
    $iterator = rex_dir::iterator($this->getPath())->ignoreFiles();
    $array = iterator_to_array($iterator, true);
    $this->assertEquals(2, count($array), 'ignoreFiles() returns only dirs');
    $this->assertArrayHasKey($this->getPath('dir1'), $array, 'dir1 is in array');
    $this->assertArrayHasKey($this->getPath('dir2'), $array, 'dir2 is in array');
  }

  public function testIgnoreSpecificFiles()
  {
    $iterator = rex_dir::iterator($this->getPath())->ignoreFiles(array('file1.txt'));
    $array = iterator_to_array($iterator, true);
    $this->assertEquals(3, count($array), 'ignoreFiles() with array ignores specific files');
    $this->assertArrayHasKey($this->getPath('file2.txt'), $array, 'file2 is in array');
    $this->assertArrayHasKey($this->getPath('dir1'), $array, 'dir1 is in array');
    $this->assertArrayHasKey($this->getPath('dir2'), $array, 'dir2 is in array');
  }

  public function testIgnorePrefixes()
  {
    $iterator = rex_dir::iterator($this->getPath())->ignorePrefixes(array('file1', 'dir1'));
    $array = iterator_to_array($iterator, true);
    $this->assertEquals(2, count($array), 'ignorePrefixes() ignores files and dirs with the given prefixes');
    $this->assertArrayHasKey($this->getPath('file2.txt'), $array, 'file2 is in array');
    $this->assertArrayHasKey($this->getPath('dir2'), $array, 'dir2 is in array');
  }

  public function testIgnoreSuffixes()
  {
    $iterator = rex_dir::iterator($this->getPath())->ignoreSuffixes(array('1.txt', '1'));
    $array = iterator_to_array($iterator, true);
    $this->assertEquals(2, count($array), 'ignoreSuffixes ignores files and dirs with the given suffixes');
    $this->assertArrayHasKey($this->getPath('file2.txt'), $array, 'file2 is in array');
    $this->assertArrayHasKey($this->getPath('dir2'), $array, 'dir2 is in array');
  }
}
