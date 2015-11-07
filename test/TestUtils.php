<?php

/**
 * Utilities for testing.
 */
class TestUtils
{
    const PASSED_STRING = "\033[1;32mPASSED\033[0m\n";
    const FAILED_STRING = "\033[1;31mFAILED!\033[0m - ";

    /**
     * Convert given value into a human readable string.
     * @param mixed $value The value to convert
     * @return string
     */
    static private function convertValueToString($value)
    {
        if ( is_bool($value) ) {
            return $value ? 'true' : 'false';
        }
        elseif ( is_int($value) || is_float($value) ) {
            return $value;
        }
        return "'$value'";
    }

    /**
     * @param string $name The name of the test.
     * @param callable $func The function to execute.
     * @return bool
     * @throws Exception
     */
    static function test($name, $func, $andByResult = false, $expectedResult = null)
    {
        echo "\033[0;34mTesting \033[1;34m$name\033[0m: ";
        try {
            $result = $func();
            if ( $andByResult ) {
                if ( $expectedResult === $result ) {
                    echo self::PASSED_STRING;
                } else {
                    echo self::FAILED_STRING;
                    echo "\033[0;31mExpected " . self::convertValueToString($expectedResult) . " but got " . self::convertValueToString($result) . "\033[0m\n";
                    return false;
                }
            } else {
                echo self::PASSED_STRING;
            }
            return true;
        } catch (Exception $e) {
            echo self::FAILED_STRING;
            echo "\033[0;31m{$e->getMessage()}\033[0m\nin {$e->getFile()} on line {$e->getLine()}\n{$e->getTraceAsString()}\n";
            return false;
        }
    }

    static function checkFuncEqualsTo($funcName, $expectedResult, $funcArgs = [])
    {
        return self::test($funcName, function() use ($funcName, $funcArgs){
            return call_user_func_array($funcName, $funcArgs);
        }, true, $expectedResult );
    }

    static function checkFuncTrue($funcName, $funcArgs = [])
    {
        return self::checkFuncEqualsTo($funcName, true, $funcArgs);
    }

    static function checkFuncFalse($funcName, $funcArgs = [])
    {
        return self::checkFuncEqualsTo($funcName, false, $funcArgs);
    }

}