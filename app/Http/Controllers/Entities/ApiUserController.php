<?php

namespace App\Http\Controllers\Entities;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiUserController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

//--------------------------------------------Получаем_сущности-------------------------------------------------------//
    public function get(Request $request)
    {
        $fromEmployers = [];
        $toEmployers = [];
        $headEmployers = [];

        if (empty($request)
            || $request->secret_key != '78a37c16edcc13f5c0179ce54f52d5f2'
            || empty($request['transferEntities'])
            || empty($request['transferEntities']['from'])) {
            return 'Некорректно отданы параметры';
        }

        $fromEmployers['from'] = explode(', ', $request['transferEntities']['from']);
        if (isset($request['transferEntities']['to'])) {
            $toEmployers['to'] = explode(', ', $request['transferEntities']['to']);
        }
        if (isset($request['transferEntities']['head'])) {
            $headEmployers['head'] = $request['transferEntities']['head'];
        }
        if ($request->transfer_type == 'move') {
            $this->getDepartmentsIDs($request, $fromEmployers, isset($request['transferEntities']['head']) ? $headEmployers : 0,isset($request['transferEntities']['to']) ? $toEmployers : 0, $request->transfer_type);
        } else {
            $this->getDepartmentsIDs($request, $fromEmployers, isset($request['transferEntities']['head']) ? $headEmployers : 0, isset($request['transferEntities']['to']) ? $toEmployers : 0, 0);
        }
    }

    public function getDepartmentsIDs($request, $fromEmployers, $headParam, $otherParams, $transferTypeParam)
    {
        $usersBatchParams = [];
        $departmentsIDs = [];

        $entitiesGetController = new EntitiesGetController();
        //Метод для callBatch
        $method = 'user.get';
        //Формируем массив для callBatch
        foreach ($fromEmployers['from'] as $employerIndex => $employerID) {
            $usersBatchParams[$employerIndex]['method'] = $method;
            $usersBatchParams[$employerIndex]['params']['id'] = $employerID;
        }

        $userFields = CRest::callBatch($usersBatchParams);

        if (!isset($userFields['result'])) return 'Ошибка, не найдены пользовательские поля';

        foreach ($userFields['result']['result'] as $userValue) {
            if (count($userValue[0]['UF_DEPARTMENT']) > 1) {
                foreach ($userValue[0]['UF_DEPARTMENT'] as $departmentID) {
                    $departmentsIDs['departments'][] = $departmentID;
                }
            } else {
                $departmentsIDs['departments'][] = $userValue[0]['UF_DEPARTMENT'][0];
            }
        }

        if (isset($fromEmployers['from'])) {
            // Параметр transfer_type == 'move'
            if ($transferTypeParam == 'move') {
                if (!isset($request->transferEntities['old_department'])) return 'error';
                    $departmentsIDs = [];
                    $departmentsIDs['departments'][] = $request->transferEntities['old_department'];
            }
            // Только параметр from
            if ($otherParams == 0 && $headParam == 0) {
                $this->getEmployersIDsByDepartmentIDs($entitiesGetController, $request, $departmentsIDs, $fromEmployers);
                // Параметры from и to
            } else if (isset($otherParams['to'])) {
                $entitiesGetController->set($request, $fromEmployers, $otherParams);
                // Параметры from и head
            } else if (isset($headParam) && $headParam['head']) {
                $this->getHeadByDepartmentIDs($entitiesGetController, $request, $departmentsIDs, $fromEmployers);
            } else {
                return 'error';
            }
        } else {
            return 'error';
        }
    }

    public function getEmployersIDsByDepartmentIDs($entitiesGetController, $request, $departmentsIDs, $fromUsers)
    {
        $toUsersBatchParams = [];
        $toUsers = [];

        $method = 'user.get';
        $active = 1;

        foreach ($departmentsIDs['departments'] as $departmentsIndex => $id) {
            $toUsersBatchParams[$departmentsIndex]['method'] = $method;
            $toUsersBatchParams[$departmentsIndex]['params']['UF_DEPARTMENT'] = $id;
            $toUsersBatchParams[$departmentsIndex]['params']['ACTIVE'] = $active;
        }
        $toUsersFields = CRest::callBatch($toUsersBatchParams);

        foreach ($fromUsers as $fromUserIndex => $fromUser) {
            foreach ($toUsersFields['result']['result'] as $toUsersIndex => $toUsersValue) {
                foreach ($toUsersValue as $toUserValueIndex => $toUsersValueValue) {
                    if ($fromUser == $toUsersValueValue['ID']) continue;
                    $toUsers['to'][] = $toUsersFields['result']['result'][$toUsersIndex][$toUserValueIndex]['ID'];
                }
            }
            $entitiesGetController->set($request, $fromUsers, $toUsers);
        }
    }

    public function getHeadByDepartmentIDs($entitiesGetController, $request, $departmentsIDs, $fromUsers)
    {
        $toUsersBatchParams = [];
        $toUsersHead = [];

        $method = 'department.get';
        foreach ($departmentsIDs['departments'] as $department) {
            $toUsersBatchParams[$department]['method'] = $method;
            $toUsersBatchParams[$department]['params']['id'] = $department;
        }
        $departmentFields = CRest::callBatch($toUsersBatchParams);
        foreach ($departmentFields['result']['result'] as $department) {
            if (strripos($department[0]['NAME'], 'Призыв')) {
                continue;
            }
            if (isset($department[0]['UF_HEAD'])) {
                $toUsersHead['to'][] = $department[0]['UF_HEAD'];
            }
        }
        $entitiesGetController->set($request, $fromUsers, $toUsersHead);
    }
}
