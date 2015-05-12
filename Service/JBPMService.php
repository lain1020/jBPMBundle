<?php
namespace wuv\jBPMBundle\Service;

use GuzzleHttp\Client;
use wuv\jBPMBundle\src\JBPM\ProcessInstance;
use wuv\jBPMBundle\src\JBPM\ProcessDefinition;

class JBPMService 
{
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
        $this->client = new Client();
        $this->processInstance = new ProcessInstance($config);
        $this->processDefinition = new ProcessDefinition($config);
    }
    /*
     * @param string name Name of Project
     * @return array of Process
     * @throw Exception If Project does not exist
     */
    public function getProcesses( $name )
    {
        $this->process->setProcessName($name);
        $id_array= array();
        $processes=$this->client->get($this->baseurl.'/deployment/processes', ['auth' => [$this->username, $this->password],
                                                                               'headers'=>['Accept'=>'application/json']]);
        $processList=$processes->json();
        foreach ($processList as $processItems)
        {
            foreach($processItems as $processItem)
            {
                if(strpos($processItem['process-definition']['id'],$name) !== false)
                {
                    $deployment_id_temp=$processItem['process-definition']['deployment-id'];
                    $process_def_id_temp=$processItem['process-definition']['id'];
                    $id_array[$process_def_id_temp]=$deployment_id_temp;
                }
            }
        }
        if(count($id_array)!==0)
        {
            $this->process->setID($id_array);
            return $this->process;
        }else{
            return NULL;
        }
    }
    /*
     * @param string name Name of Process
     * @return Objeck of Process
     * @throw Exception If Project does not exist
     */
    public function getProcess($processName)
    {
        $this->processDefinition->setProcessName($processName);
        $id_array= array();
        $processes=$this->client->get($this->baseurl.'/deployment/processes', ['auth' => [$this->username, $this->password],
                                                                               'headers'=>['Accept'=>'application/json']]);
        $processList=$processes->json();
        foreach ($processList as $processItems)
        {
            foreach($processItems as $processItem)
            {
                if(strpos($processItem['process-definition']['id'],$processName) !== false)
                {
                    $deployment_id_temp=$processItem['process-definition']['deployment-id'];
                    $process_def_id_temp=$processItem['process-definition']['id'];
                    $id_array[$process_def_id_temp]=$deployment_id_temp;
                }
            }
        }
        if(count($id_array)!==0)
        {
            $this->processDefinition->setID($id_array);
            return $this->processDefinition;
        }else{
            return NULL;
        }
    }
}