<?php

namespace Uisoft\App\Bitrix;

use ALS\Helper\CacheManager;

class ComponentEngine extends \CComponentEngine
{
    private $resolveCallback = false;
    private $greedyParts = array();
    /**
     * Функция возвращает шаблон урла для фильтра
     *
     * @param $template
     *
     * @return string
     */
    public static function prepareSefRule($template, $path): string
    {
        return str_replace('#SECTION_CODE_PATH#', $path, $template);
        //return '/catalog/tsilindr_dvuhstoronniy/f/#SMART_FILTER_PATH#/';
    }


    // Возвращает УРЛ для товара
    public function getDetailUrl(&$detailUrl)
    {
        //\ALS\Helper\Dbg::show(&$detailUrl, true, true);
    }
    public function setResolveCallback($resolveCallback)
    {
        if (is_callable($resolveCallback))
            $this->resolveCallback = $resolveCallback;
    }

    public function prepareUrl(&$requestUrl, &$arVariables, $arUrlTemplates = []): array
    {
        $result = [];
        $arParams = $this->getComponent()->arParams;

        // Регулярное выражение. Умный фильтр
        // Регулярное выражение примерно такое должно получиться: (.*)/f/(.*)/[^/]*$ (это для умного фильтра)
        $pattern = preg_replace('/#([^#]+)#/', '(.*)', $arUrlTemplates['smart_filter']) . '[^/]*$';
        preg_match('#' . $pattern . '#', $requestUrl, $matches);

        if (empty($matches)) {
            // Если умный фильтр не установлен, пробуем просто раздел
            $pattern = preg_replace('/#([^#]+)#/', '(.*)', $arUrlTemplates['section']) . '[^/]*$';
            preg_match('#' . $pattern . '#', $requestUrl, $matches);
        }

        if (empty($matches)) {
            // если не подходит под регулярное выражение раздела, пробуем товар детально
            $pattern = preg_replace('/#([^#]+)#/', '(.*)', $arUrlTemplates['element']) . '[^/]*$';
            preg_match('#' . $pattern . '#', $requestUrl, $matches);
        }

        // Убираем папку
        $strSectionStructurePath = str_replace($arParams['SEF_FOLDER'], '', $matches[1]);

        
        // Получаем код реального раздел из структуры (закешируем)
        // Разделы из структуры могут быть привязаны как к корневому разделу, так и к потомкам
        // !!! Если раздел в структуре не найден – 404 ошибка

        $sectionStructure = CacheManager::getIblockSectionsFromCache(
            [
                'IBLOCK_ID' => $arParams['IBLOCK_ID_STRUCTURE'],
                'FILTER' => [
                    'UF_URL' => $matches[1] . '/'
                ],
                'SELECT' => [
                    'ID:int>id',
                    'CODE:string>code',
                    'NAME:string>name',
                    'UF_CATALOG_SECTION:int>section'
                ],
                '__SKIP_CACHE' => true
            ]
        );


        if (!empty($sectionStructure)) {
            $sectionStructure = array_shift($sectionStructure);

            // Получим подразделы для фильтра, если они есть
            $subSectionsStructure = CacheManager::getIblockSectionsFromCache(
                [
                    'IBLOCK_ID' => $arParams['IBLOCK_ID_STRUCTURE'],
                    'FILTER' => [
                        'SECTION_ID' => $sectionStructure['id']
                    ],
                    'SELECT' => [
                        'ID:int>id',
                        'CODE:string>code',
                        'NAME:string>name',
                        'UF_CATALOG_SECTION:int>section',
                        'UF_URL:string>url'
                    ],
                    '__SKIP_CACHE' => true
                ]
            );
            $sectionStructure['items'] = $subSectionsStructure;

            $result = $sectionStructure;

            // Добавляем путь в структуре
            $result['sectionStructurePath'] = $strSectionStructurePath;


            if ($sectionStructure['section'] > 0) {
                $section = CacheManager::getIblockSectionsFromCache(
                    [
                        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                        'FILTER' => [
                            'ID' => $sectionStructure['section']
                        ],
                        'SELECT' => [
                            'ID:int>id',
                            'CODE:string>code',
                            'NAME:string>name',
                        ],
                        '__SKIP_CACHE' => true
                    ]
                );

                $arVariables['SECTION_ID'] = $sectionStructure['section'];

                // получим путь к разделу
                $sectionStructurePath = CacheManager::getNavChainFromCache([
                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                    'SECTION_ID' => $sectionStructure['section'],
                    ['SELECT' => ['ID:int>id']]
                ]);

                // Тут соединяются все разделы реального каталога. Принял решение отдать туда только id для фильтра
                $strSectionPath = implode('/', array_column($sectionStructurePath, 'code'));
                $result['sectionPath'] = $strSectionPath;

                if (!empty($section)) {
                    $section = array_shift($section);

                    //$requestUrl = preg_replace('#' . $strSectionStructurePath . '#', $strSectionPath, $requestUrl, 1);
                    $requestUrl = preg_replace('#' . $strSectionStructurePath . '#', $section['code'], $requestUrl, 1);
                }
            }
        }
        return $result;
    }

    /**
     * Finds match between requestURL and on of the url templates.
     *
     * <p>Lets using the engine object and greedy templates.</p>
     *
     * @param string      $folder404
     * @param array[string]string $arUrlTemplates
     * @param array[string]string &$arVariables
     * @param string|bool $requestURL
     *
     * @return string
     *
     */
    public function guessComponentPath($folder404, $arUrlTemplates, &$arVariables, $requestURL = false)
    {
        if (!isset($arVariables) || !is_array($arVariables)) {
            $arVariables = [];
        }

        if ($requestURL === false) {
            $requestURL = \Bitrix\Main\Context::getCurrent()->getRequest()->getRequestedPage();
        }

        $folder404 = str_replace("\\", "/", $folder404);
        if ($folder404 != "/") {
            $folder404 = "/" . trim($folder404, "/ \t\n\r\0\x0B") . "/";
        }

        //SEF base URL must match curent URL (several components on the same page)
        if (mb_strpos($requestURL, $folder404) !== 0) {
            return false;
        }

        $currentPageUrl = mb_substr($requestURL, mb_strlen($folder404));
        $this->cacheSalt = md5($currentPageUrl);

        $pageCandidates = array();
        $arUrlTemplates = $this->sortUrlTemplates($arUrlTemplates, $bHasGreedyPartsInTemplates);

        if (
            $bHasGreedyPartsInTemplates
            && is_callable($this->resolveCallback)
        ) {
            foreach ($arUrlTemplates as $pageID => $pageTemplate) {
                $arVariablesTmp = $arVariables;

                if ($this->__CheckPath4Template($pageTemplate, $currentPageUrl, $arVariablesTmp)) {
                    if ($this->hasNoVariables($pageTemplate)) {
                        $arVariables = $arVariablesTmp;
                        return $pageID;
                    } else {
                        $pageCandidates[$pageID] = $arVariablesTmp;
                    }
                }
            }
        } else {
            // поставим первым шаблон фильтра
            $filterTemplate = $arUrlTemplates['smart_filter'];
            unset($arUrlTemplates['smart_filter']);
            $arUrlTemplates = array_merge(['smart_filter' => $filterTemplate], $arUrlTemplates);

            foreach ($arUrlTemplates as $pageID => $pageTemplate) {
                if ($this->checkPath4Template($pageTemplate, $currentPageUrl, $arVariables)) {
                    return $pageID;
                }
            }
        }

        if (!empty($pageCandidates) && is_callable($this->resolveCallback)) {
            return call_user_func_array($this->resolveCallback, array($this, $pageCandidates, &$arVariables));
        }

        return false;
    }

    /**
     * Checks if page template matches current URL.
     *
     * <p>In case of succsessful match fills in parsed variables.</p>
     *
     * @param string $pageTemplate
     * @param string $currentPageUrl
     * @param array[string]string &$arVariables
     *
     * @return bool
     *
     */
    protected function checkPath4Template(string $pageTemplate, string $currentPageUrl, &$arVariables): bool
    {
        if (!empty($this->greedyParts)) {
            $pageTemplateReg = preg_replace("'#(?:" . implode("|", $this->greedyParts) . ")#'", "(.+?)", $pageTemplate);
            // Добавил в регулярное выражение (.+?) чтоб определялся раздел с учетом чпу. Иначе он не находил
            $pageTemplateReg = preg_replace("'#[^#]+?#'", "([^/]+?)", $pageTemplateReg);
        } else {
            $pageTemplateReg = preg_replace("'#[^#]+?#'", "([^/]+?)", $pageTemplate);
        }

        if (mb_substr($pageTemplateReg, -1, 1) == "/") {
            $pageTemplateReg .= "index\\.php";
        }


        $arValues = array();
        if (preg_match("`^" . $pageTemplateReg . "$`", $currentPageUrl, $arValues)) {
            $arMatches = array();
            if (preg_match_all("'#([^#]+?)#'", $pageTemplate, $arMatches)) {
                for ($i = 0, $cnt = count($arMatches[1]); $i < $cnt; $i++) {
                    $arVariables[$arMatches[1][$i]] = $arValues[$i + 1];
                }
            }
            return true;
        }
        return false;
    }
}
