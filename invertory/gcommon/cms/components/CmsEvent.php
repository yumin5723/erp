<?php
namespace gcommon\cms\components;
use Yii;
use yii\base\Component;
class CmsEvent extends Component {

    public $host = "127.0.0.1";

    public $port = "5672";

    public $user = "guest";

    public $password = "guest";

    protected $_amqp = null;

    protected $_channel = null;


    public $default_exchange="cms_event";

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
        Yii::$app->autoloader->getAutoloader()->addNamespace('PhpAmqpLib',
            (dirname(dirname(__DIR__)) . '/lib/PhpAmqpLib')
        );
        $this->open();
        $this->_channel->exchange_declare($this->default_exchange, 'topic');
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
        Yii::Info("Connected to ". $this->host . ":".$this->port);
    }

    /**
     * publish event
     *
     * @param $object_id : integer object id
     * @param $object_type: string object type, content, page, template, block etc.
     * @param $action: string create, update etc.
     * @param $from: who fire this event
     * @param $info: other info
     * @param $parent_event_id: the parent event id if have
     *
     * @return
     */
    public function publishEvent($object_id, $object_type, $action, $from, $info = "", $parent_event_id=null) {
        $msg = array(
            'eid'      => \gcommon\components\GHelper::generateUniqueId(),
            'obj_type' => $object_type,
            'obj_id'   => $object_id,
            'action'   => $action,
            'from'     => $from,
            'info'     => $info,
            'pid'      => $parent_event_id ?: 0,
            'etime'    => time(),
        );
        $routing_key = $object_type . ":" .$action;
        Yii::info("send cms event routing_key :".$routing_key);
        $this->_channel->basic_publish(new \PhpAmqpLib\Message\AMQPMessage(json_encode($msg)),
            $this->default_exchange,
            $routing_key
        );
    }


}