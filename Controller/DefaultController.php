<?php

namespace xrow\jBPMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp\Client;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $data = array();
        $data["name"] = "Test Name";
        $data["email"] = "test@email.de";
        
        //get Guzzle Client
        $client=$this->container->get('jbpm.client');
        
        $processDefinition = $client->getProcess('cms.publish');
         
        $processInstance=$processDefinition->start($data);
        if(!is_null($processInstance))
        {
            $task=$processInstance->currentTask();
            if(!is_null($task))
            {
                return $this->render('xrowjBPMBundle:Default:index.html.twig',array('processid' => $processInstance->getProcessInstanceId(),'processName'=>$processDefinition->getProcessName()));
            }else{
                return NULL;
            }
        }else{
            return NULL;
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

