<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE == 'BE')	{
		
	t3lib_extMgm::addModule('tools','txmagentoimportM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
}


t3lib_extMgm::allowTableOnStandardPages('tx_magentoimport_cat');

$TCA["tx_magentoimport_cat"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:magentoimport/locallang_db.xml:tx_magentoimport_cat',		
		'label'     => 'fromold',
	  'label_alt' => 'tonew',
	  'label_alt_force' => 1,					
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_magentoimport_cat.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "hidden, fromold, tonew",
	)
);
?>
