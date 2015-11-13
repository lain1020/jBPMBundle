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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $jbpmService = $container->get('jbpm.client');
        $curtaskObjects = $container->get('jbpm.task')->getTaskNameArray();
        
        foreach ($curtaskObjects as $taskString => $taskClass) {
            if (strpos($taskString ,"StoreSalesforce") !== false) {
                $StoreSalesforceClass = $taskClass;
            }
            elseif(strpos($taskString ,"StoreMediabase") !== false) {
                $StoreMediabaseClass = $taskClass;
            }
        }
        
        $reserved_tasks = $jbpmService->getTasks(Task::STATUS_IN_RESERVED);
        if (count( $reserved_tasks ) > 0) {
            foreach ($reserved_tasks as $reserved_task) {
              if($reserved_task->taskname != 'SalesforceFeedback') {
                $forwardtasks = $jbpmService->getForwardTasks($reserved_task->processinstanceid);

                if (count($forwardtasks) > 0) {
                    foreach ($forwardtasks as $forwardtask) {
                        //special cronjob for "Process Order"
                        if ($forwardtask->taskname === 'StoreSalesforce') {
                            if (!is_null($StoreSalesforceClass) && !is_null($StoreMediabaseClass)) {
                                try{
                                    $forwardtask->start();
                                    $SalesforceInstance = new ProcessInstance($reserved_task->processinstanceid, $jbpmService->getClient());
                                    $StoreSalesforcetaskInstance = new $StoreSalesforceClass($SalesforceInstance, $forwardtask, $container);
                                    $status = $StoreSalesforcetaskInstance->execute();
                                    if ($status !== false)
                                        $forwardtask->complete();
                                } catch (Exception $e) {
                                    $logger = $container->get('logger');
                                    $errorText = 'Error TaskCronjobCommand: ' . $e->getMessage();
                                    $logger->error($errorText);
                                    $output->writeln($errorText);
                                    $forwardtask->suspend();
                                    continue;
                                }
                                $getStoreMediabaseTasks = $jbpmService->getForwardTasks($reserved_task->processinstanceid);
                                foreach ($getStoreMediabaseTasks as $getStoreMediabaseTask) {
                                    if ($getStoreMediabaseTask->taskname === 'StoreMediabase') {
                                        try{
                                            $getStoreMediabaseTask->start();
                                            $MediabaseInstance = new ProcessInstance($reserved_task->processinstanceid, $jbpmService->getClient());
                                            $StoreMediabaseTaskInstance = new $StoreMediabaseClass($MediabaseInstance, $getStoreMediabaseTask, $container);
                                            $status = $StoreMediabaseTaskInstance->execute();
                                            if ($status !== false)
                                                $getStoreMediabaseTask->complete();
                                        } catch (Exception $e) {
                                            $logger = $container->get('logger');
                                            $errorText = 'Error TaskCronjobCommand: ' . $e->getMessage();
                                            $logger->error($errorText);
                                            $output->writeln($errorText);
                                            $getStoreMediabaseTask->suspend();
                                            continue;
                                        }
                                    }
                                }
                            }
                        } else {
                            try {
                                $forwardtask->start();
                            } catch (Exception $e) {
                                $logger = $container->get('logger');
                                $errorText = 'Error TaskCronjobCommand: ' . $e->getMessage();
                                $logger->error($errorText);
                                $output->writeln($errorText);
                                $forwardtask->suspend();
                                continue;
                            }
                        }
                    }
                }
              }
            }
        }

        $inprogress_tasks = $jbpmService->getTasks(Task::STATUS_IN_PROGRESS);
        if (count($inprogress_tasks) > 0) {
            foreach ($inprogress_tasks as $task) {
                //special cronjob for "Process Order"
                if ($task->taskname === 'StoreSalesforce') {
                    if (!is_null($StoreSalesforceClass) && !is_null($StoreMediabaseClass)) {
                        try{
                            $SalesforceInstance = new ProcessInstance($task->processinstanceid, $jbpmService->getClient());
                            $StoreSalesforcetaskInstance = new $StoreSalesforceClass($SalesforceInstance, $task, $container);
                            $status = $StoreSalesforcetaskInstance->execute();
                            if ($status !== false)
                                $task->complete();
                        } catch (Exception $e) {
                            $logger = $container->get('logger');
                            $errorText = 'Error TaskCronjobCommand: ' . $e->getMessage();
                            $logger->error($errorText);
                            $output->writeln($errorText);
                            $task->suspend();
                            continue;
                        }
                        $getStoreMediabaseTasks = $jbpmService->getForwardTasks($task->processinstanceid);
                        foreach ($getStoreMediabaseTasks as $getStoreMediabaseTask) {
                            if ($getStoreMediabaseTask->taskname === 'StoreMediabase') {
                                try{
                                    $getStoreMediabaseTask->start();
                                    $MediabaseInstance = new ProcessInstance($task->processinstanceid, $jbpmService->getClient());
                                    $StoreMediabaseTaskInstance = new $StoreMediabaseClass($MediabaseInstance, $getStoreMediabaseTask, $container);
                                    $status = $StoreMediabaseTaskInstance->execute();
                                    if ($status !== false)
                                        $getStoreMediabaseTask->complete();
                                } catch (Exception $e) {
                                    $logger = $container->get('logger');
                                    $errorText = 'Error TaskCronjobCommand: ' . $e->getMessage();
                                    $logger->error($errorText);
                                    $output->writeln($errorText);
                                    $getStoreMediabaseTask->suspend();
                                    continue;
                                }
                            }
                        }
                    }
                } else {
                    foreach ($curtaskObjects as $taskString => $taskClass) {
                        $taskStringArray = explode("-",$taskString);
                        $processId = $taskStringArray[0];
                        $taskName = $taskStringArray[1];
                        if ($task->taskname === $taskName && $task->processid === $processId) {
                            $processInstance = new ProcessInstance($task->processinstanceid, $jbpmService->getClient());
                            try {
                                if (class_exists($taskClass)) {
                                    $taskInstance = new $taskClass($processInstance, $task, $container);
                                    $status = $taskInstance->execute();
                                    if ($status !== false)
                                        $task->complete();
                                }
                            } catch (Exception $e) {
                                $logger = $container->get('logger');
                                $errorText = 'Error TaskCronjobCommand: ' . $e->getMessage();
                                $logger->error($errorText);
                                $output->writeln($errorText);
                                $task->suspend();
                                continue;
                            }
                        }
                    }
                }
            }
        } else {
            //Do Nothing!
           // $output->writeln("there is no running instance of the Task!");
        }
        unset($reserved_tasks，$inprogress_tasks);
    }
}
