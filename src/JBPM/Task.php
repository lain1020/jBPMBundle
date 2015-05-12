<?php
namespace wuv\jBPMBundle\src\JBPM;

use GuzzleHttp\Client;

class Task 
{
    private $taskid;
    private $processInstanceId;

    public function __construct($config)
    {
        $this->taskid="";
        $this->processInstanceId="";
        $this->baseurl = $config['baseurl'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->client = new Client();
    }

    public function setTaskId($taskid)
    {
        $this->taskid = $taskid;
    }
    
    public function getTaskId()
    {
        return $this->taskid;
    }
    
    public function setProcessInstanceId($processInstanceId)
    {
        $this->processInstanceId = $processInstanceId;
    }
    
    public function getProcessInstanceId()
    {
        return $this->processInstanceId;
    }
    
    public function TaskComplete()
    {
        $task_id=$this->getTaskId();
        $task_complete = $this->client->post($this->baseurl.'/task/'.$task_id.'/complete', ['auth' => [$this->username, $this->password]]);
        $task_status=$task_complete->xml();
        if($task_status->status == "SUCCESS")
        {
            return true;
        }else{
            return false;
        }
    }
    
    public function getData()
    {
        $data=array();
        $procInstId=$this->getProcessInstanceId();
        $datalist = $this->client->get($this->baseurl.'/history/instance/'.$procInstId.'/variable', ['auth' => [$this->username, $this->password],
                                                                                                     'headers'=>['Accept'=>'application/json']]);
        $datas=$datalist->json();
        foreach($datas['historyLogList'] as $dataitems)
        {
            $data[$dataitems['variable-instance-log']['variable-id']] = $dataitems['variable-instance-log']['value'];
        }
        return $data;
    }
    
}