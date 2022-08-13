<?php
/**
* MIT License
*
* Copyright (c) 2022 Lee Howarth
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*/

/**
 * Bencoder.
 */
class Bencoder
{
     /**
      * Encode.
      *
      * @param  mixed       $data The array, integer, string or null value to encode.
      * @return string|null       A bencoded string or null on failure.
      */
     public function encode($data) : ?string
     {
         $dataType = getType($data);
         switch ($dataType) {
             case 'array':
                 return $this->encodeArr($data);
             case 'integer':
                 return $this->encodeInt($data);
             case 'string':
                 return $this->encodeStr($data);
             case 'NULL':
                 return $this->encodeStr('');
             default:
                 return null;
         }
     }

     /**
      * Encode array.
      *
      * @param  array       $data An array of data.
      * @return string|null       A bencoded dictionary, A bencoded list or null on failure.
      */
     public function encodeArr(array $data) : ?string
     {
         $index = key($data);
         if (is_string($index)) {
             ksort($data, SORT_STRING);
             $isDict = true;
             $isList = false;
          } elseif (is_integer($index)) {
             ksort($data, SORT_NUMERIC);
             $isDict = false;
             $isList = true;
          } else {
             $isDict = false;
             $isList = true;
          }

          $buffer = '';
          foreach ($data as $index => $value) {
              if ($isDict) {
                  if (is_integer($index)) {
                      return null;
                  }
                  $buffer .= $this->encodeStr($index);
              } else {
                  if (is_string($index)) {
                      return null;
                  }
              }
              $value = $this->encode($value);
              if ($value === null) {
                  return null;
              }
              $buffer .= $value;
         }

         if ($isList) {
             $buffer = 'l' . $buffer . 'e';
         } else {
             $buffer = 'd' . $buffer . 'e';
         }
         return $buffer;
     }

     /**
      * Encode integer.
      *
      * @param  integer $data A integer value.
      * @return string        A bencoded string representing the integer value.
      */
     public function encodeInt(int $data) : string
     {
         return 'i' . $data . 'e';
     }

     /**
      * Encode string.
      *
      * @param  string $data A string value.
      * @return string       A bencoded string representing the string value.
      */
     public function encodeStr(string $data) : string
     {
         return strlen($data) . ':' . $data;
     }
}

