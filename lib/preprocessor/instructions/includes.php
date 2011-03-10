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
class PreProcessor_Instructions_Includes extends PreProcessor_Instruction {

    public function getParsableTokens() {
        return array(
            T_INCLUDE       => true,
            T_INCLUDE_ONCE  => true,
            T_REQUIRE       => true,
            T_REQUIRE_ONCE  => true,
        );
    }

    public function processToken($token, ArrayIterator $it, PreProcessor_State $state) {
        $newTokens = array();
        $newTokens[] = array(T_STRING, 'PreProcessor', 1);
        $newTokens[] = array(T_PAAMAYIM_NEKUDOTAYIM, '::', 1);
        $newTokens[] = array(T_STRING, 'instance', 1);
        $newTokens[] = '(';
        $newTokens[] = ')';
        $newTokens[] = array(T_OBJECT_OPERATOR, '->', 1);
        $newTokens[] = array(T_STRING, '_' . strtolower($token[1]), 1);
        do {
            $it->next();
            $cur = $it->current();
        } while (is_array($cur) && $cur[0] == T_WHITESPACE);
        if (is_array($it->current())) {
            $newTokens[] = '(';
            while ($it->current() != ';') {
                $newTokens[] = $it->current();
                $it->next();
            }
            $newTokens[] = ')';
        } else {
            while ($it->current() != ';') {
                $newTokens[] = $it->current();
                $it->next();
            }
        }
        $newTokens[] = ';';
        return $newTokens;
    }
    
}
