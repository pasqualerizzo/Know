<?php

class ConnessioneCrmNuovo {

    protected $link;
    private $_stato = false;
    private $_host = 'crm.novaholding.it';
    private $_port = '3306';
    private $_pwd = '2025N0v4k3y!@';
    private $_userDB = 'vtiger_external';
    private $_nomedb = 'vtiger_db';

    public function apriConnessioneCrmNuovo() {
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