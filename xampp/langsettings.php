<?php
        $lang=@file_get_contents("lang.tmp");
        @include("lang/languages.php");
        @include("lang/en.php");
        @include("lang/$lang.php");
        if($lang=="zh")
        {
                header("Content-Type: text/html; charset=gb2312");
        }
        else if($lang=="jp")
        {
                header("Content-Type: text/html; charset=shift-jis");
        }
?>
