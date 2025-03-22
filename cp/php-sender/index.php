<?php

    function curlToRabbit($url,$str,$type = "post") {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://".getenv("RABBITMQ_HOST").":15672/".$url);
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

    if (isset($_GET["getmsg"])) {
        $msg = getMessageFromQueue();
        if ($msg) {
            print_r(getMessageFromQueue());
        } else {
            echo "No messages in queue\n";
        }
    } else {
        if (isset($_GET["msgcnt"])) {
            for ($i = 0; $i < $_GET["msgcnt"]; $i++) {
                sendMessage("Some message from client");
            }
            echo $_GET["msgcnt"]." Messages sent to queue\n";
        } else {
            sendMessage("Some message from client");
            echo "Message sent to queue\n";
        }

    }
