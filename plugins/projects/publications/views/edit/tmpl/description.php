<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Determine pane title
$ptitle = '';
if($this->version == 'dev') {
	$ptitle .= $this->last_idx > $this->current_idx  ? ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_EDIT_DESCRIPTION')) : ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_DESCRIBE_YOUR_PUBLICATION')) ;
}
else {
	$ptitle .= ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PANEL_DESCRIPTION'));	
}

$pubtitle = $this->row->title;
$this->row->title = $this->row->title == JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_DEFAULT_TITLE') ? '' : $this->row->title;

$fields = array();
if (trim($this->customFields) != '') {
	$fs = explode("\n", trim($this->customFields));
	foreach ($fs as $f) 
	{
		$fields[] = explode('=', $f);
	}
} 

// Filter meta data (old resources)
if (!empty($fields)) {
	for ($i=0, $n=count( $fields ); $i < $n; $i++) 
	{
		preg_match("#<nb:".$fields[$i][0].">(.*?)</nb:".$fields[$i][0].">#s", $this->row->abstract, $matches);
		if (count($matches) > 0) {
			$match = $matches[0];
			$match = str_replace('<nb:'.$fields[$i][0].'>','',$match);
			$match = str_replace('</nb:'.$fields[$i][0].'>','',$match);
		} else {
			$match = '';
		}
		
		// Explore the text and pull out all matches
		array_push($fields[$i], $match);
		
		// Clean the original text of any matches
		$this->row->abstract = str_replace('<nb:'.$fields[$i][0].'>'.end($fields[$i]).'</nb:'.$fields[$i][0].'>','',$this->row->abstract);
	}
	$this->row->abstract = trim($this->row->abstract);
}

// Are we allowed to edit?
$canedit = ($this->pub->state == 1 || $this->pub->state == 0 || $this->pub->state == 6) ? 0 : 1;

$noedit  = ($canedit || in_array($this->active, $this->mayupdate)) ? 0 : 1;

?>
<form action="<?php echo $this->url; ?>" method="post" id="plg-form">	
	<?php echo $this->project->provisioned == 1 
				? PublicationHelper::showPubTitleProvisioned( $this->pub, $this->route)
				: PublicationHelper::showPubTitle( $this->pub, $this->route, $this->title); ?>
		<fieldset>	
			<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" id="projectid" />
			<input type="hidden" name="version" value="<?php echo $this->version; ?>" />
			<input type="hidden" name="active" value="publications" />					
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="base" id="base" value="<?php echo $this->pub->base; ?>" />
			<input type="hidden" name="section" id="section" value="<?php echo $this->active; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="move" id="move" value="<?php echo $this->move; ?>" />
			<input type="hidden" name="review" value="<?php echo $this->inreview; ?>" />
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="vid" id="vid" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="add_metadata" value="<?php echo ($this->pubconfig->get('show_metadata', 0)) ? 1 : 0; ?>" />
			<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
			<?php if($this->project->provisioned == 1 ) { ?>
			<input type="hidden" name="task" value="submit" />
			<?php } ?>
		</fieldset>

<?php
// Include status bar - publication steps/sections/version navigation
$view = new Hubzero_Plugin_View(
	array(
		'folder'=>'projects',
		'element'=>'publications',
		'name'=>'edit',
		'layout'=>'statusbar'
	)
);
$view->row = $this->row;
$view->version = $this->version;
$view->panels = $this->panels;
$view->active = $this->active;
$view->move = $this->move;
$view->step = 'abstract';
$view->lastpane = $this->lastpane;
$view->option = $this->option;
$view->project = $this->project;
$view->current_idx = $this->current_idx;
$view->last_idx = $this->last_idx;
$view->checked = $this->checked;
$view->url = $this->url;
$view->show_substeps = ($this->pubconfig->get('show_metadata', 0)) ? 1 : 0;
$view->display();

if ($this->move) {
	$panel_number = 1;
	while ($panel = current($this->panels)) {
	    if ($panel == $this->active) {
	        $panel_number = key($this->panels) + 1;
	    }
	    next($this->panels);
	}
}
// Section body starts:
?>
	<div id="pub-editor" class="pane-desc">
	  <div id="c-pane" class="columns">
		 <div class="c-inner">
			<?php if (!$noedit) { ?>
			<span class="c-submit"><input type="submit" value="<?php if($this->move) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_AND_CONTINUE'); } else { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_CHANGES'); } ?>" <?php if(count($this->checked['description']) == 0) { echo 'class="disabled"'; } ?> class="c-continue" id="c-continue" /></span>
			<?php } ?>
			<h4><?php echo $ptitle; ?></h4>
			<?php if ($noedit) { ?>
				<p class="notice"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ADVANCED_CANT_CHANGE').' <a href="'.$this->url.'/?action=newversion">'.ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION')).'</a>'; ?></p>
			<?php } 
			elseif($canedit) { ?>	
			<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_DESCRIPTION_WRITE'); ?></p>
			<?php } else { ?>
				<p class="notice"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TITLE_CANT_CHANGE').' <a href="'.$this->url.'/?action=newversion">'.ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION')).'</a>'; ?></p>
			<?php } ?>
				<table class="tbl-panel">
					<tbody>
					<tr>
						<td colspan="2">
							<label>
								<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_TITLE'); ?>:
								<?php if($canedit) { ?>
								<span class="required"><?php echo JText::_('REQUIRED'); ?></span>
								<span class="pub-info-pop tooltips" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TIPS_TITLE').' :: '.JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TIPS_TITLE_ABOUT'); ?>">&nbsp;</span>
								<input name="title" id="pub_title" maxlength="200" size="35" type="text" value="<?php echo $this->row->title; ?>" class="long pubinput" />
								<?php } else { ?>
									<p class="pubt"><?php echo stripslashes($this->row->title); ?></p>
								<?php } ?>
							</label>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label>
								<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_COMPOSE_MINI_ABSTRACT'); ?>: 
								<?php if($canedit) { ?>
									<span class="required"><?php echo JText::_('REQUIRED'); ?></span>
									<span class="pub-info-pop tooltips" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TIPS_ABSTRACT').' :: '.JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TIPS_ABSTRACT_ABOUT'); ?>">&nbsp;</span>
									<textarea name="abstract" id="pub_abstract" cols="40" rows="3" class="pubinput"><?php echo $this->row->abstract; ?></textarea>
									<span id="counter_abstract" class="leftfloat mini"></span>
								<?php } else { ?>
									<p class="pubt"><?php echo stripslashes($this->row->abstract); ?></p>
								<?php } ?>
							</label>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label>
								<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_COMPOSE_FULL_ABSTRACT'); ?>: 
									<span class="required"><?php echo JText::_('REQUIRED'); ?></span>
							</label>								
							<span class="clear"></span>
							<?php if ($noedit) { ?>
							<?php 
								echo $this->parser->parse( stripslashes($this->row->description), $this->wikiconfig );
							?>
							<?php } else { 
								ximport('Hubzero_Wiki_Editor');
								$editor =& Hubzero_Wiki_Editor::getInstance();
								echo $editor->display('description', 'description', $this->row->description, '', '35', '20'); 
							} ?>
						</td>
					</tr>
				  </tbody>
				</table>
		 </div>
	   </div>
	</div>
</form>