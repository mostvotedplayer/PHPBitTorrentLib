<?php

use PHPUnit\Framework\TestCase;

class BdecoderTest extends TestCase
{
    public function setUp() : void
    { 
        include_once("src/Bdecoder.php");
    }

    public function testBdecodeEmptyDictionary()
    {
        $dictionary = 'de';
        $expect = [2, []];
        $actual = (new Bdecoder())->decode($dictionary);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeEmptyList()
    {
        $list = 'le';
        $expect = [2, []];
        $actual = (new Bdecoder())->decode($list);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeListOfIntegers()
    {
        $list = 'li1ei2ei3ei4ei5ee';
        $expect = [17, [1,2,3,4,5]];
        $actual = (new Bdecoder())->decode($list);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeListOfStrings()
    {
        $list = 'l3:foo3:bare';
        $expect = [12, ['foo','bar']];
        $actual = (new Bdecoder())->decode($list);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeDictionary()
    {
        $dictionary = 'd3:keyi1e4:test0:e';
        $expect = [18, [
            'key'  => 1,
            'test' => null
        ]];
        $actual = (new Bdecoder())->decode($dictionary);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeInvalidDictionary()
    {
        $dictionary = 'di4ei4ee';
        $expect = null;
        $actual = (new Bdecoder())->decode($dictionary);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeBrokenDictionary()
    {
        $dictionary = 'd';
        $expect = null;
        $actual = (new Bdecoder())->decode($dictionary);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeBrokenList()
    {
        $list = 'l';
        $expect = null;
        $actual = (new Bdecoder())->decode($list);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodePositiveInteger()
    {
        $integer = 'i1024e';
        $expect = [6, 1024];
        $actual = (new Bdecoder())->decode($integer);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeNegativeInteger()
    {
        $integer = 'i-4096e';
        $expect = [7, -4096];
        $actual = (new Bdecoder())->decode($integer);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeNegativeZero()
    {
        $integer = 'i-0e';
        $expect = null;
        $actual = (new Bdecoder())->decode($integer);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeDouble()
    {
        $double = 'i1.5e';
        $expect = null;
        $actual = (new Bdecoder())->decodeInt($double, 0);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeNull()
    {
        $string = '0:';
        $expect = [2, null];
        $actual = (new Bdecoder())->decode($string);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeString()
    {
        $string = '4:spam';
        $expect = [6, 'spam'];
        $actual = (new Bdecoder())->decode($string);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeInvalidString()
    {
        $string = '-1:ab';
        $expect = null;
        $actual = (new Bdecoder())->decode($string);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeFromInvalidOffset()
    { 
        $list = 'li1024ei2048ee';
        $expect = null;
        $actual = (new Bdecoder())->decode($list, 100);
        $this->assertEquals($actual, $expect);
    }

    public function testBdecodeFromValidOffset()
    {
        $list = 'l3:leee';
        $expect = [6, 'lee'];
        $actual = (new Bdecoder())->decode($list, 1);
        $this->assertEquals($actual, $expect);
    }
}
