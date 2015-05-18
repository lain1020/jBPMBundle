<?php
namespace xrow\jBPMBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use xrow\jBPMBundle\src\JBPM\Task;
use xrow\jBPMBundle\src\JBPM\ProcessInstance;

use Exception;

class TaskCronjobCommand extends ContainerAwareCommand
{
    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName( 'jbpm:task' )
            ->setDescription( "jBPM task controller" )
            ->setDefinition(
                array()
            );
    }
    
   /**
     * Executes the command
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $jbpmService=$this->getContainer()->get('jbpm.client');
        $curtaskObjects=$this->getContainer()->get('jbpm.task')->getTaskNameArray();
        
        $reserved_tasks = $jbpmService->getTasks(Task::STATUS_IN_RESERVED);

        if(count($reserved_tasks) > 0)
        {
            foreach($reserved_tasks as $task_r)
            {
                $task_r->getTaskSummaryArray();
                foreach($inprogress_tasks as $task_i)
                {
                    $task_i->getTaskSummaryArray();
                    
                    if($task_r->processinstanceid == $task_i->processinstanceid)
                    {
                        try{
                            $task_r->start();
                        }catch(Exception $e)
                        {
                            echo 'Caught exception: ',  $e->getMessage(), "\n";
                        }
                    }
                 }
             }
         }
        
        $inprogress_tasks = $jbpmService->getTasks(Task::STATUS_IN_PROGRESS);
        if(count( $inprogress_tasks ) > 0 )
        {
            foreach($inprogress_tasks as $task)
            {
                $task->getTaskSummaryArray();

                foreach($curtaskObjects as $taskString => $taskExecuter)
                {
                    $taskStringArray= explode("-",$taskString);
                    $processId=$taskStringArray[0];
                    $taskName=$taskStringArray[1];
                   
                    if($task->taskname === $taskName && $task->processid === $processId)
                    {
                        $processInstance=new ProcessInstance($task->processinstanceid, $jbpmService->getClient());
                        $taskClass=$taskExecuter;
                        $taskFunction = new $taskClass($processInstance,$task);
                        try{
                            $taskFunction->execute();
                            $task->complete();
                        }catch (Exception $e)
                        {
                            echo 'Caught exception: ',  $e->getMessage(), "\n";
                        }
                    }
                }
            }
        }else{
            $output->writeln( "there is no running instance of the Task!" );
        }
    }
}
