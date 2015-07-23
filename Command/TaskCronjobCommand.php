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

        $reserved_tasks = $jbpmService->getTasks(Task::STATUS_IN_RESERVED);
        if (count( $reserved_tasks ) > 0) {
            foreach ($reserved_tasks as $reserved_task) {
                $forwardtasks = $jbpmService->getForwardTasks($reserved_task->processinstanceid);

                if (count($forwardtasks) > 0) {
                    foreach ($forwardtasks as $forwardtask) {
                        try {
                            $forwardtask->start();
                        } catch (Exception $e) {
                            $logger = $container->get('logger');
                            $errorText = 'Error TaskCronjobCommand: ' . $e->getMessage();
                            $logger->error($errorText);
                            $output->writeln($errorText);
                        }
                    }
                }
            }
        }

        $inprogress_tasks = $jbpmService->getTasks(Task::STATUS_IN_PROGRESS);
        if (count($inprogress_tasks) > 0) {
            foreach ($inprogress_tasks as $task) {
                foreach ($curtaskObjects as $taskString => $taskClass) {
                    $taskStringArray = explode("-",$taskString);
                    $processId = $taskStringArray[0];
                    $taskName = $taskStringArray[1];
                    if ($task->taskname === $taskName && $task->processid === $processId) {
                        $processInstance = new ProcessInstance($task->processinstanceid, $jbpmService->getClient());
                        if (class_exists($taskClass)) {
                            $taskInstance = new $taskClass($processInstance, $task, $container);
                            $taskInstance->execute();
                        }
                        try {
                            $task->complete();
                        } catch (Exception $e) {
                            $logger = $container->get('logger');
                            $errorText = 'Error TaskCronjobCommand: ' . $e->getMessage();
                            $logger->error($errorText);
                            $output->writeln($errorText);
                        }
                    }
                }
            }
        } else {
            $output->writeln("there is no running instance of the Task!");
        }
    }
}