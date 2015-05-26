<?php
namespace xrow\jBPMBundle\Tests;

use xrow\jBPMBundle\Interfaces\TaskExecuter;
use xrow\JBPM\AbstractTask;

/* Here is a simple example */
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