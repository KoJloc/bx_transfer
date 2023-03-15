<?php

namespace App\Http\Controllers\Entities;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRest;
use App\Jobs\ProcessUpdate;
use App\Models\History;
use App\Models\TransferGroup;

class EntitiesUpdateController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    protected $counterEntities = 0;
    protected $markedActivePeopleCount = 0;
    protected $currentPerson = -1; // Ебать рофл, но работает в getCurrentPersonID()

    //Рандомайзер ID сотрудников, для равномерной передачи сущностей
    private function getCurrentPersonID()
    {
        $this->markedActivePeopleCount = count($this->markedActivePeople);
        if (++$this->currentPerson == $this->markedActivePeopleCount) {
            $this->currentPerson = 0;
        }
        return $this->currentPerson;
    }

    private function clearCurrentPersonID()
    {
        $this->currentPerson = -1;
    }

    protected $transferGroup = [];

    public function update($request, $entitiesByEmployerId, ...$toUsers)
    {
        //Если пустой массив на кого кидаем сущности выходим
        if (!empty($request['onlyActiveDepartments'])) {
            $this->markedActivePeople = $request['onlyActiveDepartments'];
            info('VUE(toUsers):', [$this->markedActivePeople]);
        } elseif (!empty($toUsers)) {
            $this->markedActivePeople = $toUsers[0];
            info('API(toUsers):', [$this->markedActivePeople]);
        } else {
            return 'Неверные входные данные';
        }
        //Создаем новую трансфер группу
        $this->transferGroup = TransferGroup::create();
        info($this->transferGroup);
        //Записываем массив в переменную
        if (!empty($entitiesByEmployerId['leads'])) {
            //Перебираем массив лидов
            foreach ($entitiesByEmployerId['leads'] as $leadID => $lead) {
                ++$this->counterEntities;
                //Вызываем рандомайзер ID сотрудников
                $currentPerson = $this->markedActivePeople[$this->getCurrentPersonID()];
                //Перебираем таски у Лида
                if (isset($entitiesByEmployerId['leads'][$leadID]['tasks'])) {
                    foreach ($entitiesByEmployerId['leads'][$leadID]['tasks'] as $taskID => $task) {
                        ++$this->counterEntities;
                        //Запись в БД
                        History::firstOrCreate([
                            'entity_ID' => $taskID,
                            'entity_type' => 'task',
                            'old_responsible_ID' => $task,
                            'new_responsible_ID' => $currentPerson,
                            'transfer_group_ID' => $this->transferGroup->id,
                        ]);
                        $entitiesByEmployerId['leads'][$leadID]['tasks'][$taskID] = $currentPerson;
                    }
                }
                if (isset($entitiesByEmployerId['leads'][$leadID]['activity'])) {
                    foreach ($entitiesByEmployerId['leads'][$leadID]['activity'] as $activityID => $activity) {
                        ++$this->counterEntities;
                        History::firstOrCreate([
                            'entity_ID' => $activityID,
                            'entity_type' => 'activity',
                            'old_responsible_ID' => $activity,
                            'new_responsible_ID' => $currentPerson,
                            'transfer_group_ID' => $this->transferGroup->id,
                        ]);
                        $entitiesByEmployerId['leads'][$leadID]['activity'][$activityID] = $currentPerson;
                    }
                }
                History::firstOrCreate([
                    'entity_ID' => $leadID,
                    'entity_type' => 'lead',
                    'old_responsible_ID' => $entitiesByEmployerId['leads'][$leadID]['resp'],
                    'new_responsible_ID' => $currentPerson,
                    'transfer_group_ID' => $this->transferGroup->id,
                ]);
                $entitiesByEmployerId['leads'][$leadID]['resp'] = $currentPerson;
            }
        }
        $this->clearCurrentPersonID();
        if (!empty(isset($entitiesByEmployerId['deals']))) {
            foreach ($entitiesByEmployerId['deals'] as $dealID => $deal) {
                ++$this->counterEntities;
                $currentPerson = $this->markedActivePeople[$this->getCurrentPersonID()];
                if (isset($entitiesByEmployerId['deals'][$dealID]['tasks'])) {
                    foreach ($entitiesByEmployerId['deals'][$dealID]['tasks'] as $taskID => $task) {
                        ++$this->counterEntities;
                        History::firstOrCreate([
                            'entity_ID' => $taskID,
                            'entity_type' => 'task',
                            'old_responsible_ID' => $task,
                            'new_responsible_ID' => $currentPerson,
                            'transfer_group_ID' => $this->transferGroup->id,
                        ]);
                        $entitiesByEmployerId['deals'][$dealID]['tasks'][$taskID] = $currentPerson;
                    }
                }
                if (isset($entitiesByEmployerId['deals'][$dealID]['activity'])) {
                    foreach ($entitiesByEmployerId['deals'][$dealID]['activity'] as $activityID => $activity) {
                        ++$this->counterEntities;
                        History::firstOrCreate([
                            'entity_ID' => $activityID,
                            'entity_type' => 'activity',
                            'old_responsible_ID' => $activity,
                            'new_responsible_ID' => $currentPerson,
                            'transfer_group_ID' => $this->transferGroup->id,
                        ]);
                        $entitiesByEmployerId['deals'][$dealID]['activity'][$activityID] = $currentPerson;
                    }
                }
                History::firstOrCreate([
                    'entity_ID' => $dealID,
                    'entity_type' => 'deal',
                    'old_responsible_ID' => $deal['resp'],
                    'new_responsible_ID' => $currentPerson,
                    'transfer_group_ID' => $this->transferGroup->id,
                ]);
                $entitiesByEmployerId['deals'][$dealID]['resp'] = $currentPerson;
            }
        }
        $this->clearCurrentPersonID();
        if (!empty(isset($entitiesByEmployerId['contacts']))) {
            foreach ($entitiesByEmployerId['contacts'] as $contactID => $contact) {
                ++$this->counterEntities;
                $currentPerson = $this->markedActivePeople[$this->getCurrentPersonID()];
                if (isset($entitiesByEmployerId['contacts'][$contactID]['tasks'])) {
                    foreach ($entitiesByEmployerId['contacts'][$contactID]['tasks'] as $taskID => $task) {
                        ++$this->counterEntities;
                        $contactTaskHistory = History::firstOrCreate([
                            'entity_ID' => $taskID,
                            'entity_type' => 'task',
                            'old_responsible_ID' => $task,
                            'new_responsible_ID' => $currentPerson,
                            'transfer_group_ID' => $this->transferGroup->id,
                        ]);
                        ProcessUpdate::dispatch($contactTaskHistory);
                        $entitiesByEmployerId['contacts'][$contactID]['tasks'][$taskID] = $currentPerson;
                    }
                }
                if (isset($entitiesByEmployerId['contacts'][$contactID]['activity'])) {
                    foreach ($entitiesByEmployerId['contacts'][$contactID]['activity'] as $activityID => $activity) {
                        ++$this->counterEntities;
                        $contactActivityHistory = History::firstOrCreate([
                            'entity_ID' => $activityID,
                            'entity_type' => 'activity',
                            'old_responsible_ID' => $activity,
                            'new_responsible_ID' => $currentPerson,
                            'transfer_group_ID' => $this->transferGroup->id,
                        ]);
                        ProcessUpdate::dispatch($contactActivityHistory);
                        $entitiesByEmployerId['contacts'][$contactID]['activity'][$activityID] = $currentPerson;
                    }
                }
                $contactHistory[] = History::firstOrCreate([
                    'entity_ID' => $contactID,
                    'entity_type' => 'contact',
                    'old_responsible_ID' => $contact['resp'],
                    'new_responsible_ID' => $currentPerson,
                    'transfer_group_ID' => $this->transferGroup->id,
                ]);
                ProcessUpdate::dispatch($contactHistory);
                $entitiesByEmployerId['contacts'][$contactID]['resp'] = $currentPerson;
            }
        }
        $this->clearCurrentPersonID();
        $this->generateBitrixUpdateRequest($entitiesByEmployerId);
    }

    public $updateParams = [];

    //Генерируем update метод для Битрикса
    private function updateMethod($changeableEntity)
    {
        if ($changeableEntity['entity_type'] == 'task') {
            return 'tasks.task.update';
        }
        $changeableEntity['entity_type'] = ($changeableEntity['entity_type'] == 'activity') ? 'activity' : $changeableEntity['entity_type'];
        return 'crm.' . $changeableEntity['entity_type'] . '.update';
    }

    //Получаем индекс сущности из БД
    private function getIdFromDatabase($entityID)
    {
        return History::where('entity_ID', $entityID)->first();
    }

    private $counter = 0;

    private $counterIter = 0;

    private function sendUpdateRequest($counter)
    {
        $overallCountIter = (int)($this->counterEntities / 50);
        if ($counter == 50) {
            $this->counter = 0;
//            $this->storeBitrix($this->updateParams);
            $this->updateParams = [];
            $this->counterIter++;
        } elseif ($this->counterIter == $overallCountIter && $this->counter == $this->counterEntities % 50) {
//            $this->storeBitrix($this->updateParams);
            $this->counter = 0;
            $this->updateParams = [];
        }
    }

    private function respFieldType($changeableEntity)
    {
        //CONTACT - 'ASSIGNED_BY_ID'
        //LEAD - 'ASSIGNED_BY_ID'
        //DEAL - 'ASSIGNED_BY_ID'
        //ACTIVITY - 'RESPONSIBLE_ID';
        //TASKS - 'RESPONSIBLE_ID';
        return $changeableEntity['entity_type'] == ('contact' || 'lead' || 'deal') ? 'ASSIGNED_BY_ID' : 'RESPONSIBLE_ID';
    }

    private function generateUpdateParams($historyId, $id, $fields)
    {
        $respFieldType = $this->respFieldType($historyId);
        $this->updateParams[$historyId['id']]['method'] = $this->updateMethod($historyId);
        $this->updateParams[$historyId['id']]['params']['id'] = $id;
        $this->updateParams[$historyId['id']]['params']['fields'][$respFieldType] = $fields;
    }

    public function generateBitrixUpdateRequest($entitiesByEmployerId)
    {
        foreach ($entitiesByEmployerId as $entityType => $entityValues) {
            foreach ($entityValues as $entityID => $values) {
                if (!empty($values['tasks'])) {
                    foreach ($values['tasks'] as $taskID => $task) {
                        $historyTaskID = $this->getIdFromDatabase($taskID);
                        $this->generateUpdateParams($historyTaskID, $taskID, $values['resp']);
                        ++$this->counter;
                        $this->sendUpdateRequest($this->counter);
                    }
                }
                if (!empty($values['activity'])) {
                    foreach ($values['activity'] as $activityID => $activity) {
                        $historyActivityID = $this->getIdFromDatabase($activityID);
                        $this->generateUpdateParams($historyActivityID, $activityID, $values['resp']);
                        ++$this->counter;
                        $this->sendUpdateRequest($this->counter);
                    }
                }
                $historyEntityID = $this->getIdFromDatabase($entityID);
                $this->generateUpdateParams($historyEntityID, $entityID, $values['resp']);
                ++$this->counter;
                $this->sendUpdateRequest($this->counter);
            }
        }
    }

    private $successIDs = [];
    private $errorIDs = [];

    public function storeBitrix($updateParams)
    {
        info('Update response:', $updateParams);
        $updateResponse = CRest::callBatch($updateParams);
        info('Update response:', $updateResponse);
        if (isset($updateResponse) && !empty($updateResponse['result'])) {
            if (!empty($updateResponse['result']['result_error'])) {
                foreach ($updateResponse['result']['result_error'] as $responseKey => $responseValue) {
                    $this->errorIDs[] = $responseKey;
                }
                info('Errors:', $this->errorIDs);
                History::whereIn('id', $this->errorIDs)->update(['transfer_status' => false]);
                $this->errorIDs = [];
            } elseif (!empty($updateResponse['result']['result'])) {
                foreach ($updateResponse['result']['result'] as $responseKey => $responseValue) {
                    $this->successIDs[] = $responseKey;
                }
                info('Success:', $this->successIDs);
                History::whereIn('id', $this->successIDs)->update(['transfer_status' => true]);
                $this->successIDs = [];
            }
        }
    }

    public function checkBDforErrors()
    {
        $transferGroupStatus = TransferGroup::where('transfer_group_status', 0)->first();
        $historyList = History::where('transfer_group_ID', $transferGroupStatus['id'])->get();
        $historyCount = count($historyList);
        $trueCounter = 0;
        $needToRetryIDs = [];
        foreach ($historyList as $history) {
            if ($history['transfer_status'] == 1) {
                $trueCounter++;
            } else {
                $needToRetryIDs[] = $history;
            }
        }
        if ($historyCount == $trueCounter) {
            TransferGroup::where('id', $transferGroupStatus['id'])->update(['transfer_group_status' => true]);
        } else {
            $this->storeBitrixRetry($needToRetryIDs);
        }
    }

    private $counterRetryIter;
    private $retryUpdateParams;
    private $retryCounter = 0;

    public function sendRetryToBitrix($overallCountIter, $retryListCount)
    {
        if ($this->retryCounter == 50) {
            $this->storeBitrix($this->retryUpdateParams);
            $this->retryUpdateParams = [];
            $this->counterRetryIter++;
            $this->retryCounter = 0;
        } elseif ($this->counterRetryIter == $overallCountIter && $this->retryCounter == $retryListCount % 50) {
            $this->storeBitrix($this->retryUpdateParams);
            $this->retryCounter = 0;
            $this->retryUpdateParams = [];
        }
    }

    public function storeBitrixRetry($retryList)
    {
        info($retryList);
        $retryListCount = count($retryList);
        $this->retryUpdateParams = [];
        $overallCountIter = (int)($retryListCount / 50);
        foreach ($retryList as $retry) {
            $this->retryCounter++;
            $respFieldType = $this->respFieldType($retry);
            $this->retryUpdateParams[$retry['id']]['method'] = $this->updateMethod($retry);
            $this->retryUpdateParams[$retry['id']]['params']['id'] = $retry['entity_ID'];
            $this->retryUpdateParams[$retry['id']]['params']['fields'][$respFieldType] = $retry['new_responsible_ID'];

            $this->sendRetryToBitrix($overallCountIter, $retryListCount);
        }
    }
}
