<?php
namespace xrow\jBPMBundle\Service;

class TaskService 
{
    /**
     * @var an array of Task Name
     */
    protected $taskNameArray;
    
    /**
     * class constructor
     * 
     * @param array $config an array of configuration values
     */
    public function __construct( $config)
    {
        foreach($config as $project_name => $config_items)
        {
            foreach($config_items as $process_name => $config_item)
            {
                foreach($config_item as $task_name => $task_executer)
                {
                    $task_string= $project_name.".".$process_name."-".$task_name;
                    $task_name_array[$task_string]=$task_executer;
                }
            }
        }
        $this->task_name_array = $task_name_array;
    }

    /**
     * gets the task names
     * 
     * @return array of Task Name
     */
    public function getTaskNameArray()
    {
        $taskNameArray = $this->task_name_array;
        return $taskNameArray;
    }
}