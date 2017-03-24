<?php
namespace thinkweb\zacaptcha;

abstract class Base{

    protected $sessionKey = 'zacaptchakey';
    protected $config = [];
    protected $session;

    function __construct($config, $session = null){
        $this->setConfig($config);
        $this->session = $session;
    }

    public function setConfig($config){
        $this->config = $config;
    }

    public function getConfig($key, $def = null){
        return isset($this->config[$key]) ? $this->config[$key] : $def;
    }

    protected function setSession(){
        if($this->session){
            $this->session->set($this->sessionKey, true);
        } else {
            $_SESSION[$this->sessionKey] = true;
        }
    }

    protected function getSession(){
        $session = '';
        if($this->session){
            $session = $this->session->get($this->sessionKey);
        } else {
            if(isset($_SESSION[$this->sessionKey])){
                $session = $_SESSION[$this->sessionKey];
            }
        }
        return $session;
    }

    protected function loadSdk(){
        $dir = __DIR__ . '/../sdk/gt-php-sdk/';

        require_once $dir . 'lib/class.geetestlib.php';
        require_once $dir . 'config/config.php';
        $GtSdk = new \GeetestLib(CAPTCHA_ID, PRIVATE_KEY);

    }

}