<?php
namespace xrow\jBPMBundle\Service;

class TaskService 
{
    protected $taskExecuterArray;
    protected $taskNameArray;
    protected $projectName;
    protected $processNameArray;
    
    public function __construct( $config)
    {
        foreach($config as $project_name => $config_items)
        {
            $this->project_name =$project_name;
            foreach($config_items as $process_name => $config_item)
            {
                $process_name_array[]= $process_name;
                foreach($config_item as $task_name => $task_executer)
                {
                    $task_name_array[$task_executer]=$task_name;
                }
            }
            $this->process_name_array=$process_name_array;
            $this->task_name_array = $task_name_array;
        }
    }

    public function getTaskNameArray()
    {
        $taskNameArray = $this->task_name_array;
        return $taskNameArray;
    }
    public function getProjectName()
    {
        $projectName = $this->project_name;
        return $projectName;
    }
    public function getProcessNameArray()
    {
        $processNameArray = $this->process_name_array;
        return $processNameArray;
    }
}