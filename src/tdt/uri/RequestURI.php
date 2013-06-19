<?php

/**
 * This class will provide you a tool to ask for URI parameters
 *
 * @package The-DataTank
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 */

namespace tdt\uri;

class RequestURI {

    private static $instance;
    private $protocol, $host, $port, $filters, $format, $GETParameters;

    private function __construct() {

        $this->protocol = 'http';
        if (!empty($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 'on') {
                $this->protocol .= "s";
            }
        }
        $this->host = $_SERVER['SERVER_NAME'] . "/";
        $this->port = $_SERVER["SERVER_PORT"];

        $requestURI = $_SERVER["REQUEST_URI"];

        //Now for the hard part: parse the REQUEST_URI
        //This can look like this: /package/resource/identi/fiers.json

        $path = explode("/", $requestURI);

        $i = 0;
        //shift the path chunks as long as they exist and add them to the right variable
        while (sizeof($path) > 0) {
            //if this is the last element in the array
            //we might get the format out of it
            $arrayformat = explode(".", $path[0]);
            if (sizeof($path) == 1 && sizeof($arrayformat) > 1) {
                $this->format = array_pop($arrayformat);
                $this->filters[] = implode(".", $arrayformat);
            } else {
                $this->filters[] = $path[0];
            }
            array_shift($path);
            $i++;
        }
        //we need to sort all the GET parameters, otherwise we won't have a unique identifier for for instance caching purposes
        if (is_null($_GET)) {
            $this->GETParameters = $_GET;
            asort($GETParameters);
        }
    }

    public static function getInstance(array $config = array()) {
        if (!isset(self::$instance)) {
            self::$instance = new RequestURI($config);
        }
        return self::$instance;
    }

    public function getProtocol() {
        return $this->protocol;
    }

    public function getHostname() {
        return $this->host;
    }

    public function getFilters() {
        if (!is_null($this->filters)) {
            return $this->filters;
        }
        return array();
    }

    public function getGET() {
        if (!is_null($this->GETParameters)) {
            return $this->GETParameters;
        }
        return array();
    }

    public function getGivenFormat() {
        return $this->format;
    }

    public function getRealWorldObjectURI() {
        $URI = $this->protocol . "://" . $this->host . "/";
        $URI .= $this->getResourcePath();

        return $URI;
    }

    public function getResourcePath() {
        if (isset($this->filters) && !is_null($this->filters)) {
            $URI .= "/";
            $URI .= implode("/", $this->filters);
        }

        //Remove trailing slash
        if (strripos($URI, '/') == strlen($URI) - 1)
            $URI = substr($URI, 0, strlen($URI) - 1);

        return $URI;
    }

    public function getURI() {
        $URI = $this->protocol . "://" . $this->host . $this->getSubDir();
        if (isset($this->filters) && !is_null($this->filters)) {
            $URI .= "/";
            $URI .= implode("/", $this->filters);
        }
        if ($this->format != "") {
            $URI .= "." . $this->format;
        }
        if (sizeof($this->GETParameters) > 0) {
            $URI .= "?";
            foreach ($this->GETParameters as $key => $value) {
                $URI .= $key . "=" . $value . "&";
            }
            $URI = rtrim($URI, "&");
        }
        return $URI;
    }

}