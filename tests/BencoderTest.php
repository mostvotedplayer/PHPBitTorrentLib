<?php

use PHPUnit\Framework\TestCase;
use BitTorrent\Encoding\Bencoder;

class BencoderTest extends TestCase
{
    public function testBencodeEmptyArray()
    {
        $array = [];
        $expect = 'le';
        $actual = (new Bencoder())->encode($array);
        $this->assertEquals($expect, $actual);
    }

    public function testBencodeList()
    {
        $list = [1,2,3,4,5];
        $expect = 'li1ei2ei3ei4ei5ee';
        $actual = (new Bencoder())->encode($list);
        $this->assertEquals($expect, $actual);
    }

    public function testBencodeDict()
    {
        $dict = ['apples' => 1, 'pears' => 2];
        $expect = 'd6:applesi1e5:pearsi2ee';
        $actual = (new Bencoder())->encode($dict);
        $this->assertEquals($expect, $actual);
    }

    public function testBencodeMixedArray()
    {
        $mixed = ['apples' => 1, 2];
        $expect = null;
        $actual = (new Bencoder())->encode($mixed);
        $this->assertEquals($expect, $actual);   
    }

    public function testBencodeSortList()
    {
        $list = [5 => 5, 4 => 4, 1 => 1, 2 => 2, 3 => 3]; 
        $expect = 'li1ei2ei3ei4ei5ee';
        $actual = (new Bencoder())->encode($list);
        $this->assertEquals($expect, $actual);
    }

    public function testBencodeSortDict()
    {
        $dict = ['orange' => 1, 'apples' => 1];
        $expect = 'd6:applesi1e6:orangei1ee';
        $actual = (new Bencoder())->encode($dict);
        $this->assertEquals($expect, $actual);
    }

    public function testBencodeNegativeZero()
    {
        $integer = -0;
        $expect = 'i0e';
        $actual = (new Bencoder())->encode($integer);
        $this->assertEquals($expect, $actual);
    }

    public function testBencodeNegativeInteger()
    {
        $integer = -4096;
        $expect = 'i-4096e';
        $actual = (new Bencoder())->encode($integer);
        $this->assertEquals($expect, $actual);
    }

    public function testBencodeDouble()
    {
        $integer = PHP_INT_MAX + 1;
        $expect = null;
        $actual = (new Bencoder())->encode($integer);
        $this->assertEquals($expect, $actual);
    }

    public function testBencodeNull()
    {
        $value = null;
        $expect = '0:';
        $actual = (new Bencoder())->encode($value);
        $this->assertEquals($expect, $actual);
    }

    public function testBencodeString()
    {
        $string = 'foo';
        $expect = '3:foo';
        $actual = (new Bencoder())->encode($string);
        $this->assertEquals($expect, $actual);
    }
}
