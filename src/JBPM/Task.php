<?php
namespace xrow\JBPM;

use GuzzleHttp\Client;
use Exception;

class Task 
{
    const STATUS_IN_PROGRESS = "InProgress";
    const STATUS_IN_RESERVED = "Reserved";
    const STATUS_SUCCESS = "SUCCESS";

    /**
     * @var Taskid
     */
    public $taskid;
    /**
     * @var TaskName
     */
    public $taskname;
    /**
     * @var ProcessId
     */
    public $processid;
    /**
     * @var Status
     */
    public $status;
    /**
     * @var Process instance id
     */
    public $processinstanceid;
    /**
     * @var \GuzzleHttp\Client
     */
    public $client;

    /**
     * class constructor
     * 
     * @param int $id Task Id
     * @param object $client a Guzzle client
     */
    public function __construct($id, Client $client)
    {
        $this->taskid = $id;
        $this->client = $client;
        try {
            $taskSummaryList = $client->get('task/query?taskId='.$id);
            $taskSummaryArray = $taskSummaryList->json();
            if (is_array($taskSummaryArray) && count($taskSummaryArray) > 0) {
                foreach ($taskSummaryArray['taskSummaryList'] as $taskSummary) {
                    $this->taskname = $taskSummary['task-summary']['name'];
                    $this->processid = $taskSummary['task-summary']['process-id'];
                    $this->status = $taskSummary['task-summary']['status'];
                    $this->processinstanceid = $taskSummary['task-summary']['process-instance-id'];
                }
            }
        } catch(Exception $e) {
            // do nothing
        }
    }

    /**
     * @return int Task id
     */
    public function getID()
    {
        return $this->taskid;
    }

    /**
     * Task start
     * 
     * @return boolean
     * @throws Exception If Task is start with error
     */
    public function start()
    {
        $task_id = $this->getID();
        try {
            $task_start = $this->client->post('task/'.$task_id.'/start');
            // Get only allowed tasks
            if ($task_start->getStatusCode() == 200) {
                $task_status = $task_start->json();
                if ($task_status['status'] == self::STATUS_SUCCESS) {
                    return true;
                }
                else {
                    throw new Exception("Task starts with error!");
                }
            }
        } catch(Exception $e) {
            // do nothing
        }
    }

   /**
    * Task Complete
    * 
    * @return boolean 
    * @throws Exception If Task is finished with error
    */
    public function complete()
    {
        $task_id = $this->getID();
        try {
            $task_complete = $this->client->post('task/'.$task_id.'/complete');
            // Get only allowed tasks
            if ($task_complete->getStatusCode() == 200) {
                $task_status = $task_complete->json();
                if ($task_status['status'] == self::STATUS_SUCCESS) {
                    return true;
                }
                else {
                    throw new Exception("Task is complete with error!");
                }
            }
        } catch(Exception $e) {
            // do nothing
        }
    }
    
    /**
     * Suspends a task
     *
     * @return boolean
     * @throws Exception If suspends a Task with error
     */
    public function suspend()
    {
        $task_id = $this->getID();
        try {
            $task_suspend = $this->client->post('task/'.$task_id.'/suspend');
            // Get only allowed tasks
            if ($task_suspend->getStatusCode() == 200) {
                $task_status = $task_suspend->json();
                if ($task_status['status'] == self::STATUS_SUCCESS) {
                    return true;
                }
                else {
                    throw new Exception("Suspends Task with error!");
                }
            }
        } catch(Exception $e) {
            // do nothing
        }
    }
}