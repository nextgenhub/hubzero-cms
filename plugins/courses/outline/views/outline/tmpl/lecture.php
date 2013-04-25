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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juser = JFactory::getUser();

ximport('Hubzero_User_Profile');
ximport('Hubzero_User_Profile_Helper');

$unit = $this->course->offering()->unit($this->unit);

if (!$unit)
{
	JError::raiseError(404, JText::_('uh-oh'));
}

$lecture = $unit->assetgroup($this->group);
if (!$lecture)
{
	JError::raiseError(404, JText::_('uh-oh'));
}

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . '&active=outline';
$current = $unit->assetgroups()->key();

if (!$this->course->offering()->access('view')) { ?>
	<p class="info"><?php echo JText::_('Access to the "Syllabus" section of this course is restricted to members only. You must be a member to view the content.'); ?></p>
<?php } else { ?>

	<div class="video container">
		<div class="video-wrap">
			<div class="video-player-wrap">
			<?php
			$used = 0;
			if ($lecture->assets()->total())
			{
				// Render video
				foreach ($lecture->assets() as $a)
				{
					if ($a->get('type') == 'video' && $a->isPublished())
					{
						$used = $a->get('id');
						echo $a->render($this->course);

						// Break - only 'render' first video available (should we do something about multiple video assets?)
						break;
					}
				}
			}
			?>
			</div><!-- / .video-player-wrap -->
			<div class="video-meta">
				<h3>
					<?php echo $lecture->get('title'); ?>
				</h3>
			

			<ul class="lecture-assets">
				<?php
				$exams = array();
				// Are there any assets?
				if ($lecture->assets()->total())
				{
					// Loop through the assets
					foreach ($lecture->assets() as $a)
					{
						// Was this asset already used elsewhere on the page?
						// This should generally only happen with the video asset
						if ($a->get('id') == $used || !$a->isPublished())
						{
							continue;
						}
						$href = JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . '&asset=' . $a->get('id'));
						/*if ($a->get('type') == 'video')
						{
							$href = JRoute::_($base . '&unit=' . $unit->get('alias') . '&b=' . $lecture->get('alias'));
						}*/
						$cls = 'download';
						if ($a->get('type') == 'exam')
						{
							$cls = 'edit';
							$exams[] = '<a class="' . $cls . ' btn" href="' . $href . '" target="_blank">' . $this->escape(stripslashes($a->get('title'))) . '</a>';
						}
						else
						{
							if ($a->get('type') == 'link')
							{
								$cls = 'link';
							}
							echo '<li><a class="' . $cls . '" href="' . $href . '" target="_blank">' . $this->escape(stripslashes($a->get('title'))) . '</a></li>';
						}
					}
				}
				else
				{
					echo '<li><small>' . JText::_('COURSES_NO_ASSETS_FOR_GROUPING') . '</small></li>';
				}
				?>
			</ul>
			</div>

			<p class="lecture-nav">
			<?php 
			$lecture->key($current);

			if ($unit->isFirst() && $lecture->isFirst()) { ?>
				<span class="prev btn">
					<?php echo JText::_('Prev'); ?>
				</span>
			<?php } else { ?>
				<a class="prev btn" href="<?php echo JRoute::_($base . '&unit=' . $unit->get('alias') . '&b=' . $lecture->sibling('prev')->get('alias')); ?>">
					<?php echo JText::_('Prev'); ?>
				</a>
			<?php } ?>
			<?php 

			$uAlias = $unit->get('alias');
			$gAlias = '';

			// If the last unit AND last asstegroup in the unit
			if ($this->course->offering()->units()->isLast() && $unit->assetgroups()->isLast()) 
			{
				$gAlias = '';
			}
			else
			{
				// If NOT the last assetgroup
				if (!$unit->assetgroups()->isLast())
				{
					$gAlias = $unit->assetgroups()->fetch('next')->get('alias');
				}
				// If the last assetgroup AND NOT the last unit
				if ($unit->assetgroups()->isLast() && !$this->course->offering()->units()->isLast())
				{
					// Get the alias of the next unit
					$next = $this->course->offering()->units()->fetch('next');
					// Make sure it's published
					if ($next->isAvailable())
					{
						$uAlias = $next->get('alias');
						// Does the next unit have any assetgroups?
						if ($next->assetgroups()->total())
						{
							// Get the alias of the next assetgroup
							$gAlias = $next->assetgroups(0)->get('alias');
						}
					}
				}
			}

			if (!$uAlias || !$gAlias) { ?>
				<span class="next btn">
					<?php echo JText::_('Next'); ?>
				</span>
			<?php } else { ?>
				<a class="next btn" href="<?php echo JRoute::_($base . '&unit=' . $uAlias . '&b=' . $gAlias); ?>">
					<?php echo JText::_('Next'); ?>
				</a>
			<?php } ?>
			<?php 
			if (count($exams) > 0) {
				echo implode("\n", $exams);
			}
			?>
			</p>

		<?php if ($lecture->get('description')) { ?>
			<p class="lecture-description">
				<?php echo $this->escape(stripslashes($lecture->get('description'))); ?>
			</p>
		<?php } ?>
		</div><!-- / .video-wrap -->
	</div><!-- / .video container -->

	<?php
		// Trigger event
		$dispatcher =& JDispatcher::getInstance();
		$results = $dispatcher->trigger('onCourseAfterLecture', array(
			$this->course,
			$unit,
			$lecture
		));
		// Output results
		echo implode("\n", $results);
	?>
<?php } ?>