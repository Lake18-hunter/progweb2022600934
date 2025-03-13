<?php
class persona {
    private $nombre;
    private $fecNac;
    private $tel;
    
    public function __construct($nombre, $fecNac, $tel) {
        $this->nombre = $nombre;
        $this->fecNac = $fecNac;
        $this->tel = $tel;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getFecNac() {
        return $this->fecNac;
    }

    public function setFecNac($value) {
        $this->fecNac = $value;
    }

    public function getTel() {
        return $this->tel;
    }

    public function setTel($value) {
        $this->tel = $value;
    }
}

