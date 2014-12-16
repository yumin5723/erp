<?php
namespace gcommon\components\gqueue;
/**
 * Yii2 Resque Component
 * work with php resque
 */
use Yii;
// use gcommon\components\gqueue\lib\Resque;
// use gcommon\components\gqueue\lib\ResqueScheduler;
use yii\base\Component;

// require_once(__DIR__.'/lib/Resque.php');
// require_once(__DIR__.'/lib/ResqueScheduler.php');
// require_once(__DIR__.'/lib/Resque/Worker.php');
require_once(__DIR__."/GResqueAutoloader.php");
\GResqueAutoloader::register();


class GQueue extends Component
{
    /**
     * @var string Redis server address
     */
    public $server = 'localhost';

    /**
     * @var string Redis port number
     */
    public $port = '6379';

    /**
     * @var int Redis database index
     */
    public $database = 0;

    /**
     * @var string Redis password auth
     */
    public $password = '';


    /**
     * @var string redis key prefix
     */
    public $prefix = '';

    /**
     * Initializes the connection.
     */
    public function init()
    {
        parent::init();
        \Resque::setBackend($this->server . ':' . $this->port, $this->database, $this->password);
        if ($this->prefix) {
          \Resque::redis()->prefix($this->prefix);    
        }

    }

    /**
     * Create a new job and save it to the specified queue.
     *
     * @param string $queue The name of the queue to place the job in.
     * @param string $class The name of the class that contains the code to execute the job.
     * @param array $args Any optional arguments that should be passed when the job is executed.
     *
     * @return string
     */
    public function createJob($queue, $class, $args = array(), $track_status = false)
    {
        return \Resque::enqueue($queue, $class, $args, $track_status);
    }

    /**
     * Return Redis
     *
     * @return object Redis instance
     */
    public function redis()
    {
        return \Resque::redis();
    }

    /**
    * Create a new scheduled job and save it to the specified queue.
    *
    * @param int $in Second count down to job.
    * @param string $queue The name of the queue to place the job in.
    * @param string $class The name of the class that contains the code to execute the job.
    * @param array $args Any optional arguments that should be passed when the job is executed.
    *
    * @return string
    */
    public function enqueueJobIn($in, $queue, $class, $args = array())
    {
        return \ResqueScheduler::enqueueIn($in, $queue, $class, $args);
    }
    /**
    * Create a new scheduled job and save it to the specified queue.
    *
    * @param timestamp $at UNIX timestamp when job should be executed.
    * @param string $queue The name of the queue to place the job in.
    * @param string $class The name of the class that contains the code to execute the job.
    * @param array $args Any optional arguments that should be passed when the job is executed.
    *
    * @return string
    */
    public function enqueueJobAt($at, $queue, $class, $args = array())
    {
        return \ResqueScheduler::enqueueAt($at, $queue, $class, $args);
    }

     /**
    * Get delayed jobs count
    *
    * @return int
    */
    public function getDelayedJobsCount()
    {
        return (int)\Resque::redis()->zcard('delayed_queue_schedule');
    }
}

