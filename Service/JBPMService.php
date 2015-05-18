<?php
namespace xrow\jBPMBundle\Service;

use GuzzleHttp\Client;
use xrow\jBPMBundle\src\JBPM\ProcessDefinition;
use xrow\jBPMBundle\src\JBPM\Task;
use Exception;

class JBPMService 
{
    /**
     * @param array $config an array of configuration values
     */
    public function __construct($config)
    {
        $this->config = $config;
        if (isset($config['baseurl'])) {
            $this->baseurl = $config['baseurl'];
        }
        if (isset($config['username'])) {
            $this->username = $config['username'];
        }
        if (isset($config['password'])) {
            $this->password = $config['password'];
        }
        $this->client = new Client([
              'base_url' => $this->baseurl,
              'defaults' => [
                   'auth' => [$this->username, $this->password],
                   'headers'=>['Accept'=>'application/json']
              ]
        ]);
    }
    
    /**
     * Return the Instance of GuzzuleClient.
     * @return object
     */
    public function getClient()
    {
        return $this->client;
    }
    
    /**
     * @return array of ProcessDefinition
     * @throws Exception If Project does not exist
     */
    public function getProcesses()
    {
        $processes=$this->client->get('deployment/processes');
        $processList=$processes->json();
        foreach ($processList as $processItems)
        {
            foreach($processItems as $processItem)
            {
                $processDefinitionObject = new ProcessDefinition($processItem['process-definition']['id'],$this->client);
                $processDefinitionObject_array[]=$processDefinitionObject;
            }
        }
        if(count($processDefinitionObject_array)==0)
        {
            throw  new Exception( "Project does not exist!");
        }
        return $processDefinitionObject_array;
    }
    /**
     * @param string processDefinitionId DefinitionId of Process
     * @return Objeck of ProcessDefinition
     * @throws Exception If Project does not exist
     */
    public function getProcess($processDefinitionId)
    {
        $processDefinition = new ProcessDefinition($processDefinitionId,$this->client);
        $id_array= array();
        $processes=$this->client->get('deployment/processes');
        $processList=$processes->json();
        foreach ($processList as $processItems)
        {
            foreach($processItems as $processItem)
            {
                if(strpos($processItem['process-definition']['id'],$processDefinitionId) !== false)
                {
                    $deployment_id_temp=$processItem['process-definition']['deployment-id'];
                    $process_def_id_temp=$processItem['process-definition']['id'];
                    $id_array[$process_def_id_temp]=$deployment_id_temp;
                }
            }
        }
        if(count($id_array)!==0)
        {
            $processDefinition->setDeploymentID($id_array);
            return $processDefinition;
        }else{
            throw  new Exception( "Project does not exist!");
        }
    }
   /**
    * @param string type Status Type of Taskfo
    * @return Array of Task  Status "In Progress" oder Status "Reserved"
    */
    public function getTasks( $type = Task::STATUS_IN_PROGRESS )
    {
        $taskArray=array();
        $tasklist=$this->client->get('task/query?status='.$type );
        $taskSummaryArray=$tasklist->json();
        foreach($taskSummaryArray['taskSummaryList'] as $taskSummary)
        {
           $taskArray[] = new Task($taskSummary['task-summary']['id'], $this->client);
        }
        return $taskArray;
    }
    
    /**
     * @param int taskid
     * @return object of Task
     */
    public function getForwardTasks( $taskid )
    {
        $forwardTask = new Task($taskid, $this->client);
        return $forwardTask;
    }
}