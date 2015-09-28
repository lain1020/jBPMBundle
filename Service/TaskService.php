<?php
namespace xrow\jBPMBundle\Service;

class TaskService 
{
    /**
     * @var an array of Task Name
     */
    protected $taskNameArray;
    
    /**
     * @var an array of Process Name
     */
    protected $processNameArray;

    /**
     * class constructor
     * 
     * @param array $config an array of configuration values
     */
    public function __construct( $config)
    {
        $process_name_array = array();
        foreach($config as $project_name => $config_items)
        {
            foreach($config_items as $process_name => $config_item)
            {
                foreach($config_item as $task_name => $task_executer)
                {
                    $task_string = $project_name.".".$process_name."-".$task_name;
                    $task_name_array[$task_string] = $task_executer;
                    array_push($process_name_array,$process_name);
                }
            }
        }
        $this->task_name_array = $task_name_array;
        $this->process_name_array = $process_name_array;
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
    
    /**
     * gets the Process names
     *
     * @return array of Process Name
     */
    public function getProcessNameArray()
    {
        $processNameArray = $this->process_name_array;
        return $processNameArray;
    }
}