<?php
namespace gcommon\components;
class GHelper {

    /**
     * function_description
     *
     *
     * @return
     */
    public static function generateUniqueId() {
        $prefix = substr(md5(gethostname()), 0, 5);
        return uniqid($prefix);
    }

    public static function urlBase64Encode($str){
        return strtr(base64_encode($str), "+/=", "-_.");
    }
    public static function urlBase64Decode($str){
        return base64_decode(strtr($str, "-_.","+/="));
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public static function generateRandomString($length = 10) {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWSXYZ";
        $randomString = "";
        for($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    protected static function randomReadLine($fileName, $buffer_length = 4096) {
        $handle = @fopen($fileName, "r");
        if ($handle) {
            $random_line = null;
            $line = null;
            $count = 0;
            while (($line = fgets($handle, $buffer_length)) !== false) {
                $count++;
                // P(1/$count) probability of picking current line as random line
                if(rand() % $count == 0) {
                    $random_line = $line;
                }
            }
            if (!feof($handle)) {
                fclose($handle);
                return null;
            } else {
                fclose($handle);
            }
            return $random_line;
        }
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public static function getRandomNickName() {
        $fileName = dirname(__FILE__)."/wuxia.txt";
        return self::randomReadLine($fileName, 64);
    }


}