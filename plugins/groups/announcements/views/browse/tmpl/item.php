<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//date/time formating strings
$dateFormat  = '%d %b, %Y';
$timeFormat  = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat  = 'd M, Y';
	$timeFormat  = 'h:i a';
	$tz = true;
}

//wiki parser setup
$wikiconfig = array(
	'option'   => $this->option,
	'scope'    => 'groups',
	'pagename' => $this->group->get('cn'),
	'pageid'   => 0,
	'filepath' => JPATH_ROOT . DS . 'site' . DS . 'groups' . DS . $this->group->get('gidNumber'),
	'domain'   => '' 
);
ximport('Hubzero_Wiki_Parser');
$p =& Hubzero_Wiki_Parser::getInstance();

//class of announcement 
$class = 'unpublished';
$now = time();
if (($now >= strtotime($this->announcement->publish_up) || $this->announcement->publish_up == '0000-00-00 00:00:00') 
 && ($now <= strtotime($this->announcement->publish_down) || $this->announcement->publish_down == '0000-00-00 00:00:00'))
{
	$class = 'published';
}

//are we high priority
if ($this->announcement->priority)
{
	$class .= ' high';
}

//are we high priority
if ($this->announcement->sticky)
{
	$class .= ' sticky';
}

//did the user already close this
$closed = JRequest::getWord('group_announcement_' . $this->announcement->id, '', 'cookie');
if ($closed == 'closed' && $this->showClose == true)
{
	return;
}
?>

<div class="announcement-container <?php echo $class; ?>">
	<?php if (strstr($class, 'unpublished')) : ?>
		<span class="unpublished-message">Not Currently Active</span>
	<?php endif; ?>
	<div class="announcement">
		<?php echo $p->parse(stripslashes($this->announcement->content), $wikiconfig); ?>
		<dl class="entry-meta">
			<dt class="entry-id"><?php echo $this->announcement->id; ?></dt> 
			<?php if ($this->authorized == 'manager') : ?>
				<dd class="entry-author">
					<?php
						ximport('Hubzero_User_Profile');
						$profile = Hubzero_User_Profile::getInstance( $this->announcement->created_by );
						if (is_object($profile) && $profile->get('name') != '')
						{
							echo $profile->get('name');
						}
					?>
				</dd>
			<?php endif; ?>
			<dd class="time">
				<time datetime="<?php echo $this->announcement->created; ?>">
					<?php echo JHTML::_('date', $this->announcement->created, $timeFormat, $tz); ?>
				</time>
			</dd>
			<dd class="date">
				<time datetime="<?php echo $this->announcement->created; ?>">
					<?php echo JHTML::_('date', $this->announcement->created, $dateFormat, $tz); ?>
				</time>
			</dd>
		
			<?php if ($this->authorized == 'manager' && !$this->showClose) : ?>
				<dd class="entry-options">
					<?php if ($this->juser->get('id') == $this->announcement->created_by) : ?>
						<a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=announcements&action=edit&id=' . $this->announcement->id); ?>" title="<?php echo JText::_('Edit'); ?>">
							<?php echo JText::_('Edit'); ?>
						</a>
						<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=announcements&action=delete&id=' . $this->announcement->id); ?>" title="<?php echo JText::_('Delete'); ?>">
							<?php echo JText::_('Delete'); ?>
						</a>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
		</dl>
		<?php if ($this->showClose) : ?>
			<a class="close" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=announcements'); ?>" data-id="<?php echo $this->announcement->id; ?>" data-duration="30" title="<?php echo JText::_('Close this announcement?'); ?>">
				<span><?php echo JText::_('Close'); ?></span>
			</a>
		<?php endif; ?>
	</div>
</div>