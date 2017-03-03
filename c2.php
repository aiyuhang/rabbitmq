<?php
    header("Content-type: text/html; charset=utf-8");

    //配置
    $conn_set = array(
        'host' => '127.0.0.1',
        'port' => '5672',
        'login' => 'guest',
        'password' => 'guest',
        'vhost' => '/'
    );
    $e_name = 'e_line'; //交换机名
    $q_name = 'q_line'; //队列名
    //$k_name = 'key_line'; //路由key
    $k_name = 'key2'; //路由key

    //创建channel和连接
    $conn = new AMQPConnection($conn_set);
    if(!$conn->connect()){
        die("Cannot connect to the broker!\n");
    }
    $channel = new AMQPChannel($conn);

    //创建交换机
    $ex = new AMQPExchange($channel);
    $ex->setName($e_name);
    $ex->setType(AMQP_EX_TYPE_DIRECT);//direct类型
    $ex->setFlags(AMQP_DURABLE);//持久化
    echo "Exchange Status:".$ex->declare()."\n";

    //创建队列
    $q = new AMQPQueue($channel);
    $q->setName($q_name);
    $q->setFlags(AMQP_DURABLE);//持久化
    echo "Message Total:".$q->declare()."\n";

    //绑定交换机与队列，并指定路由键
    echo 'Queue Bind:'.$q->bind($e_name,$k_name)."\n";

    //阻塞模式接收消息
    echo "Message:\n";
    while(True){
        $q->consume('processMessage');
    }
    $conn->disconnect();

    /**
    * 消费回调函数
    * 处理消息
     */
    function processMessage($envelope,$queue){
        $msg = $envelope->getBody();
        echo $msg."\n";//处理消息
        $queue->ack($envelope->getDeliveryTag());//手动发送ACK应答
    }
