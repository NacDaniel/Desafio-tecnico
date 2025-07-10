<?php


class userView
{
    private static $instance = null;
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new userView();
        }

        return self::$instance;
    }

    public static function deleteInstance()
    {
        if (self::$instance == null) {
            $instance = null;
        }
    }

    public function loadIndex()
    {
        echo file_get_contents("./view/public/index.html");
    }


}

?>