<?php

/**
 * WoW Top Weekly Contributors
 *
 * @author     Branko Wilhelm <bw@z-index.net>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 Branko Wilhelm
 * @package    mod_wow_top_weekly_contributors
 * @license    GNU General Public License v3
 * @version    $Id$
 */

defined('_JEXEC') or die;

JFactory::getDocument()->addStyleSheet(JURI::base(true) . '/modules/' . $module->module . '/tmpl/stylesheet.css');
?>
<div class="mod_wow_top_weekly_contributors">
    <?php foreach ($contributors as $contributor): ?>
    <div>
    <?php print_r($$contributor); ?>
    </div>
    <?php endforeach; ?>
</div>