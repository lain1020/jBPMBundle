<?php
namespace xrow\JBPM;

class ProcessInstance
{
    /**
     * @var Process definition id
     */
    private $processInstanceId;
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * class constructor
     * 
     * @param int $id processInstanceId
     * @param object $client a Guzzle client
     */
    public function __construct($id, $client)
    {
        $this->processInstanceId = $id;
        $this->client = $client;
    }

    /**
     * @return Process Instance Id
     */
    public function getProcessInstanceId()
    {
        return $this->processInstanceId;
    }

   /**
    * @return Objeck of Task
    * @throws Exception If Task does not exist
    */
    public function currentTask()
    {
        $processInsId = $this->getProcessInstanceId();
        try {
            $task_summary = $this->client->get('task/query?processInstanceId='.$processInsId);
            // Get only allowed tasks
            if ($task_summary->getStatusCode() == 200) {
                $task_json = $task_summary->json();

                foreach ($task_json['taskSummaryList'] as $task_att) {
                    if (!isset($task_id)) {
                        $task_id = $task_att['task-summary']['id'];
                        break;
                    }
                }
            }

            $task_start = $this->client->post('task/'.$task_id.'/start');
            // Get only allowed tasks
            if ($task_start->getStatusCode() == 200) {
                $task_status = $task_start->json();
                if ($task_status['status'] == Task::STATUS_SUCCESS) {
                    $task = new Task($task_id, $this->client);
                    return $task;
                } else {
                    throw new Exception("Task does not exist!");
                }
            }
        } catch(\Exception $e) {
            // do nothing
        }
    }

   /**
    * @return  data of Process in an array
    */
    public function getData()
    {
        $data = array();
        $processInstId = $this->getProcessInstanceId();
        try {
            $datalist = $this->client->get('history/instance/'.$processInstId.'/variable');
            // Get only allowed tasks
            if ($datalist->getStatusCode() == 200) {
                $datas = $datalist->json();
                foreach ($datas['historyLogList'] as $dataitems) {
                    $data[$dataitems['variable-instance-log']['variable-id']] = $dataitems['variable-instance-log']['value'];
                }
            }
        } catch(\Exception $e) {
            // do nothing
        }
        return $data;
    }

    /**
     * Abort process instance
     */
    public function abort($deploymentId)
    {
        $processInstId = $this->getProcessInstanceId();
        try {
            $processInstAbortRequest = $this->client->post('runtime/'.$deploymentId.'/process/instance/'.$processInstId.'/abort');
        } catch(\Exception $e) {
            // do nothing
        }
    }
}