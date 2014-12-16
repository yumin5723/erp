<?php
namespace gcommon\components\gqueue\commands;

use Yii;
use yii\console\Controller;
// use gcommon\components\gqueue\lib\Resque\Worker;
// use gcommon\components\gqueue\lib\Resque;
// use gcommon\components\gqueue\lib\ResqueScheduler;
// require_once(__DIR__.'/../lib/Resque.php');
// require_once(__DIR__.'/../lib/ResqueScheduler.php');
// require_once(__DIR__.'/../lib/ResqueScheduler/Worker.php');
// require_once(__DIR__.'/../lib/Resque/Worker.php');

require_once(__DIR__."/../GResqueAutoloader.php");
\GResqueAutoloader::register();

class ResqueController extends Controller
{

    public $count = 1;
    public $queue = "*";
    public $logLevel = 1;
    public $interval = 5;
    public $pidfile = '/tmp/resque.pid';

    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['count','queue','logLevel','interval','pidfile'] // global for all actions
        );
    }

    public function actionIndex() {
        Yii::$app->get('gqueue');
        if ($this->count > 1) {
            for($i = 0; $i < $this->count; ++$i) {
                $pid = \Resque::fork();
                if ($pid == -1) {
                    die('Could not fork worker '.$i."\n");
                } elseif (!$pid) {
                    // child, start the worker
                    $this->startWorker($this->queue,$this->logLevel,$this->interval);
                    break;
                }
            }
        } else {
            if ($this->pidfile) {
                file_put_contents($this->pidfile, getmypid()) or
                    die('Could not write PID information to '.$this->pidfile);
            }

            $this->startWorker($this->queue,$this->logLevel,$this->interval);
        }
    }

    public function actionScheduler() {
        Yii::$app->get('gqueue');
        
        if ($this->pidfile) {
            file_put_contents($this->pidfile, getmypid()) or
                die('Could not write PID information to '.$this->pidfile);
        }

        $this->startSchedulerWorker($this->logLevel,$this->interval);
    }

    protected function startWorker($queues, $logLevel, $interval, $logger=null)
    {
        $worker = new \Resque_Worker($queues);
        if (!empty($logger)) {
            $worker->registerLogger($logger);
        } else {
            fwrite(STDOUT, '*** Starting worker '."\n");
        }

        $worker->logLevel = $logLevel;
        $worker->work($interval);
    }

    protected function startSchedulerWorker($logLevel,$interval = 1,$logger=null)
    {
        $worker = new \ResqueScheduler_Worker($queues);
        if (!empty($logger)) {
            $worker->registerLogger($logger);
        } else {
            fwrite(STDOUT, '*** Starting worker '."\n");
        }

        $worker->logLevel = $logLevel;
        $worker->work($interval);
    }
}
