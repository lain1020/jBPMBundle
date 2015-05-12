<?php
namespace wuv\jBPMBundle\src\JBPM;

use GuzzleHttp\Client;
use wuv\jBPMBundle\src\JBPM\Task;

class ProcessInstance
{
    private $id;
    private $processName;

    public function __construct($config)
    {
        $this->id="";
        $this->processName="";
        $this->baseurl = $config['baseurl'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->client = new Client();
        $this->task = new Task($config);
    }

    public function setID($id)
    {
        $this->id = $id;
    }

    public function getID()
    {
        return $this->id;
    }
    
    public function setProcessName($processName)
    {
        $this->processName = $processName;
    }
    
    public function getProcessName()
    {
        return $this->processName;
    }
    
    public function currentTask()
    {
        $task_id=$this->task->getTaskId();
        $this->task->setProcessInstanceId($task_id);
        $task_start = $this->client->post($this->baseurl.'/task/'.$task_id.'/start', ['auth' => [$this->username, $this->password]]);
        $task_status=$task_start->xml();
        if($task_status->status == "SUCCESS")
        {
            return $this->task;
        }else{
            return NULL;
        }
    }
}