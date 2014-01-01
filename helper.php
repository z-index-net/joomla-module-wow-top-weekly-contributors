<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

abstract class ModWowTopWeeklyContributorsHelper
{

    public static function getAjax()
    {
        $module = JModuleHelper::getModule('mod_' . JFactory::getApplication()->input->get('module'));

        if (empty($module)) {
            return false;
        }

        JFactory::getLanguage()->load($module->module);

        $params = new JRegistry($module->params);
        $params->set('ajax', 0);

        ob_start();

        require(dirname(__FILE__) . '/' . $module->module . '.php');

        return ob_get_clean();
    }

    public static function getData(JRegistry &$params)
    {

        if ($params->get('ajax')) {
            return;
        }

        $params->set('guild', rawurlencode(JString::strtolower($params->get('guild'))));
        $params->set('realm', rawurlencode(JString::strtolower($params->get('realm'))));
        $params->set('region', JString::strtolower($params->get('region')));
        $params->set('lang', JString::strtolower($params->get('lang', 'en')));
        $params->set('link', $params->get('link', 'battle.net'));

        $url = 'http://' . $params->get('region') . '.battle.net/wow/' . $params->get('lang') . '/guild/' . $params->get('realm') . '/' . $params->get('guild') . '/';

        $cache = JFactory::getCache('wow', 'output');
        $cache->setCaching(1);
        $cache->setLifeTime($params->get('cache_time', 60));

        $key = md5($url);

        if (!$result = $cache->get($key)) {
            try {
                $http = JHttpFactory::getHttp();
                $http->setOption('userAgent', 'Joomla! ' . JVERSION . '; WoW Top Weekly Contributors; php/' . phpversion());

                $result = $http->get($url, null, $params->get('timeout', 10));
            } catch (Exception $e) {
                return $e->getMessage();
            }

            $cache->store($result, $key);
        }

        if ($result->code != 200) {
            return __CLASS__ . ' HTTP-Status ' . JHtml::_('link', 'http://wikipedia.org/wiki/List_of_HTTP_status_codes#' . $result->code, $result->code, array('target' => '_blank'));
        }

        if (strpos($result->body, '<div id="roster" class="table">') === false) {
            return 'no contributors found';
        }

        // get only roster data
        preg_match('#<div id="roster" class="table">(.+?)</div>#is', $result->body, $result->body);

        $result->body = $result->body[1];

        $result->dom = new DOMDocument;
        $result->dom->loadHTML('<?xml encoding="UTF-8">' . $result->body);
        $result->table = $result->dom->getElementsByTagName('tbody')->item(0)->getElementsByTagName('tr');
        $result->rows = array();

        for ($c = 0; $c < $result->table->length; $c++) {
            $result->rows[] = $result->table->item($c)->getElementsByTagName('td');
        }

        foreach ($result->rows as $key => $row) {
            $contributors[$key] = new stdClass;
            $contributors[$key]->name = trim($row->item(1)->textContent);
            $contributors[$key]->class = $row->item(2)->getElementsByTagName('img')->item(0)->getAttribute('src');
            $contributors[$key]->level = trim($row->item(3)->textContent);
            $contributors[$key]->points = trim($row->item(4)->textContent);
            $contributors[$key]->link = self::link($contributors[$key]->name, $params);
        }

        return $contributors;
    }

    private static function link($member, JRegistry &$params)
    {
        $sites['battle.net'] = 'http://' . $params->get('region') . '.battle.net/wow/' . $params->get('lang') . '/character/' . $params->get('realm') . '/' . $member . '/';
        $sites['wowhead.com'] = 'http://' . $params->get('lang') . '.wowhead.com/profile=' . $params->get('region') . '.' . $params->get('realm') . '.' . $member;
        return $sites[$params->get('link')];
    }
}