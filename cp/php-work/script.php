<?php

    const _WORKERS_COUNT = 5;

    $cmd = isset($argv,$argv[1])? $argv[1] : (isset($_GET["cmd"])? $_GET["cmd"] : "work" );
    fwrite(STDOUT, date("[j M Y G:i:s]")." ".gethostname()." recived command ".$cmd."\n");

    if ($cmd == "readiness") {
        $res = shell_exec("ps -ax | grep 'script.php wo'");
        preg_match_all("/script.php.work/",$res,$res);
        $res = count($res[0]);
        if (!$res || $res < _WORKERS_COUNT) {
            header("HTTP/1.0 404 Not Found");
            echo "NOT ready";
        } else {
            echo "0";
        }
        exit;
    }

    if ($cmd == "init") {
        for($i = 1; $i <= _WORKERS_COUNT; $i++) {
            shell_exec("php /app/script.php work ".($i*50)." > /dev/null 2>&1 &");
        }
        exit;
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    if ($cmd == "work") {
        $speed = isset($argv,$argv[2])? $argv[2] : 100;
        do {
            md5(generateRandomString(64));
            usleep($speed);
        } while(true);
    }