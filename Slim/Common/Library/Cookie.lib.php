<?php
/**
 * cookie library
 *
 * author : saeed
 * date   : 2013-7-26
 */
class Cookie
{
    private static $secret_key;

    public function __construct(){
        $config = load_cfg('app');
        self::$secret_key = $config['secret_key'];
    }

    private static function _decrypt($encryptedText)
    {
        $key = self::$secret_key;
        $cryptText = base64_decode($encryptedText);
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $decryptText = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $cryptText, MCRYPT_MODE_ECB, $iv);
        return trim($decryptText);
    }


    private static function _encrypt($plainText)
    {
        $key = self::$secret_key;
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $encryptText = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $plainText, MCRYPT_MODE_ECB, $iv);
        return trim(base64_encode($encryptText));
    }

    public static function del()
    {
        //multi params
        $args = func_get_args();
        foreach($args as $arg){
            $name = $arg['name'];
            $domain = isset($arg['domain']) ? $arg['domain'] : null;
            return isset($_COOKIE[$name]) ? setcookie($name, '', time() - 86400, '/', $domain) : true;
        }
    }

    public static function get($name)
    {
        return isset($_COOKIE[$name]) ? self::_decrypt($_COOKIE[$name]) : null;
    }

    public static function set($arg)
    {
        $name = $arg['name'];
        $value= self::_encrypt($arg['value']);
        $expire = isset($arg['expire']) ? $arg['expire'] : null;
        $path = isset($arg['path']) ? $arg['path'] : '/';
        $domain = isset($arg['domain']) ? $arg['domain'] : null;
        $secure = isset($arg['secure']) ? $arg['secure'] : 0;
        return setcookie($name, $value, $expire, $path, $domain, $secure);
    }
}
