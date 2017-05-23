<?php

/* 
 * 
 */
class Mail
{
    protected static $headers = array(
        "MIME-Version: 1.0\r\n",
        "Content-type: text/html; charset=UTF-8\r\n",
        "From: onlineMap.org <no-reply@onlineMap.org>\r\n"
    );
    public static function send($to, $subject, $message, $dopHeaders = false) {
        if (is_array($dopHeaders)) self::$headers = array_merge (self::$headers, $dopHeaders);

        mail($to, $subject, $message, implode(self::$headers));
    }
}

