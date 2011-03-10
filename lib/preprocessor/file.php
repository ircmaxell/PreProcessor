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
class PreProcessor_File {

    protected $file = '';
    protected $instructions = null;
    protected $optimizers = null;
    protected $rawFile = '';
    protected $state = null;
    protected $tokens = array();

    public function __construct($file, PreProcessor_InstructionRunner $instructions, PreProcessor_OptimizerRunner $optimizers) {
        $this->file = $file;
        $this->instructions = $instructions;
        $this->optimizers = $optimizers;
        $this->rawFile = file_get_contents($file);
        $tokens = token_get_all($this->rawFile);
        foreach ($tokens as $token) {
            if (!is_array($token) || $token[0] != T_WHITESPACE) {
                $this->tokens[] = $token;
            } elseif ($token[1][0] == ' ') {
                $this->tokens[] = array(T_WHITESPACE, ' ', 1);
            }
        }
        $this->state = new PreProcessor_State;
    }

    public function getState() {
        return $this->state;
    }

    public function getTokens() {
        return $this->tokens;
    }

    public function process() {
        $newTokens = array();
        $it = new ArrayIterator($this->tokens);
        foreach ($it as $token) {
            $tmp = $this->instructions->processToken($token, $it, $this->state);
            if (is_array($tmp)) {
                foreach ($tmp as $value) {
                    $newTokens[] = $value;
                }
            } elseif ($tmp !== false) {
                $newTokens[] = $tmp;
            }
        }
        $this->tokens = $newTokens;
        $this->optimizers->parse($this);
        $fileData = $this->writeTokens();
        return $fileData;
    }

    public function setTokens(array $tokens) {
        $this->tokens = $tokens;
    }

    protected function writeTokens() {
        $result = '';
        foreach ($this->tokens as $token) {
            if (is_array($token)) {
                $result .= $token[1];
            } else {
                $result .= $token;
            }
        }
        return $result;
    }
}
