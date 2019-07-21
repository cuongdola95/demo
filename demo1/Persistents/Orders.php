<?php

class Persistents_Orders extends Persistents_Core {
    
    private $id           = 0;
    public $user_id          = 0;
    public $note = '';
    public $time          = 0; 
    public $orders        = 0;
    public $status        = 0; 

    /**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}
    
    function getClassName() {
        return __CLASS__;
    }
}