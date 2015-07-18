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
use GanbaroDigital\TokenStreams\StreamTypes\MixedStream;
use PHPUnit_Framework_TestCase;
use stdClass;

class WriteMixedTest_StreamHead extends BasicStreamHead implements MixedStream
{
    use WriteMixed;
}

/**
 * @coversDefaultClass GanbaroDigital\TokenStreams\TypesafeWriters\WriteMixed
 */
class WriteMixedTest extends PHPUnit_Framework_TestCase
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

        $stream = new WriteMixedTest_StreamHead($state, $tokeniser, [], $writer);

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($stream instanceof BasicStreamHead);
    }

    /**
     * @covers ::writeMixed
     */
    public function testCanWriteMixedToStream()
    {
        // ----------------------------------------------------------------
        // setup your test

        $state = new StreamState;
        $tokeniser = new StreamHeadTest_Tokeniser;
        $writer = new StreamHeadTest_TokenWriter;

        $stream = new WriteMixedTest_StreamHead($state, $tokeniser, [], $writer);

        $expectedResult = 1234;

        // ----------------------------------------------------------------
        // perform the change

        $stream->writeMixed($expectedResult);
        $stream->writeMixed($expectedResult);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult . $expectedResult, $writer->buffer);
    }

    /**
     * @covers ::writeMixed
     */
    public function testCanWriteBooleanToStream()
    {
        // ----------------------------------------------------------------
        // setup your test

        $state = new StreamState;
        $tokeniser = new StreamHeadTest_Tokeniser;
        $writer = new StreamHeadTest_TokenWriter;

        $stream = new WriteMixedTest_StreamHead($state, $tokeniser, [], $writer);

        $expectedResult = 'truefalse';

        // ----------------------------------------------------------------
        // perform the change

        $stream->writeMixed(true);
        $stream->writeMixed(false);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $writer->buffer);
    }

    /**
     * @covers ::writeMixed
     */
    public function testCanWriteBooleanToStreamUsingGenericWriteMethod()
    {
        // ----------------------------------------------------------------
        // setup your test

        $state = new StreamState;
        $tokeniser = new StreamHeadTest_Tokeniser;
        $writer = new StreamHeadTest_TokenWriter;

        $stream = new WriteMixedTest_StreamHead($state, $tokeniser, [], $writer);

        $expectedResult = 'truefalse';

        // ----------------------------------------------------------------
        // perform the change

        $stream->write(true);
        $stream->write(false);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $writer->buffer);
    }

    /**
     * @covers ::writeMixed
     * @expectedException GanbaroDigital\TokenStreams\Exceptions\E4xx_UnsupportedType
     * @dataProvider provideNonMixedData
     */
    public function testThrowsExceptionWhenNonMixedDataWrittenToStream($data)
    {
        // ----------------------------------------------------------------
        // setup your test

        $state = new StreamState;
        $tokeniser = new StreamHeadTest_Tokeniser;
        $writer = new StreamHeadTest_TokenWriter;

        $stream = new WriteMixedTest_StreamHead($state, $tokeniser, [], $writer);

        // ----------------------------------------------------------------
        // perform the change

        $stream->writeMixed($data);
    }

    /**
     * @covers ::writeMixed
     * @expectedException GanbaroDigital\TokenStreams\Exceptions\E4xx_UnsupportedType
     * @dataProvider provideNonMixedData
     */
    public function testThrowsExceptionWhenNonMixedDataWrittenToStreamUsingGenericWriteMethod($data)
    {
        // ----------------------------------------------------------------
        // setup your test

        $state = new StreamState;
        $tokeniser = new StreamHeadTest_Tokeniser;
        $writer = new StreamHeadTest_TokenWriter;

        $stream = new WriteMixedTest_StreamHead($state, $tokeniser, [], $writer);

        // ----------------------------------------------------------------
        // perform the change

        $stream->write($data);
    }

    public function provideNonMixedData()
    {
        return [
            [ null ],
            [ [] ],
            [ new stdClass ],
            [ fopen("php://input", "r") ],
        ];
    }
}