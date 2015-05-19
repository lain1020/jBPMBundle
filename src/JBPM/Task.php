<?php
namespace xrow\JBPM;

use GuzzleHttp\Client;
use Exception;

class Task 
{
    const STATUS_IN_PROGRESS = "InProgress";
    const STATUS_IN_RESERVED = "Reserved";
    
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
     * class constructor
     * 
     * @param int $id Task Id
     * @param object $client a Guzzle client
     */
    public function __construct($id, $client)
    {
        $this->taskid= $id;
        $this->client = $client;
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
        $task_id=$this->getID();
        $task_start = $this->client->post('task/'.$task_id.'/start');
        $task_status=$task_start->json();
        if($task_status['status'] == "SUCCESS")
        {
            return true;
        }else{
            throw  new Exception( "Task is start with error!");
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
        $task_id=$this->getID();
        $task_complete = $this->client->post('task/'.$task_id.'/complete');
        $task_status=$task_complete->json();
        if($task_status['status'] == "SUCCESS")
        {
            return true;
        }else{
            throw  new Exception( "Task is complete with error!");
        }
    }
    
    /**
     * It is an assignment function.
     */
    public function TaskSummaryArray()
    {
        $taskSummaryList=$this->client->get('task/query?taskId='.$this->getID() );
        $taskSummaryArray=$taskSummaryList->json();
        foreach($taskSummaryArray['taskSummaryList'] as $taskSummary)
        {
            $this->taskname =$taskSummary['task-summary']['name'];
            $this->processid =$taskSummary['task-summary']['process-id'];
            $this->status =$taskSummary['task-summary']['status'];
            $this->processinstanceid =$taskSummary['task-summary']['process-instance-id'];
        }
    }
}