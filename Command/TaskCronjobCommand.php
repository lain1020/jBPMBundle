<?php
namespace xrow\jBPMBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use xrow\JBPM\Task;
use xrow\JBPM\ProcessInstance;

use Exception;

/**
 * Cronjob: Control of Start/Stop Task.
 * Command: php -d memory_limit=1024M ezpublish/console jbpm:task
 * */

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
        if(count( $reserved_tasks ) > 0 )
        {
            foreach($reserved_tasks as $reserved_task)
            {
                $forwardtasks=$jbpmService->getForwardTasks($reserved_task->processinstanceid);
                
                if(count($forwardtasks) > 0)
                {
                    foreach($forwardtasks as $forwardtask)
                    {
                        try{
                            $forwardtask->start();
                        }catch (Exception $e)
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
                foreach($curtaskObjects as $taskString => $taskExecuter)
                {
                    $taskStringArray= explode("-",$taskString);
                    $processId=$taskStringArray[0];
                    $taskName=$taskStringArray[1];
                   
                    if($task->taskname === $taskName && $task->processid === $processId)
                    {
                        $processInstance=new ProcessInstance($task->processinstanceid, $jbpmService->getClient());
                        $taskClass=$taskExecuter;
                        if(class_exists($taskClass))
                        {
                            $taskFunction = new $taskClass($processInstance,$task);
                            $taskFunction->execute();
                        }
                        try{
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
