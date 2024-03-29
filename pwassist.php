<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
* Password assistance facility for users who have forgotten their password
* or for users for whom no password has been assigned yet.
*
* @author Werner Randelshofer <wrandels@hsw.fhz.ch>
* @version $Id: pwassist.php 33447 2012-03-01 14:00:01Z jluetzen $
*
* @package ilias-core
*/

require_once("Services/Init/classes/class.ilInitialisation.php");
ilInitialisation::initILIAS();

$ilCtrl->initBaseClass("ilStartUpGUI");
$ilCtrl->setTargetScript("ilias.php");
$ilCtrl->setCmd("jumpToPasswordAssistance");
$ilCtrl->callBaseClass();
$ilBench->save();

exit;

?>