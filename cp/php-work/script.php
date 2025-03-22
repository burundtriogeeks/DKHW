<?php

    const _WORKERS_COUNT = 5;

    $cmd = isset($argv,$argv[1])? $argv[1] : (isset($_GET["cmd"])? $_GET["cmd"] : "work" );
    fwrite(STDOUT, date("[j M Y G:i:s]")." ".gethostname()." received command ".$cmd."\n");

    function curlToRabbit($url,$str,$type = "post") {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://".getenv("MY_RELEASE_RABBITMQ_SERVICE_HOST").":15672/".$url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($str) {
            if($type == "post") {
                curl_setopt($ch, CURLOPT_POST, true);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS,$str);
        }
        curl_setopt($ch, CURLOPT_USERPWD, "user:".getenv("RABBITMQ_PASSWORD"));
        $res = curl_exec($ch);

        curl_close($ch);
        return $res;
    }

    function createQueue() {
        $res = curlToRabbit("api/queues/","");
        if ($res == "[]") {
            curlToRabbit("api/exchanges/%2f/my.exchange.name",'{"type":"fanout","durable":true}',"PUT");
            curlToRabbit("api/queues/%2f/my.queue",'{"durable":true,"arguments":{"x-dead-letter-exchange":"", "x-dead-letter-routing-key": "my.queue.dead-letter"}}',"PUT");
            curlToRabbit("api/bindings/%2f/e/my.exchange.name/q/my.queue",'{"routing_key":"my.queue","arguments":{}}');
        }
    }

    function getMessageFromQueue(){
        $res = curlToRabbit("api/queues/%2f/my.queue/get",'{"count":1,"ackmode":"ack_requeue_false","encoding":"auto","truncate":50000}');
        if ($res == "[]") {
            return false;
        } else {
            return $res;
        }
    }

    function sendMessage($str) {
        curlToRabbit("api/exchanges/%2f/my.exchange.name/publish",'{"properties":{},"routing_key":"my.queue","payload":"'.$str.'","payload_encoding":"string"}');
    }

    if ($cmd == "readiness") {
        $res = shell_exec("ps -ax | grep 'script.php wo'");
        preg_match_all("/script.php.work/",$res,$res);
        $res = count($res[0]);
        if (!$res || $res < _WORKERS_COUNT) {
            header("HTTP/1.0 404 Not Found");
            echo "NOT ready";
            exit(1);
        } else {
            echo "0";
        }
        exit;
    }

    if ($cmd == "init") {
        createQueue();
        for($i = 1; $i <= _WORKERS_COUNT; $i++) {
            shell_exec("php /app/script.php work ".($i*25)." > /dev/null 2>&1 &");
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
            $msg = getMessageFromQueue();
            if (!$msg) {
                usleep($speed*10000);
            } else {
                $start = microtime(true);
                do {
                    md5(generateRandomString(64));
                    usleep($speed);
                } while (microtime(true) - $start < 10);
            }
        } while(true);
    }