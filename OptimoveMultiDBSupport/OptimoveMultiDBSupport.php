<?php

/**
* Piwik - free/libre analytics platform
*
* @link http://piwik.org
* @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
* @version $Id$
*
*/

namespace Piwik\Plugins\OptimoveMultiDBSupport;

use Piwik\Db;
use Piwik\Piwik;
use Piwik\Plugins\SitesManager\API as SitesManagerApi;
use Piwik\Common;
use Piwik\Tracker\Request;
use Piwik\Tracker\RequestSet;

/**
*
*/
class OptimoveMultiDBSupport extends \Piwik\Plugin
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
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $rawData = $this->getRawBulkRequest();

            if($this->isUsingBulkRequest($rawData)){
                $reqAndToken = $this->getRequestsArrayFromBulkRequest($rawData);
                list($request, $token) = $reqAndToken;
            }
            
            $url = @parse_url($request[0]);
            if (!empty($url['query'])) {
             //   die(var_dump($url['query']));
                @parse_str($url['query'], $params);
                $dbConfig['dbname'] .= '_'.$params['idsite'];
				$dbConfig['host'] = 'optitrackSQLSrvr_location_'.$params['idsite'].'.optimove.net';

            }

        }

        if(isset($_GET['idsite'])){
            echo 'inside get';
            $dbConfig['dbname'] .= '_'.$_GET['idsite'];
			$dbConfig['host'] = 'optitrackSQLSrvr_location_'.$_GET['idsite'].'.optimove.net';
        }
    }

    /**
     * @return string
     */
    public function getRawBulkRequest()
    {
        // die('get bulk');
        return file_get_contents("php://input");
    }

    public function isUsingBulkRequest($rawData)
    {
        if (!empty($rawData)) {
            return strpos($rawData, '"requests"') || strpos($rawData, "'requests'");
        }

        return false;
    }

    public function getRequestsArrayFromBulkRequest($rawData)
    {
        $rawData = trim($rawData);
        $rawData = Common::sanitizeLineBreaks($rawData);

        // POST data can be array of string URLs or array of arrays w/ visit info
        $jsonData = json_decode($rawData, $assoc = true);

        $tokenAuth = Common::getRequestVar('token_auth', false, 'string', $jsonData);

        $requests = array();
        if (isset($jsonData['requests'])) {
            $requests = $jsonData['requests'];
        }

        return array($requests, $tokenAuth);
    }

}

