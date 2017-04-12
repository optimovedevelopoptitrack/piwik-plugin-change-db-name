<?php

/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id$
 *
 */

namespace Piwik\Plugins\DatabaseConcatNameWithIdsite;

use Piwik\Db;
use Piwik\Piwik;
use Piwik\Plugins\SitesManager\API as SitesManagerApi;

/**
 *
 */
class DatabaseConcatNameWithIdsite extends \Piwik\Plugin
{

	public function getListHooksRegistered()
	{
		return array(
		    'Tracker.getDatabaseConfig' => 'concatDatabaseNamwWithIdsite'
		);
	}

    /**
     * @param $dbConfig array
     */
    public function concatDatabaseNamwWithIdsite(&$dbConfig){
        $dbConfig['dbname'] .= '_'.$_GET['idsite'];
		$dbConfig['host'] = 'optitrackSQLSrvr_location_'.$_GET['idsite'].'.optimove.net';
		
    }






    /**
     * @return array
     */
    private function getAllAlerts()
    {
        return $this->getModel()->getAllAlerts();
    }
}
