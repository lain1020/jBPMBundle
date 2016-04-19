<?php

namespace xrow\jBPMBundle\Service;

use GuzzleHttp\Client;
use xrow\JBPM\ProcessDefinition;
use xrow\JBPM\Task;
use Exception;

class JBPMService 
{
    /**
     * @var \GuzzleHttp\Client
     */
    public $client;

    /**
     * class constructor
     * 
     * @param array $config an array of configuration values
     */
    public function __construct($config)
    {
        if (!isset($config['baseurl'])) {
            throw new Exception("baseurl is required for jBPM Service");
        }
        if (!isset($config['username'])) {
            throw new Exception("username is required for jBPM Service");
        }
        if (!isset($config['password'])) {
            throw new Exception("password is required for jBPM Service");
        }
        $verify = true;
        if (isset($config['defaults_verify']) && $config['defaults_verify'] === false)
            $verify = false;
        $this->client = new Client([
              'base_url' => $config['baseurl'],
              'defaults' => [
                   'auth' => [$config['username'], $config['password']],
                   'headers' => ['Accept'=>'application/json'],
                   'connect_timeout' => '10.00',
                   'timeout' => '10.00',
                   'verify' => $verify
              ]
        ]);
    }

    /**
     * @return GuzzleHttp\Client
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
        $processes = $this->client->get('deployment/processes');
        $processList = $processes->json();
        foreach ($processList as $processItems)
        {
            foreach ($processItems as $processItem)
            {
                $processDefinitionObject = new ProcessDefinition($processItem['process-definition']['id'], $this->client);
                $processDefinitionObject_array[] = $processDefinitionObject;
            }
        }
        if (count($processDefinitionObject_array) == 0)
        {
            throw new Exception("Project does not exist!");
        }
        return $processDefinitionObject_array;
    }

    /**
     * @param string processDefinitionId DefinitionId of Process
     * @return Object of ProcessDefinition
     * @throws Exception if Project does not exist
     */
    public function getProcess($processDefinitionId)
    {
        $processDefinition = new ProcessDefinition($processDefinitionId, $this->client);
        $id_array = array();
        $processes = $this->client->get('deployment/processes');
        $processList = $processes->json();
        foreach ($processList as $processItems)
        {
            foreach ($processItems as $processItem)
            {
                if (strpos($processItem['process-definition']['id'], $processDefinitionId) !== false)
                {
                    $deployment_id_temp = $processItem['process-definition']['deployment-id'];
                    $process_def_id_temp = $processItem['process-definition']['id'];
                    $id_array[$process_def_id_temp] = $deployment_id_temp;
                }
            }
        }
        if (count($id_array)!== 0)
        {
            $processDefinition->setDeploymentID($id_array);
            return $processDefinition;
        }
        else {
            throw  new Exception("Project does not exist!");
        }
    }

   /**
    * @param string type Status Type of Task
    * @return Array of Task Status "In Progress" or "Reserved", only for creator as task owner
    */
    public function getTasks($type, $taskOwner)
    {
        $taskArray = array();
        $tasklist = $this->client->get('task/query?status='.$type.'&taskOwner='.$taskOwner);
        $taskSummaryArray = $tasklist->json();
        foreach ($taskSummaryArray['taskSummaryList'] as $taskSummary)
        {
           $taskArray[] = new Task($taskSummary['task-summary']['id'], $this->client);
        }
        return $taskArray;
    }

    /**
     * @param int $processinstanceid
     * @return array of forwardtask object
     */
    public function getForwardTasks($processinstanceid)
    {
        $forwardTask = array();
        if ($processinstanceid != 0 && $processinstanceid != '') {
            $forwardtasklist = $this->client->get('task/query?processInstanceId='.$processinstanceid);
            $forwardtaskArray = $forwardtasklist->json();
            foreach ($forwardtaskArray['taskSummaryList'] as $forwardtask)
            {
                if ($forwardtask['task-summary']['status'] == Task::STATUS_IN_RESERVED)
                {
                    $forwardTask[] = new Task($forwardtask['task-summary']['id'], $this->client);
                }
            }
        }
        return $forwardTask;
    }
}
