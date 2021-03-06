<?php

namespace xrow\JBPM;

class ProcessDefinition
{
    /**
     * @var Deploymentid
     */
    private $deploymentid;
    /**
     * @var Process definition id
     */
    private $processDefinitionId;
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * class constructor
     * 
     * @param int $processDefinitionId  process Definitionsid
     * @param object $client a Guzzle client
     */
    public function __construct($processDefinitionId, $client)
    {
        $this->deploymentid = "";
        $this->processDefinitionId = $processDefinitionId;
        $this->client = $client;
    }

    /**
     * @param string $deploymentid
     * @return Deploymentid
     */
    public function setDeploymentID($deploymentid)
    {
        $this->deploymentid = $deploymentid;
    }
    
    /**
     * @return deployment Id
     */
    public function getDeploymentID()
    {
        return $this->deploymentid;
    }
    
    public function getProcessDefinitionID()
    {
        return $this->processDefinitionId;
    }
    
   
   /**
    * @param array data Data of Process
    * @return object of Process Instance
    * @throws Exception If Process starting error
    */
    public function start($data)
    {
        $parameters = array();
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $this->getValueAsString($value, $key, $parameters);
            }
        }
        $id_array = $this->getDeploymentID();
        if (count($id_array)>0)
        {
            foreach ($id_array as $processDef_id => $deploy_id)
            {
                $process_start = $this->client->post('runtime/'.$deploy_id.'/process/'.$processDef_id.'/start?'.implode('&', $parameters));
                $process_status = $process_start->json();
                if ($process_status['status'] == Task::STATUS_SUCCESS)
                {
                    $processInstance = new ProcessInstance($process_status['id'], $this->client);
                    return $processInstance;
                } else {
                    throw new \Exception("Process starting error!");
                }
            }
        } else {
            throw new \Exception("Process starting error!");
        }
    }

    /**
     * Create an array of all given values (array, string, numbers etc.)
     * 
     * @param array|string|integer|boolean etc. $value
     * @param string $key
     * @param array $params
     */
    private function getValueAsString($value, $key, &$params)
    {
        if (!is_array($value) && !is_object($value)) {
            $params[] = 'map_'.$key.'="'.urlencode($value).'"';
        }
        else {
            foreach ($value as $keyItem => $valueItem) {
                if (!is_array($valueItem) && !is_object($valueItem)) {
                    $params[] = 'map_'.$key.'_'.$keyItem.'="'.urlencode($valueItem).'"';
                }
                else {
                    $this->getValueAsString($valueItem, $key."_".$keyItem, $params);
                }
            }
        }
    }
}