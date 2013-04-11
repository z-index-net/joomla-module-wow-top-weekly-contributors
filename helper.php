<?php

/**
 * WoW top Weekly Contributors
 *
 * @author     Branko Wilhelm <bw@z-index.net>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 Branko Wilhelm
 * @package    mod_wow_top_weekly_contributors
 * @license    GNU General Public License v3
 * @version    $Id$
 */

defined('_JEXEC') or die;

abstract class mod_wow_top_weekly_contributors {
	
    public static function _(JRegistry &$params) {

        if(!$params->get('lang') || !$params->get('realm') || !$params->get('guild')) {
            return 'please configure Module - ' . __CLASS__;
        }

        $url = 'http://' . $params->get('region') . '.battle.net/wow/' . $params->get('lang') . '/guild/' . $params->get('realm') . '/' . $params->get('guild') . '/';
        
        $cache = JFactory::getCache(__CLASS__, 'output');
        $cache->setCaching(1);
        $cache->setLifeTime($params->get('cache_time', 60) * 60);
         
        $key = md5($url);
         
        if(!$result = $cache->get($key)) {
        	$http = new JHttp;
        	$http->setOption('userAgent', 'Joomla! ' . JVERSION . '; WoW Top Weekly Contributors; php/' . phpversion());

        	try {
        		$result = $http->get($url, null, $params->get('timeout', 10));
        	}catch(Exception $e) {
        		return $e->getMessage();
        	}
        	
        	$cache->store($result, $key);
        }
         
        if($result->code != 200) {
        	return __CLASS__ . ' HTTP-Status ' . JHTML::_('link', 'http://wikipedia.org/wiki/List_of_HTTP_status_codes#'.$result->code, $result->code, array('target' => '_blank'));
        }
        
        if(strpos($result->body, '<div id="roster" class="table">') === false) {
        	return 'no $contributors found';
        }

        // get only roster data
        preg_match('#<div id="roster" class="table">(.+?)</div>#is', $result->body, $result->body);
        
        $result->body = $result->body[1];
        //domix($result->body,1);
        
        return 'null';
        
        // find all contributors
        preg_match_all('#<a.*href="achievement\#([0-9:]+):a(\d+)".*>.*background-image: url\("(.*)"\);.*<strong class="title">(.*)</strong>#Uis', $result->body, $matches, PREG_SET_ORDER);
        
        foreach($matches as $key => $match) {
        	$contributors[$key] = new stdClass;
        	$contributors[$key]->name = $match[4];
        	$contributors[$key]->image = $match[3];
        	$contributors[$key]->id = $match[2];
        	$contributors[$key]->link = $url . '#' . $match[1] . ':' . $match[2];
        	$contributors[$key]->link = self::link($achievements[$key], $params);
        }
        
        return $contributors;
    }
    
    private static function link($member, JRegistry &$params) {
    	$sites['battle.net'] = 'http://' . $params->get('region') . '.battle.net/wow/' . $params->get('lang') . '/character/' . $params->get('realm') . '/' . $member . '/';
    	$sites['wowhead.com'] = 'http://' . $params->get('lang') . '.wowhead.com/profile=' . $params->get('region') . '.' . $params->get('realm'). '.' . $member;
    	return $sites[$params->get('link')];
    }
}