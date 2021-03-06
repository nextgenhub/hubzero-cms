<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

namespace Components\Publications\Models\BlockElement;

use Components\Publications\Models\BlockElement as Base;

/**
 * Renders URL selector element
 */
class Dataselector extends Base
{
	/**
	* Element name
	*
	* @var		string
	*/
	protected	$_name = 'dataselector';

	/**
	* Git helper
	*
	* @var		string
	*/
	protected	$_git = NULL;

	/**
	* Project repo path
	*
	* @var		string
	*/
	protected	$path = NULL;

	/**
	 * Render
	 *
	 * @return  object
	 */
	public function render( $elementid, $manifest, $pub = NULL, $viewname = 'edit',
		$status = NULL, $master = NULL, $order = 0 )
	{
		$html = '';

		// Get project path
		$this->path = $pub->_project->repo()->get('path');

		$showElement 	= $master->props['showElement'];
		$total 			= $master->props['total'];

		// Incoming
		$activeElement  = Request::getInt( 'el', $showElement );

		// Git helper
		if (!$this->_git)
		{
			include_once( PATH_CORE . DS . 'components' . DS
				. 'com_projects' . DS . 'helpers' . DS . 'githelper.php' );
			$this->_git = new \Components\Projects\Helpers\Git($this->path);
		}

		// Do we need to collapse inactive elements?
		$collapse = isset($master->params->collapse_elements) && $master->params->collapse_elements ? 1 : 0;

		switch ($viewname)
		{
			case 'edit':
			default:
				$html = $this->drawSelector( $elementid, $manifest, $pub,
						$status->elements->$elementid, $activeElement,
						$collapse, $total, $master, $order
				);

			break;

			case 'freeze':
			case 'curator':
				$html = $this->drawItem( $elementid, $manifest, $pub,
						$status->elements->$elementid, $master, $viewname
				);
			break;
		}

		return $html;
	}

	/**
	 * Draw element without editing capabilities
	 *
	 * @return  object
	 */
	public function drawItem( $elementId, $manifest, $pub = NULL,
		$status = NULL, $master = NULL, $viewname = 'freeze')
	{
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'publications',
				'name'		=>'freeze',
				'layout'	=>'dataselector'
			)
		);

		// Get attachment type model
		$attModel = new \Components\Publications\Models\Attachments($this->_parent->_db);

		// Make sure we have attachments
		if (!isset($pub->_attachments))
		{
			// Get attachments
			$pContent = new \Components\Publications\Tables\Attachment( $this->_parent->_db );
			$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );
		}

		// Get attached items
		$attachments = $pub->_attachments;
		$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : NULL;
		$attachments = $attModel->getElementAttachments($elementId, $attachments,
					   $manifest->params->type, $manifest->params->role);

		$view->type 		 = $manifest->params->type;
		$view->path			 = $this->path;
		$view->pub 			 = $pub;
		$view->manifest		 = $manifest;
		$view->status		 = $status;
		$view->elementId	 = $elementId;
		$view->attachments	 = $attachments;
		$view->database		 = $this->_parent->_db;
		$view->master		 = $master;
		$view->name			 = $viewname;
		$view->viewer		 = 'freeze';
		$view->git			 = $this->_git;

		return $view->loadTemplate();
	}

	/**
	 * Draw file selector
	 *
	 * @return  object
	 */
	public function drawSelector( $elementId, $manifest, $pub = NULL, $status = NULL,
		$active = 0, $collapse = 0, $total = 0,
		$master = NULL, $order = 0)
	{
		// Get attachment type model
		$attModel = new \Components\Publications\Models\Attachments($this->_parent->_db);

		// Make sure we have attachments
		if (!isset($pub->_attachments))
		{
			// Get attachments
			$pContent = new \Components\Publications\Tables\Attachment( $this->_parent->_db );
			$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );
		}

		// Get attached items
		$attachments = $pub->_attachments;
		$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : NULL;
		$attachments = $attModel->getElementAttachments($elementId, $attachments,
					   $manifest->params->type, $manifest->params->role);

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=> 'projects',
				'element'	=> 'publications',
				'name'		=> 'blockelement',
				'layout'	=> 'dataselector'
			)
		);

		$view->type 		 = $manifest->params->type;
		$view->path			 = $this->path;
		$view->pub 			 = $pub;
		$view->manifest		 = $manifest;
		$view->status		 = $status;
		$view->elementId	 = $elementId;
		$view->attachments	 = $attachments;
		$view->active		 = $active;
		$view->collapse		 = $collapse;
		$view->total		 = $total;
		$view->master 		 = $master;
		$view->database		 = $this->_parent->_db;
		$view->order		 = $order;
		$view->viewer		 = 'edit';
		$view->git			 = $this->_git;

		return $view->loadTemplate();
	}
}