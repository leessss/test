<?php

/**
 * 验证码
 */
class CaptchaController extends Controller
{
    public function index(){
        //期望 CaptchaTool工具类可以帮我们生成验证码
        CaptchaTool::generate(4);
    }
}