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

use GanbaroDigital\Reflection\Requirements\RequireTraversable;
use GanbaroDigital\Reflection\ValueBuilders\FirstMethodMatchingType;
use GanbaroDigital\TokenStreams\Exceptions\E4xx_TokenProcessorDidNotReturnAnArray;
use GanbaroDigital\TokenStreams\Exceptions\E4xx_UnsupportedType;
use GanbaroDigital\TokenStreams\StreamTypes\TokenStream;
use GanbaroDigital\TokenStreams\Tokens\Token;
use GanbaroDigital\TokenStreams\Tokens\Tokeniser;
use GanbaroDigital\TokenStreams\Tokens\TokenProcessor;

class BasicStreamHead
{
    /**
     * an object that can convert a stream of text into a set of tokens
     *
     * @var Tokeniser
     */
    private $tokeniser;

    /**
     * a (possibly empty) list of token processors
     *
     * @var array<TokenProcessor>
     */
    private $tokenProcessors;

    /**
     * where we are going to send our final set of tokens to
     *
     * @var TokenStream
     */
    private $tokenWriter;

    /**
     * keep track of the state of our stream
     *
     * @var StreamState
     */
    private $streamState;

    /**
     * create our token stream
     *
     * @param StreamState  $state
     *        something to keep track of the state of the stream
     * @param Tokeniser    $tokeniser
     *        something to turn strings into tokens
     * @param array<TokenProcessor> $tokenProcessors
     *        a (possibly empty) list of objects to manipulate the tokens
     *        that we receive
     * @param TokenStream $tokenWriter
     *        where we send the final list of tokens to
     */
    public function __construct(StreamState $state, Tokeniser $tokeniser, $tokenProcessors, TokenStream $tokenWriter)
    {
        // robustness!
        RequireTraversable::check($tokenProcessors);

        // remember everything for later
        $this->streamState = $state;
        $this->tokeniser = $tokeniser;
        $this->tokenProcessors = $tokenProcessors;
        $this->tokenWriter = $tokenWriter;
    }

    /**
     * write an item of data to the stream
     *
     * if you know the type of the data you are writing, call the writeXXX()
     * method instead for better performance
     *
     * @param  mixed $data
     *         the data to write
     * @return void
     */
    public function write($data)
    {
        $methodName = FirstMethodMatchingType::from($data, $this, 'write', E4xx_UnsupportedType::class);
        return $this->{$methodName}($data);
    }

    /**
     * write an item of data to the stream
     *
     * if you know the type of the data you are writing, call the writeXXX()
     * method instead for better performance
     *
     * @param  mixed $data
     *         the data to write
     * @return void
     */
    public function __invoke($data)
    {
        $this->write($data);
    }

    /**
     * reset the stream, ready for a new set of output
     *
     * this tells the tokenizer to reset its internal state, because we are
     * about to start sending through a new set of output
     *
     * @return void
     */
    public function resetState()
    {
        $this->tokeniser->resetState($this->streamState);
    }

    /**
     * tokenise data, and write it to the stream
     *
     * this is an entry point to call from a public writeXXX method
     *
     * @param  mixed $data
     *         the data to tokenise and write to the stream
     * @return void
     */
    protected function tokeniseAndWriteToStream($data)
    {
        // tokenize the string
        $tokens = $this->tokeniser->tokenise($data, $this->streamState);
        RequireTraversable::check($tokens, E4xx_TokeniserDidNotReturnAnArray::class);

        // send the tokens down the stream
        $this->writeTokensToStream($tokens);
    }

    /**
     * send tokens to our token processors and the final token writer
     *
     * this is an entry point to call from a public writeXXX method
     *
     * @param  array $tokensToProcess
     *         the tokens to process
     * @return void
     */
    protected function writeTokensToStream($tokensToProcess)
    {
        // use the token processors
        $tokensToWrite = $this->processTokens($tokensToProcess);

        // at this point, we have a list of tokens to send to the
        // output writer
        foreach ($tokensToWrite as $token) {
            $this->tokenWriter->writeToken($token);
        }
    }

    /**
     * inspect a token, possibly transforming it into one or more different
     * tokens
     *
     * @param  array<Token> $tokens
     *         the tokens to process
     * @return array<Token>
     */
    private function processTokens($tokens)
    {
        $retval = $tokens;

        // work our way through the chain of token processors
        // we may end up with a completely different set of tokens at the end
        foreach ($this->tokenProcessors as $tokenProcessor) {
            $retval = $this->processTokensUsingProcessor($tokenProcessor, $retval);
        }

        // all done
        return $retval;
    }

    /**
     * process a set of tokens using a single token processor
     *
     * @param  TokenProcessor $tokenProcessor
     *         the token processor to use
     * @param  array<Token> $tokens
     *         the set of tokens to process
     * @return array<Token>
     *         the results of processing $tokens
     */
    private function processTokensUsingProcessor(TokenProcessor $tokenProcessor, array $tokens)
    {
        // our return value
        $retval = [];

        foreach ($tokens as $token) {
            $newTokens = $tokenProcessor->processToken($token, $this->streamState);
            RequireTraversable::check($newTokens, E4xx_TokenProcessorDidNotReturnAnArray::class);

            $retval = array_merge($retval, $newTokens);
        }

        // all done
        return $retval;
    }
}