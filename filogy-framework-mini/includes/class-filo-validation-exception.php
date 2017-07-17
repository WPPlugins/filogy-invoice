<?php
/**
 * Validation Exception
 *
 * @package     Filogy/Classes
 * @subpackage 	Framework
 * @category    Class
 * 
 */
class FILO_Validation_Exception extends Exception {
	
	/**
	 * Constructor
	 */
    public function __construct($message, $code = 0, Exception $previous = null) {
    
		wsl_log(null, 'FILO_Validation_Exception: ' . wsl_vartotext($message) . ' (code: ' . wsl_vartotext($code) . ')');		
		//wsl_log(null, 'class-filo-validation-exception.php FILO_Validation_Exception $this: ' . wsl_vartotext($this));
    
        parent::__construct($message, $code, $previous);

    }
	
} 
