<?php

namespace App\Http\Controllers\Entities;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function __invoke(Request $request): array
    {
        //Очистка кеша для тестов
//        Cache::flush();

        $leadStatusList = [];
        $leadTypeList = [];
        $dealTypeList = [];
        $dealFunnelList = [];
        $citiesList = [];
        $regionList = [];
        $sourceList = [];
        $salesDepartmentsLeadList = [];
        $salesDepartmentsDealList = [];
        $salesDepartmentsContactList = [];
        $hotLeadList = [];

        if (\Cache::has('leadStatusList')) {
            $leadStatusList = \Cache::get('leadStatusList');
        } else {
            $leadStatus = CRest::call('crm.status.list', [
                'FILTER' => [
                    'ENTITY_ID' => 'STATUS',
                ]
            ]);

            foreach ($leadStatus['result'] as $status) {
                $leadStatusList[] =
                    [
                        'id' => $status['ID'],
                        'text' => $status['NAME'],
                        'status' => $status['STATUS_ID'],
                    ];
            }
            \Cache::put("leadStatusList", $leadStatusList, Carbon::now()->addMinutes(360));
        }

        if (\Cache::has('leadTypeList')) {
            $leadTypeList = \Cache::get('leadTypeList');
        } else {
            $leadFilters = CRest::call('crm.lead.fields', [
                'SELECT' => [
                    'UF_CRM_1662639727', // Тип лида
                ],
            ]);

            foreach ($leadFilters['result']['UF_CRM_1662639727']['items'] as $item) {
                $leadTypeList[] = [
                    'id' => $item['ID'],
                    'text' => $item['VALUE'],
                ];
            }
            \Cache::put("leadTypeList", $leadTypeList, Carbon::now()->addMinutes(360));
        }

        if (\Cache::has('regionList')) {
            $regionList = \Cache::get('regionList');
        } else {
            $leadFilters = CRest::call('crm.lead.fields', [
                'SELECT' => [
                    'UF_CRM_1626242852', // Регион
                ],
            ]);

            foreach ($leadFilters['result']['UF_CRM_1626242852']['items'] as $item) {
                $regionList[] = [
                    'id' => $item['ID'],
                    'text' => $item['VALUE'],
                ];
            }
            \Cache::put("regionList", $regionList, Carbon::now()->addMinutes(360));
        }
        if (\Cache::has('salesDepartmentsLeadList')) {
            $salesDepartmentsLeadList = \Cache::get('salesDepartmentsLeadList');
        } else {
            $leadFilters = CRest::call('crm.lead.fields', [
                'SELECT' => [
                    'UF_CRM_1561882407', // Отдел продаж
                ],
            ]);

            foreach ($leadFilters['result']['UF_CRM_1561882407']['items'] as $item) {
                $salesDepartmentsLeadList[] = [
                    'id' => $item['ID'],
                    'text' => $item['VALUE'],
                ];
            }
            \Cache::put("salesDepartmentsLeadList", $salesDepartmentsLeadList, Carbon::now()->addMinutes(360));
        }

        if (\Cache::has('salesDepartmentsDealList')) {
            $salesDepartmentsDealList = \Cache::get('salesDepartmentsDealList');
        } else {
            $deadFilters = CRest::call('crm.deal.fields', [
                'SELECT' => [
                    'UF_CRM_1561882407', // Отдел продаж
                ],
            ]);

            foreach ($deadFilters['result']['UF_CRM_1561882407']['items'] as $item) {
                $salesDepartmentsDealList[] = [
                    'id' => $item['ID'],
                    'text' => $item['VALUE'],
                ];
            }
            \Cache::put("salesDepartmentsDealList", $salesDepartmentsDealList, Carbon::now()->addMinutes(360));
        }

        if (\Cache::has('salesDepartmentsContactList')) {
            $salesDepartmentsContactList = \Cache::get('salesDepartmentsContactList');
        } else {
            $contactFilters = CRest::call('crm.contact.fields', [
                'SELECT' => [
                    'UF_CRM_5D19073319DA8', // Отдел продаж
                ],
            ]);

            foreach ($contactFilters['result']['UF_CRM_5D19073319DA8']['items'] as $item) {
                $salesDepartmentsContactList[] = [
                    'id' => $item['ID'],
                    'text' => $item['VALUE'],
                ];
            }
            \Cache::put("salesDepartmentsContactList", $salesDepartmentsContactList, Carbon::now()->addMinutes(360));
        }

        if (\Cache::has('sourceList')) {
            $sourceList = \Cache::get('sourceList');
        } else {
            $sourceIDs = CRest::firstBatch("crm.status.list");

            foreach ($sourceIDs as $key => $value) {
                if ($value['ENTITY_ID'] != 'SOURCE') continue;
                $sourceList[] = [
                    'id' => $value['STATUS_ID'],
                    'text' => $value['NAME'],
                ];
            }
            \Cache::put("sourceList", $sourceList, Carbon::now()->addMinutes(360));
        }

        if (\Cache::has('dealType')) {
            $dealTypeList = \Cache::get('dealTypeList');
        } else {
            $dealType = CRest::call('crm.status.list', [
                'FILTER' => [
                    'ENTITY_ID' => 'DEAL_TYPE',
                ],
                'ORDER' => [
                    'SORT' => 'ASC',
                ],
            ]);

            foreach ($dealType['result'] as $item) {
                $dealTypeList[] = [
                    'id' => $item['ID'],
                    'text' => $item['NAME'],
                    'status' => $item['STATUS_ID'],
                ];
            }
            \Cache::put("dealTypeList", $dealTypeList, Carbon::now()->addMinutes(360));
        }

        if (\Cache::has('dealFunnelList')) {
            $dealFunnelList = \Cache::get('dealFunnelList');
        } else {
            $dealFunnel = CRest::call('crm.status.entity.types');

            $filter = 'DEAL_STAGE';
            foreach ($dealFunnel['result'] as $item) {
                if (strpos($item['ID'], $filter) !== false) {
                    $dealFunnelList[] = [
                        'id' => $item['CATEGORY_ID'],
                        'text' => mb_substr($item['NAME'], 14),
                    ];
                }
            }
            \Cache::put("dealFunnelList", $dealFunnelList, Carbon::now()->addMinutes(360));
        }


        if (\Cache::has('peopleMultiSelect') && \Cache::has('activePeopleMultiSelect')) {
            $this->peopleMultiSelect = \Cache::get('peopleMultiSelect');
            $this->activePeopleMultiSelect = \Cache::get('activePeopleMultiSelect');
        } else {
            $departments = CRest::firstBatch('department.get');

            foreach ($departments as $department) {
                $this->peopleMultiSelect[] = [
                    'department_id' => $department['ID'],
                    'department' => $department['NAME'],
                    'params' => [],
                ];
                $this->activePeopleMultiSelect[] = [
                    'department_id' => $department['ID'],
                    'department' => $department['NAME'],
                    'params' => [],
                ];
            }

            $this->activePeopleMultiSelect[] = [
                'department_id' => 0,
                'department' => 'Без отдела',
                'params' => [],
            ];
            $this->peopleMultiSelect[] = [
                'department_id' => 0,
                'department' => 'Без отдела',
                'params' => [],
            ];

            $people = CRest::firstBatch('user.get');

            foreach ($people as $person) {
                foreach ($this->peopleMultiSelect as $key => $item) {
                    if (isset($item['department_id'])) {
                        if ($person['UF_DEPARTMENT'][0] == $item['department_id']) {
                            $this->peopleMultiSelect[$key]['params'][] = [
                                'id' => $person['ID'],
                                'text' => trim($person['LAST_NAME'] . ' ' . $person['NAME'] . ' ' . $person['SECOND_NAME']),
                                'img' => $person['PERSONAL_PHOTO'],
                                'job' => $person['WORK_POSITION'],
                                'active' => $person['ACTIVE'],
                            ];
                        }
                    } else if ($item['department'] == 'Без отдела') {
                        $this->peopleMultiSelect[$key]['params'][] = [
                            'id' => $person['ID'],
                            'text' => trim($person['LAST_NAME'] . ' ' . $person['NAME'] . ' ' . $person['SECOND_NAME']),
                            'img' => $person['PERSONAL_PHOTO'],
                            'job' => $person['WORK_POSITION'],
                            'active' => $person['ACTIVE'],
                        ];
                    }
                }
                foreach ($this->activePeopleMultiSelect as $key => $item) {
                    if($person['ACTIVE']) {
                        if (isset($item['department_id'])) {
                            if ($person['UF_DEPARTMENT'][0] == $item['department_id']) {
                                $this->activePeopleMultiSelect[$key]['params'][] = [
                                    'id' => $person['ID'],
                                    'text' => trim($person['LAST_NAME'] . ' ' . $person['NAME'] . ' ' . $person['SECOND_NAME']),
                                    'img' => $person['PERSONAL_PHOTO'],
                                    'job' => $person['WORK_POSITION'],
                                    'active' => $person['ACTIVE'],
                                ];
                            }
                        } else if ($item['department'] == 'Без отдела') {
                            $this->activePeopleMultiSelect[$key]['params'][]= [
                                'id' => $person['ID'],
                                'text' => trim($person['LAST_NAME'] . ' ' . $person['NAME'] . ' ' . $person['SECOND_NAME']),
                                'img' => $person['PERSONAL_PHOTO'],
                                'job' => $person['WORK_POSITION'],
                                'active' => $person['ACTIVE'],
                            ];
                        }
                    }
                }
            }
            \Cache::put("peopleMultiSelect", $this->peopleMultiSelect, Carbon::now()->addMinutes(30));
            \Cache::put("activePeopleMultiSelect", $this->activePeopleMultiSelect, Carbon::now()->addMinutes(30));
        }

        return [
			'peopleMultiSelect' => $this->peopleMultiSelect,
            'activePeopleMultiSelect' => $this->activePeopleMultiSelect,
            'leadStatusList' => $leadStatusList,
            'leadTypeList' => $leadTypeList,
            'dealTypeList' => $dealTypeList,
            'dealFunnelList' => $dealFunnelList,
            'regionsList' => $regionList,
            'citiesList' => $citiesList,
            'sourcesList' => $sourceList,
            'salesDepartmentsLeadList' => $salesDepartmentsLeadList,
            'salesDepartmentsDealList' => $salesDepartmentsDealList,
            'salesDepartmentsContactList' => $salesDepartmentsContactList,
		];
    }
}
