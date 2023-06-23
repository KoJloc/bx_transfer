<?php

namespace App\Http\Controllers\Entities;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRest;
use App\Jobs\ProcessUpdate;
use Illuminate\Http\Request;
use App\Models\History;
use App\Models\TransferGroup;
use Illuminate\Support\Facades\Log;

class EntitiesUpdateController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    private $currentPerson = -1;

    //Распределитель ID сотрудников
    private function getCurrentPersonID($markedActivePeople)
    {
        $markedActivePeopleCount = count($markedActivePeople);
        if (++$this->currentPerson == $markedActivePeopleCount) {
            $this->currentPerson = 0;
        }
        return $this->currentPerson;
    }

    private function clearCurrentPersonID()
    {
        $this->currentPerson = -1;
    }


//--------------------------------------------------------------------------------------------------------------------//
    public function update($entitiesByEmployerId, $toUsers, $newSource, $newSalesDepartment, $filtered)
    {
        $counterEntities = 0;
        $counterLeads = 0;
        $counterDeals = 0;
        $counterContacts = 0;
        $counterActivity = 0;
        $counterTasks = 0;

        if ($filtered) {
            $markedActivePeople = $toUsers;
            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/TransferInfo.log')
            ])->info('ToUsers:', [$markedActivePeople]);
        } else if (!empty($toUsers['to'])) {
            $markedActivePeople = $toUsers['to'];
            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/TransferInfo.log')
            ])->info('ToUsers:', [$markedActivePeople]);
        } else {
            return 'Неверные входные данные';
        }

        //Создаем новую трансфер группу
        $transferGroup = TransferGroup::create();
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/TransferInfo.log')
        ])->info('New transfer group: ', [$transferGroup['id']]);

        if (!empty($entitiesByEmployerId['leads'])) {
            foreach ($entitiesByEmployerId['leads'] as $leadID => $lead) {
                ++$counterEntities;
                ++$counterLeads;
                $currentPerson = $markedActivePeople[$this->getCurrentPersonID($markedActivePeople)];
                if (isset($entitiesByEmployerId['leads'][$leadID]['tasks'])) {
                    foreach ($entitiesByEmployerId['leads'][$leadID]['tasks'] as $taskID => $task) {
                        ++$counterEntities;
                        ++$counterTasks;
                        History::firstOrCreate([
                            'entity_ID' => $taskID,
                            'entity_type' => 'task',
                            'old_responsible_ID' => $task,
                            'new_responsible_ID' => $currentPerson,
                            'transfer_group_ID' => $transferGroup->id,
                        ]);
                        $entitiesByEmployerId['leads'][$leadID]['tasks'][$taskID] = $currentPerson;
                    }
                }
                if (isset($entitiesByEmployerId['leads'][$leadID]['activity'])) {
                    foreach ($entitiesByEmployerId['leads'][$leadID]['activity'] as $activityID => $activity) {
                        ++$counterEntities;
                        ++$counterActivity;
                        History::firstOrCreate([
                            'entity_ID' => $activityID,
                            'entity_type' => 'activity',
                            'old_responsible_ID' => $activity,
                            'new_responsible_ID' => $currentPerson,
                            'transfer_group_ID' => $transferGroup->id,
                        ]);
                        $entitiesByEmployerId['leads'][$leadID]['activity'][$activityID] = $currentPerson;
                    }
                }
                if (!isset($lead['resp'])) continue;
                History::firstOrCreate([
                    'entity_ID' => $leadID,
                    'entity_type' => 'lead',
                    'old_responsible_ID' => $lead['resp'],
                    'new_responsible_ID' => $currentPerson,
                    'transfer_group_ID' => $transferGroup->id,
                ]);
                $entitiesByEmployerId['leads'][$leadID]['resp'] = $currentPerson;
            }
        }
        $this->clearCurrentPersonID();
        if (!empty(isset($entitiesByEmployerId['deals']))) {
            foreach ($entitiesByEmployerId['deals'] as $dealID => $deal) {
                ++$counterEntities;
                ++$counterDeals;
                $currentPerson = $markedActivePeople[$this->getCurrentPersonID($markedActivePeople)];
                if (isset($entitiesByEmployerId['deals'][$dealID]['tasks'])) {
                    foreach ($entitiesByEmployerId['deals'][$dealID]['tasks'] as $taskID => $task) {
                        ++$counterEntities;
                        ++$counterTasks;
                        History::firstOrCreate([
                            'entity_ID' => $taskID,
                            'entity_type' => 'task',
                            'old_responsible_ID' => $task,
                            'new_responsible_ID' => $currentPerson,
                            'transfer_group_ID' => $transferGroup->id,
                        ]);
                        $entitiesByEmployerId['deals'][$dealID]['tasks'][$taskID] = $currentPerson;
                    }
                }
                if (isset($entitiesByEmployerId['deals'][$dealID]['activity'])) {
                    foreach ($entitiesByEmployerId['deals'][$dealID]['activity'] as $activityID => $activity) {
                        ++$counterEntities;
                        ++$counterActivity;
                        History::firstOrCreate([
                            'entity_ID' => $activityID,
                            'entity_type' => 'activity',
                            'old_responsible_ID' => $activity,
                            'new_responsible_ID' => $currentPerson,
                            'transfer_group_ID' => $transferGroup->id,
                        ]);
                        $entitiesByEmployerId['deals'][$dealID]['activity'][$activityID] = $currentPerson;
                    }
                }
                if (!isset($deal['resp'])) continue;
                History::firstOrCreate([
                    'entity_ID' => $dealID,
                    'entity_type' => 'deal',
                    'old_responsible_ID' => $deal['resp'],
                    'new_responsible_ID' => $currentPerson,
                    'transfer_group_ID' => $transferGroup->id,
                ]);
                $entitiesByEmployerId['deals'][$dealID]['resp'] = $currentPerson;
            }
        }
        $this->clearCurrentPersonID();
        if (!empty(isset($entitiesByEmployerId['contacts']))) {
            foreach ($entitiesByEmployerId['contacts'] as $contactID => $contact) {
                ++$counterEntities;
                ++$counterContacts;
                $currentPerson = $markedActivePeople[$this->getCurrentPersonID($markedActivePeople)];
                if (isset($entitiesByEmployerId['contacts'][$contactID]['tasks'])) {
                    foreach ($entitiesByEmployerId['contacts'][$contactID]['tasks'] as $taskID => $task) {
                        ++$counterEntities;
                        ++$counterTasks;
                        History::firstOrCreate([
                            'entity_ID' => $taskID,
                            'entity_type' => 'task',
                            'old_responsible_ID' => $task,
                            'new_responsible_ID' => $currentPerson,
                            'transfer_group_ID' => $transferGroup->id,
                        ]);
                        $entitiesByEmployerId['contacts'][$contactID]['tasks'][$taskID] = $currentPerson;
                    }
                }
                if (isset($entitiesByEmployerId['contacts'][$contactID]['activity'])) {
                    foreach ($entitiesByEmployerId['contacts'][$contactID]['activity'] as $activityID => $activity) {
                        ++$counterEntities;
                        ++$counterActivity;
                        History::firstOrCreate([
                            'entity_ID' => $activityID,
                            'entity_type' => 'activity',
                            'old_responsible_ID' => $activity,
                            'new_responsible_ID' => $currentPerson,
                            'transfer_group_ID' => $transferGroup->id,
                        ]);
                        $entitiesByEmployerId['contacts'][$contactID]['activity'][$activityID] = $currentPerson;
                    }
                }
                if (!isset($contact['resp'])) continue;
                History::firstOrCreate([
                    'entity_ID' => $contactID,
                    'entity_type' => 'contact',
                    'old_responsible_ID' => $contact['resp'],
                    'new_responsible_ID' => $currentPerson,
                    'transfer_group_ID' => $transferGroup->id,
                ]);
                $entitiesByEmployerId['contacts'][$contactID]['resp'] = $currentPerson;
            }
        }
        $countersToUpdateMessage['counterLeads'] = $counterLeads;
        $countersToUpdateMessage['counterDeals'] = $counterDeals;
        $countersToUpdateMessage['counterContacts'] = $counterContacts;
        $countersToUpdateMessage['counterTasks'] = $counterTasks;
        $countersToUpdateMessage['counterActivity'] = $counterActivity;

        $this->generateTransferMessage($counterLeads, $counterDeals, $counterContacts, $counterTasks, $counterActivity, $transferGroup);
        $this->generateBitrixUpdateRequest($entitiesByEmployerId, $counterEntities, $transferGroup, $countersToUpdateMessage, $newSource, $newSalesDepartment);
    }

//---------------------------------------------Make-update-params-----------------------------------------------------//
    private function respFieldType($changeableEntity): string
    {
        //CONTACT - 'ASSIGNED_BY_ID'
        //LEAD - 'ASSIGNED_BY_ID'
        //DEAL - 'ASSIGNED_BY_ID'
        //ACTIVITY - 'RESPONSIBLE_ID';
        //TASKS - 'RESPONSIBLE_ID';
        $entitiesTypes = ['contact', 'lead', 'deal'];

        return in_array($changeableEntity['entity_type'], $entitiesTypes) ? 'ASSIGNED_BY_ID' : 'RESPONSIBLE_ID';
    }

    private function updateMethod($changeableEntity): string
    {
        if ($changeableEntity['entity_type'] == 'task') {
            return 'tasks.task.update';
        }
        return 'crm.' . $changeableEntity['entity_type'] . '.update';
    }

    private function generateUpdateParams($historyObject, $id, $fields, $newSource, $newSalesDepartment): array
    {
        $respFieldType = $this->respFieldType($historyObject);
        $updateParams['method'] = $this->updateMethod($historyObject);
        $updateParams['params']['id'] = $id;
        $updateParams['params']['fields'][$respFieldType] = $fields;
        !empty($newSalesDepartment) ? $updateParams['params']['fields']['UF_CRM_1561882407'] = $newSalesDepartment : [];
        !empty($newSource) ? $updateParams['params']['fields']['SOURCE_ID'] = $newSource : [];

        return $updateParams;
    }

    private function getIdFromDatabase($entityID, $transferGroup)
    {
        return History::where('entity_ID', $entityID)->where('transfer_group_ID', $transferGroup['id'])->first();
    }

    private $counterIter = 0;

    public function generateBitrixUpdateRequest($entitiesByEmployerId, $counterEntities, $transferGroup, $countersToUpdateMessage, $newSource, $newSalesDepartment)
    {
        $callWay = 'Api';
        $overallCountIter = (int)($counterEntities / 50);
        $counter = 0;
        $updateParams = [];
        foreach ($entitiesByEmployerId as $entityType => $entityValues) {
            foreach ($entityValues as $entityID => $values) {
                if (!empty($values['tasks'])) {
                    foreach ($values['tasks'] as $taskID => $task) {
                        $historyTaskID = $this->getIdFromDatabase($taskID, $transferGroup);
                        if(isset($values['resp'])) {
                            $updateParams[$historyTaskID['id']] = $this->generateUpdateParams($historyTaskID, $taskID, $values['resp'], $newSource, $newSalesDepartment);
                        }
                        if (++$counter == 50) {
                            $this->storeBitrix($updateParams, $transferGroup, $countersToUpdateMessage, $callWay);
                            $this->counterIter++;
                            $counter = 0;
                            $updateParams = [];
                        }
                    }
                }
                if (!empty($values['activity'])) {
                    foreach ($values['activity'] as $activityID => $activity) {
                        $historyActivityID = $this->getIdFromDatabase($activityID, $transferGroup);
                        if(isset($values['resp'])) {
                            $updateParams[$historyActivityID['id']] = $this->generateUpdateParams($historyActivityID, $activityID, $values['resp'], $newSource, $newSalesDepartment);
                        }
                        if (++$counter == 50) {
                            $this->storeBitrix($updateParams, $transferGroup, $countersToUpdateMessage, $callWay);
                            $this->counterIter++;
                            $counter = 0;
                            $updateParams = [];
                        }
                    }
                }
                $historyEntityID = $this->getIdFromDatabase($entityID, $transferGroup);
                if(isset($values['resp'])) {
                $updateParams[$historyEntityID['id']] = $this->generateUpdateParams($historyEntityID, $entityID, $values['resp'], $newSource, $newSalesDepartment);
                }
                if (++$counter == 50) {
                    $this->storeBitrix($updateParams, $transferGroup, $countersToUpdateMessage, $callWay);;
                    $this->counterIter++;
                    $counter = 0;
                    $updateParams = [];
                } else if ($this->counterIter == $overallCountIter && $counter == $counterEntities % 50) {
                    $this->storeBitrix($updateParams, $transferGroup, $countersToUpdateMessage, $callWay);
                    $counter = 0;
                    $updateParams = [];
                }
            }
        }
    }

//--------------------------------------------------------------------------------------------------------------------//

    public function storeBitrix($updateParams, $transferGroup, $countersToUpdateMessage, $callWay = [])
    {
        $errorIDs = [];
        $successIDs = [];

        $updateResponse = CRest::callBatch($updateParams);
        if (isset($updateResponse) && !empty($updateResponse['result'])) {
            if (!empty($updateResponse['result']['result_error'])) {
                foreach ($updateResponse['result']['result_error'] as $responseKey => $responseValue) {
                    $errorIDs[] = $responseKey;
                }
                // $callWay
                // 0 - Api
                // 1 - Vue
                // 2 - Retry
                if ($callWay == 'Retry') {
                    Log::build([
                        'driver' => 'single',
                        'path' => storage_path('logs/RetryErrors.log')
                    ])->info('Errors:', $errorIDs);
                } else {
                    Log::build([
                        'driver' => 'single',
                        'path' => storage_path('logs/StoreErrors.log')
                    ])->info('Errors:', $errorIDs);
                }
                History::whereIn('id', $errorIDs)->update(['transfer_status' => false]);
            } elseif (!empty($updateResponse['result']['result'])) {
                foreach ($updateResponse['result']['result'] as $responseKey => $responseValue) {
                    $successIDs[] = $responseKey;
                }
                if ($callWay == 'Retry') {
                    Log::build([
                        'driver' => 'single',
                        'path' => storage_path('logs/RetrySuccesses.log')
                    ])->info('Success:', $successIDs);
                } else {
                    Log::build([
                        'driver' => 'single',
                        'path' => storage_path('logs/StoreSuccesses.log')
                    ])->info('Success:', $successIDs);
                }
                History::whereIn('id', $successIDs)->update(['transfer_status' => true]);
            }
        }
        $this->updateTransferMessage($transferGroup, $countersToUpdateMessage);
    }

//----------------------------------------------Отправка-сообщений----------------------------------------------------//
    public function generateEmployersStringToMessages($transferGroup): array
    {
            $entities = History::where('transfer_group_ID', $transferGroup['id'])->get();

        $departments = CRest::firstBatch('department.get');

        $oldEmployers = [];
        $newEmployers = [];

        foreach ($entities as $entity) {
            if (!in_array($entity['old_responsible_ID'], $oldEmployers)) {
                $oldEmployers[] = $entity['old_responsible_ID'];
            }
            if (!in_array($entity['new_responsible_ID'], $newEmployers)) {
                $newEmployers[] = $entity['new_responsible_ID'];
            }
        }

        $oldBxRequest = CRest::call('user.get', [
            'ID' => $oldEmployers
        ]);

        $newBxRequest = CRest::call('user.get', [
            'ID' => $newEmployers
        ]);

        $oldUsers = [];
        $newUsers = [];

        $depCounter = 0;

        foreach ($oldBxRequest['result'] as $key => $user) {
            $oldUsers[$key]['ID'] = $user['ID'];
            $oldUsers[$key]['LAST_NAME'] = $user['LAST_NAME'];
            $oldUsers[$key]['NAME'] = $user['NAME'];
            $oldUsers[$key]['SECOND_NAME'] = $user['SECOND_NAME'];
            foreach ($departments as $department) {
                if ($department['ID'] == $user['UF_DEPARTMENT'][0]) {
                    $oldUsers[$key]['DEPARTMENTS'][$depCounter]['DEP_ID'] = $department['ID'];
                    $oldUsers[$key]['DEPARTMENTS'][$depCounter]['DEP_NAME'] = $department['NAME'];
                    $depCounter++;
                }
            }
            $depCounter = 0;
        }

        foreach ($newBxRequest['result'] as $key => $user) {
            $newUsers[$key]['ID'] = $user['ID'];
            $newUsers[$key]['LAST_NAME'] = $user['LAST_NAME'];
            $newUsers[$key]['NAME'] = $user['NAME'];
            $newUsers[$key]['SECOND_NAME'] = $user['SECOND_NAME'];
            foreach ($departments as $dep_key => $department) {
                if (in_array($department['ID'], $user['UF_DEPARTMENT'])) {
                    $newUsers[$key]['DEPARTMENTS'][$depCounter]['DEP_ID'] = $department['ID'];
                    $newUsers[$key]['DEPARTMENTS'][$depCounter]['DEP_NAME'] = $department['NAME'];
                    $depCounter++;
                }
            }
            $depCounter = 0;
        }

        $oldString = '';
        $oldDepartmentString = '';

        $newString = '';
        $newDepartmentString = '';

        foreach ($oldUsers as $item) {
            $oldDepartmentString = '';
            if (count($item['DEPARTMENTS']) > 1) {
                foreach ($item['DEPARTMENTS'] as $department) {
                    $oldDepartmentString .= "[url=/company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT={$department['DEP_ID']}/]{$department['DEP_NAME']}[/url] ";
                }
            } else {
                $oldDepartmentString .= "[url=/company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT={$item['DEPARTMENTS'][0]['DEP_ID']}/]{$item['DEPARTMENTS'][0]['DEP_NAME']}[/url] ";
            }
            $oldString .= "[url=/company/personal/user/{$item['ID']}/]{$item['LAST_NAME']} {$item['NAME']} {$item['SECOND_NAME']}[/url] ( {$oldDepartmentString})
";
        }
        foreach ($newUsers as $item) {
            $newDepartmentString = '';
            if (count($item['DEPARTMENTS']) > 1) {
                foreach ($item['DEPARTMENTS'] as $department) {
                    $newDepartmentString .= "[url=/company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT={$department['DEP_ID']}/]{$department['DEP_NAME']}[/url] ";
                }
            } else {
                $newDepartmentString .= "[url=/company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT={$item['DEPARTMENTS'][0]['DEP_ID']}/]{$item['DEPARTMENTS'][0]['DEP_NAME']}[/url] ";
            }
            $newString .= "[url=/company/personal/user/{$item['ID']}/]{$item['LAST_NAME']} {$item['NAME']} {$item['SECOND_NAME']}[/url] ( {$newDepartmentString})
";
        }
        return [$oldString, $newString];
    }

    public function generateTransferMessage($counterLeads, $counterDeals, $counterContacts, $counterTasks, $counterActivity, $transferGroup)
    {
        $buffer = 0;
        $employersString = $this->generateEmployersStringToMessages($transferGroup);
        $messageTransferNumber = $transferGroup['id'];
        $sendMessage = CRest::call('im.message.add', [
            'DIALOG_ID' => 'chat568134', // test chat534425 main chat568134
            'MESSAGE' => "
Запущена передача №{$messageTransferNumber}: \r

С кого: 
{$employersString[0]}
На кого: 
{$employersString[1]}
Сущности:
Всего: \r
    Лиды: {$counterLeads} \r
    Сделки: {$counterDeals} \r
    Контакты: {$counterContacts} \r
    Звонки\Встречи: {$counterActivity} \r
    CRM-задачи: {$counterTasks} \r
Успешно передано:
    Лиды: {$buffer} \r
    Сделки: {$buffer} \r
    Контакты: {$buffer} \r
    Звонки\Встречи: {$buffer} \r
    CRM-задачи: {$buffer} \r

",
        ]);
        $messageID = $sendMessage['result'];
        TransferGroup::where('id', $transferGroup['id'])->update(['message_id' => $messageID]);
    }

    public function updateTransferMessage($transferGroup, $countersToUpdateMessage)
    {
        $messageID = TransferGroup::find($transferGroup['id']);

        $transferredLeads = 0;
        $transferredDeals = 0;
        $transferredContacts = 0;
        $transferredActivities = 0;
        $transferredTasks = 0;

        $messageTransferNumber = $transferGroup['id'];
        $employersString = $this->generateEmployersStringToMessages($transferGroup);
        $sucTrans = History::where('transfer_status', true)->where('transfer_group_ID', $transferGroup['id'])->get();
        foreach ($sucTrans as $suc) {
            if ($suc['entity_type'] == 'lead') {
                $transferredLeads++;
            } elseif ($suc['entity_type'] == 'deal') {
                $transferredDeals++;
            } elseif ($suc['entity_type'] == 'contact') {
                $transferredContacts++;
            } elseif ($suc['entity_type'] == 'activity') {
                $transferredActivities++;
            } elseif ($suc['entity_type'] == 'task') {
                $transferredTasks++;
            }
        }
        CRest::call('im.message.update', [
            'MESSAGE_ID' => $messageID->message_id,
            'MESSAGE' => "
Запущена передача №{$messageTransferNumber}: \r

С кого: 
{$employersString[0]}
На кого: 
{$employersString[1]}
Сущности:
Всего: \r
    Лиды: {$countersToUpdateMessage['counterLeads']} \r
    Сделки: {$countersToUpdateMessage['counterDeals']} \r
    Контакты: {$countersToUpdateMessage['counterContacts']} \r
    Звонки\Встречи: {$countersToUpdateMessage['counterActivity']} \r
    CRM-задачи: {$countersToUpdateMessage['counterTasks']} \r
Успешно передано:
    Лиды: {$transferredLeads} \r
    Сделки: {$transferredDeals} \r
    Контакты: {$transferredContacts} \r
    Звонки\Встречи: {$transferredActivities} \r
    CRM-задачи: {$transferredTasks} \r

",
        ]);
    }

//-----------------------------------------------------Retry----------------------------------------------------------//
    public function checkBDforErrors()
    {
        $countersToUpdateMessage = [
            'counterLeads' => 0,
            'counterDeals' => 0,
            'counterContacts' => 0,
            'counterActivity' => 0,
            'counterTasks' => 0,
        ];

        $transferGroup = TransferGroup::where('transfer_group_status', 0)->first();

        if (empty($transferGroup['id'])) return;

        $historyList = History::where('transfer_group_ID', $transferGroup['id'])->get();

        foreach ($historyList as $item) {
            if ($item['entity_type'] == 'lead') {
                $countersToUpdateMessage['counterLeads'] += 1;
            } elseif ($item['entity_type'] == 'deal') {
                $countersToUpdateMessage['counterDeals'] += 1;
            } elseif ($item['entity_type'] == 'contact') {
                $countersToUpdateMessage['counterContacts'] += 1;
            } elseif ($item['entity_type'] == 'activity') {
                $countersToUpdateMessage['counterActivity'] += 1;
            } elseif ($item['entity_type'] == 'task') {
                $countersToUpdateMessage['counterTasks'] += 1;
            }
        }

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
            TransferGroup::where('id', $transferGroup['id'])->update(['transfer_group_status' => true]);
            $this->checkBDforErrors();
        } else {
            $this->storeBitrixRetry($needToRetryIDs, $transferGroup, $countersToUpdateMessage);
        }
    }

    private $counterRetryIter = 0;

    public function storeBitrixRetry($retryList, $transferGroup, $countersToUpdateMessage)
    {
        $callWay = 'Retry';
        $retryListCount = count($retryList);
        $retryCounter = 0;
        $retryUpdateParams = [];
        $overallCountIter = (int)($retryListCount / 50);
        foreach ($retryList as $retry) {
            $respFieldType = $this->respFieldType($retry);
            $retryUpdateParams[$retry['id']]['method'] = $this->updateMethod($retry);
            $retryUpdateParams[$retry['id']]['params']['id'] = $retry['entity_ID'];
            $retryUpdateParams[$retry['id']]['params']['fields'][$respFieldType] = $retry['new_responsible_ID'];

            if (++$retryCounter == 50) {
                $this->storeBitrix($retryUpdateParams, $transferGroup, $countersToUpdateMessage, $callWay);
                $retryCounter = 0;
                $retryUpdateParams = [];
                $this->counterRetryIter++;
            } elseif ($this->counterRetryIter == $overallCountIter && $retryCounter == $retryListCount % 50) {
                $this->storeBitrix($retryUpdateParams, $transferGroup, $countersToUpdateMessage, $callWay);
            }
        }
    }
}



