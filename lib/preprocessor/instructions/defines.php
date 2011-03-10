<?php
/**
 *
 *
 * PHP Version 5.2
 *
 * @category
 * @package
 * @subpackage
 */

/**
 *
 *
 * @category   
 * @package
 * @subpackage
 */
class PreProcessor_Instructions_Defines extends PreProcessor_Instruction {

    public function getParsableTokens() {
        return array(
            T_COMMENT => function($token, PreProcessor_State $state) {
                if ($token[1][0] != '#') return false;
                $parts = explode(' ', trim(substr($token[1], 1)), 2);
                return strtolower($parts[0]) == 'define';
            },
        );
    }

    public function processToken($token, ArrayIterator $it, PreProcessor_State $state) {
        $command = explode(' ', trim(substr($token[1], 1)), 2);
        $command[0] = strtolower(trim($command[0]));
        if (!isset($command[1])) {
            $command[1] = '';
        }
        $parts = explode(' ', $command[1], 2);
        $name = $parts[0];
        $value = $parts[1];
        define($name, $value);
        return array(
            array(T_STRING, 'define', 1),
            '(',
            array(T_CONSTANT_ENCAPSED_STRING, "'".addcslashes($name, "'")."'", 1),
            ',',
            array(T_CONSTANT_ENCAPSED_STRING, "'".addcslashes($value, "'")."'", 1),
            ')',
            ";",
        );
    }

}
