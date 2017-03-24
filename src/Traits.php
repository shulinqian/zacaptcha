<?php
namespace thinkweb\zacaptcha;

trait Traits{

    /**
     * 第一次验证
     * @param $user_id
     * @return mixed
     */
    protected function getGeetestCode($user_id){
        $client = $this->getGeetestClient();
        return $client->getCode($user_id);
    }

    /**
     * 第二次验证，返回success后请执行登录等操作
     * @param $user_id
     * @return string
     */
    protected function geetestCheck($user_id){
        $client = $this->getGeetestClient();
            $post = array();
        if(function_exists('request')){
            $post['geetest_challenge'] = request()->post('geetest_challenge');
            $post['geetest_validate'] = request()->post('geetest_validate');
            $post['geetest_seccode'] = request()->post('geetest_seccode');
        } else {
            $post = $_POST;
        }
        $rs = $client->check($user_id, $post);
        if($rs == 1){
            return 'success';
        }
        return 'error';
    }

    protected function getGeetestClient(){
        $config = $this->getZaCaptchaConfig();
        return new Geetest($config);
    }

    protected function getZaCaptchaConfig(){
        return config('zacaptcha.geetest');
    }

    /**
     * @param $id
     * @param $codeUrl
     * @param $checkUrl
     * @param $callBack 成功后js回调函数名
     * @param string $product [float，embed，popup]
     * @return string
     */
    protected function getGeetestHtml($id, $codeUrl, $checkUrl, $callBack, $product="popup"){
        $codeUrl .= strpos($codeUrl, '?') === false ? '?' : '&';
        return <<<EOF
<script src="http://static.geetest.com/static/tools/gt.js"></script>
<div id="{$id}"></div>
<script>
    var handlerPopup{$id} = function (captchaObj) {
        // 成功的回调
        captchaObj.onSuccess(function () {
            var validate = captchaObj.getValidate();
            $.ajax({
                url: "{$checkUrl}", // 进行二次验证
                type: "post",
                dataType: "json",
                data: {
                    type: "pc",
                    username: $('#username1').val(),
                    password: $('#password1').val(),
                    geetest_challenge: validate.geetest_challenge,
                    geetest_validate: validate.geetest_validate,
                    geetest_seccode: validate.geetest_seccode
                },
                success: function (data) {
                    //data && data.status === "success" 成功
                    {$callBack}(data);
                }
            });
        });
        $("#{$id}-submit").click(function () {
            captchaObj.show();
        });
        captchaObj.appendTo("#{$id}");
    };
    $.ajax({
        url: "{$codeUrl}r=" + (new Date()).getTime(),
        type: "get",
        dataType: "json",
        success: function (data) {
            initGeetest({
                gt: data.gt,
                challenge: data.challenge,
                product: "{$product}", 
                offline: !data.success 
            }, handlerPopup{$id});
        }
    });
</script>
EOF;

    }

}