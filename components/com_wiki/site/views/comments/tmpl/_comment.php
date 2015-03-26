<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die('Restricted access');

	$cls = isset($this->cls) ? $this->cls : 'odd';

	if ($this->page->get('created_by') == $this->comment->get('created_by'))
	{
		$cls .= ' author';
	}
	$cls .= ($this->comment->isReported()) ? ' abusive' : '';
	if ($this->comment->get('state') == 1)
	{
		$cls .= ' chosen';
	}

	$name = Lang::txt('COM_WIKI_ANONYMOUS');
	if (!$this->comment->get('anonymous'))
	{
		$name = $this->escape(stripslashes($this->comment->creator('name', $name)));
		if ($this->comment->creator('public'))
		{
			$name = '<a href="' . Route::url($this->comment->creator()->getLink()) . '">' . $name . '</a>';
		}
	}

	if ($this->comment->isReported())
	{
		$comment = '<p class="warning">' . Lang::txt('COM_WIKI_COMMENT_REPORTED_AS_ABUSIVE') . '</p>';
	}
	else
	{
		$comment  = $this->comment->content('parsed');
	}

	$this->comment->set('category', 'answercomment');
?>
	<li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
		<p class="comment-member-photo">
			<img src="<?php echo $this->comment->creator()->getPicture($this->comment->get('anonymous')); ?>" alt="" />
		</p>
		<div class="comment-content">
			<?php
			if ($this->comment->get('rating'))
			{
				switch ($this->comment->get('rating'))
				{
					case 0:   $rcls = ' no-stars';        break;
					case 0.5: $rcls = ' half-stars';      break;
					case 1:   $rcls = ' one-stars';       break;
					case 1.5: $rcls = ' onehalf-stars';   break;
					case 2:   $rcls = ' two-stars';       break;
					case 2.5: $rcls = ' twohalf-stars';   break;
					case 3:   $rcls = ' three-stars';     break;
					case 3.5: $rcls = ' threehalf-stars'; break;
					case 4:   $rcls = ' four-stars';      break;
					case 4.5: $rcls = ' fourhalf-stars';  break;
					case 5:   $rcls = ' five-stars';      break;
					default:  $rcls = ' no-stars';        break;
				}
				?>
				<p><span class="avgrating<?php echo $rcls; ?>"><span><?php echo Lang::txt('COM_WIKI_COMMENT_RATING', $this->comment->get('rating')); ?></span></span></p>
				<?php
			}
			?>

			<p class="comment-title">
				<strong><?php echo $name; ?></strong>
				<a class="permalink" href="<?php echo Route::url($this->page->link('comments') . '#c' . $this->comment->get('id')); ?>" title="<?php echo Lang::txt('COM_WIKI_PERMALINK'); ?>">
					<span class="comment-date-at">@</span>
					<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
					<span class="comment-date-on"><?php echo Lang::txt('COM_WIKI_ON'); ?></span>
					<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
				</a>
			</p>

			<div class="comment-body">
				<?php echo $comment; ?>
			</div>

			<p class="comment-options">
				<?php if ($this->page->access('delete', 'comment')) { ?>
					<a class="icon-delete delete" href="<?php echo Route::url($this->comment->link('delete')); ?>"><!--
						--><?php echo Lang::txt('COM_WIKI_DELETE'); ?><!--
					--></a>
				<?php } ?>
				<?php if ($this->page->access('edit', 'comment')) { ?>
					<a class="icon-edit edit" href="<?php echo Route::url($this->comment->link('edit')); ?>"><!--
						--><?php echo Lang::txt('COM_WIKI_EDIT'); ?><!--
					--></a>
				<?php } ?>

			<?php if (!$this->comment->isReported()) { ?>
				<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
					<?php if (JRequest::getInt('reply', 0) == $this->comment->get('id')) { ?>
					<a class="icon-reply reply active" data-txt-active="<?php echo Lang::txt('COM_WIKI_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_WIKI_REPLY'); ?>" href="<?php echo Route::url($this->comment->link()); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('COM_WIKI_CANCEL'); ?><!--
				--></a>
					<?php } else { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('COM_WIKI_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_WIKI_REPLY'); ?>" href="<?php echo Route::url($this->comment->link('reply')); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('COM_WIKI_REPLY'); ?><!--
				--></a>
					<?php } ?>
				<?php } ?>
				<a class="icon-abuse abuse" data-txt-flagged="<?php echo Lang::txt('COM_WIKI_COMMENT_REPORTED_AS_ABUSIVE'); ?>" href="<?php echo Route::url($this->comment->link('report')); ?>"><!--
					--><?php echo Lang::txt('COM_WIKI_REPORT_ABUSE'); ?><!--
				--></a>
			<?php } ?>
			</p>

		<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
			<div class="addcomment comment-add<?php if (Request::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->get('id'); ?>">
				<?php if (User::isGuest()) { ?>
				<p class="warning">
					<?php echo Lang::txt('COM_WIKI_WARNING_LOGIN_REQUIRED', '<a href="' . Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->page->link('comments'), false, true))) . '">' . Lang::txt('COM_WIKI_LOGIN') . '</a>'); ?>
				</p>
				<?php } else { ?>
				<form id="cform<?php echo $this->comment->get('id'); ?>" action="<?php echo Route::url($this->page->link('comments')); ?>" method="post" enctype="multipart/form-data">
					<a name="commentform<?php echo $this->comment->get('id'); ?>"></a>
					<fieldset>
						<legend><span><?php echo Lang::txt('COM_WIKI_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : Lang::txt('COM_WIKI_ANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('id'); ?>" />
						<input type="hidden" name="comment[pageid]" value="<?php echo $this->page->get('id'); ?>" />
						<input type="hidden" name="comment[created]" value="" />
						<input type="hidden" name="comment[created_by]" value="<?php echo User::get('id'); ?>" />
						<input type="hidden" name="comment[version]" value="<?php echo $this->page->revision()->get('version'); ?>" />
						<input type="hidden" name="comment[status]" value="1" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="comments" />
						<input type="hidden" name="scope" value="<?php echo $this->page->get('scope'); ?>" />
						<input type="hidden" name="pagename" value="<?php echo $this->page->get('pagename'); ?>" />
						<input type="hidden" name="<?php echo $this->page->get('group_cn') ? 'action' : 'task'; ?>" value="savecomment" />

						<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
							<span class="label-text"><?php echo Lang::txt('COM_WIKI_ENTER_COMMENTS'); ?></span>
							<?php
							echo \Components\Wiki\Helpers\Editor::getInstance()->display('comment[ctext]', 'comment_' . $this->comment->get('id') . '_content', '', 'minimal no-footer', '35', '4');
							?>
						</label>

						<label id="comment-anonymous-label" for="comment-anonymous">
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
							<?php echo Lang::txt('COM_WIKI_POST_COMMENT_ANONYMOUSLY'); ?>
						</label>

						<?php echo JHTML::_('form.token'); ?>

						<p class="submit">
							<input type="submit" value="<?php echo Lang::txt('COM_WIKI_SUBMIT'); ?>" />
						</p>
					</fieldset>
				</form>
				<?php } ?>
			</div><!-- / .addcomment -->
		<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		if ($this->depth < $this->config->get('comments_depth', 3))
		{
			$filters = array('version' => '');
			if ($this->version)
			{
				$filters['version'] = 'AND version=' . $this->version;
			}

			$this->view('_list', 'comments')
			     ->setBasePath(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'site')
			     ->set('parent', $this->comment->get('id'))
			     ->set('page', $this->page)
			     ->set('option', $this->option)
			     ->set('comments', $this->comment->replies('list', $filters))
			     ->set('config', $this->config)
			     ->set('depth', $this->depth)
			     ->set('version', $this->version)
			     ->set('cls', $cls)
			     ->display();
		}
		?>
	</li>