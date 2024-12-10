<?php
require_once 'vendor/autoload.php';

$conf = new RdKafka\Conf();
$conf->set('group.id', 'testGroup');
$conf->set('metadata.broker.list', 'localhost:9092');
$conf->set('auto.offset.reset', 'earliest');

$consumer = new RdKafka\KafkaConsumer($conf);

$consumer->subscribe(['mysql.testdb.my_table']);

while (true) {
    $message = $consumer->consume(120*1000);
    switch ($message->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            var_dump(json_decode($message->payload, true));
            break;
        case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            echo "No more messages; will wait for more\n";
            break;
        case RD_KAFKA_RESP_ERR__TIMED_OUT:
            echo "Timed out\n";
            break;
        default:
            throw new Exception($message->errstr(), $message->err);
            break;
    }
}
