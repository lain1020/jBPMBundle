<?php
namespace xrow\jBPMBundle\src\JBPM;

use xrow\jBPMBundle\src\JBPM\ProcessInstance;
use Exception;

class ProcessDefinition
{
    /**
     * @var Deploymentid
     */
    private $deploymentid;

    /**
     * @param int $processDefinitionId  process Definitionsid
     * @param object $client a Guzzle client
     */
    public function __construct($processDefinitionId, $client)
    {
        $this->deploymentid="";
        $this->processDefinitionId=$processDefinitionId;
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
        $id_array=$this->getDeploymentID();
        if(count($id_array)>0)
        {
            foreach($id_array as $proce_id => $dep_id)
            {
                $processDef_id = $proce_id;
                $deploy_id = $dep_id;
            
                $process_start = $this->client->post('runtime/'.$deploy_id.'/process/'.$processDef_id.'/start'.$parameters);
                $process_status=$process_start->json();
                if($process_status['status'] == "SUCCESS")
                {
                    $processInstance = new ProcessInstance($process_status['id'], $this->client);
                    return $processInstance;
                }else{
                    throw  new Exception( "Process starting error!");
                }
            }
        }else{
            throw  new Exception( "Process starting error!");
        }
    }
}