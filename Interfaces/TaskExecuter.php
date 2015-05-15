<?php
namespace xrow\jBPMBundle\Interfaces;

interface TaskExecuter
{
    /*
     * Throws Exception on error Task is not compelted
    */
    public function execute( $taskid );
}