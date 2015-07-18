<?php

/**
 * Copyright (c) 2015-present Ganbaro Digital Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   TokenStreams/TypesafeWriters
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-token-streams
 */

namespace GanbaroDigital\TokenStreams\TypesafeWriters;

require_once(__DIR__ . '/../Streams/StreamHeadTestHelpers.inc.php');

use GanbaroDigital\TokenStreams\Streams\BasicStreamHead;
use GanbaroDigital\TokenStreams\Streams\StreamState;
use GanbaroDigital\TokenStreams\Streams\StreamHeadTest_Tokeniser;
use GanbaroDigital\TokenStreams\Streams\StreamHeadTest_TokenWriter;
use GanbaroDigital\TokenStreams\StreamTypes\StringStream;
use PHPUnit_Framework_TestCase;

class WriteStringTest_StreamHead extends BasicStreamHead implements StringStream
{
    use WriteString;
}

/**
 * @coversDefaultClass GanbaroDigital\TokenStreams\TypesafeWriters\WriteString
 */
class WriteArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @coversNothing
     */
    public function testCanInstantiate()
    {
        // ----------------------------------------------------------------
        // setup your test

        $state = new StreamState;
        $tokeniser = new StreamHeadTest_Tokeniser;
        $writer = new StreamHeadTest_TokenWriter;

        // ----------------------------------------------------------------
        // perform the change

        $stream = new WriteStringTest_StreamHead($state, $tokeniser, [], $writer);

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($stream instanceof BasicStreamHead);
    }

    /**
     * @covers ::writeString
     */
    public function testCanWriteStringToStream()
    {
        // ----------------------------------------------------------------
        // setup your test

        $state = new StreamState;
        $tokeniser = new StreamHeadTest_Tokeniser;
        $writer = new StreamHeadTest_TokenWriter;

        $stream = new WriteStringTest_StreamHead($state, $tokeniser, [], $writer);

        $expectedResult = "I have a string typesafe writer";

        // ----------------------------------------------------------------
        // perform the change

        $stream->writeString($expectedResult);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $writer->buffer);
    }

    /**
     * @covers ::writeString
     */
    public function testCanWriteStringToStreamUsingGenericWriteMethod()
    {
        // ----------------------------------------------------------------
        // setup your test

        $state = new StreamState;
        $tokeniser = new StreamHeadTest_Tokeniser;
        $writer = new StreamHeadTest_TokenWriter;

        $stream = new WriteStringTest_StreamHead($state, $tokeniser, [], $writer);

        $expectedResult = "I have a string typesafe writer";

        // ----------------------------------------------------------------
        // perform the change

        $stream->write($expectedResult);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $writer->buffer);
    }
}