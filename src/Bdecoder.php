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
 * Bdecoder.
 */
class Bdecoder
{
    /**
     * Decode.
     *
     * @param  string     $data   The bencoded data that needs decoding.
     * @param  int        $offset Optional, the offset to start decoding from.
     * @return array|null         A list containing the length of the decoded data and the actual decoded data or null on failure.
     */
    public function decode(string $data, int $offset = 0) : ?array
    {
        $char = substr($data, $offset, 1);
        if ($char == 'd') {
            return $this->decodeDict($data, $offset);
        } elseif ($char == 'l') {
            return $this->decodeList($data, $offset);
        } elseif ($char == 'i') {
            return $this->decodeInt($data, $offset);
        } elseif (ctype_digit($char)) {
            return $this->decodeStr($data, $offset);
        } else {
            return null;
        }
    }

    /**
     * Decode dictionary.
     *
     * @param  string     $data   A bencoded dictionary.
     * @param  int        $offset The offset to start decoding from.
     * @return array|null         A list containing the length of the decoded dictionary and the dictionary itself or null on failure.
     */
    public function decodeDict(string $data, int $offset) : ?array
    {
        $start = substr($data, $offset, 1);
        $limit = substr($data, -1);
        if ($start != 'd' || $limit != 'e') {
            return null;
        }
        $offset++;
        $result = [];
        while ($data[$offset] != 'e') {
            $index = $this->decodeStr($data, $offset);
            if (! is_array($index)) {
                return null;
            } elseif (! isset($index[0]) || ! is_int($index[0])) {
                return null;
            } elseif (! isset($data[$index[0]])) {
                return null;
            } elseif (! isset($index[1]) || ! is_string($index[1])) {
                return null;
            } else {
                $offset = $index[0];
            }
            $value = $this->decode($data, $offset);
            if (! is_array($value)) {
                return null;
            } elseif (! isset($value[0]) || ! is_int($value[0])) {
                return null;
            } elseif (! isset($data[$value[0]])) {
                return null;
            } elseif (! isset($value[1])) {
                return null;
            } else {
                $offset = $value[0];
            }
            $result[$index[1]] = $value[1];
        }
        $offset++;
        return [$offset,$result];
    }

    /**
     * Decode list.
     *
     * @param  string     $data   A bencoded list.
     * @param  int        $offset The offset to start decoding from.
     * @return array|null         A list containing the length of the decoded list and the actual list or null on failure.
     */
    public function decodeList(string $data, int $offset) : ?array
    {
        $start = substr($data, $offset, 1);
        $limit = substr($data, -1);
        if ($start != 'l' || $limit != 'e') {
            return null;
        }
        $offset++;
        $result = [];
        while ($data[$offset] != 'e') {
            $value = $this->decode($data, $offset);
            if (! is_array($value)) {
                return null;
            } elseif (! isset($value[0]) || ! is_int($value[0])) {
                return null;
            } elseif (! isset($value[1])) {
                return null;
            } elseif (! isset($data[$value[0]])) {
                return null;
            } else {
                $offset = $value[0];
            }
            $result[] = $value[1];
        }
        $offset++;
        return [$offset,$result];
    }

    /**
     * Decode integer.
     *
     * @param  string     $data   A bencoded integer.
     * @param  int        $offset The offset to start decoding from.
     * @return array|null         A list containing the length of the decoded integer and the integer itself or null on failure.
     */
    public function decodeInt(string $data, int $offset) : ?array
    {
        $start = substr($data, $offset, 1);
        $limit = substr($data, -1);
        if ($start != 'i' || $limit != 'e') {
            return null;
        }
        $offset++;
        $buffer = '';
        while ($data[$offset] != 'e') {
            $buffer .= $data[$offset];
            $offset++;
        }
        $offset++;
        $intval = intval($buffer);
        if (0 !== strcmp($intval, $buffer)) {
            return null;
        }
        return [$offset,$intval];
    }

    /**
     * Decode string.
     *
     * @param  string     $data   A bencoded string.
     * @param  int        $offset The offset to start decoding from.
     * @return array|null         A list containing the length of the decoded string and the string itself or null on failure.
     */
    public function decodeStr(string $data, int $offset) : ?array
    {
        $start = $offset;
        for (;;) {
            if (! isset($data[$start])) {
                return null;
            } elseif ($data[$start] != ':') {
                $start++;
            } else {
                break;
            }
        }
        $length = $start - $offset;
        $buffer = substr($data, $offset, $length);
        $intval = intval($buffer);
        if (0 !== strcmp($buffer, $intval)) {
            return null;
        }
        $offset += $length + 1;
        $buffer = substr($data, $offset, $intval);
        $strlen = strlen($buffer);
        if ($strlen !== $intval) {
            return null;
        }
        $offset += $intval;
        return [$offset,$buffer];
    }
}

