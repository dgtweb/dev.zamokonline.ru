<?php

namespace ALS\Helper;

use CEventLog;
use Uisoft\App\nf_pp;


class Dbg
{

    const EVENT_TYPE_SECURITY = 'SECURITY';
    const EVENT_TYPE_ERROR = 'ERROR';
    const EVENT_TYPE_WARNING = 'WARNING';
    const EVENT_TYPE_INFO = 'INFO';
    const EVENT_TYPE_DEBUG = 'DEBUG';

    /**
     * Дебагер
     *
     * @param      $arr     - Массив
     * @param bool $die     - die()
     * @param bool $all     - Видно всем пользователям, иначе только администраторам
     * @param bool $varDump - Добавляет var_dump
     */
    public static function show($arr, $die = false, $all = false, $varDump = false)
    {
        global $USER;

        if ($USER->IsAdmin() || ($all == true)) {
            $pp = new nf_pp(array('trimString' => 0));
            $pp->pp($arr);

//            echo '<br clear="all" />';
//            $info = debug_backtrace();
//            echo "\n";
//            echo '<pre style="text-align: left;">';
//            echo "\n{$info[0]['file']} ({$info[0]['line']})\n";
//            if ($varDump === true) {
//                var_dump($arr);
//            }
//            echo print_r($arr, true) . "\n";
//            echo "\n";
//            echo '</pre>';
        }

        if ($die) {
            die;
        }
    }

    public static function showDebug($arr, $die = false, $all = false, $varDump = false)
    {
        if ($_REQUEST['debug'] === 'Y') {
            global $USER;

            if ($USER->IsAdmin() || ($all == true)) {
                echo '<br clear="all" />';
                $info = debug_backtrace();
                echo "\n";
                echo '<pre style="text-align: left;">';
                echo "\n{$info[0]['file']} ({$info[0]['line']})\n";
                if ($varDump === true) {
                    var_dump($arr);
                }
                echo print_r($arr, true) . "\n";
                echo "\n";
                echo '</pre>';
            }
            if ($die) {
                die;
            }
        }
    }

    /**
     * Функция добавляет новую запись в log-файл.
     *
     * @param string | array $str
     */
    public static function addLog($str): void
    {
        AddMessage2Log(print_r($str, true));
    }

    /**
     * @param       $type   Тип ошибки, одна из констант self::EVENT_TYPE_SECURITY ...
     * @param array $params Массив с параметрами:
     *                      <li>auditTypeId - собственный ID типа события
     *                      <li>moduleId - модуль, который записывает в лог
     *                      <li>id - идентификатор связанного объекта
     *                      <li>message - сообщение, которое будет отображаться в логе
     *                      <li>
     *                      <li>
     */
    public static function addEventLog($type, array $params)
    {
        CEventLog::Add([
            'SEVERITY' => $type,
            'AUDIT_TYPE_ID' => $params['auditTypeId'] ?? '',
            'MODULE_ID' => $params['moduleId'] ?? 'als.project',
            'ITEM_ID' => $params['id'] ?? '',
            'DESCRIPTION' => $params['message'] ?? '',
        ]);
    }
}
