<?php
namespace thinkweb\zacaptcha;

class Geetest extends Base {

    protected $sessionKey = 'zacaptchaGeetest';

    /**
    * 获取验证码
    */
    public function getCode($user_id){
        // TODO: Implement getCode() method.
        $sdk = $this->loadSdk();
        $status = $sdk->pre_process($user_id);
        $rs = $sdk->get_response_str();
        if($status){
            $this->setSession();
        }
        return $rs;
    }

    /**
     * 验证 1有效 0无效
     * @param $user_id
     * @param $post ['geetest_challenge' => '', 'geetest_validate' => '', 'geetest_seccode' => '']
     * @return int
     */
    public function check($user_id, $post){
        $sdk = $this->loadSdk();
        if($this->getSession()){
            return $sdk->success_validate($post['geetest_challenge'], $post['geetest_validate'], $post['geetest_seccode'], $user_id);
        } else {
            return $sdk->fail_validate($post['geetest_challenge'],$post['geetest_validate'],$post['geetest_seccode']);
        }
        // TODO: Implement check() method.
    }

    protected $sdk;

    /**
     * 加载sdk
     * @return \GeetestLib
     */
    protected function loadSdk(){
        if($this->sdk){
            return $this->sdk;
        }
        $dir = __DIR__ . '/../sdk/gt-php-sdk/';
        require_once $dir . 'lib/class.geetestlib.php';
        require_once $dir . 'config/config.php';
        $CAPTCHA_ID = $this->getConfig('CAPTCHA_ID');
        $PRIVATE_KEY = $this->getConfig('PRIVATE_KEY');
        return $this->sdk = new \GeetestLib($CAPTCHA_ID, $PRIVATE_KEY);
    }

}