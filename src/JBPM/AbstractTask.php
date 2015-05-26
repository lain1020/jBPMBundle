<?php
namespace xrow\JBPM;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AbstractTask
{
    /**
     * @var \xrow\JBPM\ProcessInstance
     */
    public $processInstance;
    /**
     * @var \xrow\JBPM\Task
     */
    public $task;
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public $container;

    /**
     * class constructor
     * 
     * @param object $process the instance of Process
     * @param object $task the instance of Task
     */
    public function __construct(ProcessInstance $processInstance, Task $task, ContainerInterface $container)
    {
        $this->processInstance = $processInstance;
        $this->task = $task;
        $this->container = $container;
    }
}