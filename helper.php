<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 - 2015 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

class ModWowTopWeeklyContributorsHelper extends WoWModuleAbstract
{
    protected function getInternalData()
    {
        try {
            $result = WoW::getInstance()->getAdapter('BattleNET')->getData('top_weekly_contributors');
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (strpos($result->body, '<div class="summary-weekly-contributors">') === false) {
            return JText::_('MOD_WOW_TOP_WEEKLY_CONTRIBUTORS_NOT_FOUND');
        }

        // get only summary data
        preg_match('#<div class="summary-weekly-contributors">(.+?)</div>#is', $result->body, $result->body);
        $result->body = $result->body[1];


        $result->dom = new DOMDocument;
        $result->dom->loadHTML('<?xml encoding="UTF-8">' . $result->body);
        $result->table = $result->dom->getElementsByTagName('tbody');

        if ($result->table->length == 0) {
            return JString::trim($result->dom->getElementsByTagName('div')->item(0)->textContent);
        }

        $result->rows = array();

        $result->table = $result->table->item(0)->getElementsByTagName('tr');

        for ($c = 0; $c < $result->table->length; $c++) {
            $result->rows[] = $result->table->item($c)->getElementsByTagName('td');
        }

        $contributors = array();
        foreach ($result->rows as $key => $row) {
            $contributors[$key] = new stdClass;
            $contributors[$key]->name = trim($row->item(1)->textContent);
            $contributors[$key]->color = $row->item(1)->getElementsByTagName('a')->item(0)->getAttribute('class');
            $contributors[$key]->class = $row->item(2)->getElementsByTagName('img')->item(0)->getAttribute('src');
            $contributors[$key]->level = trim($row->item(3)->textContent);
            $contributors[$key]->points = trim($row->item(4)->textContent);
            $contributors[$key]->link = $this->link($contributors[$key]->name);
        }

        return $contributors;
    }

    private function link($member)
    {
        $sites['battle.net'] = 'http://' . $this->params->global->get('region') . '.battle.net/wow/' . $this->params->global->get('locale') . '/character/' . $this->params->global->get('realm') . '/' . $member . '/';
        $sites['wowhead.com'] = 'http://' . $this->params->global->get('locale') . '.wowhead.com/profile=' . $this->params->global->get('region') . '.' . $this->params->global->get('realm') . '.' . $member;
        return $sites[$this->params->global->get('link')];
    }
}