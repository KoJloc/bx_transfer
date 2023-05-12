<?php

namespace App\Http\Controllers\Entities;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRest;
use App\Jobs\ProcessUpdate;
use App\Models\History;
use App\Models\TransferGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EntitiesRollbackController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

//--------------------------------------------------------------------------------------------------------------------//
    /** function get()
     *
     * @param Request $request
     * @return void
     */
    public function get(Request $request)
    {
        $transferGroupID = $request['transferGroupID'];

        $entitiesList = History::where('transfer_group_ID', $transferGroupID)->where('transfer_status', 1)->where('rollback_status', 0)->get();
        $entitiesForCountList = History::where('transfer_group_ID', $transferGroupID)->where('transfer_status', 1)->get();

        $countersToUpdateMessage = [
            'counterLeads' => 0,
            'counterDeals' => 0,
            'counterContacts' => 0,
            'counterActivity' => 0,
            'counterTasks' => 0,
        ];

        foreach ($entitiesForCountList as $item) {
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

        $this->generateRollbackParams($entitiesList, $transferGroupID, $countersToUpdateMessage);
    }

    private $counterRetryIter = 0;

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

    private function generateRollbackParams($entitiesList, $transferGroup, $countersToUpdateMessage)
    {
        $this->generateRollbackMessage($transferGroup, $countersToUpdateMessage);

        $rollbackListCount = count($entitiesList);
        $rollbackCounter = 0;
        $rollbackUpdateParams = [];
        $overallCountIter = (int)($rollbackListCount / 50);
        foreach ($entitiesList as $rollback) {
            $respFieldType = $this->respFieldType($rollback);
            $rollbackUpdateParams[$rollback['id']]['method'] = $this->updateMethod($rollback);
            $rollbackUpdateParams[$rollback['id']]['params']['id'] = $rollback['entity_ID'];
            $rollbackUpdateParams[$rollback['id']]['params']['fields'][$respFieldType] = $rollback['old_responsible_ID'];
            if (++$rollbackCounter == 50) {
                $this->sendRollbackToBitrix($rollbackUpdateParams, $transferGroup, $countersToUpdateMessage);
                $rollbackCounter = 0;
                $rollbackUpdateParams = [];
                $this->counterRetryIter++;
            } elseif ($this->counterRetryIter == $overallCountIter && $rollbackCounter == $rollbackListCount % 50) {
                $this->sendRollbackToBitrix($rollbackUpdateParams, $transferGroup, $countersToUpdateMessage);
            }
        }
        TransferGroup::where('id', $transferGroup)->update(['rollback_status' => true]);
    }

    private function sendRollbackToBitrix($updateParams, $transferGroup, $countersToUpdateMessage)
    {

        $errorIDs = [];
        $successIDs = [];

        $updateResponse = CRest::callBatch($updateParams);
        if (isset($updateResponse) && !empty($updateResponse['result'])) {
            if (!empty($updateResponse['result']['result_error'])) {
                foreach ($updateResponse['result']['result_error'] as $responseKey => $responseValue) {
                    $errorIDs[] = $responseKey;
                }
                Log::build([
                    'driver' => 'single',
                    'path' => storage_path('logs/RollbacksErrors.log')
                ])->info('Rollback errors:', $errorIDs);
                History::whereIn('id', $errorIDs)->update(['rollback_status' => false]);
            } elseif (!empty($updateResponse['result']['result'])) {
                foreach ($updateResponse['result']['result'] as $responseKey => $responseValue) {
                    $successIDs[] = $responseKey;
                }
                Log::build([
                    'driver' => 'single',
                    'path' => storage_path('logs/RollbacksSuccesses.log')
                ])->info('Rollback success:', $successIDs);
                History::whereIn('id', $successIDs)->update(['rollback_status' => true]);
            }
        }
        $this->updateTransferMessage($transferGroup, $countersToUpdateMessage);
    }

//----------------------------------------------Отправка-сообщений----------------------------------------------------//
    private function generateEmployersStringToMessages($transferGroup): array
    {
        $entities = History::where('transfer_group_ID', $transferGroup)->get();

        $departments = CRest::firstBatch('department.get');

        $oldEmployers = [];
        $newEmployers = [];

        foreach ($entities as $entity) {
            if (!in_array($entity['old_responsible_ID'], $oldEmployers)) {
                $oldEmployers[] = $entity['new_responsible_ID'];
            }
            if (!in_array($entity['new_responsible_ID'], $newEmployers)) {
                $newEmployers[] = $entity['old_responsible_ID'];
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
                if (in_array($department['ID'], $user['UF_DEPARTMENT'])) {
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
                    $oldDepartmentString .= "[url=/company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT={$department['DEP_ID']}/]{$department['DEP_NAME']}[/url]  ";
                }
            } else {
                $oldDepartmentString .= "[url=/company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT={$item['DEPARTMENTS'][0]['DEP_ID']}/]{$item['DEPARTMENTS'][0]['DEP_NAME']}[/url]  ";
            }
            $oldString .= "[url=/company/personal/user/{$item['ID']}/]{$item['LAST_NAME']} {$item['NAME']} {$item['SECOND_NAME']}[/url] (  {$oldDepartmentString})
";
        }

        foreach ($newUsers as $item) {
            $newDepartmentString = '';
            if (count($item['DEPARTMENTS']) > 1) {
                foreach ($item['DEPARTMENTS'] as $department) {
                    $newDepartmentString .= "[url=/company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT={$department['DEP_ID']}/]{$department['DEP_NAME']}[/url]  ";
                }
            } else {
                $newDepartmentString .= "[url=/company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT={$item['DEPARTMENTS'][0]['DEP_ID']}/]{$item['DEPARTMENTS'][0]['DEP_NAME']}[/url]  ";
            }
            $newString .= "[url=/company/personal/user/{$item['ID']}/]{$item['LAST_NAME']} {$item['NAME']} {$item['SECOND_NAME']}[/url] (  {$newDepartmentString})
";
        }
        return [$oldString, $newString];
    }

    private function generateRollbackMessage($transferGroup, $countersToUpdateMessage)
    {
        $buffer = 0;
        $employersString = $this->generateEmployersStringToMessages($transferGroup);
        $messageTransferNumber = $transferGroup;
        $sendMessage = CRest::call('im.message.add', [
            'DIALOG_ID' => 'chat534425', // test chat534425 main chat568134
            'MESSAGE' => "
Запущен откат передачи №{$messageTransferNumber}: \r

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
    Лиды: {$buffer} \r
    Сделки: {$buffer} \r
    Контакты: {$buffer} \r
    Звонки\Встречи: {$buffer} \r
    CRM-задачи: {$buffer} \r
",
        ]);
        $messageID = $sendMessage['result'];
        TransferGroup::where('id', $transferGroup)->update(['rollback_message_id' => $messageID]);
    }

    private function updateTransferMessage($transferGroup, $countersToUpdateMessage)
    {
        $messageID = TransferGroup::find($transferGroup);

        $transferredLeads = 0;
        $transferredDeals = 0;
        $transferredContacts = 0;
        $transferredActivities = 0;
        $transferredTasks = 0;

        $messageTransferNumber = $transferGroup;
        $employersString = $this->generateEmployersStringToMessages($transferGroup);
        $successRollbacks = History::where('rollback_status', true)->where('transfer_group_ID', $transferGroup)->get();
        foreach ($successRollbacks as $successRollback) {
            if ($successRollback['entity_type'] == 'lead') {
                $transferredLeads++;
            } elseif ($successRollback['entity_type'] == 'deal') {
                $transferredDeals++;
            } elseif ($successRollback['entity_type'] == 'contact') {
                $transferredContacts++;
            } elseif ($successRollback['entity_type'] == 'activity') {
                $transferredActivities++;
            } elseif ($successRollback['entity_type'] == 'task') {
                $transferredTasks++;
            }
        }
        CRest::call('im.message.update', [
            'MESSAGE_ID' => $messageID->rollback_message_id,
            'MESSAGE' => "
Запущен откат передачи №{$messageTransferNumber}: \r

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
//-------------------------------------------Конец-отправки-сообщений-------------------------------------------------//
}



