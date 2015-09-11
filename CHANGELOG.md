# CHANGELOG

## develop branch

Nothing yet.

## 1.0.2 - Fri Sep 11 2015

### Fixes

* TypesafeWriters\WriteMixed - deprecated; replace with WriteEverythingElse instead
* Switched all calls to xxxMixed() in other packages for xxx() - xxxMixed() is now deprecated

## 1.0.1 - Sun Jul 25 2015

### Fixes

* Wrong package name in composer.json :(

## 1.0.0 - Sat Jul 18 2015

Initial release.

### New

* Exceptions\Exxx_TokenStreamException - base class for all exceptions thrown by this library
* Exceptions\E4xx_TokenStreamException - base class for all 'bad input' exceptions thrown by this library
* Exceptions\E4xx_UnsupportedType - exception thrown when a method parameter is a bad type
* Streams\BasicStreamHead - a stream head for you to extend with the typesafe writers of your choice
* Streams\StreamState - container for data about the stream
* StreamTypes\ArrayStream - interface for streams that accept an array as input
* StreamTypes\BooleanStream - interface for streams that accept a boolean as input
* StreamTypes\MixedStream - interface for streams that accept a number as input
* StreamTypes\StringStream - interface for streams that accept a string as input
* StreamTypes\TokenStream - interface for streams that accept tokens as input
* Tokens\Token - interface for a token to implement
* Tokens\Tokeniser - interface for a tokeniser to implement
* Tokens\TokenProcessor - interface for a token processor to implement
* TypesafeWriters\WriteArray - trait for streams that accept an array as input
* TypesafeWriters\WriteBoolean - trait for streams that accept a boolean as input
* TypesafeWriters\WriteMixed - trait for streams that accept numbers as input
* TypesafeWriters\WriteString - trait for streams that accept a string as input
* TypesafeWriters\WriteToken - trait for streams that accept tokens as input
