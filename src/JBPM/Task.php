<?php
namespace xrow\jBPMBundle\src\JBPM;

use GuzzleHttp\Client;

class Task 
{
    private $taskid;

    public function __construct($client)
    {
        $this->taskid="";
        $this->client = $client;
    }

    public function setTaskId($taskid)
    {
        $this->taskid = $taskid;
    }
    
    public function getTaskId()
    {
        return $this->taskid;
    }
    
   /*
    * @return boolean 
    * @throw Exception If Task is finished with error
    */
    public function TaskComplete()
    {
        $task_id=$this->getTaskId();
        $task_complete = $this->client->post('task/'.$task_id.'/complete');
        $task_status=$task_complete->json();

        if($task_status['status'] == "SUCCESS")
        {
            return true;
        }else{
            return false;
        }
    }
}