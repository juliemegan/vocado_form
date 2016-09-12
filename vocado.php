<?php

//DocumentFA-BirthCertificate.xml
$doc_fa_birth_certificate_array = array (
	"vc:MessageHeaders" => array (
		"vc:MessageId" => "4195fd26-463d-9836-91d5-14831a0451e5",
		"vc:CreationDateTime" => "2016-05-12T17:20:15.000-07:00"
	),
	"ExternalStudentId" => "0110141638637",
	"ExternalDocumentId" => "342774830",
	"DocumentCode" => "FA-BirthCertificate",
	"DateReviewed" => "2012-07-16T19:20:30.450+01:00",
	"DateReceived" => "2012-07-16T19:20:30.450+01:00",
	"Reviewer" => "John",
	"DocumentStatus" => "Acceptable",
	"DocumentSource" => "ExP",
	"ElectronicSourceFlag" => "true",
	"MetadataFields" => array(
		array(
			"FieldCode" => "AC59",
			"FieldName" => "Student First Name",
			"FieldValue" => "Jamas"
		),
		array(
			"FieldCode" => "AC191",
			"FieldName" => "Middle Name",
			"FieldValue" => ""
		),
		array(
			"FieldCode" => "AC79",
			"FieldName" => "Student Last Name",
			"FieldValue" => "Bond"
		),
		array(
			"FieldCode" => "AC410",
			"FieldName" => "Gender",
			"FieldValue" => ""
		),
		array(
			"FieldCode" => "AC31",
			"FieldName" => "Date of Birth",
			"FieldValue" => "1976-08-23"
		)
	),
	"DocumentComments" => "BirthCertDocument"
);
/*
<?xml version="1.0" encoding="utf-8"?>
<FasDocumentReceiptEvent xmlns="http://www.vocado.com/vm/fas" xmlns:vc="http://www.vocado.com/vm/common">
	<vc:MessageHeaders>
		<vc:MessageId>4195fd26-463d-9836-91d5-14831a0451e5</vc:MessageId>
		<vc:CreationDateTime>2016-05-12T17:20:15.000-07:00</vc:CreationDateTime>
	</vc:MessageHeaders>
	<ExternalStudentId>0110141638637</ExternalStudentId>
	<ExternalDocumentId>342774830</ExternalDocumentId>
	<DocumentCode>FA-BirthCertificate</DocumentCode>
	<DateReviewed>2012-07-16T19:20:30.450+01:00</DateReviewed>
	<DateReceived>2012-07-16T19:20:30.450+01:00</DateReceived>
	<Reviewer>John</Reviewer>
	<DocumentStatus>Acceptable</DocumentStatus>
	<DocumentSource>ExP</DocumentSource>
	<ElectronicSourceFlag>true</ElectronicSourceFlag>
	<MetadataFields>
		<MetadataField>
			<FieldCode>AC59</FieldCode>
			<FieldName>Student First Name</FieldName>
			<FieldValue>Jamas</FieldValue>
		</MetadataField>
		<MetadataField>
			<FieldCode>AC191</FieldCode>
			<FieldName>Middle Name</FieldName>
			<FieldValue/>
		</MetadataField>
		<MetadataField>
			<FieldCode>AC79</FieldCode>
			<FieldName>Student Last Name</FieldName>
			<FieldValue>Bond</FieldValue>
		</MetadataField>
		<MetadataField>
			<FieldCode>AC410</FieldCode>
			<FieldName>Gender</FieldName>
			<FieldValue></FieldValue>
		</MetadataField>
		<MetadataField>
			<FieldCode>AC31</FieldCode>
			<FieldName>Date of Birth</FieldName>
			<FieldValue>1976-08-23</FieldValue>
		</MetadataField>
	</MetadataFields>
	<DocumentComments>BirthCertDocument</DocumentComments>
</FasDocumentReceiptEvent>
*/



















$users_array = array(
		"total_users" => 3,
		"users" => array(
				array(
						"id" => 1,
						"name" => "Nitya",
						"address" => array(
								"country" => "India",
								"city" => "Kolkata",
								"zip" => 700102,
						)
				),
				array(
						"id" => 2,
						"name" => "John",
						"address" => array(
								"country" => "USA",
								"city" => "Newyork",
								"zip" => "NY1234",
						)
				),
				array(
						"id" => 3,
						"name" => "Viktor",
						"address" => array(
								"country" => "Australia",
								"city" => "Sydney",
								"zip" => 123456,
						)
				),
		)
);

// function defination to convert array to xml
// http://www.codexworld.com/convert-array-to-xml-in-php/
function array_to_xml($array, &$xml_user_info) {
    foreach($array as $key => $value) {
        if(is_array($value)) {
            if(!is_numeric($key)){
                $subnode = $xml_user_info->addChild("$key");
                array_to_xml($value, $subnode);
            }else{
                //$subnode = $xml_user_info->addChild("item$key");
                $subnode = $xml_user_info->addChild("MetadataField");
                array_to_xml($value, $subnode);
            }
        }else {
            $xml_user_info->addChild("$key",htmlspecialchars("$value"));
        }
    }
}

//creating object of SimpleXMLElement
$xml_user_info = new SimpleXMLElement("<?xml version=\"1.0\"?><user_info></user_info>");
$doc_fa_birth_certificate_info = new SimpleXMLElement(
		"<?xml version=\"1.0\" encoding=\"utf-8\"?><FasDocumentReceiptEvent></FasDocumentReceiptEvent>");

//function call to convert array to xml
//array_to_xml($users_array,$xml_user_info);
array_to_xml($doc_fa_birth_certificate_array,$doc_fa_birth_certificate_info);
$doc_fa_birth_certificate_info->addAttribute('xmlns:xmlnsaa', "http://www.vocado.com/vm/fas");
$doc_fa_birth_certificate_info->addAttribute('xmlns:xmlnsvc', "http://www.vocado.com/vm/common");

//saving generated xml file
//$xml_file = $xml_user_info->asXML('users.xml');
$xml_file = $doc_fa_birth_certificate_info->asXML('doc_fa_birth_certificate.xml');

//success and error message based on xml creation
if($xml_file){
    echo 'XML file has been generated successfully.';
}else{
    echo 'XML file generation error.';
}

?>