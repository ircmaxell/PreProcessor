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
class PreProcessor_Optimizers_InlineFunction extends PreProcessor_Optimizer {
    protected $inlineFunctions = array();

    public function parse(PreProcessor_File $file) {
        $this->findNewFunctions($file);
    }

    protected function findNewFunctions(PreProcessor_File $file) {
        $it = new ArrayIterator($file->getTokens());
        foreach ($it as $key => $token) {
            if (is_array($token) && $token[0] == T_STRING && $token[1] == 'inline') {
                $it->offsetUnset($key); //Remove inline from token sequence
                $this->parseFunctionBody($it);
            }
        }
    }

    protected function parseFunctionBody(ArrayIterator $it) {
        $name = '';
        $params = '';
        
    }
}
