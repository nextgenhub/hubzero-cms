<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//default all pic vars
$picture = new stdClass;
$picture->src    = $this->config->get('defaultpic');
$picture->name   = 'n/a';
$picture->size   = 'n/a';
$picture->width  = 'n/a';
$picture->height = 'n/a';

//get user id in nice format
$uid = \Hubzero\Utility\String::pad($this->profile->get('uidNumber'));

//get profile pic and path to picture
$pic = $this->profile->get("picture");
$path = DS . trim($this->config->get("webpath", '/site/members'), DS) . DS . $uid . DS;

//if we have a picture and it exists in the file system
if ($pic && file_exists(PATH_APP . $path . $pic))
{
	$size = filesize(PATH_APP . $path . $pic);
	list($width, $height, $type, $attr) = getimagesize(PATH_APP . $path . $pic);

	$picture->src    = substr(PATH_APP, strlen(PATH_ROOT)) . $path . $pic;
	$picture->name   = $pic;
	$picture->size   = \Hubzero\Utility\Number::formatBytes($size);
	$picture->width  = $width . ' <abbr title="pixels">px</abbr>';
	$picture->height = $height . ' <abbr title="pixels">px</abbr>';
}

$default = $this->config->get('defaultpic', '/core/components/com_members/site/assets/img/profile.gif');
if ($default == '/components/com_members/assets/img/profile.gif')
{
	$default = '/core/components/com_members/site/assets/img/profile.gif';
}
?>
<div id="ajax-upload-container">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" enctype="multipart/form-data">
		<h2><?php echo Lang::txt('Upload a New Profile Picture'); ?></h2>
		<div id="ajax-upload-left">
			<img id="picture-src" src="<?php echo $picture->src; ?>" alt="<?php echo $this->escape($picture->name); ?>" data-default-pic="<?php echo $this->escape($default); ?>" />
			<?php if ($this->profile->get("picture") != '') : ?>
				<a href="#" id="remove-picture"><?php echo Lang::txt('[Remove Picture]'); ?></a>
			<?php endif; ?>
		</div><!-- /#ajax-upload-left -->
		<div id="ajax-upload-right">
			<div id="ajax-uploader" data-action="<?php echo Request::base(true); ?>/index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;id=<?php echo $this->profile->get('uidNumber'); ?>&amp;task=doajaxupload&amp;no_html=1&amp;<?php echo Session::getFormToken(); ?>=1"></div>
			<table>
				<tbody>
					<tr>
						<td class="key"><?php echo Lang::txt('Name:'); ?></td>
						<td id="picture-name"><?php echo $this->escape($picture->name); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo Lang::txt('Size:'); ?></td>
						<td id="picture-size"><?php echo $picture->size; ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo Lang::txt('Width:'); ?></td>
						<td id="picture-width"><?php echo $picture->width; ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo Lang::txt('Height:'); ?></td>
						<td id="picture-height"><?php echo $picture->height; ?></td>
					</tr>
				</tbody>
			</table>
		</div><!-- /#ajax-upload-right -->
		<br class="clear" />
		<div id="ajax-upload-actions">
			<button class="btn section-edit-cancel"><?php echo Lang::txt('Cancel'); ?></button>
			<button class="btn btn-success section-edit-submit"><?php echo Lang::txt('Save Changes'); ?></button>
		</div><!-- /#ajax-upload-actions -->

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="ajaxuploadsave" />
		<input type="hidden" name="id" value="<?php echo $this->profile->get('uidNumber'); ?>" />
		<input type="hidden" name="profile[picture]" id="profile-picture" value="<?php echo $this->escape($this->profile->get('picture')); ?>" />
		<input type="hidden" name="no_html" value="1" />

		<?php echo Html::input('token'); ?>
	</form>
</div><!-- /#ajax-upload-container -->