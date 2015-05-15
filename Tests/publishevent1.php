<?php
namespace xrow\jBPMBundle\Tests;

use xrow\jBPMBundle\Interfaces\TaskExecuter;
use xrow\jBPMBundle\src\JBPM\Task;
use xrow\jBPMBundle\src\JBPM\ProcessInstance;
use xrow\jBPMBundle\src\JBPM\ProcessDefinition;
use GuzzleHttp\Client;

class publishevent1 implements TaskExecuter
{
    public function __construct()
    {
        $this->client = new Client(['base_url'=>'http://workflow.xrow.lan/jbpm-console/rest/', 
                                    'defaults'=>[
                                         'auth' => ['admin', 'admin'],
                                         'headers'=>['Accept'=>'application/json']]]);
        $this->task = new Task($this->client);
        $this->processDefinition = new ProcessDefinition($this->client);
        $this->processInstance = new ProcessInstance($this->client);
    }
     
    /*
     * Throws Exception on error Task is not compelted
     */
    public function execute( $taskid )
    {
        $valueName="";
        $value="";
        
        $this->task->setTaskId($taskid);
        $task_complate_status=$this->task->TaskComplete();
        
        if($task_complate_status)
        {
            $this->processInstance->setProcessInstanceId($taskid);
            $dataArray=$this->processInstance->getData();
            
            foreach($dataArray as $valueName =>$value)
            {
                if($valueName == "email")
                {
                    return array('processid'=>$taskid,'valueName'=>$valueName,'value'=>$value);
                }
            }
        }else{
            return array();
        }
    }
}