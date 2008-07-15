<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_magentoimport_cat"] = array (
	"ctrl" => $TCA["tx_magentoimport_cat"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,fromold,tonew"
	),
	"feInterface" => $TCA["tx_magentoimport_cat"]["feInterface"],
	"columns" => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		"fromold" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:magentoimport/locallang_db.xml:tx_magentoimport_cat.fromold",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tt_products_cat",	
				"foreign_table_where" => "ORDER BY tt_products_cat.title",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"tonew" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:magentoimport/locallang_db.xml:tx_magentoimport_cat.tonew",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1;;1-1-1, fromold, tonew")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);
?>
