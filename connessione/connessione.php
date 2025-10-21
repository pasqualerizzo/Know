<?php

class Connessione
{

    protected $link;
    private $_stato = false;
    private $_host = '127.0.0.1';
    private $_port = '8889';
    private $_pwd = 'root';
    private $_userDB = 'root';
    private $_nomedb = 'Know';

    public function apriConnessione()
    {
        $this->link = mysqli_connect($this->_host, $this->_userDB, $this->_pwd, $this->_nomedb, $this->_port);
        if (mysqli_connect_errno()) {
            die("Errore nella connessione:" . mysqli_connect_errno());
            exit();
        } else {
            $this->_stato = true;
            // Disabilita ONLY_FULL_GROUP_BY per compatibilità con query legacy
            mysqli_query($this->link, "SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
            return $this->link;
        }
    }

    function chiudiConnessione()
    {
        $this->_stato = false;
        mysqli_close($this->link);
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getStato()
    {
        return $this->_stato;
    }

    public function __toString()
    {
        return (string)$this->_stato;
    }

}


?>