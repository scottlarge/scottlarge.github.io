<!DOCTYPE html>
<html>
	<head>
		<title>GoldLink with PHP - Find an Account</title>
	</head>
	<body>
		<h3>GoldLink with PHP - Find an Account</h3>
		<?php
			// Include the required classes
			include 'GVGoldLinkNTLM.php';

			//Get the passed email
			if (isset($_GET["email"]))
				$email = $_GET["email"];
			else
				$email = '';
				
			//Get the passed firstName
			if (isset($_GET["firstName"]))
				$firstName = $_GET["firstName"];
			else
				$firstName = '';
				
			//Get the passed lastName
			if (isset($_GET["lastName"]))
				$lastName = $_GET["lastName"];
			else
				$lastName = '';

			//Get the passed company
			if (isset($_GET["company"]))
				$company = $_GET["company"];
			else
				$company = '';

			//Output the passed search term
			echo 'Creating contact:  Email=' . $email . ' First Name='. $firstName . ' Last Name='. $lastName  . ' Company=' . $company . '</br/>';
			
			// The URL of the WSDL file for Gold-Link
			$url = 'http://' . $GVAddress . '/gold-link/goldlink.asmx?wsdl';

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
			$FindItem->XmlFilters = new SoapVar('<ns1:XmlFilters><filters xmlns=""><filter dbcolumn="ACC_PREF_EMAIL" type="text" value="'.$email.'" /></filters></ns1:XmlFilters>',XSD_ANYXML);

			// Get the found accounts (if any)
			$result = $GVGLclient->FindItem($FindItem);
			if ($result->{'success'})
			{
				$GVAccountXML = new SimpleXMLElement($result->{'FindItemResult'}->{'any'});
				$ReturnedAccounts = $GVAccountXML->children();

				// Set the name of the atribute to display
				$att = 'id';
			
				$numberFound = $ReturnedAccounts->children()->count();

				try {
					$acId = $GVGLclient->getAccountId($company);

					if ($acId == 'null') {
						echo 'Account DOES NOT exist<br/>';
						$acId = $GVGLclient->createAccount($company);

						echo 'Account created<br/>';
						if ($acId == 'null') {
							throw new Exception('Account could not be created');
						}
					} else {
						echo 'Account already exists<br/>';
					}

					if ($GVGLclient->doesContactExist($email)) {
						echo 'Contact already exists<br/>';
					} else {
						echo 'Contact DOES NOT exist<br/>';
						//'PPH_Scott_Test1', 'PPH_Scott_Test1@pphtest.com', 'PPH Test'
						$rid = $GVGLclient->createPotential($firstName, $lastName, $email, $acId, $company);
						echo 'Create contact: ' . $rid . '<br/>';
					}
				} catch (Exception $e) {
				    echo 'Caught exception: ',  $e->getMessage(), '<br/>';
				}

				// if ($numberFound == 1) 
				// {
				// 	//Loop and output
				// 	foreach ($ReturnedAccounts->children() as $child)
				// 	{
				// 		echo '<p>Account retrieved by email: ' . $child->attributes()->$att . "</p>";
				// 	}
				// } else if ($numberFound > 2) {
				// 	echo '<p>More than result found (' . $numberFound . ')</p>';
				// } else {
				// 	//No records found
				// 	echo '<p>No record found, adding new record</p>';
				// }
			}
			else
			{
				echo 'Failed: '.$result->{'message'} . '<br/>';
				echo '<br />' . $GVGLclient->__getLastRequest() . '<br/>';
			}

			// Restore the original HTTP stream wrapper
			stream_wrapper_restore('http');
		?>
	</body>
</html>