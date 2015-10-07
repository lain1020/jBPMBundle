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
 * Command: php ezpublish/console jbpm:copy:process --processId=123
 * */

class CopyProcessInstanceCommand extends ContainerAwareCommand
{
    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
        ->setName('jbpm:copy:process')
        ->setDescription('jBPM copy a process instance with data')
        ->addOption(
            'processId',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Sets redirect uri for client.'
        )
        ->setHelp(<<<EOT
The <info>%command.name%</info>command copy a process instance.
<info>php %command.full_name% [--processId=...]...] name</info>
EOT
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
        // Get the old process instance
        $processId = '';
        $processIdArray = $input->getOption('processId');
        if (isset($processIdArray[0]))
            $processId = $processIdArray[0];
        if ($processId != '') {
            $jbpmService = $container->get('jbpm.client');
            $copyFromProcessInstance = new ProcessInstance($processId, $jbpmService->client);
            $data = $copyFromProcessInstance->getData();
            $processData = $copyFromProcessInstance->getProcessInstanceData();
            if (count($data) > 0 && count($processData) > 0 && isset($processData['process-id'])) {
                $output->writeln(
                    sprintf(
                        'Get process instance with id <info>%s</info>, data <info>%s</info>',
                        $processId,
                        implode('| ', $data)
                    )
                );
                // Copy process instance
                $processDefinition = $jbpmService->getProcess($processData['process-id']);
                // Send our data array to the workflow engine
                $processInstance = $processDefinition->start($data);
                if($processInstance !== null) {
                    $output->writeln(
                        sprintf(
                            'Copied process instance with id <info>%s</info>',
                            $processInstance->getProcessInstanceId()
                        )
                    );
                    $task = $processInstance->currentTask();
                    if($task !== null) {
                        $output->writeln('Created task');
                    }
                }
            }
        }
    }
}
