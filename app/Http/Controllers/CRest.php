<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class CRest extends Controller
{
    const VERSION 		 	= '1.36';
    const BATCH_COUNT    	= 50;		//count batch 1 query
    const TYPE_TRANSPORT 	= 'json';	// json or xml

    protected static $dataExt = [
        'access_token' 		=> '',
        'domain' 			=> '',
        'refresh_token' 	=> '',
        'application_token' => '',
        'client_endpoint' 	=> ''
    ];

    /**
     * call where install application even url
     * only for rest application, not webhook
     */

    public static function installApp(Request $request) {
        $result = [
            'rest_only' => true,
            'install' => false
        ];

        if ($request->event == 'ONAPPINSTALL' && !empty($request->auth)) {
            $result['install'] = static::setAppSettings($request->auth, true);
        } elseif ($request->PLACEMENT == 'DEFAULT') {
            $result['rest_only'] = false;
            $result['install'] = static::setAppSettings(
                [
                    'access_token' => htmlspecialchars($request->AUTH_ID),
                    'expires_in' => htmlspecialchars($request->AUTH_EXPIRES),
                    'application_token' => htmlspecialchars($request->APP_SID),
                    'refresh_token' => htmlspecialchars($request->REFRESH_ID),
                    'domain' => htmlspecialchars($request->DOMAIN),
                    'client_endpoint' => 'https://' . htmlspecialchars($request->DOMAIN) . '/rest/',
                ],
                true
            );
        }

        return $result;
    }

    public static function firstBatch($method, $params = [], $array_values = false, $show = false): array
    {
        $start = 0;
        $arParams = [];
        $return_data = [];

        $review_request_first = CRest::call($method, $params);

        if(array_key_exists('total', $review_request_first) and $review_request_first['total'] >= 50){
            if($show)	echo "\n".$method." Всего: ".$review_request_first['total'];

            if($review_request_first['total'] > 2500){
                for($mass = 0; $mass < ceil($review_request_first['total'] / 2500); $mass++){
                    for($i = 0; $i < ceil(($review_request_first['total'] - $mass * 2500) / 50); $i++){
                        if($i >= 50)	break;

                        $arParams['statistic_'.($i+1)] = [
                            'method' => $method,
                            'params' => array_merge($params, ['start' => $start])
                        ];

                        $start += 50;
                    }

                    if($show)	echo "\n- ".$start;
                    $review_request = CRest::callBatch($arParams);

                    if(is_array($review_request) and array_key_exists('result', $review_request) and array_key_exists('result', $review_request['result'])){
                        foreach($review_request['result']['result'] as $stats){
                            if($method == 'crm.stagehistory.list'){
                                foreach($stats['items'] as $stat){
                                    $return_data[$stat['ID']] = $stat;
                                }
                            }else{
                                foreach($stats as $stat){
                                    if(array_key_exists('ID', $stat)){
                                        $return_data[$stat['ID']] = $stat;
                                    }elseif(array_key_exists('ANCHOR_ID', $stat)){
                                        $return_data[$stat['ANCHOR_ID'].'_'.$stat['TYPE_ID'].'_'.$stat['ENTITY_TYPE_ID'].'_'.$stat['ENTITY_TYPE_ID']] = $stat;
                                    };
                                }
                            }
                        }
                    }else{
                        print_r($review_request);
                    };
                };
            }else{
                for($i = 0; $i < ceil($review_request_first['total'] / 50); $i++){
                    $arParams['statistic_'.($i+1)] = [
                        'method' => $method,
                        'params' => array_merge($params, ['start' => $start])
                    ];

                    $start += 50;
                }

                if($show)	echo "\n- ".$start;
                $review_request = CRest::callBatch($arParams);

                if(is_array($review_request) and array_key_exists('result', $review_request) and array_key_exists('result', $review_request['result'])){
                    foreach($review_request['result']['result'] as $stats){
                        if($method == 'crm.stagehistory.list'){
                            foreach($stats['items'] as $stat){
                                $return_data[$stat['ID']] = $stat;
                            }
                        }else{
                            foreach($stats as $stat){
                                if(array_key_exists('ID', $stat)){
                                    $return_data[$stat['ID']] = $stat;
                                }elseif(array_key_exists('ANCHOR_ID', $stat)){
                                    $return_data[$stat['ANCHOR_ID'].'_'.$stat['TYPE_ID'].'_'.$stat['ENTITY_TYPE_ID'].'_'.$stat['ENTITY_TYPE_ID']] = $stat;
                                };
                            }
                        }
                    }
                }else{
                    print_r($review_request);
                };
            };
        }else{
            if($show)	echo "\nВсего: ".count($review_request_first['result']);

            foreach($review_request_first['result'] as $stat){
                if(array_key_exists('ID', $stat)){
                    $return_data[$stat['ID']] = $stat;
                }elseif(array_key_exists('ANCHOR_ID', $stat)){
                    $return_data[$stat['ANCHOR_ID'].'_'.$stat['TYPE_ID'].'_'.$stat['ENTITY_TYPE_ID'].'_'.$stat['ENTITY_TYPE_ID']] = $stat;
                };
            }
        };

        return $array_values ? array_values($return_data) : $return_data;
    }

    /**
     * @return mixed array|string|boolean curl-return or error
     *
     *@var $arParams array
     * $arParams = [
     *      'method'    => 'some rest method',
     *      'params'    => []//array params of method
     * ];
     */
    protected static function callCurl(array $arParams)
    {

        if(!function_exists('curl_init')) {
            return [
                'error'             => 'error_php_lib_curl',
                'error_information' => 'need install curl lib'
            ];
        }
        $arSettings = static::getAppSettings();
        if($arSettings !== false) {
            if(isset($arParams[ 'this_auth' ]) && $arParams[ 'this_auth' ] == 'Y') {
                $url = 'https://oauth.bitrix.info/oauth/token/';
            } else {
                $url = $arSettings[ "client_endpoint" ] . $arParams[ 'method' ] . '.' . static::TYPE_TRANSPORT;
                if(empty($arSettings[ 'is_web_hook' ]) || $arSettings[ 'is_web_hook' ] != 'Y')
                {
                    $arParams[ 'params' ][ 'auth' ] = $arSettings[ 'access_token' ];
                }
            }

            $sPostFields = http_build_query($arParams[ 'params' ]);

            try
            {
                $obCurl = curl_init();
                curl_setopt($obCurl, CURLOPT_URL, $url);
                curl_setopt($obCurl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($obCurl, CURLOPT_POSTREDIR, 10);
                curl_setopt($obCurl, CURLOPT_TIMEOUT, 300);
                curl_setopt($obCurl, CURLOPT_CONNECTTIMEOUT, 300);
                curl_setopt($obCurl, CURLOPT_USERAGENT, 'Bitrix24 CRest PHP ' . static::VERSION);
                if($sPostFields) {
                    curl_setopt($obCurl, CURLOPT_POST, true);
                    curl_setopt($obCurl, CURLOPT_POSTFIELDS, $sPostFields);
                }
                curl_setopt(
                    $obCurl, CURLOPT_FOLLOWLOCATION, (isset($arParams[ 'followlocation' ]))
                    ? $arParams[ 'followlocation' ] : 1
                );

                if(defined("C_REST_IGNORE_SSL") && C_REST_IGNORE_SSL === true) {
                    curl_setopt($obCurl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($obCurl, CURLOPT_SSL_VERIFYHOST, false);
                }

                $out = curl_exec($obCurl);
                $info = curl_getinfo($obCurl);
                if(curl_errno($obCurl)) {
                    $info[ 'curl_error' ] = curl_error($obCurl);
                }

                //dd($out);

                if(static::TYPE_TRANSPORT == 'xml' && (!isset($arParams[ 'this_auth' ]) || $arParams[ 'this_auth' ] != 'Y')){
                    $result = $out;
                } else {
                    $result = static::expandData($out);
                }

                curl_close($obCurl);

                if(!empty($result[ 'error' ])) {
                    if($result[ 'error' ] == 'expired_token' && empty($arParams[ 'this_auth' ])) {
                        $result = static::GetNewAuth($arParams);
                    } else {
                        $arErrorInform = [
                            'expired_token'          => 'Токен с истекшим сроком действия, не можете получить новую авторизацию? Проверьте доступ к серверу oauth.',
                            'invalid_token'          => 'Неверный токен, необходимо переустановить приложение',
                            'invalid_grant'          => 'Недопустимый грант, проверьте корректность C_REST_CLIENT_SECRET или C_REST_CLIENT_ID',
                            'invalid_client'         => 'Недопустимый клиент, проверьте определение C_REST_CLIENT_SECRET или C_REST_CLIENT_ID',
                            'QUERY_LIMIT_EXCEEDED'   => 'Слишком много запросов, максимум 2 запроса в секунду',
                            'ERROR_METHOD_NOT_FOUND' => 'Метод не найден! Вы можете увидеть разрешения приложения: CRest::call(\'scope\')',
                            'NO_AUTH_FOUND'          => 'Некоторая ошибка настройки b24, проверьте в таблице событие "b_module_to_module" "OnRestCheckAuth"',
                            'INTERNAL_SERVER_ERROR'  => 'Сервер отключен, попробуйте позже'
                        ];

                        if(!empty($arErrorInform[ $result[ 'error' ] ])) {
                            $result[ 'error_information' ] = $arErrorInform[ $result[ 'error' ] ];
                            $result[ 'arParams' ] = $arParams;
                        }
                    }
                }

                if(!empty($info[ 'curl_error' ])) {
                    $result[ 'error' ] = 'curl_error';
                    $result[ 'error_information' ] = $info[ 'curl_error' ];
                    $result[ 'log' ] = [
                        'url'    => $url,
                        'info'   => $info,
                        'params' => $arParams,
                        'result' => $result
                    ];
                }

                static::setLog([
                    'url'    => $url,
                    'info'   => $info,
                    'params' => $arParams,
                    'result' => $result
                ], 'callCurl');

                return $result;
            } catch(\Exception $e) {
                static::setLog([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'trace' => $e->getTrace(),
                    'params' => $arParams
                ], 'exceptionCurl');

                return [
                    'error' => 'exception',
                    'error_exception_code' => $e->getCode(),
                    'error_information' => $e->getMessage(),
                ];
            }
        } else {
            static::setLog([
                'params' => $arParams
            ], 'emptySetting');
        }

        return [
            'error'             => 'no_install_app',
            'error_information' => 'error install app, pls install local application '
        ];
    }

    public static function call($method, $params = [])
    {
        $arPost = [
            'method' => $method,
            'params' => $params
        ];
        if(defined('C_REST_CURRENT_ENCODING')) {
            $arPost[ 'params' ] = static::changeEncoding($arPost[ 'params' ]);
        }

        return static::callCurl($arPost);
    }

    /**
     * @example $arData:
     * $arData = [
     *      'find_contact' => [
     *          'method' => 'crm.duplicate.findbycomm',
     *          'params' => [ "entity_type" => "CONTACT",  "type" => "EMAIL", "values" => array("info@bitrix24.com") ]
     *      ],
     *      'get_contact' => [
     *          'method' => 'crm.contact.get',
     *          'params' => [ "id" => '$result[find_contact][CONTACT][0]' ]
     *      ],
     *      'get_company' => [
     *          'method' => 'crm.company.get',
     *          'params' => [ "id" => '$result[get_contact][COMPANY_ID]', "select" => ["*"],]
     *      ]
     * ];
     *
     * @var $arData array
     * @var $halt   integer 0 or 1 stop batch on error
     * @return array
     *
     */

    public static function callBatch($arData, $halt = 0)
    {
        $arResult = [];
        if(is_array($arData))
        {
            if(defined('C_REST_CURRENT_ENCODING')) {
                $arData = static::changeEncoding($arData);
            }
            $arDataRest = [];
            $i = 0;
            foreach($arData as $key => $data) {
                if(!empty($data[ 'method' ])) {
                    $i++;
                    if(static::BATCH_COUNT >= $i) {
                        $arDataRest[ 'cmd' ][ $key ] = $data[ 'method' ];
                        if(!empty($data[ 'params' ])) {
                            $arDataRest[ 'cmd' ][ $key ] .= '?' . http_build_query($data[ 'params' ]);
                        }
                    }
                }
            }
            if(!empty($arDataRest)) {
                $arDataRest[ 'halt' ] = $halt;
                $arPost = [
                    'method' => 'batch',
                    'params' => $arDataRest
                ];
                $arResult = static::callCurl($arPost);
            }
        }
        return $arResult;
    }

    /**
     * Getting a new authorization and sending a request for the 2nd time
     *
     * @var $arParams array request when authorization error returned
     * @return array query result from $arParams
     *
     */

    private static function GetNewAuth($arParams)
    {
        $result = [];
        $arSettings = static::getAppSettings();
        if($arSettings !== false) {
            $arParamsAuth = [
                'this_auth' => 'Y',
                'params'    => [
                    'client_id'     => env('C_REST_CLIENT_ID'),
                    'grant_type'    => 'refresh_token',
                    'client_secret' => env('C_REST_CLIENT_SECRET'),
                    'refresh_token' => $arSettings[ "refresh_token" ],
                ]
            ];

            $newData = static::callCurl($arParamsAuth);

            if(isset($newData[ 'C_REST_CLIENT_ID' ]))
                unset($newData[ 'C_REST_CLIENT_ID' ]);

            if(isset($newData[ 'C_REST_CLIENT_SECRET' ]))
                unset($newData[ 'C_REST_CLIENT_SECRET' ]);

            if(isset($newData[ 'error' ]))
                unset($newData[ 'error' ]);

            if(static::setAppSettings($newData)){
                $arParams[ 'this_auth' ] = 'N';
                $result = static::callCurl($arParams);
            }
        }
        return $result;
    }

    /**
     * @var $arSettings array settings application
     * @var $isInstall  boolean true if install app by installApp()
     * @return boolean
     */

    private static function setAppSettings($arSettings, $isInstall = false)
    {
        $return = false;
        if(is_array($arSettings)) {
            $oldData = static::getAppSettings();
            if($isInstall != true && !empty($oldData) && is_array($oldData))
                $arSettings = array_merge($oldData, $arSettings);

            $return = static::setSettingData($arSettings);
        }
        return $return;
    }

    /**
     * @return mixed setting application for query
     */

    private static function getAppSettings()
    {
        if(defined("C_REST_WEB_HOOK_URL") && !empty(C_REST_WEB_HOOK_URL)) {
            $arData = [
                'client_endpoint' => C_REST_WEB_HOOK_URL,
                'is_web_hook'     => 'Y'
            ];
            $isCurrData = true;
        } else {
            $arData = static::getSettingData();
            $isCurrData = false;
            if(
                !empty($arData[ 'access_token' ]) &&
                !empty($arData[ 'domain' ]) &&
                !empty($arData[ 'refresh_token' ]) &&
                !empty($arData[ 'application_token' ]) &&
                !empty($arData[ 'client_endpoint' ])
            ){
                $isCurrData = true;
            }
        }

        return ($isCurrData) ? $arData : false;
    }

    /**
     * Can overridden this method to change the data storage location.
     *
     * @return array setting for getAppSettings()
     */

    protected static function getSettingData()
    {
        $return = [
            'access_token' 		=> static::$dataExt['access_token'],
            'domain' 			=> static::$dataExt['domain'],
            'client_endpoint' 	=> static::$dataExt['client_endpoint'],
            'refresh_token' 	=> static::$dataExt['refresh_token'],
            'application_token' => static::$dataExt['application_token']
        ];

        return $return;
    }

    /**
     * @var $data mixed
     * @var $encoding boolean true - encoding to utf8, false - decoding
     *
     * @return string json_encode with encoding
     */
    protected static function changeEncoding($data, $encoding = true)
    {
        if(is_array($data)) {
            $result = [];
            foreach ($data as $k => $item) {
                $k = static::changeEncoding($k, $encoding);
                $result[$k] = static::changeEncoding($item, $encoding);
            }
        } else {
            if($encoding) {
                $result = iconv(C_REST_CURRENT_ENCODING, "UTF-8//TRANSLIT", $data);
            } else {
                $result = iconv( "UTF-8",C_REST_CURRENT_ENCODING, $data);
            }
        }

        return $result;
    }

    /**
     * @var $data mixed
     * @var $debag boolean
     *
     * @return string json_encode with encoding
     */
    protected static function wrapData($data, $debag = false)
    {
        if(defined('C_REST_CURRENT_ENCODING')) {
            $data = static::changeEncoding($data, true);
        }
        $return = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
        //$return = json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);

        if($debag) {
            $e = json_last_error();
            if ($e != JSON_ERROR_NONE) {
                if ($e == JSON_ERROR_UTF8) {
                    return 'Failed encoding! Recommended \'UTF - 8\' or set define C_REST_CURRENT_ENCODING = current site encoding for function iconv()';
                }
            }
        }

        return $return;
    }

    /**
     * @var $data mixed
     * @var $debag boolean
     *
     * @return string json_decode with encoding
     */
    protected static function expandData($data)
    {
        $return = json_decode($data, true);
        if(defined('C_REST_CURRENT_ENCODING'))
            $return = static::changeEncoding($return, false);

        return $return;
    }

    /**
     * Can overridden this method to change the data storage location.
     *
     * @var $arSettings array settings application
     * @return boolean is successes save data for setSettingData()
     */

    protected static function setSettingData($arSettings)
    {
        Partner::where('domain', static::$dataExt['domain'])
            ->update([
                'access_token' => $arSettings['access_token'],
                'application_token' => $arSettings['application_token'],
                'refresh_token' => $arSettings['refresh_token']
            ]);

        static::$dataExt['access_token'] = htmlspecialchars($arSettings['access_token']);
        static::$dataExt['refresh_token'] = htmlspecialchars($arSettings['refresh_token']);
        static::$dataExt['application_token'] = htmlspecialchars($arSettings['application_token']);

        return true;
    }

    /**
     * Can overridden this method to change the log data storage location.
     *
     * @var $arData array of logs data
     * @var $type   string to more identification log data
     * @return boolean is successes save log data
     */

    public static function setLog($arData, $type = '')
    {
        /*if(!defined("C_REST_BLOCK_LOG") || C_REST_BLOCK_LOG !== true)
        {
            if(defined("C_REST_LOGS_DIR"))
            {
                $path = C_REST_LOGS_DIR;
            }
            else
            {
                $path = __DIR__ . '/logs/';
            }
            $path .= date("Y-m-d/H") . '/';

            if (!file_exists($path))
            {
                @mkdir($path, 0775, true);
            }

            $path .= time() . '_' . $type . '_' . rand(1, 9999999) . 'log';
            if(!defined("C_REST_LOG_TYPE_DUMP") || C_REST_LOG_TYPE_DUMP !== true)
            {
                $jsonLog = static::wrapData($arData);
                if ($jsonLog === false)
                {
                    $return = file_put_contents($path . '_backup.txt', var_export($arData, true));
                }
                else
                {
                    $return = file_put_contents($path . '.json', $jsonLog);
                }
            }
            else
            {
                $return = file_put_contents($path . '.txt', var_export($arData, true));
            }
        }*/
        //return $return;
    }

    public static function setDataExt($request)
    {
        static::$dataExt = $request;
    }
}
