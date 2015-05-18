<?php
namespace xrow\jBPMBundle\Tests;

use xrow\jBPMBundle\Interfaces\TaskExecuter;
#Richtig! PSR 4
//use xrow\JBPM\Task;
#FALSCH ! nicht PSR 4
use xrow\jBPMBundle\src\JBPM\Task;
use xrow\jBPMBundle\src\JBPM\ProcessInstance;
use xrow\jBPMBundle\src\JBPM\AbstractTask;
use GuzzleHttp\Client;

class publishevent1 extends AbstractTask implements TaskExecuter
{
    /**
     * Throws Exception on error Task is not compelted
     * @return void 
     */
    public function execute()
    {
        $data=$this->processInstance->getData();
        /*mail("bjoern@xrow.de", "mail", $data["email"]);
        if(!file_get_contents("http://domain-is-not-here-asdf.com/"))
        { 
            throw  new \Exception( "Server down");
        }*/
        return $data;
    }
}