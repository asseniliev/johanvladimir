<?php

include("../../core/Pager.php");
include("../../core/Debug.php");

session_start();

if(!$_SESSION['Pager']) {
  $data = array(0=>"Item row 0<br />",
		1=>"Item row 1<br />",
		2=>"Item row 2<br />",
		3=>"Item row 3<br />",
		4=>"Item row 4<br />",
		5=>"Item row 5<br />",
		6=>"Item row 6<br />",
		7=>"Item row 7<br />",
		8=>"Item row 8<br />",
		9=>"Item row 9<br />",
		10=>"Item row 10<br />",
		11=>"Item row 11<br />",
		12=>"Item row 12<br />",
		13=>"Item row 13<br />",
		14=>"Item row 14<br />",
		15=>"Item row 15<br />",
		16=>"Item row 16<br />",
		17=>"Item row 17<br />",
		18=>"Item row 18<br />",
		19=>"Item row 19<br />",
		20=>"Item row 20<br />",
		21=>"Item row 21<br />",
		22=>"Item row 22<br />",
		23=>"Item row 23<br />",
		24=>"Item row 24<br />",
		25=>"Item row 25<br />",
		26=>"Item row 26<br />",
		27=>"Item row 27<br />",
		28=>"Item row 28<br />",
		29=>"Item row 29<br />",
		30=>"Item row 30<br />",
		31=>"Item row 31<br />",
		32=>"Item row 32<br />",
		33=>"Item row 33<br />",
		34=>"Item row 34<br />",
		35=>"Item row 35<br />",
		36=>"Item row 36<br />",
		37=>"Item row 37<br />",
		38=>"Item row 38<br />",
		39=>"Item row 39<br />",
		40=>"Item row 40<br />");
  
  $_SESSION['Pager'] = new PHPWS_Pager;
  $_SESSION['Pager']->setData($data);
  $_SESSION['Pager']->setLinkBack("./pager_example.php?");
}

//echo PHPWS_Debug::testObject($_SESSION['Pager']) . "<br />";

$_SESSION['Pager']->pageData();

echo $_SESSION['Pager']->getData();
echo "<br />" . $_SESSION['Pager']->getBackLink() . " " . $_SESSION['Pager']->getSectionLinks() . " " . $_SESSION['Pager']->getForwardLink() . "<br />";
echo $_SESSION['Pager']->getSectionInfo() . "<br />";
echo $_SESSION['Pager']->getLimitLinks();

?>

