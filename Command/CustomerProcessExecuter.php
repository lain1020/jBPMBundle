<?php
namespace xrow\jBPMBundle\Command;

use xrow\JBPM\Task;
use xrow\JBPM\ProcessInstance;
use xrow\jBPMBundle\Interfaces\ProcessExecuter;
use Exception;

/**
 * 
 *  Default implementation of ProcessExecuter.
 *
 */
class CustomerProcessExecuter implements ProcessExecuter
{
    /**
     * Constructs a CustomerProcessExecuter object.
     *
     * @param  $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    /**
     * Implements ProcessExecuter::getRelatedTasks($processName).
     */
    public function getRelatedTasks($processName) {
        $classArray = array();
        $curtaskObjects = $this->container->get('jbpm.task')->getTaskNameArray();
        foreach ($curtaskObjects as $taskString => $taskClass) {
            if (strpos($taskString ,$processName) !== false) {
                $taskNameTemp = explode("-",$taskString);
                $taskName = $taskNameTemp[1];
                $classArray[$taskName] = $taskClass;
            }
        }
        if(count($classArray)==0)
        {
            throw new Exception("Didn't find class!");
        } else {
            return $classArray;
        }
    }
    
    /**
     * Implements ProcessExecuter::runProcess($classArray,$jbpmService).
     */
    public function runProcess($classArray,$jbpmService) {
        foreach($classArray as $taskName => $className) {
            $reserved_tasks = $jbpmService->getTasks(Task::STATUS_IN_RESERVED);
            if (count( $reserved_tasks ) > 0) {
                foreach ($reserved_tasks as $reserved_task) {
                    if ($reserved_task->taskname === $taskName && $className != '') {
                        try{
                            $reserved_task->start();
                            $processInstance = new ProcessInstance($reserved_task->processinstanceid, $jbpmService->getClient());
                            $taskInstance = new $className($processInstance, $reserved_task, $this->container);
                            $taskInstance->execute();
                            $reserved_task->complete();
                        } catch (Exception $e) {
                            $logger = $this->container->get('logger');
                            $errorText = 'Error TaskCronjobCommand: ' . $e->getMessage();
                            $logger->error($errorText);
                            $reserved_task->suspend();
                            continue;
                        }
                    }
                }
            }
            $inprogress_tasks = $jbpmService->getTasks(Task::STATUS_IN_PROGRESS);
            if (count($inprogress_tasks) > 0) {
                foreach ($inprogress_tasks as $inprogress_task) {
                    if ($inprogress_task->taskname === $taskName && $className != '') {
                        try{
                            $processInstance = new ProcessInstance($inprogress_task->processinstanceid, $jbpmService->getClient());
                            $taskInstance = new $className($processInstance, $inprogress_task, $this->container);
                            $taskInstance->execute();
                            $inprogress_task->complete();
                        } catch (Exception $e) {
                            $logger = $this->container->get('logger');
                            $errorText = 'Error TaskCronjobCommand: ' . $e->getMessage();
                            $logger->error($errorText);
                            $inprogress_task->suspend();
                            continue;
                        }
                    }
                }
            }
        }
    }
}