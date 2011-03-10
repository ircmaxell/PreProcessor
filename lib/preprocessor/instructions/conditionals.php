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
 * Support for the following conditional statements
 *   #define
 *   #if
 *   #ifdef
 *   #ifndef
 *   #else
 *   #elif
 *   #endif
 *
 * @category   
 * @package
 * @subpackage
 */
class PreProcessor_Instructions_Conditionals extends PreProcessor_Instruction {

    const STATE_CONTINUE = 1; //Continue parsing
    const STATE_SKIP     = 2; //Skip to next conditional
    const STATE_SKIP_END = 3; //Skip to endif

    public static $commands = array(
        'if',
        'ifdef',
        'ifndef',
        'else',
        'elif',
        'endif',
    );

    public function getParsableTokens() {
        return array(
            T_COMMENT => function($token, PreProcessor_State $state) {
                if ($token[1][0] != '#') return false;
                $parts = explode(' ', trim(substr($token[1], 1)), 2);
                return in_array(strtolower($parts[0]), PreProcessor_Instructions_Conditionals::$commands);
            },
        );
    }

    public function processToken($token, ArrayIterator $it, PreProcessor_State $state) {
        $command = explode(' ', trim(substr($token[1], 1)), 2);
        $command[0] = strtolower(trim($command[0]));
        if (!isset($command[1])) {
            $command[1] = '';
        }
        $method = 'do'.$command[0];
        return $this->$method($command[1], $state);
    }
  
    protected function doElse($args, PreProcessor_State $state) {
        if ($state->getState() == PreProcessor_State::STATE_CONTINUE) {
            $state->setState(PreProcessor_State::STATE_SKIP_UNTIL);
            $state->setNextToken(function($token) {
                if (!is_array($token) || $token[0] != T_COMMENT) {
                    return false;
                }
                $t = strtolower(trim($token[1]));
                list ($stub,) = explode(' ', $t, 2);
                return in_array($stub, array('#endif'));
            });
        } else {
            $state->setState(PreProcessor_State::STATE_CONTINUE);
        }
        return false;
    }
    
    protected function doElif($args, PreProcessor_State $state) {
        if ($state->getState() == PreProcessor_State::STATE_CONTINUE) {
            $state->setState(PreProcessor_State::STATE_SKIP_UNTIL);
            $state->setNextToken(function($token) {
                if (!is_array($token) || $token[0] != T_COMMENT) {
                    return false;
                }
                $t = strtolower(trim($token[1]));
                list ($stub,) = explode(' ', $t, 2);
                return in_array($stub, array('#endif'));
            });
        } else {
            $test = @eval('return ' . $args . ';');
            if ($test) {
                $state->setState(PreProcessor_State::STATE_CONTINUE);
            }
        }
        return false;
    }

    protected function doEndIf($args, PreProcessor_State $state) {
        $state->setState(PreProcessor_State::STATE_CONTINUE);
        return false;
    }

    protected function doIf($args, PreProcessor_State $state) {
        $test = @eval('return ' . $args . ';');
        if ($test) {
            $state->setState(PreProcessor_State::STATE_CONTINUE);
        } else {
            $state->setState(PreProcessor_State::STATE_SKIP_UNTIL);
            $state->setNextToken(function($token) {
                if (!is_array($token) || $token[0] != T_COMMENT) {
                    return false;
                }
                $t = strtolower(trim($token[1]));
                list ($stub,) = explode(' ', $t, 2);
                return in_array($stub, array('#else', '#elif', '#endif'));
            });
        }
        return false;
    }

    protected function doIfDef($args, PreProcessor_State $state) {
        $parts = explode(' ', $args, 2);
        if (defined(trim($parts[0]))) {
            $state->setState(PreProcessor_State::STATE_CONTINUE);
        } else {
            $state->setState(PreProcessor_State::STATE_SKIP_UNTIL);
            $state->setNextToken(function($token) {
                if (!is_array($token) || $token[0] != T_COMMENT) {
                    return false;
                }
                $t = strtolower(trim($token[1]));
                list ($stub,) = explode(' ', $t, 2);
                var_dump($stub, $token[1]);
                return in_array($stub, array('#else', '#elif', '#endif'));
            });
        }
        return false;
    }

    protected function doIfNDef($args, PreProcessor_State $state) {
        $parts = explode(' ', $args, 2);
        if (!defined(trim($parts[0]))) {
            $state->setState(PreProcessor_State::STATE_SKIP_UNTIL);
            $state->setNextToken(function($token) {
                if (!is_array($token) || $token[0] != T_COMMENT) {
                    return false;
                }
                $t = strtolower(trim($token[1]));
                list ($stub, $dump) = explode(' ', $t, 2);
                return in_array($stub, array('#else', '#elif', '#endif'));
            });
        } else {
            $state->setState(PreProcessor_State::STATE_CONTINUE);
        }
        return false;
    }

}
