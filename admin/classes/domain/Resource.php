<?php

/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.0
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/

class Resource extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}



	//returns resource objects by title
	public function getResourceByTitle($title){

		$query = "SELECT *
			FROM Resource
			WHERE UPPER(titleText) = '" . str_replace("'", "''", strtoupper($title)) . "'
			ORDER BY 1";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceID'])){
			$object = new Resource(new NamedArguments(array('primaryKey' => $result['resourceID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Resource(new NamedArguments(array('primaryKey' => $row['resourceID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}



	//returns array of related resource objects
	public function getParentResource(){

		$query = "SELECT *
			FROM ResourceRelationship
			WHERE resourceID = '" . $this->resourceID . "'
			AND relationshipTypeID = '1'
			ORDER BY 1";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceID'])){
			$object = new ResourceRelationship(new NamedArguments(array('primaryKey' => $result['resourceRelationshipID'])));
		}

		return $object;
	}


	//returns array of related resource objects
	public function getChildResources(){

		$query = "SELECT *
			FROM ResourceRelationship
			WHERE relatedResourceID = '" . $this->resourceID . "'
			AND relationshipTypeID = '1'
			ORDER BY 1";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['relatedResourceID'])){
			$object = new ResourceRelationship(new NamedArguments(array('primaryKey' => $result['resourceRelationshipID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourceRelationship(new NamedArguments(array('primaryKey' => $row['resourceRelationshipID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}



	//returns array of purchase site objects
	public function getResourcePurchaseSites(){

		$query = "SELECT PurchaseSite.* FROM PurchaseSite, ResourcePurchaseSiteLink RPSL where RPSL.purchaseSiteID = PurchaseSite.purchaseSiteID AND resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['purchaseSiteID'])){
			$object = new PurchaseSite(new NamedArguments(array('primaryKey' => $result['purchaseSiteID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new PurchaseSite(new NamedArguments(array('primaryKey' => $row['purchaseSiteID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//returns array of ResourcePayment objects
	public function getResourcePayments(){

		$query = "SELECT * FROM ResourcePayment WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourcePaymentID'])){
			$object = new ResourcePayment(new NamedArguments(array('primaryKey' => $result['resourcePaymentID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourcePayment(new NamedArguments(array('primaryKey' => $row['resourcePaymentID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}


	//returns array of associated licenses
	public function getLicenseArray(){
		$config = new Configuration;

		//if the lic module is installed get the lic name from lic database
		if ($config->settings->licensingModule == 'Y'){
			$dbName = $config->settings->licensingDatabaseName;

			$resourceLicenseArray = array();

			$query = "SELECT * FROM ResourceLicenseLink WHERE resourceID = '" . $this->resourceID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['licenseID'])){
				$licArray = array();

				//first, get the license name
				$query = "SELECT shortName FROM " . $dbName . ".License WHERE licenseID = " . $result['licenseID'];

				if ($licResult = mysql_query($query)){
					while ($licRow = mysql_fetch_assoc($licResult)){
						$licArray['license'] = $licRow['shortName'];
						$licArray['licenseID'] = $result['licenseID'];
					}
				}

				array_push($resourceLicenseArray, $licArray);
			}else{
				foreach ($result as $row) {
					$licArray = array();

					//first, get the license name
					$query = "SELECT shortName FROM " . $dbName . ".License WHERE licenseID = " . $row['licenseID'];

					if ($licResult = mysql_query($query)){
						while ($licRow = mysql_fetch_assoc($licResult)){
							$licArray['license'] = $licRow['shortName'];
							$licArray['licenseID'] = $row['licenseID'];
						}
					}

					array_push($resourceLicenseArray, $licArray);

				}

			}

			return $resourceLicenseArray;
		}
	}




	//returns array of resource license status objects
	public function getResourceLicenseStatuses(){

		$query = "SELECT * FROM ResourceLicenseStatus WHERE resourceID = '" . $this->resourceID . "' ORDER BY licenseStatusChangeDate desc;";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceLicenseStatusID'])){
			$object = new ResourceLicenseStatus(new NamedArguments(array('primaryKey' => $result['resourceLicenseStatusID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourceLicenseStatus(new NamedArguments(array('primaryKey' => $row['resourceLicenseStatusID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//returns LicenseStatusID of the most recent resource license status
	public function getCurrentResourceLicenseStatus(){

		$query = "SELECT licenseStatusID FROM ResourceLicenseStatus RLS WHERE resourceID = '" . $this->resourceID . "' AND licenseStatusChangeDate = (SELECT MAX(licenseStatusChangeDate) FROM ResourceLicenseStatus WHERE ResourceLicenseStatus.resourceID = '" . $this->resourceID . "') LIMIT 0,1;";

		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['licenseStatusID'])){
			return $result['licenseStatusID'];
		}

	}


	//returns array of authorized site objects
	public function getResourceAuthorizedSites(){

		$query = "SELECT AuthorizedSite.* FROM AuthorizedSite, ResourceAuthorizedSiteLink RPSL where RPSL.authorizedSiteID = AuthorizedSite.authorizedSiteID AND resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['authorizedSiteID'])){
			$object = new AuthorizedSite(new NamedArguments(array('primaryKey' => $result['authorizedSiteID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new AuthorizedSite(new NamedArguments(array('primaryKey' => $row['authorizedSiteID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//returns array of administering site objects
	public function getResourceAdministeringSites(){

		$query = "SELECT AdministeringSite.* FROM AdministeringSite, ResourceAdministeringSiteLink RPSL where RPSL.administeringSiteID = AdministeringSite.administeringSiteID AND resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['administeringSiteID'])){
			$object = new AdministeringSite(new NamedArguments(array('primaryKey' => $result['administeringSiteID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new AdministeringSite(new NamedArguments(array('primaryKey' => $row['administeringSiteID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//deletes all parent resources associated with this resource
	public function removeParentResources(){

		$query = "DELETE FROM ResourceRelationship WHERE resourceID = '" . $this->resourceID . "'";

		return $this->db->processQuery($query);
	}




	//returns array of alias objects
	public function getAliases(){

		$query = "SELECT * FROM Alias WHERE resourceID = '" . $this->resourceID . "' order by shortName";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['aliasID'])){
			$object = new Alias(new NamedArguments(array('primaryKey' => $result['aliasID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Alias(new NamedArguments(array('primaryKey' => $row['aliasID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}







	//returns array of contact objects
	public function getUnarchivedContacts(){


		$config = new Configuration;
		$contactsArray = array();

		//get resource specific contacts first
		$query = "SELECT C.*, GROUP_CONCAT(CR.shortName SEPARATOR '<br /> ') contactRoles
			FROM Contact C, ContactRole CR, ContactRoleProfile CRP
			WHERE (archiveDate = '0000-00-00' OR archiveDate is null)
			AND C.contactID = CRP.contactID
			AND CRP.contactRoleID = CR.contactRoleID
			AND resourceID = '" . $this->resourceID . "'
			GROUP BY C.contactID
			ORDER BY C.name";

		$result = $this->db->processQuery($query, 'assoc');


		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['contactID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($contactsArray, $resultArray);

		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}

				array_push($contactsArray, $resultArray);
			}
		}



		//if the org module is installed also get the org contacts from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$query = "SELECT distinct OC.*, O.name organizationName, GROUP_CONCAT(DISTINCT CR.shortName SEPARATOR '<br /> ') contactRoles
					FROM " . $dbName . ".Contact OC, " . $dbName . ".ContactRole CR, " . $dbName . ".ContactRoleProfile CRP, " . $dbName . ".Organization O, Resource R, ResourceOrganizationLink ROL
					WHERE (OC.archiveDate = '0000-00-00' OR OC.archiveDate is null)
					AND R.resourceID = ROL.resourceID
					AND ROL.organizationID = OC.organizationID
					AND CRP.contactID = OC.contactID
					AND CRP.contactRoleID = CR.contactRoleID
					AND O.organizationID = OC.organizationID
					AND R.resourceID = '" . $this->resourceID . "'
					GROUP BY OC.contactID, O.name
					ORDER BY OC.name";

			$result = $this->db->processQuery($query, 'assoc');


			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['contactID'])){

				foreach (array_keys($result) as $attributeName) {
					$resultArray[$attributeName] = $result[$attributeName];
				}

				array_push($contactsArray, $resultArray);

			}else{
				foreach ($result as $row) {
					$resultArray = array();
					foreach (array_keys($row) as $attributeName) {
						$resultArray[$attributeName] = $row[$attributeName];
					}

					array_push($contactsArray, $resultArray);
				}
			}



		}


		return $contactsArray;
	}




	//returns array of contact objects
	public function getArchivedContacts(){

		$config = new Configuration;
		$contactsArray = array();

		//get resource specific contacts
		$query = "SELECT C.*, GROUP_CONCAT(CR.shortName SEPARATOR '<br /> ') contactRoles
			FROM Contact C, ContactRole CR, ContactRoleProfile CRP
			WHERE (archiveDate != '0000-00-00' && archiveDate != '')
			AND C.contactID = CRP.contactID
			AND CRP.contactRoleID = CR.contactRoleID
			AND resourceID = '" . $this->resourceID . "'
			GROUP BY C.contactID
			ORDER BY C.name";

		$result = $this->db->processQuery($query, 'assoc');


		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['contactID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($contactsArray, $resultArray);

		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}

				array_push($contactsArray, $resultArray);
			}
		}



		//if the org module is installed also get the org contacts from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$query = "SELECT DISTINCT OC.*, O.name organizationName, GROUP_CONCAT(DISTINCT CR.shortName SEPARATOR '<br /> ') contactRoles
					FROM " . $dbName . ".Contact OC, " . $dbName . ".ContactRole CR, " . $dbName . ".ContactRoleProfile CRP, " . $dbName . ".Organization O, Resource R, ResourceOrganizationLink ROL
					WHERE (OC.archiveDate != '0000-00-00' && OC.archiveDate is not null)
					AND R.resourceID = ROL.resourceID
					AND ROL.organizationID = OC.organizationID
					AND CRP.contactID = OC.contactID
					AND CRP.contactRoleID = CR.contactRoleID
					AND O.organizationID = OC.organizationID
					AND R.resourceID = '" . $this->resourceID . "'
					GROUP BY OC.contactID, O.name
					ORDER BY OC.name";


			$result = $this->db->processQuery($query, 'assoc');


			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['contactID'])){

				foreach (array_keys($result) as $attributeName) {
					$resultArray[$attributeName] = $result[$attributeName];
				}

				array_push($contactsArray, $resultArray);

			}else{
				foreach ($result as $row) {
					$resultArray = array();
					foreach (array_keys($row) as $attributeName) {
						$resultArray[$attributeName] = $row[$attributeName];
					}

					array_push($contactsArray, $resultArray);
				}
			}



		}

		return $contactsArray;



	}





	//returns array of contact objects
	public function getCreatorsArray(){

		$creatorsArray = array();
		$resultArray = array();

		//get resource specific creators
		$query = "SELECT distinct loginID, firstName, lastName
			FROM Resource R, User U
			WHERE U.loginID = R.createLoginID
			ORDER BY lastName, firstName, loginID";

		$result = $this->db->processQuery($query, 'assoc');


		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['loginID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($creatorsArray, $resultArray);

		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}

				array_push($creatorsArray, $resultArray);
			}
		}

		return $creatorsArray;


	}




	//returns array of external login records
	public function getExternalLoginArray(){


		$config = new Configuration;
		$elArray = array();

		//get resource specific accounts first
		$query = "SELECT EL.*,  ELT.shortName externalLoginType
				FROM ExternalLogin EL, ExternalLoginType ELT
				WHERE EL.externalLoginTypeID = ELT.externalLoginTypeID
				AND resourceID = '" . $this->resourceID . "'
				ORDER BY ELT.shortName;";

		$result = $this->db->processQuery($query, 'assoc');


		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['externalLoginID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($elArray, $resultArray);

		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}

				array_push($elArray, $resultArray);
			}
		}



		//if the org module is installed also get the external logins from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$query = "SELECT DISTINCT EL.*, ELT.shortName externalLoginType, O.name organizationName
						FROM " . $dbName . ".ExternalLogin EL, " . $dbName . ".ExternalLoginType ELT, " . $dbName . ".Organization O,
							Resource R, ResourceOrganizationLink ROL
						WHERE EL.externalLoginTypeID = ELT.externalLoginTypeID
						AND R.resourceID = ROL.resourceID
						AND ROL.organizationID = EL.organizationID
						AND O.organizationID = EL.organizationID
						AND R.resourceID = '" . $this->resourceID . "'
						ORDER BY ELT.shortName;";


			$result = $this->db->processQuery($query, 'assoc');


			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['externalLoginID'])){

				foreach (array_keys($result) as $attributeName) {
					$resultArray[$attributeName] = $result[$attributeName];
				}

				array_push($elArray, $resultArray);

			}else{
				foreach ($result as $row) {
					$resultArray = array();
					foreach (array_keys($row) as $attributeName) {
						$resultArray[$attributeName] = $row[$attributeName];
					}

					array_push($elArray, $resultArray);
				}
			}



		}



		return $elArray;
	}



	//returns array of notes objects
	public function getNotes($tabName = NULL){

		if ($tabName){
			$query = "SELECT * FROM ResourceNote RN
						WHERE resourceID = '" . $this->resourceID . "'
						AND UPPER(tabName) = UPPER('" . $tabName . "')
						ORDER BY updateDate desc";
		}else{
			$query = "SELECT RN.*
						FROM ResourceNote RN
						LEFT JOIN NoteType NT ON NT.noteTypeID = RN.noteTypeID
						WHERE resourceID = '" . $this->resourceID . "'
						ORDER BY updateDate desc, NT.shortName";
		}

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceNoteID'])){
			$object = new ResourceNote(new NamedArguments(array('primaryKey' => $result['resourceNoteID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourceNote(new NamedArguments(array('primaryKey' => $row['resourceNoteID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}






	//returns array of the initial note object
	public function getInitialNote(){
		$noteType = new NoteType();

		$query = "SELECT * FROM ResourceNote RN
					WHERE resourceID = '" . $this->resourceID . "'
					AND noteTypeID = " . $noteType->getInitialNoteTypeID . "
					ORDER BY noteTypeID desc LIMIT 0,1";


		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceNoteID'])){
			$resourceNote = new ResourceNote(new NamedArguments(array('primaryKey' => $result['resourceNoteID'])));
			return $resourceNote;
		} else{
			$resourceNote = new ResourceNote();
			return $resourceNote;
		}

	}







	//returns array of attachments objects
	public function getAttachments(){

		$query = "SELECT * FROM Attachment A, AttachmentType AT
					WHERE AT.attachmentTypeID = A.attachmentTypeID
					AND resourceID = '" . $this->resourceID . "'
					ORDER BY AT.shortName";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['attachmentID'])){
			$object = new Attachment(new NamedArguments(array('primaryKey' => $result['attachmentID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Attachment(new NamedArguments(array('primaryKey' => $row['attachmentID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//returns array of contact objects
	public function getContacts(){

		$query = "SELECT * FROM Contact
					WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['contactID'])){
			$object = new Contact(new NamedArguments(array('primaryKey' => $result['contactID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Contact(new NamedArguments(array('primaryKey' => $row['contactID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//returns array of externalLogin objects
	public function getExternalLogins(){

		$query = "SELECT * FROM ExternalLogin
					WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['externalLoginID'])){
			$object = new ExternalLogin(new NamedArguments(array('primaryKey' => $result['externalLoginID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ExternalLogin(new NamedArguments(array('primaryKey' => $row['externalLoginID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}





	//returns array based on search
	public function search($whereAdd, $orderBy, $limit){

		$config = new Configuration();
		$status = new Status();


		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$orgJoinAdd = "LEFT JOIN " . $dbName . ".Organization O ON O.organizationID = ROL.organizationID
						   LEFT JOIN " . $dbName . ".Alias OA ON OA.organizationID = ROL.organizationID";

		}else{

			$orgJoinAdd = "LEFT JOIN Organization O ON O.organizationID = ROL.organizationID";


		}

		//also add to not retrieve saved records
		$whereAdd[] = "UPPER(S.shortName) != 'SAVED'";

		if (count($whereAdd) > 0){
			$whereStatement = " WHERE " . implode(" AND ", $whereAdd);
		}else{
			$whereStatement = "";
		}

		if ($limit != ""){
			$limitStatement = " LIMIT " . $limit;
		}else{
			$limitStatement = "";
		}


		//now actually execute query
		$query = "SELECT R.resourceID, R.titleText, AT.shortName acquisitionType, R.createLoginID, U.firstName, U.lastName, date_format(R.createDate, '%c/%e/%Y') createDateFormatted, R.createDate, S.shortName status,
						GROUP_CONCAT(DISTINCT A.shortName ORDER BY A.shortName DESC SEPARATOR '<br />') aliases
								FROM Resource R
									LEFT JOIN Alias A ON R.resourceID = A.resourceID
									LEFT JOIN ResourceFormat RF ON R.resourceFormatID = RF.resourceFormatID
									LEFT JOIN ResourceType RT ON R.resourceTypeID = RT.resourceTypeID
									LEFT JOIN AcquisitionType AT ON R.acquisitionTypeID = AT.acquisitionTypeID
									LEFT JOIN Status S ON R.statusID = S.statusID
									LEFT JOIN User U ON R.createLoginID = U.loginID
									LEFT JOIN ResourcePayment RPAY ON R.resourceID = RPAY.resourceID
									LEFT JOIN ResourceNote RN ON R.resourceID = RN.resourceID
									LEFT JOIN ResourceOrganizationLink ROL ON R.resourceID = ROL.resourceID
									LEFT JOIN ResourcePurchaseSiteLink RPSL ON R.resourceID = RPSL.resourceID
									LEFT JOIN ResourceAuthorizedSiteLink RAUSL ON R.resourceID = RAUSL.resourceID
									LEFT JOIN ResourceAdministeringSiteLink RADSL ON R.resourceID = RADSL.resourceID
									LEFT JOIN ResourceRelationship RRC ON RRC.relatedResourceID = R.resourceID
									LEFT JOIN ResourceRelationship RRP ON RRP.resourceID = R.resourceID
									LEFT JOIN Resource RC ON RC.resourceID = RRC.resourceID
									LEFT JOIN Resource RP ON RP.resourceID = RRP.relatedResourceID
									" . $orgJoinAdd . "
								" . $whereStatement . "
								GROUP BY R.resourceID, R.titleText, R.isbnOrISSN, RF.shortName, RT.shortName, S.shortName
								ORDER BY " . $orderBy . $limitStatement;

		$result = $this->db->processQuery(stripslashes($query), 'assoc');

		$searchArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['resourceID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($searchArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($searchArray, $resultArray);
			}
		}

		return $searchArray;
	}


	//used for A-Z on search (index)
	public function getAlphabeticalList(){
		$alphArray = array();
		$result = mysql_query("SELECT DISTINCT UPPER(SUBSTR(TRIM(LEADING 'The ' FROM titleText),1,1)) letter, COUNT(SUBSTR(TRIM(LEADING 'The ' FROM titleText),1,1)) letter_count
								FROM Resource R
								GROUP BY SUBSTR(TRIM(LEADING 'The ' FROM titleText),1,1)
								ORDER BY 1;");

		while ($row = mysql_fetch_assoc($result)){
			$alphArray[$row['letter']] = $row['letter_count'];
		}

		return $alphArray;
	}





	//returns array based on search for excel output (export.php)
	public function export($whereAdd, $orderBy){

		$config = new Configuration();

		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$orgJoinAdd = "LEFT JOIN " . $dbName . ".Organization O ON O.organizationID = ROL.organizationID
						   LEFT JOIN " . $dbName . ".Alias OA ON OA.organizationID = ROL.organizationID";

			$orgSelectAdd = "GROUP_CONCAT(DISTINCT O.name ORDER BY O.name DESC SEPARATOR '<br />') organizationNames";
		}else{
			$orgJoinAdd = "LEFT JOIN Organization O ON O.organizationID = ROL.organizationID";

			$orgSelectAdd = "GROUP_CONCAT(DISTINCT O.shortName ORDER BY O.shortName DESC SEPARATOR '<br />') organizationNames";
		}


		$licSelectAdd = '';
		$licJoinAdd = '';
		if ($config->settings->licensingModule == 'Y'){
			$dbName = $config->settings->licensingDatabaseName;

			$licJoinAdd = " LEFT JOIN ResourceLicenseLink RLL ON RLL.resourceID = R.resourceID
							LEFT JOIN " . $dbName . ".License L ON RLL.licenseID = L.licenseID";

			$licSelectAdd = "GROUP_CONCAT(DISTINCT L.shortName ORDER BY L.shortName DESC SEPARATOR '<br />') licenseNames, ";

		}



		//also add to not retrieve saved records
		$whereAdd[] = "UPPER(S.shortName) != 'SAVED'";


		if (count($whereAdd) > 0){
			$whereStatement = " WHERE " . implode(" AND ", $whereAdd);
		}else{
			$whereStatement = "";
		}




		//now actually execute query
		$query = "SELECT R.resourceID, R.titleText, AT.shortName acquisitionType, CONCAT_WS(' ', CU.firstName, CU.lastName) createName,
						R.createDate createDate, CONCAT_WS(' ', UU.firstName, UU.lastName) updateName,
						R.updateDate updateDate, S.shortName status,
						RT.shortName resourceType, RF.shortName resourceFormat, R.isbnOrISSN, R.orderNumber, R.systemNumber, R.resourceURL,
						" . $orgSelectAdd . ",
						" . $licSelectAdd . "
						GROUP_CONCAT(DISTINCT A.shortName ORDER BY A.shortName DESC SEPARATOR '<br />') aliases,
						GROUP_CONCAT(DISTINCT PS.shortName ORDER BY PS.shortName DESC SEPARATOR '<br />') purchasingSites,
						GROUP_CONCAT(DISTINCT AUS.shortName ORDER BY AUS.shortName DESC SEPARATOR '<br />') authorizedSites,
						GROUP_CONCAT(DISTINCT ADS.shortName ORDER BY ADS.shortName DESC SEPARATOR '<br />') administeringSites,
						GROUP_CONCAT(DISTINCT RP.titleText ORDER BY RP.titleText DESC SEPARATOR '<br />') parentResources,
						GROUP_CONCAT(DISTINCT RC.titleText ORDER BY RC.titleText DESC SEPARATOR '<br />') childResources
								FROM Resource R
									LEFT JOIN Alias A ON R.resourceID = A.resourceID
									LEFT JOIN ResourceFormat RF ON R.resourceFormatID = RF.resourceFormatID
									LEFT JOIN ResourceType RT ON R.resourceTypeID = RT.resourceTypeID
									LEFT JOIN AcquisitionType AT ON R.acquisitionTypeID = AT.acquisitionTypeID
									LEFT JOIN ResourcePayment RPAY ON R.resourceID = RPAY.resourceID
									LEFT JOIN Status S ON R.statusID = S.statusID
									LEFT JOIN ResourceNote RN ON R.resourceID = RN.resourceID
									LEFT JOIN User CU ON R.createLoginID = CU.loginID
									LEFT JOIN User UU ON R.updateLoginID = UU.loginID
									LEFT JOIN ResourceOrganizationLink ROL ON R.resourceID = ROL.resourceID
									LEFT JOIN ResourcePurchaseSiteLink RPSL ON R.resourceID = RPSL.resourceID
									LEFT JOIN PurchaseSite PS ON RPSL.purchaseSiteID = PS.purchaseSiteID
									LEFT JOIN ResourceAuthorizedSiteLink RAUSL ON R.resourceID = RAUSL.resourceID
									LEFT JOIN AuthorizedSite AUS ON RAUSL.authorizedSiteID = AUS.authorizedSiteID
									LEFT JOIN ResourceAdministeringSiteLink RADSL ON R.resourceID = RADSL.resourceID
									LEFT JOIN AdministeringSite ADS ON RADSL.administeringSiteID = ADS.administeringSiteID
									LEFT JOIN ResourceRelationship RRC ON RRC.relatedResourceID = R.resourceID
									LEFT JOIN ResourceRelationship RRP ON RRP.resourceID = R.resourceID
									LEFT JOIN Resource RC ON RC.resourceID = RRC.resourceID
									LEFT JOIN Resource RP ON RP.resourceID = RRP.relatedResourceID
									" . $orgJoinAdd . "
									" . $licJoinAdd . "
								" . $whereStatement . "
								GROUP BY R.resourceID, R.titleText, R.isbnOrISSN, RF.shortName, RT.shortName, S.shortName
								ORDER BY " . $orderBy;

		$result = $this->db->processQuery(stripslashes($query), 'assoc');

		$searchArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['resourceID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($searchArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($searchArray, $resultArray);
			}
		}

		return $searchArray;
	}







	//search used index page drop down
	public function getOrganizationList(){
		$config = new Configuration;

		$orgArray = array();

		//if the org module is installed get the org names from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;
			$query = "SELECT name, organizationID FROM " . $dbName . ".Organization ORDER BY 1;";

		//otherwise get the orgs from this database
		}else{
			$query = "SELECT shortName name, organizationID FROM Organization ORDER BY 1;";
		}


		$result = $this->db->processQuery($query, 'assoc');

		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['organizationID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($orgArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($orgArray, $resultArray);
			}
		}

		return $orgArray;

	}



	//gets an array of organizations set up for this resource (organizationID, organization, organizationRole)
	public function getOrganizationArray(){
		$config = new Configuration;

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$resourceOrgArray = array();

			$query = "SELECT * FROM ResourceOrganizationLink WHERE resourceID = '" . $this->resourceID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['organizationID'])){
				$orgArray = array();

				//first, get the organization name
				$query = "SELECT name FROM " . $dbName . ".Organization WHERE organizationID = " . $result['organizationID'];

				if ($orgResult = mysql_query($query)){
					while ($orgRow = mysql_fetch_assoc($orgResult)){
						$orgArray['organization'] = $orgRow['name'];
						$orgArray['organizationID'] = $result['organizationID'];
					}
				}

				//then, get the role name
				$query = "SELECT * FROM " . $dbName . ".OrganizationRole WHERE organizationRoleID = " . $result['organizationRoleID'];

				if ($orgResult = mysql_query($query)){
					while ($orgRow = mysql_fetch_assoc($orgResult)){
						$orgArray['organizationRoleID'] = $orgRow['organizationRoleID'];
						$orgArray['organizationRole'] = $orgRow['shortName'];
					}
				}

				array_push($resourceOrgArray, $orgArray);
			}else{
				foreach ($result as $row) {

					$orgArray = array();

					//first, get the organization name
					$query = "SELECT name FROM " . $dbName . ".Organization WHERE organizationID = " . $row['organizationID'];

					if ($orgResult = mysql_query($query)){
						while ($orgRow = mysql_fetch_assoc($orgResult)){
							$orgArray['organization'] = $orgRow['name'];
							$orgArray['organizationID'] = $row['organizationID'];
						}
					}

					//then, get the role name
					$query = "SELECT * FROM " . $dbName . ".OrganizationRole WHERE organizationRoleID = " . $row['organizationRoleID'];


					if ($orgResult = mysql_query($query)){
						while ($orgRow = mysql_fetch_assoc($orgResult)){
							$orgArray['organizationRoleID'] = $orgRow['organizationRoleID'];
							$orgArray['organizationRole'] = $orgRow['shortName'];
						}
					}

					array_push($resourceOrgArray, $orgArray);

				}

			}






		//otherwise if the org module is not installed get the org name from this database
		}else{



			$resourceOrgArray = array();

			$query = "SELECT * FROM ResourceOrganizationLink WHERE resourceID = '" . $this->resourceID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['organizationID'])){
				$orgArray = array();

				//first, get the organization name
				$query = "SELECT shortName FROM Organization WHERE organizationID = " . $result['organizationID'];

				if ($orgResult = mysql_query($query)){
					while ($orgRow = mysql_fetch_assoc($orgResult)){
						$orgArray['organization'] = $orgRow['shortName'];
						$orgArray['organizationID'] = $result['organizationID'];
					}
				}

				//then, get the role name
				$query = "SELECT * FROM OrganizationRole WHERE organizationRoleID = " . $result['organizationRoleID'];

				if ($orgResult = mysql_query($query)){
					while ($orgRow = mysql_fetch_assoc($orgResult)){
						$orgArray['organizationRoleID'] = $orgRow['organizationRoleID'];
						$orgArray['organizationRole'] = $orgRow['shortName'];
					}
				}

				array_push($resourceOrgArray, $orgArray);
			}else{
				foreach ($result as $row) {

					$orgArray = array();

					//first, get the organization name
					$query = "SELECT shortName FROM Organization WHERE organizationID = " . $row['organizationID'];

					if ($orgResult = mysql_query($query)){
						while ($orgRow = mysql_fetch_assoc($orgResult)){
							$orgArray['organization'] = $orgRow['shortName'];
							$orgArray['organizationID'] = $row['organizationID'];
						}
					}

					//then, get the role name
					$query = "SELECT * FROM OrganizationRole WHERE organizationRoleID = " . $row['organizationRoleID'];


					if ($orgResult = mysql_query($query)){
						while ($orgRow = mysql_fetch_assoc($orgResult)){
							$orgArray['organizationRoleID'] = $orgRow['organizationRoleID'];
							$orgArray['organizationRole'] = $orgRow['shortName'];
						}
					}

					array_push($resourceOrgArray, $orgArray);

				}

			}





		}


		return $resourceOrgArray;
	}





	//gets an array of distinct organizations set up for this resource (organizationID, organization)
	public function getDistinctOrganizationArray(){
		$config = new Configuration;

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$resourceOrgArray = array();

			$query = "SELECT DISTINCT organizationID FROM ResourceOrganizationLink WHERE resourceID = '" . $this->resourceID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['organizationID'])){
				$orgArray = array();

				//first, get the organization name
				$query = "SELECT name FROM " . $dbName . ".Organization WHERE organizationID = " . $result['organizationID'];

				if ($orgResult = mysql_query($query)){
					while ($orgRow = mysql_fetch_assoc($orgResult)){
						$orgArray['organization'] = $orgRow['name'];
						$orgArray['organizationID'] = $result['organizationID'];
					}
				}

				array_push($resourceOrgArray, $orgArray);
			}else{
				foreach ($result as $row) {

					$orgArray = array();

					//first, get the organization name
					$query = "SELECT DISTINCT name FROM " . $dbName . ".Organization WHERE organizationID = " . $row['organizationID'];

					if ($orgResult = mysql_query($query)){
						while ($orgRow = mysql_fetch_assoc($orgResult)){
							$orgArray['organization'] = $orgRow['name'];
							$orgArray['organizationID'] = $row['organizationID'];
						}
					}

					array_push($resourceOrgArray, $orgArray);

				}

			}






		//otherwise if the org module is not installed get the org name from this database
		}else{



			$resourceOrgArray = array();

			$query = "SELECT DISTINCT organizationID FROM ResourceOrganizationLink WHERE resourceID = '" . $this->resourceID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['organizationID'])){
				$orgArray = array();

				//first, get the organization name
				$query = "SELECT DISTINCT shortName FROM Organization WHERE organizationID = " . $result['organizationID'];

				if ($orgResult = mysql_query($query)){
					while ($orgRow = mysql_fetch_assoc($orgResult)){
						$orgArray['organization'] = $orgRow['shortName'];
						$orgArray['organizationID'] = $result['organizationID'];
					}
				}

				array_push($resourceOrgArray, $orgArray);
			}else{
				foreach ($result as $row) {

					$orgArray = array();

					//first, get the organization name
					$query = "SELECT DISTINCT shortName FROM Organization WHERE organizationID = " . $row['organizationID'];

					if ($orgResult = mysql_query($query)){
						while ($orgRow = mysql_fetch_assoc($orgResult)){
							$orgArray['organization'] = $orgRow['shortName'];
							$orgArray['organizationID'] = $row['organizationID'];
						}
					}

					array_push($resourceOrgArray, $orgArray);

				}

			}





		}


		return $resourceOrgArray;
	}










	//removes this resource
	public function removeResource(){
		//delete data from child linked tables
		$this->removeResourceRelationships();
		$this->removePurchaseSites();
		$this->removeAuthorizedSites();
		$this->removeAdministeringSites();
		$this->removeResourceLicenses();
		$this->removeResourceLicenseStatuses();
		$this->removeResourceOrganizations();
		$this->removeResourcePayments();


		$instance = new Contact();
		foreach ($this->getContacts() as $instance) {
			$instance->removeContactRoles();
			$instance->delete();
		}

		$instance = new ExternalLogin();
		foreach ($this->getExternalLogins() as $instance) {
			$instance->delete();
		}

		$instance = new ResourceNote();
		foreach ($this->getNotes() as $instance) {
			$instance->delete();
		}

		$instance = new Attachment();
		foreach ($this->getAttachments() as $instance) {
			$instance->delete();
		}

		$instance = new Alias();
		foreach ($this->getAliases() as $instance) {
			$instance->delete();
		}


		$this->delete();
	}



	//removes resource hierarchy records
	public function removeResourceRelationships(){

		$query = "DELETE
			FROM ResourceRelationship
			WHERE resourceID = '" . $this->resourceID . "' OR relatedResourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}



	//removes resource purchase sites
	public function removePurchaseSites(){

		$query = "DELETE
			FROM ResourcePurchaseSiteLink
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}




	//removes resource authorized sites
	public function removeAuthorizedSites(){

		$query = "DELETE
			FROM ResourceAuthorizedSiteLink
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}



	//removes resource administering sites
	public function removeAdministeringSites(){

		$query = "DELETE
			FROM ResourceAdministeringSiteLink
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}



	//removes payment records
	public function removeResourcePayments(){

		$query = "DELETE
			FROM ResourcePayment
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}

	//removes resource licenses
	public function removeResourceLicenses(){

		$query = "DELETE
			FROM ResourceLicenseLink
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}

	//removes resource license statuses
	public function removeResourceLicenseStatuses(){

		$query = "DELETE
			FROM ResourceLicenseStatus
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}

	//removes resource organizations
	public function removeResourceOrganizations(){

		$query = "DELETE
			FROM ResourceOrganizationLink
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}


	//removes resource note records
	public function removeResourceNotes(){

		$query = "DELETE
			FROM ResourceNote
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}




	//removes resource steps
	public function removeResourceSteps(){

		$query = "DELETE
			FROM ResourceStep
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}





	//search used for the resource autocomplete
	public function resourceAutocomplete($q){
		$resourceArray = array();
		$result = mysql_query("SELECT titleText, resourceID
								FROM Resource
								WHERE upper(titleText) like upper('%" . $q . "%')
								ORDER BY 1;");

		while ($row = mysql_fetch_assoc($result)){
			$resourceArray[] = $row['titleText'] . "|" . $row['resourceID'];
		}

		return $resourceArray;
	}


	//search used for the organization autocomplete
	public function organizationAutocomplete($q){
		$config = new Configuration;
		$organizationArray = array();

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$result = mysql_query("SELECT CONCAT(A.name, ' (', O.name, ')') shortName, O.organizationID
									FROM " . $dbName . ".Alias A, " . $dbName . ".Organization O
									WHERE A.organizationID=O.organizationID
									AND upper(A.name) like upper('%" . $q . "%')
									UNION
									SELECT name shortName, organizationID
									FROM " . $dbName . ".Organization
									WHERE upper(name) like upper('%" . $q . "%')
									ORDER BY 1;");

		}else{

			$result = mysql_query("SELECT organizationID, shortName
									FROM Organization O
									WHERE upper(O.shortName) like upper('%" . $q . "%')
									ORDER BY shortName;");

		}


		while ($row = mysql_fetch_assoc($result)){
			$organizationArray[] = $row['shortName'] . "|" . $row['organizationID'];
		}



		return $organizationArray;
	}




	//search used for the license autocomplete
	public function licenseAutocomplete($q){
		$config = new Configuration;
		$licenseArray = array();

		//if the org module is installed get the org name from org database
		if ($config->settings->licensingModule == 'Y'){
			$dbName = $config->settings->licensingDatabaseName;

			$result = mysql_query("SELECT shortName, licenseID
									FROM " . $dbName . ".License
									WHERE upper(shortName) like upper('%" . $q . "%')
									ORDER BY 1;");

		}

		while ($row = mysql_fetch_assoc($result)){
			$licenseArray[] = $row['shortName'] . "|" . $row['licenseID'];
		}



		return $licenseArray;
	}


	///////////////////////////////////////////////////////////////////////////////////
	//
	//  Workflow functions follow
	//
	///////////////////////////////////////////////////////////////////////////////////


	//returns array of ResourceStep objects for this Resource
	public function getResourceSteps(){


		$query = "SELECT * FROM ResourceStep
					WHERE resourceID = '" . $this->resourceID . "'
					ORDER BY displayOrderSequence, stepID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceStepID'])){
			$object = new ResourceStep(new NamedArguments(array('primaryKey' => $result['resourceStepID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourceStep(new NamedArguments(array('primaryKey' => $row['resourceStepID'])));
				array_push($objects, $object);
			}
		}

		return $objects;

	}




	//returns current step location in the workflow for this resource
	//used to display the group on the tabs
	public function getCurrentStepGroup(){


		$query = "SELECT groupName FROM ResourceStep RS, UserGroup UG
					WHERE resourceID = '" . $this->resourceID . "'
					ORDER BY stepID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceStepID'])){

		}

	}



	//returns first steps (object) in the workflow for this resource
	public function getFirstSteps(){

		$query = "SELECT * FROM ResourceStep
					WHERE resourceID = '" . $this->resourceID . "'
					AND (priorStepID is null OR priorStepID = '0')
					ORDER BY stepID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceStepID'])){
			$object = new ResourceStep(new NamedArguments(array('primaryKey' => $result['resourceStepID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourceStep(new NamedArguments(array('primaryKey' => $row['resourceStepID'])));
				array_push($objects, $object);
			}
		}

		return $objects;


	}



	//enters resource into new workflow
	public function enterNewWorkflow(){
		$config = new Configuration();

		//remove any current workflow steps
		$this->removeResourceSteps();

		//make sure this resource is marked in progress in case it was archived
		$status = new Status();
		$this->statusID = $status->getIDFromName('progress');
		$this->save();


		//Determine the workflow this resource belongs to
		$workflowObj = new Workflow();
		$workflowID = $workflowObj->getWorkflowID($this->resourceTypeID, $this->resourceFormatID, $this->acquisitionTypeID);

		if ($workflowID){
			$workflow = new Workflow(new NamedArguments(array('primaryKey' => $workflowID)));


			//Copy all of the step attributes for this workflow to a new resource step
			foreach ($workflow->getSteps() as $step){
				$resourceStep = new ResourceStep();

				$resourceStep->resourceStepID 		= '';
				$resourceStep->resourceID 			= $this->resourceID;
				$resourceStep->stepID 				= $step->stepID;
				$resourceStep->priorStepID			= $step->priorStepID;
				$resourceStep->stepName				= $step->stepName;
				$resourceStep->userGroupID			= $step->userGroupID;
				$resourceStep->displayOrderSequence	= $step->displayOrderSequence;

				$resourceStep->save();

			}


			//Start the first step
			//this handles updating the db and sending notifications for approval groups
			foreach ($this->getFirstSteps() as $resourceStep){
				$resourceStep->startStep();

			}
		}


		//send an email notification to the feedback email address and the creator
		$cUser = new User(new NamedArguments(array('primaryKey' => $this->createLoginID)));
		$acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $this->acquisitionTypeID)));

		if ($cUser->firstName){
			$creator = $cUser->firstName . " " . $cUser->lastName;
		}else if ($this->createLoginID){  //for some reason user isn't set up or their firstname/last name don't exist
			$creator = $this->createLoginID;
		}else{
			$creator = "(unknown user)";
		}


		if (($config->settings->feedbackEmailAddress) || ($cUser->emailAddress)){
			$email = new Email();
			$util = new Utility();

			$email->message = $util->createMessageFromTemplate('NewResourceMain', $this->resourceID, $this->titleText, '', '', $creator);

			if ($cUser->emailAddress){
				$emailTo[] 			= $cUser->emailAddress;
			}

			if ($config->settings->feedbackEmailAddress != ''){
				$emailTo[] 			=  $config->settings->feedbackEmailAddress;
			}

			$email->to = implode(",", $emailTo);

			if ($acquisitionType->shortName){
				$email->subject		= "CORAL Alert: New " . $acquisitionType->shortName . " Resource Added: " . $this->titleText;
			}else{
				$email->subject		= "CORAL Alert: New Resource Added: " . $this->titleText;
			}

			$email->send();

		}

	}




	//completes a workflow (changes status to complete and sends notifications to creator and "master email")
	public function completeWorkflow(){
		$config = new Configuration();
		$util = new Utility();
		$status = new Status();
		$statusID = $status->getIDFromName('complete');

		if ($statusID){
			$this->statusID = $statusID;
			$this->save();
		}



		//send notification to creator and master email address

		$cUser = new User(new NamedArguments(array('primaryKey' => $this->createLoginID)));

		//formulate emil to be sent
		$email = new Email();
		$email->message = $util->createMessageFromTemplate('CompleteResource', $this->resourceID, $this->titleText, '', $this->systemNumber, '');

		if ($cUser->emailAddress){
			$emailTo[] 			= $cUser->emailAddress;
		}

		if ($config->settings->feedbackEmailAddress != ''){
			$emailTo[] 			=  $config->settings->feedbackEmailAddress;
		}

		$email->to = implode(",", $emailTo);

		$email->subject		= "CORAL Alert: Workflow completion for " . $this->titleText;


		$email->send();
	}





}

?>