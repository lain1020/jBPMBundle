<?php
namespace xrow\JBPM;

use xrow\JBPM\Task;

class ProcessInstance
{
    /**
     * class constructor
     * 
     * @param int $id processInstanceId
     * @param object $client a Guzzle client
     */
    public function __construct($id,$client)
    {
        $this->processInstanceId=$id;
        $this->client = $client;
    }
    
    /**
     * @return Process Instance Id
     */
    public function getProcessInstanceId()
    {
        return $this->processInstanceId;
    }
    
   /**
    * @return Objeck of Task
    * @throws Exception If Task does not exist
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
            $task = new Task($task_id,$this->client);
            return $task;
        }else{
            throw  new Exception( "Task does not exist!");
        }
    }
    
   /**
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