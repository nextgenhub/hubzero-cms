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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'Hubzero_Message_Component'
 * 
 * Long description (if any) ...
 */
class Hubzero_Message_Component extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id        = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'component'
	 * 
	 * @var unknown
	 */
	var $component = NULL;  // @var varchar(50)

	/**
	 * Description for 'action'
	 * 
	 * @var unknown
	 */
	var $action    = NULL;  // @var varchar(100)

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title     = NULL;  // @var varchar(255)

	//-----------

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__xmessage_component', 'id', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (trim( $this->component ) == '') {
			$this->setError( JText::_('Please provide a component.') );
			return false;
		}
		if (trim( $this->action ) == '') {
			$this->setError( JText::_('Please provide an action.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
	public function getCount($filters = array())
	{
		$query  = "SELECT COUNT(*)";
		$query .= $this->_buildQuery($filters);
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
	public function getRecords($filters = array())
	{
		$query  = "SELECT x.*, c.name";
		$query .= $this->_buildQuery($filters);
		
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Builds a query string based on filters passed
	 * 
	 * @return     string SQL
	 */
	protected function _buildQuery($filters = array())
	{
		$query  = " FROM $this->_tbl AS x";
		
		$where = array();
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$query .= ", #__extensions AS c";
			
			$where[] = "x.component = c.element";
			$where[] = "c.type = 'component'";
			if (isset($filters['component']) && $filters['component'])
			{
				$where[] = "c.element = '" . $filters['component'] . "'";
			}
		}
		else 
		{
			$query .= ", #__components AS c";
			
			$where[] = "x.component = c.option";
			$where[] = "c.parent = 0";
			if (isset($filters['component']) && $filters['component'])
			{
				$where[] = "c.option = '" . $filters['component'] . "'";
			}
		}
		
		$query .= " WHERE " . implode(" AND ", $where);
		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'c.name';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'DESC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'] . ", x.action DESC";
		
		return $query;
	}

	/**
	 * Short description for 'getComponents'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
	public function getComponents()
	{
		$query  = "SELECT DISTINCT x.component 
					FROM $this->_tbl AS x
					ORDER BY x.component ASC";

		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}
}

