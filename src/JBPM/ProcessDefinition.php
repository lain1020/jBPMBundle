<?php
namespace xrow\jBPMBundle\src\JBPM;

use xrow\jBPMBundle\src\JBPM\ProcessInstance;

class ProcessDefinition
{
    private $id;
    private $processName;

    public function __construct($client)
    {
        $this->id="";
        $this->processName="";
        $this->client = $client;
        $this->processInstance = new ProcessInstance($client);
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
    
   /*
    * @param array data Data of Process
    * @return object of Process Instance
    * @throw Exception If Process starting error
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
        $id_array=$this->getID();
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
                    $this->processInstance->setProcessInstanceId($process_status['id']);
                    return $this->processInstance;
                }else{
                    return NULL;
                }
            }
        }else{
            return NULL;
        }
    }
}