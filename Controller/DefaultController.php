<?php

namespace wuv\jBPMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp\Client;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	
    	/*$client = new Client();
    	$deploymentList=$client->post('http://workflow.xrow.lan/jbpm-console/rest/runtime/de.wuv.cms:cms:1.0/withvars/process/instance/116/signal', ['auth' => ['admin', 'admin'],
    			                                                                                                                                      'headers'=>['Accept'=>'application/json']]);
    	$content=$deploymentList->json();
    	var_export($content);die();
    	return $this->render('wuvjBPMControlBundle:Default:index.html.twig',array('processid' => "1",'processName'=>"test"));
    	*/
        $data = array();
        $data["name"] = "bjoern";
        $data["email"] = "bjoern@xrow.de";
        
        $clientCMS=$this->container->get('jbpm.client');
        //$processesCMS=$clientCMS->getProcesses('cms');
        
        $processDefinition = $clientCMS->getProcess('cms.order');
         
        $processInstance=$processDefinition->start($data);
        if($processInstance)
        {
            $task=$processInstance->currentTask();
            if($task)
            {
                $task_complate_status=$task->TaskComplete();
                if($task_complate_status)
                {
                    $processId= $task->getTaskId();
                    $processNameArray=$processesCMS->getID();
                    foreach($processNameArray as $key => $value)
                    {
                        $processName=$key;
                    }
                    
                    $dataArray=$task->getData();
                    foreach($dataArray as $valueName =>$value)
                    {
                        if($valueName == "email")
                        {
                            return $this->render('wuvjBPMControlBundle:Default:index.html.twig',array('processid' => $processId,'processName'=>$processName,'valueName'=>$valueName,'value'=>$value));
                        }
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}

/*
 * jbpm.client symfony service id
 * cms symfony service parameter 

$data = array();
$data["email"] = "bjoern@xrow.de";


$clientShop = $this->container->get('jbpm.client.shop');
$process = $client->getProcess( "order");
$process->start( $data );
$task = $process->currentTask();
$task->complete();
$process->terminate();


$clientCMS = $this->container->get('jbpm.client');
$process = $clientCMS->getProcess( "publish", "cms" );

*/

