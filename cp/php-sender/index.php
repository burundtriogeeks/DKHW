<?php

    function curlToRabbit($url,$str,$type = "post") {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://".getenv("MY_RELEASE_RABBITMQ_SERVICE_HOST").":15672/".$url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($str) {
            if($type == "post") {
                echo 1;
                curl_setopt($ch, CURLOPT_POST, true);
            } else {
                echo 2;
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
            $res = curlToRabbit("api/exchanges/%2f/my.exchange.name",'{"type":"fanout","durable":true}',"PUT");
            $res = curlToRabbit("api/queues/%2f/my.queue",'{"durable":true,"arguments":{"x-dead-letter-exchange":"", "x-dead-letter-routing-key": "my.queue.dead-letter"}}',"PUT");
            $res = curlToRabbit("api/bindings/%2f/e/my.exchange.name/q/my.queue",'{"routing_key":"my.queue","arguments":{}}');
        } else {
            return;
        }

    }

    function sendMessage($str) {
        $res = curlToRabbit("api/exchanges/%2f/my.exchange.name/publish",'{"properties":{},"routing_key":"my.queue","payload":"'.$str.'","payload_encoding":"string"}');
        var_dump($res);;
    }

    //createQueue();
    sendMessage("Some message from client");

    echo "Message sent to queue\n";
