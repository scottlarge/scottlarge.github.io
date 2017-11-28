<?php

include 'NTLMStream.php';
include 'NTLMSoapClient.php';

// CHANGE AUTHENTICATION DETAILS BELOW
class GVGoldLinkNTLMStream extends NTLMStream {
    protected $user = 'mario.macan';
    protected $password = '1234Ruby';
}

// CHANGE AUTHENTICATION DETAILS BELOW
class GVGoldLinkNTLMSoapClient extends NTLMSoapClient {
    protected $user = 'mario.macan';
    protected $password = '1234Ruby';
	protected $GVAddress = 'remote.carltonpackaging.com/gold-vision';

    function doesContactExist($searchterm) {
    	$exists = false;

		// The URL of the WSDL file for Gold-Link
		$url = 'http://' . 'remote.carltonpackaging.com/gold-vision' . '/gold-link/goldlink.asmx?wsdl';

		// Unregister the current HTTP wrapper
		stream_wrapper_unregister('http');

		// Register the new HTTP wrapper
		stream_wrapper_register('http', 'GVGoldLinkNTLMStream') or die("Failed to register protocol");

		// Now, all requests to a http page will be done by GVGoldLinkNTLMStream.
		// Instantiate the client
		$GVGLclient = new GVGoldLinkNTLMSoapClient($url,array('trace' => TRUE));

		$FindItem = new \stdClass();
		// Find a Gold-Vision Account(s)
		// Set the objectType to Account
		$FindItem->objectType = 'Contact';

		// Set FindItem XmlFilters
		$FindItem->XmlFilters = new SoapVar('<ns1:XmlFilters><filters xmlns=""><filter dbcolumn="ACC_PREF_EMAIL" type="text" value="'.$searchterm.'" /></filters></ns1:XmlFilters>',XSD_ANYXML);

		// Get the found accounts (if any)
		$result = $GVGLclient->FindItem($FindItem);
		if ($result->{'success'})
		{
			$GVAccountXML = new SimpleXMLElement($result->{'FindItemResult'}->{'any'});
			$ReturnedAccounts = $GVAccountXML->children();

			// Set the name of the atribute to display
			$att = 'id';
		
			$numberFound = $ReturnedAccounts->children()->count();

			if ($numberFound > 0) 
			{
				$exists = true;
			}
		}
		else
		{
			throw new Exception('Failed: '.$result->{'message'});
		}

		// Restore the original HTTP stream wrapper
		stream_wrapper_restore('http');

		// echo 'DEBUG: ' . $exists . '</br>' ;
		return $exists;
    }

    function getAccountId($searchterm) {
    	$exists = false;
    	$id = 'null';

		// The URL of the WSDL file for Gold-Link
		$url = 'http://' . 'remote.carltonpackaging.com/gold-vision' . '/gold-link/goldlink.asmx?wsdl';

		// Unregister the current HTTP wrapper
		stream_wrapper_unregister('http');

		// Register the new HTTP wrapper
		stream_wrapper_register('http', 'GVGoldLinkNTLMStream') or die("Failed to register protocol");

		// Now, all requests to a http page will be done by GVGoldLinkNTLMStream.
		// Instantiate the client
		$GVGLclient = new GVGoldLinkNTLMSoapClient($url,array('trace' => TRUE));

		$FindItem = new \stdClass();
		// Find a Gold-Vision Account(s)
		// Set the objectType to Account
		$FindItem->objectType = 'Account';

		// Set FindItem XmlFilters
		$FindItem->XmlFilters = new SoapVar('<ns1:XmlFilters><filters xmlns=""><filter dbcolumn="NAME" type="text" value="'.$searchterm.'" /></filters></ns1:XmlFilters>',XSD_ANYXML);

		// Get the found accounts (if any)
		$result = $GVGLclient->FindItem($FindItem);
		if ($result->{'success'})
		{
			$GVAccountXML = new SimpleXMLElement($result->{'FindItemResult'}->{'any'});
			$ReturnedAccounts = $GVAccountXML->children();

			// Set the name of the atribute to display
			$att = 'id';
		
			$numberFound = $ReturnedAccounts->children()->count();

			if ($numberFound > 0) 
			{
				$exists = true;
				$att = 'id';
			
				foreach ($ReturnedAccounts->children() as $child)
				{
					//echo '<p>Account created: ' . $child->attributes()->$att . "</p>";
					$id = $child->attributes()->$att;
					echo "Account ID: " . $id . '<br/>';
					break;
				}
			} 
		}
		else
		{
			throw new Exception('Failed: '.$result->{'message'});
		}

		// Restore the original HTTP stream wrapper
		stream_wrapper_restore('http');

		//echo 'DEBUG: ' . $id . '</br>' ;
		return $id;
    }

    function createContact($firstName, $lastName, $email, $acId, $company) {
    	$id = 'null';

		// The URL of the WSDL file for Gold-Link
		$url = 'http://' . 'remote.carltonpackaging.com/gold-vision' . '/gold-link/goldlink.asmx?wsdl';

		// Unregister the current HTTP wrapper
		stream_wrapper_unregister('http');

		// Register the new HTTP wrapper
		stream_wrapper_register('http', 'GVGoldLinkNTLMStream') or die("Failed to register protocol");

		// Now, all requests to a http page will be done by GVGoldLinkNTLMStream.
		// Instantiate the client
		$GVGLclient = new GVGoldLinkNTLMSoapClient($url,array('trace' => TRUE));

		$AddItem = new \stdClass();
		// Find a Gold-Vision Account(s)
		// Set the objectType to Account
		$AddItem->objectType = 'Contact';

		echo '<ns1:xmlData><gvdata xmlns=""><record><field name="AC_ID">' . $acId . '</field><field name="ACC_PREF_EMAIL">' . $email . '</field><field name="FIRSTNAME">' . $firstName . '</field><field name="LASTNAME">' . $lastName . '</field><field name="AC_NAME">' . $company . '</field><field name="SUMMARY">' . $company . '</field></record></gvdata></ns1:xmlData>';

		// Set FindItem XmlFilters
		$AddItem->xmlData = new SoapVar('<ns1:xmlData><gvdata xmlns=""><record><field name="AC_ID">' . $acId . '</field><field name="ACC_PREF_EMAIL">' . $email . '</field><field name="FIRSTNAME">' . $firstName . '</field><field name="LASTNAME">' . $lastName . '</field><field name="AC_NAME">' . $company . '</field><field name="SUMMARY">' . $company . '</field></record></gvdata></ns1:xmlData>',XSD_ANYXML);

		// Get the found accounts (if any)
		$result = $GVGLclient->AddItem($AddItem);
		if ($result->{'AddItemResult'})
		{
			// $GVAccountXML = new SimpleXMLElement($result->{'AddItemResult'}->{'any'});
			// $ReturnedAccounts = $GVAccountXML->children();

			// // Set the name of the atribute to display
			// $att = 'returnId';
		
			// foreach ($ReturnedAccounts->children() as $child)
			// {
			// 	//echo '<p>Account created: ' . $child->attributes()->$att . "</p>";
			// 	$id = $child->attributes()->$att;
			// }
			$att = 'returnId';
			$id = $result->{$att};
		}
		else
		{
			throw new Exception('Failed: '.$result->{'message'});
		}

		// Restore the original HTTP stream wrapper
		stream_wrapper_restore('http');

		// echo 'DEBUG: ' . $exists . '</br>' ;
		return $id;
    }

    function createPotential($firstName, $lastName, $email, $acId, $company) {
    	$id = 'null';

		// The URL of the WSDL file for Gold-Link
		$url = 'http://' . 'remote.carltonpackaging.com/gold-vision' . '/gold-link/goldlink.asmx?wsdl';

		// Unregister the current HTTP wrapper
		stream_wrapper_unregister('http');

		// Register the new HTTP wrapper
		stream_wrapper_register('http', 'GVGoldLinkNTLMStream') or die("Failed to register protocol");

		// Now, all requests to a http page will be done by GVGoldLinkNTLMStream.
		// Instantiate the client
		$GVGLclient = new GVGoldLinkNTLMSoapClient($url,array('trace' => TRUE));

		$AddItem = new \stdClass();
		// Find a Gold-Vision Account(s)
		// Set the objectType to Account
		$AddItem->objectType = 'LEADPotentials_2140313160839';
		$AddItem->overwrite = 'AllFieldsPresent';

		echo '<ns1:xmlData><gvdata xmlns=""><record><field name="CONTACT_EMAIL">' . $email . '</field><field name="CONTACT_FIRSTNAME">' . $firstName . '</field><field name="CONTACT_LASTNAME">' . $lastName . '</field><field name="SOURCE">SIGNUP_WEB</field><field name="COMPANY_NAME">' . $company . '</field><field name="SUMMARY">' . $company . '</field></record></gvdata></ns1:xmlData>';

		// Set FindItem XmlFilters
		$AddItem->xmlData = new SoapVar('<ns1:xmlData><gvdata xmlns=""><record><field name="CONTACT_EMAIL">' . $email . '</field><field name="CONTACT_FIRSTNAME">' . $firstName . '</field><field name="CONTACT_LASTNAME">' . $lastName . '</field><field name="SOURCE">SIGNUP_WEB</field><field name="COMPANY_NAME">' . $company . '</field><field name="SUMMARY">' . $company . '</field></record></gvdata></ns1:xmlData>',XSD_ANYXML);

		// Get the found accounts (if any)
		$result = $GVGLclient->AddLeadItem($AddItem);
		if ($result->{'AddLeadItemResult'})
		{
			// Set the name of the atribute to display
			$att = 'returnId';
			$id = $result->$att;
		}
		else
		{
			throw new Exception('Failed: '.$result->{'message'});
		}

		// Restore the original HTTP stream wrapper
		stream_wrapper_restore('http');

		// echo 'DEBUG: ' . $exists . '</br>' ;
		return $id;
    }

    function createAccount($name) {
    	$id = 'null';

		// The URL of the WSDL file for Gold-Link
		$url = 'http://' . 'remote.carltonpackaging.com/gold-vision' . '/gold-link/goldlink.asmx?wsdl';

		// Unregister the current HTTP wrapper
		stream_wrapper_unregister('http');

		// Register the new HTTP wrapper
		stream_wrapper_register('http', 'GVGoldLinkNTLMStream') or die("Failed to register protocol");

		// Now, all requests to a http page will be done by GVGoldLinkNTLMStream.
		// Instantiate the client
		$GVGLclient = new GVGoldLinkNTLMSoapClient($url,array('trace' => TRUE));

		$AddItem = new \stdClass();
		// Find a Gold-Vision Account(s)
		// Set the objectType to Account
		$AddItem->objectType = 'Account';

		// Set FindItem XmlFilters
		$AddItem->xmlData = new SoapVar('<ns1:xmlData><gvdata xmlns=""><record><field name="NAME">' . $name . '</field><field name="AC_NAME">' . $name . '</field><field name="SUMMARY">' . $name . '</field></record></gvdata></ns1:xmlData>',XSD_ANYXML);

		// Get the found accounts (if any)
		$result = $GVGLclient->AddItem($AddItem);
		if ($result->{'AddItemResult'})
		{
			// $GVAccountXML = new SimpleXMLElement($result->{'AddItemResult'}->{'any'});
			// $ReturnedAccounts = $GVAccountXML->children();

			// Set the name of the atribute to display
			$att = 'returnId';
			$id = $result->{$att};
		
			// foreach ($ReturnedAccounts->children() as $child)
			// {
			// 	//echo '<p>Account created: ' . $child->attributes()->$att . "</p>";
			// 	$id = $child->$att;
			// }
		}
		else
		{
			throw new Exception('Failed: '.$result->{'message'});
		}

		// Restore the original HTTP stream wrapper
		stream_wrapper_restore('http');

		// echo 'DEBUG: ' . $exists . '</br>' ;
		return $id;
    }
}

// CHANGE GOLDVISION ADDRESS - [SERVERADDRESS](/[GOLD-VISION])
//$GVAddress = "[SERVERADDRESS](/[GOLD-VISION])";
$GVAddress = 'remote.carltonpackaging.com/gold-vision';

?>
