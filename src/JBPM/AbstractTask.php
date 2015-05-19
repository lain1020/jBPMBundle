<?php
namespace xrow\JBPM;

use GuzzleHttp\Client;

class AbstractTask
{
    /**
     * class constructor
     * 
     * @param object $process the instance of Process
     * @param object $task the instance of Task
     */
    public function __construct( ProcessInstance $processInstance, Task $task )
    {
        $this->processInstance=$processInstance;
        $this->task=$task;
    }
}