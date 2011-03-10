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
class PreProcessor_State {
    /**
     * Continue storing tokens until next pre-processor hook
     */
    const STATE_CONTINUE = 1;
    /**
     * Skip tokens until next pre-processor hook
     */
    const STATE_SKIP     = 2;
    /**
     * Skip all pre-processor tokens until given token appears
     */
    const STATE_SKIP_UNTIL = 3;

    protected $nextToken = array();

    protected $state = self::STATE_CONTINUE;

    public function getNextToken() {
        return $this->nextToken;
    }

    public function getState() {
        return $this->state;
    }

    public function setNextToken(closure $token) {
        $this->nextToken = $token;
    }

    public function setState($state) {
        $this->state = $state;
    }



}
