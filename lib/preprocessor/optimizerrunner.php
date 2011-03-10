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
class PreProcessor_OptimizerRunner {
    protected $optimizers = array();

    public function __construct(array $optimizers) {
        foreach ($optimizers as $optimizer) {
            $this->addOptimizer($optimizer);
        }
    }

    public function addOptimizer(PreProcessor_Optimizer $optimizer) {
        $this->optimizers[] = $optimizer;
    }
    
    public function parse(PreProcessor_File $file) {
        foreach ($this->optimizers as $optimizer) {
            $optimizer->parse($file);
        }
    }


}
