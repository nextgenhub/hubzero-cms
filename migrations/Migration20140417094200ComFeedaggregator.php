<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20140417094200ComFeedaggregator extends Base
{
	public function up()
	{
		$this->deleteComponentEntry('feedaggregator');
		$this->addComponentEntry('feedaggregator','',1,'',true);	
	}

	public function down()
	{
		$this->deleteComponentEntry('feedaggregator');
	}
}