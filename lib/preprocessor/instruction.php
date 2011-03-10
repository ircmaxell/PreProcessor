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
abstract class PreProcessor_Instruction {

    abstract public function getParsableTokens();

    abstract public function processToken($token, ArrayIterator $it, PreProcessor_State $state);

}
