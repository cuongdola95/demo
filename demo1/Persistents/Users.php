<?php
include 'Core.php';
class Persistents_Users extends Persistents_Core {
    
    private $id           = 0;
    public $name          = '';
    public $phone         = '';
    public $orders        = 0;
    public $status        = 0; 

    function getId() {
    	return $this->id;
    }

    function getClassName() {
        return __CLASS__;
    }
}