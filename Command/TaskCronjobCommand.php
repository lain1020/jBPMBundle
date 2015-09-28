<?php
namespace xrow\jBPMBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use xrow\JBPM\Task;
use xrow\JBPM\ProcessInstance;
use xrow\jBPMBundle\Command\CustomerProcessExecuter;

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
        $curprocessNames = $container->get('jbpm.task')->getProcessNameArray();
        $processObj = new CustomerProcessExecuter($container);
        foreach($curprocessNames as $curprocessName) {
            $classArray=$processObj->getRelatedTasks($curprocessName);
            $processObj->runProcess($classArray,$jbpmService);
        }
    }
}