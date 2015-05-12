<?php
namespace wuv\jBPMBundle\src\JBPM;

use GuzzleHttp\Client;
use wuv\jBPMControlBundle\src\JBPM\Task;

class ProcessDefinition
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
    
    public function processStart($data)
    {
        $parameters="";
        if(count($data)>0)
        {
            $i=1;
            foreach($data as $key => $value)
            {
                if($i==1)
                {
                    $parameters .= "?map_".$key."=".$value;
                }else{
                    $parameters .= "&map_".$key."=".$value;
                }
                $i++;
            }
        }
        var_dump($parameters);die();
        $id_array=$this->getID();
        foreach($id_array as $proce_id => $dep_id)
        {
            $processDef_id = $proce_id;
            $deploy_id = $dep_id;
        }
        
        $process_start = $this->client->post($this->baseurl.'/runtime/'.$deploy_id.'/process/'.$processDef_id.'/start'.$parameters, ['auth' => [$this->username, $this->password]]);
        $process_status=$task_start->xml();
        if($task_status->status == "SUCCESS")
        {
            return $this;
        }else{
            return NULL;
        }
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