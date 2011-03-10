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
class PreProcessor_InstructionRunner {
    protected $instructions = array();
    protected $search = array();

    public function __construct(array $instructions) {
        foreach ($instructions as $instruction) {
            $this->addInstruction($instruction);
        }
    }

    public function addInstruction(PreProcessor_Instruction $instruction) {
        $this->instructions[] = $instruction;
        $tmp = $instruction->getParsableTokens();
        foreach ($tmp as $key => $value) {
            if (!isset($this->search[$key])) {
                $this->search[$key] = array();
            }
            $this->search[$key][] = array($value, $instruction);
        }
    }
    
    public function processToken($token, ArrayIterator $it, PreProcessor_State $state) {
        if (is_array($token)) {
            if ($state->getState() == PreProcessor_State::STATE_SKIP_UNTIL) {
                $callback = $state->getNextToken();
                if (!$callback($token)) {
                    return false;
                }
            }
            if (isset($this->search[$token[0]])) {
                foreach ($this->search[$token[0]] as $tmp) {
                    list($test, $instruction) = $tmp;
                    if ($test === true || (is_callable($test) && $test($token, $state))) {
                        return $instruction->processToken($token, $it, $state);
                    }
                }
            }
        }
        if ($state->getState() == PreProcessor_State::STATE_CONTINUE) {
            return array($token);
        } else {
            return false;
        }
    }

}
