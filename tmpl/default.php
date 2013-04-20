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

JFactory::getDocument()->addStyleSheet(JUri::base(true) . '/modules/' . $module->module . '/tmpl/stylesheet.css');
?>
<table class="mod_wow_top_weekly_contributors">
<?php if($params->get('display_thead')) { ?>
<thead>
    <tr>
   		<?php if($params->get('display_place')) { ?>
    	<th class="place"><?php echo JText::_('MOD_WOW_TOP_WEEKLY_CONTRIBUTORS_PLACE'); ?></th>
        <?php } ?>
    	<th class="name"><?php echo JText::_('MOD_WOW_TOP_WEEKLY_CONTRIBUTORS_NAME'); ?></th>
        <?php if($params->get('display_class')) { ?>
    	<th class="class"><?php echo JText::_('MOD_WOW_TOP_WEEKLY_CONTRIBUTORS_CLASS'); ?></th>
        <?php } ?>
        <?php if($params->get('display_level')) { ?>
    	<th class="level"><?php echo JText::_('MOD_WOW_TOP_WEEKLY_CONTRIBUTORS_LEVEL'); ?></th>
        <?php } ?>
        <th class="points"><?php echo JText::_('MOD_WOW_TOP_WEEKLY_CONTRIBUTORS_POINTS'); ?></th>
    </tr>
</thead>
<?php } ?>
<tbody>
    <?php foreach ($contributors as $key => $contributor) { ?>
    <tr>
   		<?php if($params->get('display_place')) { ?>
        <td class="place"><?php echo ++$key; ?></td>
        <?php } ?>
        <td class="name"><?php echo JHtml::_('link', $contributor->link, $contributor->name); ?></td>
 		<?php if($params->get('display_class')) { ?>
        <td class="class"><?php echo JHtml::_('image', $contributor->class, $contributor->name); ?></td>
        <?php } ?>
        <?php if($params->get('display_level')) { ?>
        <td class="level"><?php echo $contributor->level; ?></td>
        <?php } ?>
        <td class="points"><?php echo $contributor->points; ?></td>
    </tr>
    <?php } ?>
</tbody>
</table>