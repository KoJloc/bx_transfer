<?php

namespace App\Http\Controllers\Entities;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRest;
use Illuminate\Http\Request;

class ApiUserController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    protected $headEmployers = '';

    protected $fromEmployers = '';

    protected $toEmployers = '';

    public function get(Request $request)
    {
//-----------------------------------------------Хук------------------------------------------------------------------//
        define('C_REST_WEB_HOOK_URL', 'https://xn--24-9kc.xn--d1ao9c.xn--p1ai/rest/53083/b4ye1avz6dmkned1/');
//--------------------------------------Получаем_сущности-------------------------------------------------------------//

        if (empty($request)
            || $request->secret_key != '78a37c16edcc13f5c0179ce54f52d5f2'
            || empty($request['transferEntities'])
            || empty($request['transferEntities']['from']))
        {
            return 'Нормально отдай запрос ебло';
        }

        $this->fromEmployers = explode(', ', $request['transferEntities']['from']);
        if (isset($request['transferEntities']['to'])) {
            $this->toEmployers = explode(', ', $request['transferEntities']['to']);
        }
        if (isset($request['transferEntities']['head'])) {
            $this->headEmployers = $request['transferEntities']['head'];
        }
        if (!empty($request['transferEntities']['to'])) {
            $this->getDepartmentsIDs($request, $this->fromEmployers, $this->toEmployers);
        } elseif (!empty($request['transferEntities']['head'])) {
            $this->getDepartmentsIDs($request, $this->fromEmployers, $this->headEmployers);
        } else {
            $this->getDepartmentsIDs($request, $this->fromEmployers);
        }
    }

    private
        $usersBatchParams = [];
    private
        $departmentsIDs = [];
    private
        $fromUsersIDs = [];

    public
    function getDepartmentsIDs($request, $fromEmployers, ...$otherParams)
    {
        $entitiesGetController = new EntitiesGetController();
        //Метод для callBatch
        $method = 'user.get';
        //Формируем массив для callBatch
        foreach ($fromEmployers as $employerIndex => $employerID) {
            $this->fromUsersIDs[] = $employerID;
            $this->usersBatchParams[$employerIndex]['method'] = $method;
            $this->usersBatchParams[$employerIndex]['params']['id'] = $employerID;
        }
        //Получаем поля пользователей по ID
        $userFields = CRest::callBatch($this->usersBatchParams);

        foreach ($userFields['result']['result'] as $userValue) {
            if (count($userValue[0]['UF_DEPARTMENT']) > 1) {
                foreach ($userValue[0]['UF_DEPARTMENT'] as $departmentID) {
                    $this->departmentsIDs[] = $departmentID;
                }
            }
            $this->departmentsIDs[] = $userValue[0]['UF_DEPARTMENT'][0];
        }

        if (!empty($otherParams) && $otherParams[0] == 'true') {
            $this->getHeadByDepartmentIDs($entitiesGetController, $request, $this->departmentsIDs, $this->fromUsersIDs);
        } elseif (!empty($otherParams)) {
            $entitiesGetController->get($request, $this->fromUsersIDs, $otherParams[0]);
        } else {
            $this->getEmployersIDsByDepartmentIDs($entitiesGetController, $request, $this->departmentsIDs, $this->fromUsersIDs);
        }
    }

    private
        $toUsersBatchParams = [];
    private
        $toUsers = [];

    public
    function getEmployersIDsByDepartmentIDs($entitiesGetController, $request, $departmentsIDs, $fromUsers)
    {
        $method = 'user.get';
        $active = 1;
        foreach ($departmentsIDs as $departmentsIndex => $id) {
            $this->toUsersBatchParams[$departmentsIndex]['method'] = $method;
            $this->toUsersBatchParams[$departmentsIndex]['params']['UF_DEPARTMENT'] = $id;
            $this->toUsersBatchParams[$departmentsIndex]['params']['ACTIVE'] = $active;
        }
        $toUsersFields = CRest::callBatch($this->toUsersBatchParams);

        foreach ($fromUsers as $fromUserIndex => $fromUser) {
            foreach ($toUsersFields['result']['result'] as $toUsersIndex => $toUsers) {
                foreach ($toUsers as $toUserIndex => $toUser) {
                    if ($fromUser == $toUser['ID']) continue;
                    $this->toUsers[] = $toUsersFields['result']['result'][$toUsersIndex][$toUserIndex]['ID'];
                }
            }
            $entitiesGetController->get($request, $fromUsers, $this->toUsers);
        }
    }
    private $toUsersHead = [];
    public function getHeadByDepartmentIDs($entitiesGetController, $request, $departmentsIDs, $fromUsers)
    {
        $method = 'department.get';
        foreach ($departmentsIDs as $department) {
            $this->toUsersBatchParams[$department]['method'] = $method;
            $this->toUsersBatchParams[$department]['params']['id'] = $department;
        }
        $departmentFields = CRest::callBatch($this->toUsersBatchParams);
        foreach ($departmentFields['result']['result'] as $department) {
            if (strripos($department[0]['NAME'], 'Призыв')) {
                continue;
            }
            $this->toUsersHead[] = $department[0]['UF_HEAD'];
        }
        $entitiesGetController->get($request, $fromUsers, $this->toUsersHead);
    }
}
