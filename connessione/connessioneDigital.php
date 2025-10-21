<?php

class connessioneDigital {

    protected $link;
    private $_stato = false;
    private $_host = "162.19.161.233";
    private $_port = '3306';
    private $_pwd = 'WQnBjGQTM5LpMEYH';
    private $_userDB = 'magellanob';
    private $_nomedb = 'asterisk';

    public function apriConnessioneDigital() {
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

    function chiudiConnessioneDigital() {
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