<?php
namespace xrow\jBPMBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class TaskCronjobCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName( 'jbpm:task' )
            ->setDescription( "jBPM task controller" )
            ->setDefinition(
                array()
            );
    }
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $taskObjects=$this->getContainer()->get('jbpm.client')->getInprogressTasks();
        
        
        $curtaskObjects=$this->getContainer()->get('jbpm.task')->getTaskNameArray();
        
        foreach($curtaskObjects as $taskExecuter => $taskName)
        {
            $taskClass=$taskExecuter;
            $task = new $taskClass();
            foreach($taskObjects['taskSummaryList'] as $taskObject)
            {
                if($taskObject['task-summary']['name'] == $taskName)
                {
                    $taskOutput=$task->execute($taskObject['task-summary']['id']);

                    if(count($taskOutput)>0)
                    {
                       foreach($taskOutput as $valueName => $value)
                       {
                           $output->writeln( "{$valueName}: {$value}" );
                       }
                    }
                    else{
                        $output->writeln( "error!" );
                    }
                }
            }
        }
    }
}
