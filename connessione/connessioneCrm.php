<?php

class ConnessioneCrm {

    protected $link;
    private $_stato = false;
    private $_host = 'crm2.novaholding.it';
    private $_port = '3306';
    private $_pwd = 'RW6YUH3UDULCWRUv';
    private $_userDB = 'knowage';
    private $_nomedb = 'c1vtiger';

    public function apriConnessioneCrm() {
        $this->link = mysqli_connect($this->_host, $this->_userDB, $this->_pwd);
        if (mysqli_connect_errno()) {
            die("Errore nella connessione:" . mysqli_connect_errno());
            exit();
        } else {
            $this->_stato = true;
            mysqli_select_db($this->link, $this->_nomedb) or die("Errore nella selezione del database");
            // Disabilita ONLY_FULL_GROUP_BY per compatibilità con query legacy
            mysqli_query($this->link, "SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
            return $this->link;
        }
    }

    public function chiudiConnessioneCrm() {
        $this->_stato = false;
        mysqli_close($this->link);
    }

    public function getLink() {
        return $this->link;
    }

    public function getStato() {
        return $this->_stato;
    }

    public function __toString() {
        return $this->_stato;
    }

}

?>