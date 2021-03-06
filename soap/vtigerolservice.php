<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
include_once 'includes/main/WebUI.php';
require_once('include/Webservices/Query.php');
require_once('libraries/nusoap/nusoap.php');
require_once("modules/Users/Users.php");

$log = &LoggerManager::getLogger('vtigerolservice');

error_reporting(0);

$NAMESPACE = 'http://www.vtiger.com/products/crm';

$server = new soap_server;

$server->configureWSDL('vtigerolservice');

//ContactDetails SOAP Structure
$server->wsdl->addComplexType(
    'contactdetail',
    'complexType',
    'struct',
    'all',
    '',
    array(
	   'id' => array('name'=>'id','type'=>'xsd:string'),
        'title' => array('name'=>'title','type'=>'xsd:string'),
        'firstname' => array('name'=>'firstname','type'=>'xsd:string'),
        'middlename' => array('name'=>'middlename','type'=>'xsd:string'),
        'lastname' => array('name'=>'lastname','type'=>'xsd:string'),
        'birthdate'=> array('name'=>'birthdate','type'=>'xsd:string'),
        'emailaddress' => array('name'=>'emailaddress','type'=>'xsd:string'),
        'jobtitle'=> array('name'=>'jobtitle','type'=>'xsd:string'),
        'department'=> array('name'=>'department','type'=>'xsd:string'),
        'accountname' => array('name'=>'accountname','type'=>'xsd:string'),
        'officephone'=> array('name'=>'officephone','type'=>'xsd:string'),
        'homephone'=> array('name'=>'homephone','type'=>'xsd:string'),
        'otherphone'=> array('name'=>'otherphone','type'=>'xsd:string'),
        'fax'=> array('name'=>'fax','type'=>'xsd:string'),
        'mobile'=> array('name'=>'mobile','type'=>'xsd:string'),
        'asstname'=> array('name'=>'asstname','type'=>'xsd:string'),
        'asstphone'=> array('name'=>'asstphone','type'=>'xsd:string'),
        'reportsto'=> array('name'=>'reportsto','type'=>'xsd:string'),
        'mailingstreet'=> array('name'=>'mailingstreet','type'=>'xsd:string'),
        'mailingcity'=> array('name'=>'mailingcity','type'=>'xsd:string'),
        'mailingstate'=> array('name'=>'mailingstate','type'=>'xsd:string'),
        'mailingzip'=> array('name'=>'mailingzip','type'=>'xsd:string'),
        'mailingcountry'=> array('name'=>'mailingcountry','type'=>'xsd:string'),
        'otherstreet'=> array('name'=>'otherstreet','type'=>'xsd:string'),
        'othercity'=> array('name'=>'othercity','type'=>'xsd:string'),
        'otherstate'=> array('name'=>'otherstate','type'=>'xsd:string'),
        'otherzip'=> array('name'=>'otherzip','type'=>'xsd:string'),
        'othercountry'=> array('name'=>'othercountry','type'=>'xsd:string'),
        'description'=> array('name'=>'description','type'=>'xsd:string'),
        'category'=> array('name'=>'category','type'=>'xsd:string'),
    )
);

$server->wsdl->addComplexType(
    'contactdetails',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:contactdetail[]')
    ),
    'tns:contactdetail'
);

$server->wsdl->addComplexType(
    'taskdetail',
    'complexType',
    'struct',
    'all',
    '',
    array(
			'id'=>array('name'=>'id','type'=>'xsd:string'),
			'subject'=>array('name'=>'subject','type'=>'xsd:string'),
			'startdate'=>array('name'=>'startdate','type'=>'xsd:string'),
			'duedate'=>array('name'=>'duedate','type'=>'xsd:string'),
			'status'=> array('name'=>'status','type'=>'xsd:string'),
			'priority'=>array('name'=>'priority','type'=>'xsd:string'),
			'description'=>array('name'=>'description','type'=>'xsd:string'),
			'contactname'=>array('name'=>'contactname','type'=>'xsd:string'),
			'category'=>array('name'=>'category','type'=>'xsd:string'),
		  )
);

$server->wsdl->addComplexType(
    'taskdetails',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:taskdetail[]')
    ),
    'tns:taskdetail'
);

$server->wsdl->addComplexType(
    'clndrdetail',
    'complexType',
    'struct',
    'all',
    '',
    array(
          'id'=>array('name'=>'id','type'=>'xsd:string'),
          'subject'=>array('name'=>'subject','type'=>'xsd:string'),
          'startdate'=>array('name'=>'startdate','type'=>'xsd:string'),
          'duedate'=>array('name'=>'duedate','type'=>'xsd:string'),
          'location'=> array('name'=>'location','type'=>'xsd:string'),
          'description'=>array('name'=>'description','type'=>'xsd:string'),
          'contactname'=>array('name'=>'contactname','type'=>'xsd:string'),
          'category'=>array('name'=>'category','type'=>'xsd:string'),
        )
);

$server->wsdl->addComplexType(
    'clndrdetails',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:clndrdetail[]')
    ),
    'tns:clndrdetail'
);

$server->wsdl->addComplexType(
    'emailmsgdetail',
    'complexType',
    'struct',
    'all',
    '',
    array(
          'subject'=>array('name'=>'subject','type'=>'xsd:string'),
          'body'=>array('name'=>'body','type'=>'xsd:string'),
          'datesent'=>array('name'=>'datesent','type'=>'xsd:string'),
         )
);


$server->register(
    'LoginToVtiger',
    array('userid'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'xsd:string','session'=>'xsd:string'),
    $NAMESPACE);

$server->register(
    'CheckContactPermission',
    array('username'=>'xsd:string','session'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
    'CheckActivityPermission',
    array('username'=>'xsd:string','session'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);



$server->register(
    'SearchContactsByEmail',
    array('username'=>'xsd:string','session'=>'xsd:string','emailaddress'=>'xsd:string'),
    array('return'=>'tns:contactdetails'),
    $NAMESPACE);

$server->register(
    'AddMessageToContact',
    array('username'=>'xsd:string','session'=>'xsd:string','contactid'=>'xsd:string','msgdtls'=>'tns:emailmsgdetail'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
    'AddEmailAttachment',
    array('emailid'=>'xsd:string','filedata'=>'xsd:string',
	  'filename'=>'xsd:string','filesize'=>'xsd:string','filetype'=>'xsd:string',
	  'username'=>'xsd:string','session'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

//For Contacts Sync
$server->register(
		'GetContacts',
    array('username'=>'xsd:string','session'=>'xsd:string'),
    array('return'=>'tns:contactdetails'),
    $NAMESPACE);

$server->register(
   'AddContacts',
    array('username'=>'xsd:string','session'=>'xsd:string','cntdtls'=>'tns:contactdetails'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
   'UpdateContacts',
    array('username'=>'xsd:string','session'=>'xsd:string','cntdtls'=>'tns:contactdetails'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
   'DeleteContacts',
    array('username'=>'xsd:string','session'=>'xsd:string','crmid'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);   
//End for Contacts Sync

//For Tasks Sync
$server->register(
		'GetTasks',
    array('username'=>'xsd:string','session'=>'xsd:string'),
    array('return'=>'tns:taskdetails'),
    $NAMESPACE);

$server->register(
   'AddTasks',
    array('username'=>'xsd:string','session'=>'xsd:string','taskdtls'=>'tns:taskdetails'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
   'UpdateTasks',
    array('username'=>'xsd:string','session'=>'xsd:string','taskdtls'=>'tns:taskdetails'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
   'DeleteTasks',
    array('username'=>'xsd:string','session'=>'xsd:string','crmid'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE); 
//End for Tasks Sync

//For Calendar Sync
$server->register(
		'GetClndr',
    array('username'=>'xsd:string','session'=>'xsd:string'),
    array('return'=>'tns:clndrdetails'),
    $NAMESPACE);

$server->register(
   'AddClndr',
    array('username'=>'xsd:string','session'=>'xsd:string','clndrdtls'=>'tns:clndrdetails'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
   'UpdateClndr',
    array('username'=>'xsd:string','session'=>'xsd:string','clndrdtls'=>'tns:clndrdetails'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
   'DeleteClndr',
    array('username'=>'xsd:string','session'=>'xsd:string','crmid'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE); 
//End for Calendar Sync

function SearchContactsByEmail($username,$session,$emailaddress)
{
	if(!validateSession($username,$session))
	return null;
	$output_list = array();
	//consider case that there is no matching email address provided, search for last name
	$namepartsquery ='';
	$name_arr = explode(" ", $emailaddress);
	foreach ($name_arr as $namepart) {
		$namepartsquery .=	" OR lastname LIKE '$namepart' ";
	}
	$complete_query = "email LIKE '%$emailaddress%' OR lastname LIKE '%$emailaddress%' OR firstname LIKE '%$emailaddress%'".$namepartsquery;
	$res = getEntityEntries('Contacts', $username, 	$complete_query);
    
     // create a return array of names and email addresses.
     foreach($res as $contact)
     {
		 if (empty($contact['email'])) continue;
          $output_list[] = Array(
               "id" => $contact[id],
               "firstname" => decode_html($contact[firstname]),
               "lastname" => decode_html($contact[lastname]),
               "accountname" => decode_html($contact[accountname]),
               "emailaddress" => decode_html($contact[email]),
			   "category" => "Contact",
          );
     }
	 // crm-now added Lead functionality
	 $res = getEntityEntries('Leads', $username, "email LIKE '%$emailaddress%' OR lastname LIKE '%$emailaddress%' OR firstname LIKE '%$emailaddress%'");
	 
	 foreach($res as $lead)
     {
		if (empty($lead['email'])) continue;
          $output_list[] = Array(
               "id" => $lead[id],
               "firstname" => decode_html($lead[firstname]),
               "lastname" => decode_html($lead[lastname]),
               "accountname" => decode_html($lead[company]),
               "emailaddress" => decode_html($lead[email]),
			   "category" => "Lead",
          );
     }
	 // crm-now added Accounts functionality
	 $res = getEntityEntries('Accounts', $username, "email1 LIKE '%$emailaddress%' OR accountname LIKE '%$emailaddress%'");
	 
	 foreach($res as $acc)
     {
		 if (empty($acc['email1'])) continue;
          $output_list[] = Array(
               "id" => $acc['id'],
               "firstname" => decode_html($acc['account_no']),
               "lastname" => decode_html($acc['accountname']),
               "accountname" => '',
               "emailaddress" => decode_html($acc['email1']),
			   "category" => "Account",
          );
     }
     // end crm-now

     return $output_list;
}     

function AddMessageToContact($username,$session,$contactid,$msgdtls)
{
	if(!validateSession($username,$session))
	return null;
	
	global $current_user;
	global $adb;
	require_once('modules/Users/Users.php');
	require_once('modules/Emails/Emails.php');
	
	$current_user = new Users();
	$user_id = $current_user->retrieve_user_id($username);
	$current_user = $current_user->retrieveCurrentUserInfoFromFile($user_id);
	
	foreach($msgdtls as $msgdtl)
	{
	    if(isset($msgdtl))
	    {    
	        $email = new Emails();
			$email_body = str_replace("'", "''", $msgdtl['body']);
			$email_body = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'," ", $email_body);
	        $email_subject = str_replace("'", "''",$msgdtl['subject']);
	        $sent_date = DateTimeField::convertToUserTimeZone($msgdtl['datesent']);
			$date_sent = $sent_date->format("Y-m-d");
    		$sent_from = $msgdtl ['sent_from'];
	        $email->column_fields['subject'] =  $email_subject;
	        $email->column_fields['assigned_user_id'] = $user_id;
	        $email->column_fields['date_start'] = $date_sent;
	        $email->column_fields['description']  =  $email_body;
	        $email->column_fields['activitytype'] = 'Emails'; 
	        $email->column_fields['email_flag'] = 'SENT';
	        $email->column_fields['from_email'] = $msgdtl ['sent_from'];
	        $email->column_fields['saved_toid'] = $current_user->email1;
	        $email->plugin_save = true; 
	       	$email->save("Emails");
			$email->set_emails_se_invitee_relationship($email->id,$contactid);
			
	        if (isset ($msgdtl['reminder']) and trim($msgdtl['reminder'])!='') {
				$event = new Activity();
				//fixed values
				$event->column_fields["visibility"] = 'Private';
				$event->column_fields["activitytype"]	= 'Call';	
				$event->column_fields["time_start"]	= '09:00';
				$event->column_fields["time_end"] = '09:15';
				$event->column_fields["duration_hours"]	= '0';	
				$event->column_fields["duration_minutes"]= '15';	
				$event->column_fields["sendnotification"] = '0';
				$event->column_fields["eventstatus"] = 'Planned';
				$event->column_fields["taskpriority"] = 'Medium';
				$event->column_fields["notime"] = '0';
				$event->column_fields["recurringtype"] = '--None--';			
				$event->column_fields["date_start"]	= $msgdtl['reminder'];
				$event->column_fields["due_date"] = $msgdtl['reminder'];	
				$event->column_fields["subject"] = getTranslatedString("OL_FROM") .$msgdtl['sent_from'].getTranslatedString("OL_TO").$msgdtl['sent_to'].getTranslatedString("OL_DATE").$date_sent;
				$event->column_fields["description"] = getTranslatedString("OL_DESC")."\n\n"."https://".$GLOBALS['_ENV']['HTTP_HOST']."/index.php?module=Emails&action=EmailsAjax&file=DetailView&record=".$email->id;	
				$event->column_fields["assigned_user_id"] = $user_id;
				//switch id as contacts and leads/accounts have different links
				$query = "SELECT setype FROM vtiger_crmentity WHERE crmid = ?;";
				$result = $adb->pquery($query, array($contactid));
				// if (!$result) die(mysqli_error());
				$module = $adb->query_result($result, 0, 'setype');
				if ($module == 'Contacts')
					$event->column_fields['contact_id'] = $contactid;
				else
					$event->column_fields['parent_id'] = $contactid;
				$event->save("Calendar");
	        }
	
	        return $email->id;
		}else{
				return "";
			}
	}
}

function LoginToVtiger($user_name,$password,$version)
{
  	global $log,$adb;
	require_once('modules/Users/Users.php');
	include('vtigerversion.php');

	/* crm-now: Outlook Plugin version 1.510.001.xxx */
	if(version_compare($version,'1.510.001', '>=') === 1) {
		return array("VERSION",'00');
	}
	$return_access = array("FALSES",'00');
	
	$objuser = new Users();
	
	if($password != "")
	{
		$objuser->column_fields['user_name'] = $user_name;
		$objuser->load_user($password);
		if($objuser->is_authenticated())
		{
			$userid =  $objuser->retrieve_user_id($user_name);
			$sessionid = makeRandomPassword();
			unsetServerSessionId($userid);
			$sql="insert into vtiger_soapservice values(?,?,?)";
			$result = $adb->pquery($sql, array($userid,'Outlook' ,$sessionid));
			$return_access = array("TRUES",$sessionid);
		}else
		{
			$return_access = array("FALSES",'00');
		}
	}else
	{
			//$server->setError("Invalid username and/or password");
			$return_access = array("LOGIN",'00');
	}
	$objuser = $objuser;
	return $return_access;	
}

function AddEmailAttachment($emailid,$filedata,$filename,$filesize,$filetype,$username,$session)
{
	if(!validateSession($username,$session)) {
		return null;
	}
	if(empty($emailid)) {
		return null;
	}
	global $adb;
	require_once('modules/Users/Users.php');
	require_once('include/utils/utils.php');
	$filename = vtlib_purify(ltrim(preg_replace('/\s+/', '_', $filename), '\\'));//replace space with _ in filename
	$arr_file = explode(".", $filename);
	$ext = end($arr_file);
	if ($ext !== false) {
		if (in_array($ext, $upload_badext)) {
			$filename .= ".txt";
		}
	}
	$date_var = date('Y-m-d H:i:s');

	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);

	$crmid = $adb->getUniqueID("vtiger_crmentity");

	$upload_file_path = decideFilePath();

	$handle = fopen($upload_file_path.$crmid."_".$filename,"wb");
	fwrite($handle,base64_decode($filedata),$filesize);
	fclose($handle);

	$sql1 = "insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values (?,?,?,?,?,?,?)";
	$params1 = array($crmid, $user_id, $user_id, 'Emails Attachment', ' ', $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
	$entityresult = $adb->pquery($sql1, $params1);	

	$filetype="application/octet-stream";

	if($entityresult != false)
	{
		$sql2="insert into vtiger_attachments(attachmentsid, name, description, type, path) values (?,?,?,?,?)";
		$params2 = array($crmid, $filename, ' ', $filetype, $upload_file_path);
		$result=$adb->pquery($sql2, $params2);

		$sql3='insert into vtiger_seattachmentsrel values(?,?)';
		$adb->pquery($sql3, array($emailid, $crmid));

		return $crmid;   
	}
	else
	{
		//$server->setError("Invalid username and/or password"); 
		return "";
	}
}

function GetContacts($username,$session) {
	if(!validateSession($username,$session))
	return null;
	
	$res = getEntityEntries('Contacts', $username);
	
	$outputcount = 0;
	$outputxml = '';
	$returnAsXML = true;
	
	foreach($res AS $contact)
	{
		if($contact["birthday"] == "0000-00-00")
		{
			$contact["birthday"] = "";
		}
		if($contact["salutationtype"] == "--None--")
		{
			$contact["salutationtype"] = "";
		}
		$outputxml .= __GetContactSOAPNode($contact);
		$outputcount++;
	}
	
	//return $output_list;
	global $server;
	$server->methodreturnisliteralxml = true;
	$output = "<return xsi:type='SOAP-ENC:Array' SOAP-ENC:arrayType='tns:contactdetail[$outputcount]'>$outputxml</return>";
	
	return $output;
}

function __GetContactSOAPNode($contact) {
	global $server;
	$nodestring = "<item xsi:type='tns:contactdetail'>
<id xsi:type='xsd:string'>"         . $contact[id] . "</id>
<title xsi:type='xsd:string'>"      . SOAP_Encode($contact[salutationtype]) . "</title>
<firstname xsi:type='xsd:string'>"  . SOAP_Encode($contact[firstname]) ."</firstname>
<middlename xsi:type='xsd:string'>" . SOAP_Encode(trim($contact[middlename])) . "</middlename>
<lastname xsi:type='xsd:string'>"   . SOAP_Encode(trim($contact[lastname]))  ."</lastname>
<birthdate xsi:nil='true' xsi:type='xsd:string'>" .$contact[birthday]. "</birthdate>
<emailaddress xsi:type='xsd:string'>" .SOAP_Encode(trim($contact[email])) . "</emailaddress>
<jobtitle xsi:type='xsd:string'>"     .SOAP_Encode($contact[title]) ."</jobtitle>
<department xsi:type='xsd:string'>"   .SOAP_Encode($contact[department]) ."</department>
<accountname xsi:type='xsd:string'>"  .SOAP_Encode($contact[accountname]) ."</accountname>
<officephone xsi:type='xsd:string'>"  .SOAP_Encode($contact[phone])."</officephone>
<homephone xsi:type='xsd:string'>"    .SOAP_Encode($contact[homephone])."</homephone>
<otherphone xsi:type='xsd:string'>"   .SOAP_Encode($contact[otherphone])."</otherphone>
<fax xsi:type='xsd:string'>"          .SOAP_Encode($contact[fax])."</fax>
<mobile xsi:type='xsd:string'>"       .SOAP_Encode($contact[mobile])."</mobile>
<asstname xsi:type='xsd:stringi'>"    .SOAP_Encode($contact[assistant])."</asstname>
<asstphone xsi:type='xsd:string'>"    .SOAP_Encode($contact[assistantphone])."</asstphone>
<reportsto xsi:type='xsd:string'>"    .SOAP_Encode($contact[reports_to_name])."</reportsto>
<mailingstreet xsi:type='xsd:string'>".SOAP_Encode($contact[mailingstreet])."</mailingstreet>
<mailingcity xsi:type='xsd:string'>"  .SOAP_Encode($contact[mailingcity])."</mailingcity>
<mailingstate xsi:type='xsd:string'>" .SOAP_Encode($contact[mailingstate])."</mailingstate>
<mailingzip xsi:type='xsd:string'>"   .SOAP_Encode($contact[mailingzip])."</mailingzip>
<mailingcountry xsi:type='xsd:string'>".SOAP_Encode($contact[mailingcountry])."</mailingcountry>
<otherstreet xsi:type='xsd:string'>"   .SOAP_Encode($contact[otherstreet])."</otherstreet>
<othercity xsi:type='xsd:string'>"     .SOAP_Encode($contact[othercity])."</othercity>
<otherstate xsi:type='xsd:string'>"    .SOAP_Encode($contact[otherstate])."</otherstate>
<otherzip xsi:type='xsd:string'>".SOAP_Encode($contact[otherzip])."</otherzip>
<othercountry xsi:type='xsd:string'>".SOAP_Encode($contact[othercountry])."</othercountry>
<description xsi:type='xsd:string'>".SOAP_Encode($contact[description])."</description>
<category xsi:type='xsd:string'></category>
</item>";
// <category xsi:type='xsd:string'>".SOAP_Encode($contact[CUSTOMFIELDNAME])."</category> <-- replace <category xsi:type='xsd:string'></category> and add customfield name
	return $nodestring;
}

function AddContacts($username,$session,$cntdtls)
{
	if(!validateSession($username,$session))
	return null;
	global $adb;
	global $current_user;
	require_once('modules/Users/Users.php');
	require_once('modules/Contacts/Contacts.php');
	
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");
	
	$contact = new Contacts();
	
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
    	$sql1 = "select fieldname,columnname from vtiger_field where tabid=4 and vtiger_field.presence in (0,2)";
		$params1 = array();
  	} else {
    	$profileList = getCurrentUserProfileList();
    	$sql1 = "select fieldname,columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=4 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)";
  		$params1 = array();
		if (count($profileList) > 0) {
			$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			array_push($params1, $profileList);
		}
	}
  	$result1 = $adb->pquery($sql1, $params1);
  
  	for($i=0;$i < $adb->num_rows($result1);$i++)
  	{
      $permitted_lists[] = $adb->query_result($result1,$i,'fieldname');
  	}
	
	foreach($cntdtls as $cntrow)
	{
		if(isset($cntrow))
		{
		  		$contact->column_fields[salutationtype]=in_array('salutationtype',$permitted_lists) ? $cntrow["title"] : "";		
     			$contact->column_fields[firstname]=in_array('firstname',$permitted_lists) ? $cntrow["firstname"] : "";
    			
    			if($cntrow["middlename"] != "")
    			{
    				$contact->column_fields[lastname]=$cntrow["middlename"]." ".$cntrow["lastname"];
    			}elseif($cntrow["lastname"] != "")
    			{
    				$contact->column_fields[lastname]=$cntrow["lastname"];
    			}else
    			{
    			   $contact->column_fields[lastname]=$cntrow["firstname"]." ".$cntrow["middlename"]." ".$cntrow["lastname"];
          		}
    
    			$contact->column_fields[birthday]= in_array('birthday',$permitted_lists) ? $cntrow["birthdate"] : "";
    			$contact->column_fields[email]=in_array('email',$permitted_lists) ? $cntrow["emailaddress"] : "";
    			$contact->column_fields[title]=in_array('title',$permitted_lists) ? $cntrow["jobtitle"] : "";
    			$contact->column_fields[department]=in_array('department',$permitted_lists) ? $cntrow["department"] : "";
    			$contact->column_fields[account_id]= in_array('account_id',$permitted_lists) ? retrieve_account_id($cntrow["accountname"],$user_id) : "";
    			$contact->column_fields[phone]= in_array('phone',$permitted_lists) ? $cntrow["officephone"] : "";
    			$contact->column_fields[homephone]= in_array('homephone',$permitted_lists) ? $cntrow["homephone"] : "";
    			$contact->column_fields[otherphone]= in_array('otherphone',$permitted_lists) ? $cntrow["otherphone"] : "";
    			$contact->column_fields[fax]= in_array('fax',$permitted_lists) ? $cntrow["fax"] : "";
    			$contact->column_fields[mobile]=in_array('mobile',$permitted_lists) ? $cntrow["mobile"] : "";
    			$contact->column_fields[assistant]= in_array('assistant',$permitted_lists) ? $cntrow["asstname"] : "";
    			$contact->column_fields[assistantphone]= in_array('assistantphone',$permitted_lists) ? $cntrow["asstphone"] : "";     
    			$contact->column_fields[mailingstreet]=in_array('mailingstreet',$permitted_lists) ? $cntrow["mailingstreet"] : "";
    			$contact->column_fields[mailingcity]=in_array('mailingcity',$permitted_lists) ? $cntrow["mailingcity"] : "";
    			$contact->column_fields[mailingstate]=in_array('mailingstate',$permitted_lists) ? $cntrow["mailingstate"] : "";
    			$contact->column_fields[mailingzip]=in_array('mailingzip',$permitted_lists) ? $cntrow["mailingzip"] : "";
    			$contact->column_fields[mailingcountry]=in_array('mailingcountry',$permitted_lists) ? $cntrow["mailingcountry"] : "";    
    			$contact->column_fields[otherstreet]=in_array('otherstreet',$permitted_lists) ? $cntrow["otherstreet"] : "";
    			$contact->column_fields[othercity]=in_array('othercity',$permitted_lists) ? $cntrow["othercity"] : "";
    			$contact->column_fields[otherstate]=in_array('otherstate',$permitted_lists) ? $cntrow["otherstate"] : "";
    			$contact->column_fields[otherzip]=in_array('otherzip',$permitted_lists) ? $cntrow["otherzip"] : "";
    			$contact->column_fields[othercountry]=in_array('othercountry',$permitted_lists) ? $cntrow["othercountry"] : "";    	
    			$contact->column_fields[assigned_user_id]=in_array('assigned_user_id',$permitted_lists) ? $user_id : "";   
    			$contact->column_fields[description]= in_array('description',$permitted_lists) ? $cntrow["description"] : "";
				// $contact->column_fields[CUSTOMFIELDNAME]= in_array('CUSTOMFIELDNAME',$permitted_lists) ? $cntrow["category"] : "";
    			$contact->save("Contacts");	
		  
    }	
	}
	$contact = $contact;	
	return $contact->id;
}

function UpdateContacts($username,$session,$cntdtls)
{
	if(!validateSession($username,$session))
	return null;
	global $adb;
	global $current_user;
	require_once('modules/Users/Users.php');
	require_once('modules/Contacts/Contacts.php');
	
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");
	
	$contact = new Contacts();
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
    	$sql1 = "select fieldname,columnname from vtiger_field where tabid=4 and vtiger_field.presence in (0,2)";
  		$params1 = array();
	} else {
    	$profileList = getCurrentUserProfileList();
    	$sql1 = "select fieldname,columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=4 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)";
  		$params1 = array();
		if (count($profileList) > 0) {
			$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			array_push($params1, $profileList);
		}
	}
  	$result1 = $adb->pquery($sql1, $params1);
  
  	for($i=0;$i < $adb->num_rows($result1);$i++)
  	{
      $permitted_lists[] = $adb->query_result($result1,$i,'fieldname');
  	}
	//crm-now: disable number format
	$_REQUEST['ajxaction'] = 'Plugin';
	foreach($cntdtls as $cntrow)
	{
		if(isset($cntrow))
		{
			$contact->retrieve_entity_info($cntrow["id"],"Contacts");
			$contact->column_fields[salutationtype]=in_array('salutationtype',$permitted_lists) ? $cntrow["title"] : "";		
			$contact->column_fields[firstname]=in_array('firstname',$permitted_lists) ? $cntrow["firstname"] : "";
			if($cntrow["middlename"] != "")
			{
				$contact->column_fields[lastname]=$cntrow["middlename"]." ".$cntrow["lastname"];
			}elseif($cntrow["lastname"] != "")
			{
				$contact->column_fields[lastname]=$cntrow["lastname"];
			}else
			{
				$contact->column_fields[lastname]=$cntrow["firstname"]." ".$cntrow["middlename"]." ".$cntrow["lastname"];
      			}
				
			$contact->column_fields[birthday]= in_array('birthday',$permitted_lists) ? $cntrow["birthdate"] : "";
			$contact->column_fields[email]= in_array('email',$permitted_lists) ? $cntrow["emailaddress"] : "";
			$contact->column_fields[title]= in_array('title',$permitted_lists) ? $cntrow["jobtitle"] : "";
			$contact->column_fields[department]= in_array('department',$permitted_lists) ? $cntrow["department"] : "";
			$contact->column_fields[account_id]= in_array('account_id',$permitted_lists) ? retrieve_account_id($cntrow["accountname"],$user_id) : "";
			$contact->column_fields[phone]= in_array('phone',$permitted_lists) ? $cntrow["officephone"] : "";
			$contact->column_fields[homephone]= in_array('homephone',$permitted_lists) ? $cntrow["homephone"] : "";
			$contact->column_fields[otherphone]= in_array('otherphone',$permitted_lists) ? $cntrow["otherphone"] : "";
			$contact->column_fields[fax]= in_array('fax',$permitted_lists) ? $cntrow["fax"] : "";
			$contact->column_fields[mobile]= in_array('mobile',$permitted_lists) ? $cntrow["mobile"] : "";
			$contact->column_fields[assistant]= in_array('assistant',$permitted_lists) ? $cntrow["asstname"] : "";
			$contact->column_fields[assistantphone]= in_array('assistantphone',$permitted_lists) ? $cntrow["asstphone"] : "";     
			$contact->column_fields[mailingstreet]= in_array('mailingstreet',$permitted_lists) ? $cntrow["mailingstreet"] : "";
			$contact->column_fields[mailingcity]= in_array('mailingcity',$permitted_lists) ? $cntrow["mailingcity"] : "";
			$contact->column_fields[mailingstate]= in_array('mailingstate',$permitted_lists) ? $cntrow["mailingstate"] : "";
			$contact->column_fields[mailingzip]= in_array('mailingzip',$permitted_lists) ? $cntrow["mailingzip"] : "";
			$contact->column_fields[mailingcountry]= in_array('mailingcountry',$permitted_lists) ? $cntrow["mailingcountry"] : "";    
			$contact->column_fields[otherstreet]= in_array('otherstreet',$permitted_lists) ? $cntrow["otherstreet"] : "";
			$contact->column_fields[othercity]= in_array('othercity',$permitted_lists) ? $cntrow["othercity"] : "";
			$contact->column_fields[otherstate]= in_array('otherstate',$permitted_lists) ? $cntrow["otherstate"] : "";
			$contact->column_fields[otherzip]= in_array('otherzip',$permitted_lists) ? $cntrow["otherzip"] : "";
			$contact->column_fields[othercountry]= in_array('othercountry',$permitted_lists) ? $cntrow["othercountry"] : ""; 
			$contact->column_fields[description]= in_array('description',$permitted_lists) ? $cntrow["description"] : "";
			$contact->id = $cntrow["id"];
			$contact->mode = "edit";
			//saving date information in 'yyyy-mm-dd' format and displaying it in user's date format
			$user_old_date_format = $current_user->date_format;
			$current_user->date_format = 'yyyy-mm-dd';
			// $contact->column_fields[CUSTOMFIELDNAME]= in_array('CUSTOMFIELDNAME',$permitted_lists) ? $cntrow["category"] : "";
			$contact->save("Contacts");	
			$current_user->date_format = $user_old_date_format;
		}	
	}	
	$contact = $contact;
	return $contact->id;
}

function DeleteContacts($username,$session,$crmid)
{
	if(!validateSession($username,$session))
	return null;
	
	global $current_user;
	require_once('modules/Users/Users.php');
	require_once('modules/Contacts/Contacts.php');

	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");

	$contact = new Contacts();
	$contact->id = $crmid;
	$contact->mark_deleted($contact->id);

	$contact = $contact;
	return $contact->id;
}

function retrieve_account_id($account_name,$user_id)
{

	if($account_name=="")
	{
		return null;
	}

	$db = PearDatabase::getInstance();
	$query = "select vtiger_account.accountname accountname,vtiger_account.accountid accountid from vtiger_account inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid where vtiger_crmentity.deleted=0 and vtiger_account.accountname=?";
	$result=  $db->pquery($query, array($account_name)) or die ("Not able to execute insert");

	$rows_count =  $db->getRowCount($result);
	if($rows_count==0)
	{
		require_once('modules/Accounts/Accounts.php');
		$account = new Accounts();
		$account->column_fields[accountname] = $account_name;
		$account->column_fields[assigned_user_id]=$user_id;
		//$account->saveentity("Accounts");
		$account->save("Accounts");
		//mysql_close();
		return $account->id;
	}
	else if ($rows_count==1)
	{
		$row = $db->fetchByAssoc($result, 0);
		//mysql_close();
		return $row["accountid"];	    
	}
	else
	{
		$row = $db->fetchByAssoc($result, 0);
		//mysql_close();
		return $row["accountid"];	    
	}

}

function GetTasks($username,$session)
{
	if(!validateSession($username,$session))
	return null;

	$res = getEntityEntries('Calendar', $username);
    
	foreach($res AS $task)
	{
  		if($task["date_start"] == "0000-00-00" || $task["date_start"] == NULL)
        	{
		       	$task["date_start"] = "";
        	}
        	if($task["due_date"] == "0000-00-00" || $task["due_date"] == NULL)
        	{
		       	$task["due_date"] = "";
        	}
        
        	if($task["status"] == "Not Started")
        	{
       			$task["status"] = "0";
        	}else if($task["status"] == "In Progress")
        	{
        		$task["status"] = "1";
        	}else if($task["status"] == "Completed")
        	{
        		$task["status"] = "2";
        	}else if($task["status"] == "Deferred")
        	{
        		$task["status"] = "4";
        	}else if($task["status"] == "Pending Input" || $task["status"] == "Planned")
        	{
        		$task["status"] = "3";
        	}else
        	{
        		$task["status"] = "0";
        	}
        
        	if($task["priority"] == "High")
        	{
       			$task["priority"] = "2";
        	}else if($task["priority"] == "Low")
        	{
        		$task["priority"] = "0";
        	}else if($task["priority"] == "Medium")
        	{
        		$task["priority"] = "1";
        	}else
			{
				$task["priority"] = "2";
			}
        
		$output_list[] = Array(
						"id" => $task["id"],
						"subject" => decode_html($task["subject"]),
						"startdate" => $task["date_start"],
						"duedate" => $task["due_date"],
						"status" => decode_html($task["status"]),
						"priority" => decode_html($task["priority"]),
						"description" => decode_html($task["description"]),
						// "contactname" => decode_html($task["firstname"])." ".decode_html($task["lastname"]),
						"category" => "",        
						);
	}
	return $output_list;
}

function AddTasks($username,$session,$taskdtls)
{
	if(!validateSession($username,$session))
	return null;
	global $current_user,$adb;
	require_once('modules/Users/Users.php');
	require_once('modules/Calendar/Activity.php');
	
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");
	
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
    	$sql1 = "select fieldname,columnname from vtiger_field where tabid=9 and vtiger_field.presence in (0,2)";
		$params1 = array();
  	} else {
    	$profileList = getCurrentUserProfileList();
    	$sql1 = "select fieldname,columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=9 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)";
  		$params1 = array();
		if (count($profileList) > 0) {
			$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			array_push($params1, $profileList);
		}
	}
  	$result1 = $adb->pquery($sql1, $params1);
  
  	for($i=0;$i < $adb->num_rows($result1);$i++)
  	{
      $permitted_lists[] = $adb->query_result($result1,$i,'fieldname');
  	}
	
	$task = new Activity();
	
	foreach($taskdtls as $taskrow)
	{
	//Currently only 3 status avail Note ************************************************
   		if(isset($taskrow))
   		{
			if($taskrow["status"] == "0")
			{
				$taskrow["status"] = "Not Started";
			}else if($taskrow["status"] == "1")
			{
				$taskrow["status"] = "In Progress";
			}else if($taskrow["status"] == "2")
			{
				$taskrow["status"] = "Completed";
			}else if($taskrow["status"] == "4")
			{
				$taskrow["status"] = "Deferred";
			}else if($taskrow["status"] == "3")
			{
				$taskrow["status"] = "Planned";
			}else
			{
				$taskrow["status"] = "Not Started";
			}

			if($taskrow["priority"] == "2")
			{
				$taskrow["priority"] = "High";
			}else if($taskrow["priority"] == "0")
			{
				$taskrow["priority"] = "Low";
			}else if($taskrow["priority"] == "1")
			{
				$taskrow["priority"] = "Medium";
			}

			$start_datetime = DateTimeField::convertToDBTimeZone($taskrow["startdate"]);
			$start_date = $start_datetime->format("Y-m-d");
			$due_datetime = DateTimeField::convertToDBTimeZone($taskrow["duedate"]);
			$due_date = $due_datetime->format("Y-m-d");
		
			$task->column_fields[subject] = in_array('subject',$permitted_lists) ? $taskrow["subject"] : "";
			$task->column_fields[date_start]= in_array('date_start',$permitted_lists) ? $start_date : "";
			$task->column_fields[due_date]= in_array('due_date',$permitted_lists) ? $due_date : "";
			$task->column_fields[taskstatus]= in_array('taskstatus',$permitted_lists) ? $taskrow["status"] : "";
			$task->column_fields[taskpriority]= in_array('taskpriority',$permitted_lists) ? $taskrow["priority"] : "";
			$task->column_fields[description]= in_array('description',$permitted_lists) ? $taskrow["description"] : "";
			$task->column_fields[activitytype]="Task";
			$task->column_fields['visibility']= 'Private';
			//$task->column_fields[contact_id]= retrievereportsto($contact_name,$user_id,null); 
			$task->column_fields[assigned_user_id]= in_array('assigned_user_id',$permitted_lists) ? $user_id : "";
			$task->save("Calendar");
		  }
	}
	return $task->id;
}

function UpdateTasks($username,$session,$taskdtls)
{
	if(!validateSession($username,$session))
	return null;
	global $current_user,$adb;
	require_once('modules/Users/Users.php');
	require_once('modules/Calendar/Activity.php');
	
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");
	
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
    	$sql1 = "select fieldname,columnname from vtiger_field where tabid=9 and vtiger_field.presence in (0,2)";
  		$params1 = array();	
	} else {
    	$profileList = getCurrentUserProfileList();
    	$sql1 = "select fieldname,columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=9 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)";
  		$params1 = array();
		if (count($profileList) > 0) {
			$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			array_push($params1, $profileList);
		}
	}
  	$result1 = $adb->pquery($sql1, $params1);
  	for($i=0;$i < $adb->num_rows($result1);$i++)
  	{
      $permitted_lists[] = $adb->query_result($result1,$i,'fieldname');
  	}
  
	$task = new Activity();
	//crm-now: disable number format
	$_REQUEST['ajxaction'] = 'Plugin';
	foreach($taskdtls as $taskrow)
	{
		if(isset($taskrow))
		{
			// crm-now retrieve old task to keep assigned_to of record
			$task->retrieve_entity_info($taskrow["id"],"Calendar");
			if($taskrow["status"] == "0")
			{
				$taskrow["status"] = "Not Started";
			}else if($taskrow["status"] == "1")
			{
				$taskrow["status"] = "In Progress";
			}else if($taskrow["status"] == "2")
			{
				$taskrow["status"] = "Completed";
			}else if($taskrow["status"] == "4")
			{
				$taskrow["status"] = "Deferred";
			}else if($taskrow["status"] == "3")
			{
				$taskrow["status"] = "Planned";
			}else
			{
				$taskrow["status"] = "Not Started";
			}
        
    	if($taskrow["priority"] == "2")
			{
      	$taskrow["priority"] = "High";
   		}else if($taskrow["priority"] == "0")
   		{
   			$taskrow["priority"] = "Low";
   		}else if($taskrow["priority"] == "1")
   		{
   		 	$taskrow["priority"] = "Medium";
   		}
		
			$start_datetime = DateTimeField::convertToDBTimeZone($taskrow["startdate"]);
			$start_date = $start_datetime->format("Y-m-d");
			$due_datetime = DateTimeField::convertToDBTimeZone($taskrow["duedate"]);
			$due_date = $due_datetime->format("Y-m-d");
			
			$task->retrieve_entity_info($taskrow["id"],"Calendar");
			$task->column_fields[subject] = in_array('subject',$permitted_lists) ? $taskrow["subject"] : "";
			$task->column_fields[date_start] = in_array('date_start',$permitted_lists) ? $start_date : "";
			$task->column_fields[due_date] = in_array('due_date',$permitted_lists) ? $due_date : "";
			$task->column_fields[taskstatus] = in_array('taskstatus',$permitted_lists) ? $taskrow["status"] : "";
			$task->column_fields[taskpriority] = in_array('taskpriority',$permitted_lists) ? $taskrow["priority"] : "";
			$task->column_fields[description] = in_array('description',$permitted_lists) ? $taskrow["description"] : "";
			$task->column_fields[activitytype] = "Task";
			$task->column_fields['visibility']= 'Private';
			//$task->column_fields[contact_id]= retrievereportsto($contact_name,$user_id,null); 
			//$task->column_fields[assigned_user_id] = in_array('assigned_user_id',$permitted_lists) ? $user_id : "";

			$task->id = $taskrow["id"];
			$task->mode="edit";

			$task->save("Calendar");
		}
	}
	return $task->id;
}

function DeleteTasks($username,$session,$crmid)
{
	if(!validateSession($username,$session))
	return null;
	
	global $current_user;
	require_once('modules/Users/Users.php');
	require_once('modules/Calendar/Activity.php');
	   
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");

	$task = new Activity();
	$task->id = $crmid;
	$task->mark_deleted($task->id);
	return $task->id;     
}

function GetClndr($username,$session)
{
	if(!validateSession($username,$session))
	return null;

	$res = getEntityEntries('Events', $username);

	foreach($res AS $clndr)
	{
		if($clndr["date_start"] == "0000-00-00" || $clndr["date_start"] == NULL)
		{
			$clndr["date_start"] = "";
		}
		if($clndr["due_date"] == "0000-00-00" || $clndr["due_date"] == NULL)
		{
			$clndr["due_date"] = "";
		}

		$start_date = DateTimeField::convertToUserTimeZone($clndr["date_start"]." ".$clndr["time_start"]);
		$due_date = DateTimeField::convertToUserTimeZone($clndr["due_date"]." ".$clndr["time_end"]);
		$clndr["date_start"] = $start_date->format("Y-m-d H:i:s");
		$clndr["due_date"] = $due_date->format("Y-m-d H:i:s");

		$output_list[] = Array(
			"id" => $clndr["id"],
			"subject" => decode_html($clndr["subject"]),
			"startdate" => $clndr["date_start"],
			"duedate" => $clndr["due_date"],
			"location" => decode_html($clndr["location"]),
			"description" => decode_html($clndr["description"]),
			// "contactname" => decode_html($clndr["firstname"])." ".decode_html($clndr["lastname"]),
			"category" => "",        
		);
	}
	//$log->fatal($output_list);
	$seed_clndr = $seed_clndr;
	return $output_list;
}

function AddClndr($username,$session,$clndrdtls)
{
	if(!validateSession($username,$session))
	return null;
	global $current_user,$adb;
	require_once('modules/Users/Users.php');
	require_once('modules/Calendar/Activity.php');
	
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");
	
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
    	$sql1 = "select fieldname,columnname from vtiger_field where tabid=16 and vtiger_field.presence in (0,2)";
		$params1 = array();
  	} else {
    	$profileList = getCurrentUserProfileList();
    	$sql1 = "select fieldname,columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=16 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)";
  		$params1 = array();
		if (count($profileList) > 0) {
			$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			array_push($params1, $profileList);
		}
	}
  	$result1 = $adb->pquery($sql1, $params1);
  
  	for($i=0;$i < $adb->num_rows($result1);$i++)
  	{
      $permitted_lists[] = $adb->query_result($result1,$i,'fieldname');
  	}
  
	$clndr = new Activity();
	
	foreach($clndrdtls as $clndrow)
	{
		if(isset($clndrow))
		{
			$start_datetime = DateTimeField::convertToDBTimeZone($clndrow["startdate"]);
			$start_date = $start_datetime->format("Y-m-d");
			$start_time = $start_datetime->format("H:i");
			$due_datetime = DateTimeField::convertToDBTimeZone($clndrow["duedate"]);
			$due_date = $due_datetime->format("Y-m-d");
			$due_time = $due_datetime->format("H:i");
			
			$clndr->column_fields[subject] = in_array('subject',$permitted_lists) ? $clndrow["subject"] : "";
			$clndr->column_fields[date_start]= in_array('date_start',$permitted_lists) ? $start_date : "";
			$clndr->column_fields[due_date]= in_array('due_date',$permitted_lists) ? $due_date : "";
			$clndr->column_fields[time_start]= in_array('time_start',$permitted_lists) ? $start_time : "";
			$clndr->column_fields[time_end]= in_array('time_end',$permitted_lists) ? $due_time : "";
        
			$clndr->column_fields[location]= in_array('location',$permitted_lists) ? $clndrow["location"] : "";
			$clndr->column_fields[description]= in_array('description',$permitted_lists) ? $clndrow["description"] : "";
			$clndr->column_fields[activitytype]="Meeting";
			$clndr->column_fields[assigned_user_id]= in_array('assigned_user_id',$permitted_lists) ? $user_id : "";
			$clndr->column_fields[eventstatus]="Planned";
			$clndr->column_fields['visibility']= 'Private';
			$clndr->save("Calendar");
		}
	}
	return $clndr->id;
}

function UpdateClndr($username,$session,$clndrdtls)
{
	if(!validateSession($username,$session))
	return null;
	global $current_user;
	global $adb,$log;
	require_once('modules/Users/Users.php');
	require_once('modules/Calendar/Activity.php');
	
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");
	
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
    	$sql1 = "select fieldname,columnname from vtiger_field where tabid=16 and vtiger_field.presence in (0,2)";
		$params1 = array();
  	} else {
    	$profileList = getCurrentUserProfileList();
    	$sql1 = "select fieldname,columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=16 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_field.presence in (0,2)";
  		$params1 = array();
		if (count($profileList) > 0) {
			$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			array_push($params1, $profileList);
		}
	}
  	$result1 = $adb->pquery($sql1, $params1);
  
  	for($i=0;$i < $adb->num_rows($result1);$i++)
  	{
      $permitted_lists[] = $adb->query_result($result1,$i,'fieldname');
  	}
	
	$clndr = new Activity();
	//crm-now: disable number format
	$_REQUEST['ajxaction'] = 'Plugin';
	foreach($clndrdtls as $clndrow)
	{
		if(isset($clndrow))
		{
			$start_datetime = DateTimeField::convertToDBTimeZone($clndrow["startdate"]);
			$start_date = $start_datetime->format("Y-m-d");
			$start_time = $start_datetime->format("H:i");
			$due_datetime = DateTimeField::convertToDBTimeZone($clndrow["duedate"]);
			$due_date = $due_datetime->format("Y-m-d");
			$due_time = $due_datetime->format("H:i");
			
			// retrieve entry to keep assigned_to id
			$clndr->retrieve_entity_info($clndrow["id"],"Calendar");
			$clndr->column_fields[subject] = in_array('subject',$permitted_lists) ? $clndrow["subject"] : "";
			$clndr->column_fields[date_start]= in_array('date_start',$permitted_lists) ? $start_date : "";
			$clndr->column_fields[due_date]= in_array('due_date',$permitted_lists) ? $due_date : "";
			$clndr->column_fields[time_start]= in_array('time_start',$permitted_lists) ? $start_time : "";
			$clndr->column_fields[time_end]= in_array('time_end',$permitted_lists) ? $due_time : "";
			//$clndr->column_fields[duration_hours]= in_array('duration_hours',$permitted_lists) ? $stimeduehr : "";       
			//$clndr->column_fields[duration_minutes]= in_array('duration_minutes',$permitted_lists) ? $stimeduemin : "";              
			$clndr->column_fields[location]= in_array('location',$permitted_lists) ? $clndrow["location"] : "";
			$clndr->column_fields[description]= in_array('description',$permitted_lists) ? $clndrow["description"] : "";
			$clndr->column_fields[activitytype]="Meeting";
			$clndr->column_fields['visibility']= 'Private';
			//$clndr->column_fields[assigned_user_id]= in_array('assigned_user_id',$permitted_lists) ? $user_id : "";
			$clndr->id = $clndrow["id"];
			$clndr->mode="edit";
			$clndr->save("Calendar");
		}
	}
	return $clndr->id;
}

function DeleteClndr($username,$session,$crmid)
{
	if(!validateSession($username,$session))
	return null;
	
	global $current_user;
	require_once('modules/Users/Users.php');
	require_once('modules/Calendar/Activity.php');
	   
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");

	$clndr = new Activity();
	$clndr->id = $crmid;
	$clndr->mark_deleted($clndr->id);
	return $clndr->id;     
}
 
function unsetServerSessionId($id)
{
	global $adb;
	$adb->println("Inside the function unsetServerSessionId");

	$id = (int) $id;

	$adb->query("delete from vtiger_soapservice where type='Outlook' and id=$id");

	return;
}
function validateSession($username, $sessionid)
{
	global $adb,$current_user;
	$adb->println("Inside function validateSession($username, $sessionid)");
	require_once("modules/Users/Users.php");
	$seed_user = new Users();
	$id = $seed_user->retrieve_user_id($username);

	$server_sessionid = getServerSessionId($id);

	$adb->println("Checking Server session id and customer input session id ==> $server_sessionid == $sessionid");

	if($server_sessionid == $sessionid)
	{
		$adb->println("Session id match. Authenticated to do the current operation.");
		return true;
	}
	else
	{
		$adb->println("Session id does not match. Not authenticated to do the current operation.");
		return false;
	}
}
function getServerSessionId($id)
{
	global $adb;
	$adb->println("Inside the function getServerSessionId($id)");

	//To avoid SQL injection we are type casting as well as bound the id variable. In each and every function we will call this function
	$id = (int) $id;

	$query = "select * from vtiger_soapservice where type='Outlook' and id={$id}";
	$sessionid = $adb->query_result($adb->query($query),0,'sessionid');

	return $sessionid;
}
/* Begin the HTTP listener service and exit. */ 
if (!isset($HTTP_RAW_POST_DATA)){
	$HTTP_RAW_POST_DATA = file_get_contents('php://input');
}
$server->service($HTTP_RAW_POST_DATA); 
exit();

function SOAP_Encode($text)
{	
	$text = decode_html($text);
	$seek[0]='/&/';
	$seek[1]='/</';
	$seek[2]='/>/';
	
	$replace[0]='&amp;';
	$replace[1]='&lt;';
	$replace[2]='&gt;';
	
	return preg_replace($seek, $replace, $text);
}

function getEntityEntries($module, $username, $oWhere = false) {
	global $current_user;
	$current_user = new Users();
	$user_id = $current_user->retrieve_user_id($username);
	$current_user = $current_user->retrieveCurrentUserInfoFromFile($user_id);

	$arr_ret = array();
	$tmp_arr = array();

	$query = "SELECT COUNT(*) FROM $module";
	if ($oWhere) {
		$query .= " WHERE $oWhere";
	}
	$query .= ";";
	try {
		$res = vtws_query($query, $current_user);
	} catch (Exception $e) {
		return $arr_ret;
	}
	
	
	$c = 0;
	$counter = $res[0]['count'];
	while ($c < $counter) {
		$query = "SELECT * FROM $module";
		if ($oWhere) {
			$query .= " WHERE $oWhere";
		}
		$query .= " LIMIT $c,100;";
		$res = vtws_query($query, $current_user);
		if ($module == 'Contacts') {
			foreach ($res AS &$cont) {
				if (isset($cont['account_id'])) {
					if (!empty($cont['account_id'])) {
						$id = $cont['account_id'];
						if (!isset($tmp_arr[$id])) {
							$tmp = getEntityEntries('Accounts', $username, "id = '$id'");
							$tmp_arr[$id] = $tmp[0]['accountname'];
						}
						$cont['accountname'] = $tmp_arr[$id];
					}
				} else {
					break;
				}
			}
		}
		$arr_ret = array_merge($arr_ret, $res);
		$c += 100;
	}
	foreach ($arr_ret AS &$ret) {
		$tmp = explode("x", $ret['id']);
		$crmid = $tmp[1];
		$ret['id'] = $crmid;
	}
	return $arr_ret;
}

?>