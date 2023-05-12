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
        $salesDepartmentsList = [];

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
        if (\Cache::has('salesDepartmentsList')) {
            $salesDepartmentsList = \Cache::get('salesDepartmentsList');
        } else {
            $leadFilters = CRest::call('crm.lead.fields', [
                'SELECT' => [
                    'UF_CRM_1561882407', // Отдел продаж
                ],
            ]);

            foreach ($leadFilters['result']['UF_CRM_1561882407']['items'] as $item) {
                $salesDepartmentsList[] = [
                    'id' => $item['ID'],
                    'text' => $item['VALUE'],
                ];
            }
            \Cache::put("salesDepartmentsList", $salesDepartmentsList, Carbon::now()->addMinutes(360));
        }

        if (\Cache::has('sourceList')) {
            $sourceList = \Cache::get('sourceList');
        } else {
            $sourceIDs = CRest::firstBatch("crm.status.list");

            foreach ($sourceIDs as $key => $value) {
                if ($value['ENTITY_ID'] != 'SOURCE') continue;
                $sourceList[] = [
                    'id' => $value['ID'],
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
            $people = CRest::firstBatch('user.get');

            foreach ($people as $person) {
                $selectedPeople = [
                    'id' => $person['ID'],
                    'text' => trim($person['LAST_NAME'] . ' ' . $person['NAME'] . ' ' . $person['SECOND_NAME']),
                    'img' => $person['PERSONAL_PHOTO'],
                    'job' => $person['WORK_POSITION'],
                    'active' => $person['ACTIVE'],
                ];
                if ($person['ACTIVE']) {
                    $this->activePeopleMultiSelect[] = $selectedPeople;
                    $this->peopleMultiSelect[] = $selectedPeople;
                } else {
                    $this->peopleMultiSelect[] = [
                        'id' => $person['ID'],
                        'text' => trim($person['LAST_NAME'] . ' ' . $person['NAME'] . ' ' . $person['SECOND_NAME']),
                        'img' => $person['PERSONAL_PHOTO'],
                        'job' => $person['WORK_POSITION'],
                        'active' => $person['ACTIVE'],
                    ];
                }
            }
            \Cache::put("peopleMultiSelect", $this->peopleMultiSelect, Carbon::now()->addMinutes(360));
            \Cache::put("activePeopleMultiSelect", $this->activePeopleMultiSelect, Carbon::now()->addMinutes(360));
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
            'salesDepartmentsList' => $salesDepartmentsList,
        ];
    }
}
