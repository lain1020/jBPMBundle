<?php
namespace xrow\jBPMBundle\src\JBPM;

use GuzzleHttp\Client;

class AbstractTask
{
    /**
     * @param object $process Instance of Process
     * @param object $task Instance of Task
     */
    public function __construct( ProcessInstance $processInstance, Task $task )
    {
        $this->processInstance=$processInstance;
        $this->task=$task;
    }
}