<?php
namespace xrow\jBPMBundle\src\JBPM;

use xrow\jBPMBundle\src\JBPM\Task;

class ProcessInstance
{
    private $processInstanceId;
    
    public function __construct($client)
    {
        $this->processInstanceId="";
        $this->client = $client;
        $this->task = new Task($client);
    }
    
    public function setProcessInstanceId($processInstanceId)
    {
        $this->processInstanceId = $processInstanceId;
    }
    
    public function getProcessInstanceId()
    {
        return $this->processInstanceId;
    }
    
   /*
    * @return Objeck of Task
    * @throw Exception If Task does not exist
    */
    public function currentTask()
    {
        $processInsId=$this->getProcessInstanceId();
        $task_summary=$this->client->get('task/query?processInstanceId='.$processInsId);
        $task_json=$task_summary->json();

        foreach($task_json['taskSummaryList'] as $task_att)
        {
            $task_id=$task_att['task-summary']['id'];
        }
        
        $task_start = $this->client->post('task/'.$task_id.'/start');
        $task_status=$task_start->json();
        if($task_status['status'] == "SUCCESS")
        {
            $this->task->setTaskId($task_id);
            return $this->task;
        }else{
            return NULL;
        }
    }
    
   /*
    * @return  data of Process in an array
    */
    public function getData()
    {
        $data=array();
        $procInstId=$this->getProcessInstanceId();
        $datalist = $this->client->get('history/instance/'.$procInstId.'/variable');
        $datas=$datalist->json();
        foreach($datas['historyLogList'] as $dataitems)
        {
            $data[$dataitems['variable-instance-log']['variable-id']] = $dataitems['variable-instance-log']['value'];
        }
        return $data;
    }
}