<?php
/**
 * The basic PHP preprocessor masterclass
 *
 * PHP Version 5.2
 *
 * @category
 * @package
 * @subpackage
 */

/**
 * The basic PHP preprocessor masterclass
 *
 * @category   
 * @package
 * @subpackage
 */
class PreProcessor {

    protected static $instance = null;

    protected $instructions = array();
    protected $loadedFiles = array();

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new PreProcessor();
        }
        return self::$instance;
    }

    protected function __construct() {
        clearstatcache();
    }

    public function _include($file) {
        if (!isset($this->loadedFiles[$file])) {
            $this->load($file);
        }
        include $this->loadedFiles[$file];
    }

    public function _include_once($file) {
        if (!isset($this->loadedFiles[$file])) {
            $this->load($file);
        }
        include_once $this->loadedFiles[$file];
    }

    public function _require($file) {
        if (!isset($this->loadedFiles[$file])) {
            $this->load($file);
        }
        require $this->loadedFiles[$file];
    }

    public function _require_once($file) {
        if (!isset($this->loadedFiles[$file])) {
            $this->load($file);
        }
        require_once $this->loadedFiles[$file];
    }

    public function addInstruction(PreProcessor_Instruction $instruction) {
        $this->instructions[] = $instruction;
    }

    protected function load($file) {
        if (!file_exists($file)) {
            $this->loadedFiles[$file] = $file;
        } elseif (file_exists($file.'c') && filemtime($file) < filemtime($file.'c')) {
            $this->loadedFiles[$file] = $file . 'c';
        } else {
            //process file!
            $processor = new PreProcessor_File($file, $this->instructions);
            $newFile = $processor->process();
            $this->loadedFiles[$file] = $newFile;
        }
    }

}
