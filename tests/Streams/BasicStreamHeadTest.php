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
 * @package   TokenStreams/Streams
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-token-streams
 */

namespace GanbaroDigital\TokenStreams\Streams;

use GanbaroDigital\Reflection\ValueBuilders\FirstMethodMatchingType;
use GanbaroDigital\TokenStreams\StreamTypes\TokenStream;
use GanbaroDigital\TokenStreams\Tokens\Token;
use GanbaroDigital\TokenStreams\Tokens\Tokeniser;
use GanbaroDigital\UnitTestHelpers\ClassesAndObjects\InvokeMethod;

use PHPUnit_Framework_TestCase;

// pull in our shared example helper classes
require_once(__DIR__ . '/StreamHeadTestHelpers.inc.php');

class BasicStreamHeadTest_HeadWithStringSupport extends BasicStreamHead
{
    public function writeString($data)
    {
        $this->tokeniseAndWriteToStream($data);
    }
}

/**
 * @coversDefaultClass GanbaroDigital\TokenStreams\Streams\BasicStreamHead
 */
class BasicStreamHeadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
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

        $obj = new BasicStreamHead($state, $tokeniser, [], $writer);

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($obj instanceof BasicStreamHead);
    }

    /**
     * @covers ::write
     * @expectedException GanbaroDigital\TokenStreams\Exceptions\E4xx_UnsupportedType
     */
    public function testThrowsExceptionBecauseNoWritersSupported()
    {
        // ----------------------------------------------------------------
        // setup your test

        $state = new StreamState;
        $tokeniser = new StreamHeadTest_Tokeniser;
        $writer = new StreamHeadTest_TokenWriter;

        $stream = new BasicStreamHead($state, $tokeniser, [], $writer);

        // ----------------------------------------------------------------
        // perform the change

        $stream->write("I have no typesafe writers!");
    }

    /**
     * @covers ::write
     */
    public function testSupportsTypesafeWriters()
    {
        // ----------------------------------------------------------------
        // setup your test

        $state = new StreamState;
        $tokeniser = new StreamHeadTest_Tokeniser;
        $writer = new StreamHeadTest_TokenWriter;

        $stream = new BasicStreamHeadTest_HeadWithStringSupport($state, $tokeniser, [], $writer);

        $expectedResult = "I have a string typesafe writer!";

        // ----------------------------------------------------------------
        // perform the change

        $stream->write($expectedResult);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $writer->buffer);
    }

    /**
     * @covers ::tokeniseAndWriteToStream
     */
    public function testProvidesEntryMethodToTokeniseData()
    {
        // ----------------------------------------------------------------
        // setup your test

        $state = new StreamState;
        $tokeniser = new StreamHeadTest_Tokeniser;
        $writer = new StreamHeadTest_TokenWriter;

        $stream = new BasicStreamHeadTest_HeadWithStringSupport($state, $tokeniser, [], $writer);

        $expectedResult = "I have a string typesafe writer!";

        // ----------------------------------------------------------------
        // perform the change

        InvokeMethod::onObject($stream, 'tokeniseAndWriteToStream', [$expectedResult] );

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $writer->buffer);
    }

    /**
     * @covers ::writeTokensToStream
     */
    public function testProvidesEntryMethodForTokenisedData()
    {
        // ----------------------------------------------------------------
        // setup your test

        $state = new StreamState;
        $tokeniser = new StreamHeadTest_Tokeniser;
        $writer = new StreamHeadTest_TokenWriter;

        $stream = new BasicStreamHeadTest_HeadWithStringSupport($state, $tokeniser, [], $writer);

        $expectedResult = "I have a string typesafe writer!";
        $token = new StreamHeadTest_StringToken($expectedResult);

        // ----------------------------------------------------------------
        // perform the change

        InvokeMethod::onObject($stream, 'writeTokensToStream', [[$token]] );

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $writer->buffer);
    }

    /**
     * @covers ::processTokens
     * @covers ::processTokensUsingProcessor
     */
    public function testWillProcessTokens()
    {
        // ----------------------------------------------------------------
        // setup your test

        $state = new StreamState;
        $tokeniser = new StreamHeadTest_Tokeniser;
        $writer = new StreamHeadTest_TokenWriter;
        $processor = new StreamHeadTest_TokenDuplicator;

        $stream = new BasicStreamHeadTest_HeadWithStringSupport($state, $tokeniser, [$processor], $writer);

        $expectedResult = "I have a string typesafe writer!";
        $token = new StreamHeadTest_StringToken($expectedResult);

        $expectedTokens = [ $token, $token ];

        // ----------------------------------------------------------------
        // perform the change

        $actualTokens = InvokeMethod::onObject($stream, 'processTokens', [[$token]]);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedTokens, $actualTokens);
    }

    /**
     * @covers ::processTokens
     * @covers ::processTokensUsingProcessor
     */
    public function testSupportsMultipleTokenProcessors()
    {
        // ----------------------------------------------------------------
        // setup your test

        $state = new StreamState;
        $tokeniser = new StreamHeadTest_Tokeniser;
        $writer = new StreamHeadTest_TokenWriter;
        $processor1 = new StreamHeadTest_TokenDuplicator;
        $processor2 = new StreamHeadTest_TokenCounter;

        $stream = new BasicStreamHeadTest_HeadWithStringSupport($state, $tokeniser, [$processor1, $processor2], $writer);

        $expectedResult = "I have a string typesafe writer!";
        $token = new StreamHeadTest_StringToken($expectedResult);

        $expectedTokens = [ $token, $token, $token, $token ];

        // ----------------------------------------------------------------
        // perform the change

        $actualTokens = InvokeMethod::onObject($stream, 'processTokens', [[$token, $token]]);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedTokens, $actualTokens);
        $this->assertEquals(4, $state->tokensSeen);
    }

}