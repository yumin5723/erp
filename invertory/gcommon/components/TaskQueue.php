<?php
namespace gcommon\components;
use Yii;
use yii\base\Component;
class TaskQueue extends Component {
    // the amqp server id
    public $host = "127.0.0.1";

    public $port = "5672";

    public $user = "guest";

    public $password = "guest";

    protected $_amqp = null;

    protected $_channel = null;

    /**
     * function_description
     *
     *
     * @return
     */
    public function __construct() {
        Yii::$app->autoloader->getAutoloader()->addNamespace("PhpAmqpLib",
            (dirname(__DIR__) . '/lib/PhpAmqpLib'));
        $this->open();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function open() {
        $this->_amqp = new \PhpAmqpLib\Connection\AMQPConnection(
            $this->host, $this->port,$this->user,$this->password
        );
        $this->_channel = $this->_amqp->channel();
        if (!$this->_channel) {
            throw new CException("Cannot create channel.");
        }
        Yii::info("Connected to ". $this->host . ":".$this->port, 'info');
    }

    /**
     * add message to task queue
     *
     * @param $task_queue:
     * @param $message:
     *
     * @return
     */
    protected function addTask($task_queue, $task_message) {
        // declare queue
        $msg = new \PhpAmqpLib\Message\AMQPMessage($task_message);
        $this->_channel->queue_declare($task_queue, false,true,false,false);
        $this->_channel->basic_publish(
            $msg,"",$task_queue
        );
        return true;
    }

    /**
     * function_description
     *
     * @param $task_queue:
     * @param $worker:
     *
     * @return
     */
    protected function register_worker($task_queue, $worker) {
        $ch = $this->_channel;
        $ch->queue_declare($task_queue,false,true,false,false);
        $ch->basic_qos(0, 1, null);
        $ch->basic_consume($task_queue, "", false,false,false,false,
            function($msg) use ($worker) {
                call_user_func($worker, $msg->body);
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            }
        );
        /*
         * register_shutdown_function(function($ch, $conn){
         *         $ch->close();
         *         $conn->close();
         *     },$ch,$this->_amqp);
         */
        // for process shutdown
        declare(ticks = 1);
        pcntl_signal(SIGTERM, function(){
                //shutdown
                echo "shutdown...";
                exit(0);
            });

        while (count($ch->callbacks)) {
            $ch->wait();
        }

    }

}