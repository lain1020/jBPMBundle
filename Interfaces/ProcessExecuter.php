<?php
namespace xrow\jBPMBundle\Interfaces;

/**
 * ProcessExecuter interface.
 */
interface ProcessExecuter
{
    /**
     * getRelatedTasks function.
     *
     * @param string $processName
     * Throws Exception on error if could not find Task
     */
    public function getRelatedTasks($processName);
    
    /**
     * runProcess function.
     * 
     * @param array $classArray--Given a number of tasks
     * @param JBPMService $jbpmService
     * Throws Exception on error Process is not compelted
    */
    public function runProcess($classArray,$jbpmService);
}