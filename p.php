<?php
    header("Content-type: text/html; charset=utf-8");
    //配置信息
    $conn_set = array(
        'host' => '127.0.0.1',
        'port' => '5672',
        'login' => 'guest',
        'password' => 'guest',
        'vhost'=>'/'
    );
    $e_name = 'e_line'; //交换机名
    //$q_name = 'q_line'; //无需队列名
    //$k_name = 'key_line'; //路由key
    //$k_name2 = 'key_line2';//路由key2
    $k_name = array(1=>'key1',2=>'key2',3=>'key3'); //绑定多个路由

    //创建连接和channel
    $conn = new AMQPConnection($conn_set);
    if (!$conn->connect()) {
        die("Cannot connect to the broker!\n");
    }
    $channel = new AMQPChannel($conn);

    //消息内容
    //$message = "TEST MESSAGE! 测试消息！";
    $message = $_POST['msg'];

    //创建交换机对象
    $ex = new AMQPExchange($channel);
    $ex->setName($e_name);

    //发送消息
    //$channel->startTransaction(); //开始事务
    for($i=1; $i<4; ++$i){
        //echo "发送消息:".$i.$ex->publish($message.$i, $k_name)."\n";
	       //$ex->publish($message.$i,$k_name);
	      $ex->publish($message.$i,$k_name[$i]);
    }
    //$channel->commitTransaction(); //提交事务

    $conn->disconnect();
