<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Georg Ringer <http://www.cyberhouse.at>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');

$LANG->includeLLFile('EXT:magentoimport/mod1/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]



/**
 * Module 'Magento Importer' for the 'magentoimport' extension.
 *
 * @author	Georg Ringer <http://www.cyberhouse.at>
 * @package	TYPO3
 * @subpackage	tx_magentoimport
 */
class  tx_magentoimport_module1 extends t3lib_SCbase {
				var $pageinfo;

				/**
				 * Initializes the Module
				 * @return	void
				 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					parent::init();

					/*
					if (t3lib_div::_GP('clear_all_cache'))	{
						$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
					}
					*/
				}

				/**
				 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
				 *
				 * @return	void
				 */
				function menuConfig()	{
					global $LANG;
					$this->MOD_MENU = Array (
						'function' => Array (
							'1' => $LANG->getLL('function1'),
							'2' => $LANG->getLL('function2'),
							'3' => $LANG->getLL('function3'),
							'4' => $LANG->getLL('function4'),							
						)
					);
					parent::menuConfig();
				}

				/**
				 * Main function of the module. Write the content to $this->content
				 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
				 *
				 * @return	[type]		...
				 */
				function main()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					// Access check!
					// The page will show only if there is a valid page and if this page may be viewed by the user
					$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
					$access = is_array($this->pageinfo) ? 1 : 0;

					if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

							// Draw the header.
						$this->doc = t3lib_div::makeInstance('mediumDoc');
						$this->doc->backPath = $BACK_PATH;


							// JavaScript
						$this->doc->JScode = '
							<script language="javascript" type="text/javascript">
								script_ended = 0;
								function jumpToUrl(URL)	{
									document.location = URL;
								}
							</script>
							<link type="text/css" rel="stylesheet" href="res/styles.css" media="all"></link>
						';
						$this->doc->postCode='
							<script language="javascript" type="text/javascript">
								script_ended = 1;
								if (top.fsMod) top.fsMod.recentIds["web"] = 0;
							</script>
						';

						$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);
						$headerSection = '';

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
						$this->content.=$this->doc->divider(5);

						// Render content:
						$this->moduleContent();

						// ShortCut
						if ($BE_USER->mayMakeShortcut())	{
							// $this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
						}

						$this->content.=$this->doc->spacer(10);
					} else {
							// If no access or if ID == zero

						$this->doc = t3lib_div::makeInstance('mediumDoc');
						$this->doc->backPath = $BACK_PATH;

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->spacer(10);
					}
				}

				/**
				 * Prints out the module HTML
				 *
				 * @return	void
				 */
				function printContent()	{

					$this->content.=$this->doc->endPage();
					echo $this->content;
				}

				/**
				 * Generates the module content
				 *
				 * @return	void
				 */
				function moduleContent()	{
					switch((string)$this->MOD_SETTINGS['function'])	{
						case 1:
							$content= $this->getFirstPage();
							$this->content.=$this->doc->section($this->ll('function1'),$content,0,1);
						break;
						case 2:
							$content= $this->createCustomer();
							$this->content.=$this->doc->section($this->ll('function2'),$content,0,1);
						break;
						case 3:
							$content= $this->createProducts();
							$this->content.=$this->doc->section($this->ll('function3'),$content,0,1);
						break;
						case 4:
							$content= $this->getHelp();
							$this->content.=$this->doc->section($this->ll('function4'),$content,0,1);
						break;

					}
				}

				/**
				 * Start page with some information
				 * @return void
				 */				
				function getFirstPage() {
					$content.= '<div class="magentoimport-img">
												<a href="http://www.magentocommerce.com/" target="_blank">
													<img src="res/magentologo.gif" /><br />
													Magento<br />Open Source eCommerce Evolved
												</a>
												
											</div>'
											.$this->ll('text_about1').
										 '<div class="magentoimport-img">
										 		<a href="http://www.cyberhouse.at/" target="_blank">
										 			<img src="res/cyberhouselogo.gif" /><br />
										 			CYBERhouse<br />Agentur für interaktive Kommunikation GmbH
												</a>
											</div>'
											.$this->ll('text_about2');
					
					return $content;
				}

				/**
				 * Export the products
				 * @return void
				 */								
				function createProducts() {
					$vars = t3lib_div::_POST();
				
						// create the form to start the transformation
					$content.= $this->ll('text_products').
										 '<br /><br />
										 <form action="" method="post" class="magentoimport-form">
											<fieldset>
												<legend>'.$this->ll('function3').'</legend>
												<label for="truncate"><input type="checkbox" name="truncate" value="1" id="truncate" />'.$this->ll('truncate').'</label>
												<input type="hidden" name="start" value="1" /><br /><br />
												<input class="submit" type="submit" value="'.$this->ll('submit').'" />
											</fieldset>
											</form>
											';	
											
						// Let the transformation begin
					if ($vars['start']==1) {
						$all = 0;
						$imported = 0;
						
							// truncate the table if checkbox activated
						if ($vars['truncate']==1) {
							$this->truncateExportTable('magento_products');
						}
						
							// get the categories just once
						$catList = array();
						$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('fromold, tonew','tx_magentoimport_cat', 'deleted=0 AND hidden=0');
						while($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)) {
							$catList[$row2['fromold']] = $row2['tonew'];
						}					
						
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tt_products_kopie', 'deleted=0 AND hidden=0');
						
						while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
							$all++;
							$insert = array();
							$insert['store'] = 'default' ;
							$insert['attribute_set'] = 'Domino' ;
							$insert['type'] = 'Simple' ;
							$insert['sku'] = $row['itemnumber'] ;
							$insert['description'] = $row['note_lang'];
							$insert['short_description'] = $row['note'] ? $row['note'] : $row['note_lang'];
							$insert['status'] = 'Aktiviert';
							$insert['tax_class_id'] = 'Taxable Goods';
							$insert['visibility'] = 'Katalog, Suche';
							$insert['price'] = $row['price'] ;
							$insert['cost'] = '';
							$insert['name'] = $row['title'].' '.$row['itemnumber_txt'];
							$insert['qty'] = 100;
							$insert['is_in_stock'] = 1;
							$insert['manufacturer'] = $row['hersteller'] ;
							$insert['oldshopid'] = $row['uid'];
							$insert['image'] = '/images/'.$row['image'];
							$insert['small_image'] = '/icons/'.$row['image_kl'];
							$insert['thumbnail'] = '/icons/'.$row['image_kl'];
							
								// hair quality
							$hair = array();
							if ($row['strapaziert']==1) $hair[] = 'strapaziertes Haar';
							if ($row['blondiert']==1) $hair[] = 'blondiertes Haar';
							if ($row['coloriert']==1) $hair[] = 'coloriertes Haar';
							if ($row['dauerwelle']==1) $hair[] = 'dauergewelltes Haar';
							if ($row['empfindlich']==1) $hair[] = 'empfindliche Kopfhaut';
							if ($row['feines']==1) $hair[] = 'feines Haar';
							if ($row['fettig']==1) $hair[] = 'fettiges Haar';
							if ($row['trocken']==1) $hair[] = 'trockenes Haar';
							if ($row['schuppig']==1) $hair[] = 'schuppiges Haar';
							if ($row['haarausfall']==1) $hair[] = 'Haarausfall';

							$insert['hairquality'] = implode(' , ', $hair) ;
							
								// targetgroups
							$target = array();
							if ($row['maenner']==1) $target[] = 'Für den Mann';
							if ($row['youngg']==1) $target[] = 'Für die Young Generation';
							if ($row['geschenk']==1) $target[] = 'Geschenkideen';
							$insert['targetgroup'] = implode(' , ', $target) ;		
							
							
							$insert['category_ids'] = $catList[$row['category']];
							
								// write the product if there is a category id
							if ($insert['category_ids']!='') {
								$imported++;
								$GLOBALS['TYPO3_DB']->exec_INSERTquery('magento_products', $insert);							
							}
							
						} // end while
						
								// success msg 
							if ($vars['truncate']==1) {
								$messages[] = $this->ll('product_msg_truncate_yes');
							}		
							$messages[] = sprintf($this->ll('product_msg_count'), $this->strong($all), $this->strong($imported)); 
							
							$content.= '<div class="magentoimport-success">
													'.implode('<br />',$messages).'
													</div>';							
						
					} // end if

					return $content;
				}
				
				/**
				 * Export the customers
				 * @return void
				 */								
				function createCustomer() {
					$vars = t3lib_div::_POST();
				
						// create the form to start the transformation
					$content.= $this->ll('text_customer').
										 '<br /><br />
										 <form action="" method="post" class="magentoimport-form">
											<fieldset>
												<legend>'.$this->ll('function2').'</legend>
												<label for="truncate"><input type="checkbox" name="truncate" value="1" id="truncate" />'.$this->ll('truncate').'</label>
												<input type="hidden" name="start" value="1" /><br /><br />
												<input class="submit" type="submit" value="'.$this->ll('submit').'" />
											</fieldset>
											</form>
											';	
											
						// Let the transformation begin
					if ($vars['start']==1) {
						$all = 0;
						$imported = 0;
						
							// truncate the table if checkbox activated
						if ($vars['truncate']==1) {
							$this->truncateExportTable('magento_users');
						}

						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tt_products_customer', '');
						
						while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
							$all++;
							
							$insert = array();
							
							$street = $row['street'].' '.$row['adress_addon'];
							
							$insert['website'] = 'base';
							$insert['email'] = $row['email'];
							$insert['group_id'] = 'general';
							$insert['firstname'] = $row['firstname'];
							$insert['lastname'] = $row['lastname'];
							$insert['password_hash'] = $row['pwd'];
							$insert['billing_firstname'] = $row['firstname'];
							$insert['billing_lastname'] = $row['lastname'];
							$insert['billing_company'] = '';
							$insert['billing_street1'] = $street;
							$insert['billing_street2'] = '';
							$insert['billing_city'] = $row['city'];
							$insert['billing_region'] = '';
							$insert['billing_country'] = $row['country'];
							$insert['billing_postcode'] = $row['postcode'];
							$insert['billing_telephone'] = '';
							$insert['billing_fax'] = $row['phone'];
							$insert['shipping_firstname'] = $row['delivery_firstname'];
							$insert['shipping_lastname'] = $row['delivery_lastname'];
							$insert['shipping_company'] = '';
							$insert['shipping_street1'] = $row['delivery_street'].' '.$row['delivery_adress_addon'];
							$insert['shipping_street2'] = '';
							$insert['shipping_city'] = $row['delivery_city'];
							$insert['shipping_region'] = '';
							$insert['shipping_country'] = $row['delivery_city'];
							$insert['shipping_postcode'] = $row['delivery_postcode'];
							$insert['shipping_telephone'] = '';
							$insert['shipping_fax'] = '';
							$insert['created_in'] = 'default';
							$insert['is_subscribed'] = $row['domino_nl'];
							
							$GLOBALS['TYPO3_DB']->exec_INSERTquery('magento_users', $insert);
						}
																	
							// success msg 
						if ($vars['truncate']==1) {
							$messages[] = $this->ll('customer_msg_truncate_yes');
						}		
						$messages[] = sprintf($this->ll('customer_msg_count'), $this->strong($all)); 
						
						$content.= '<div class="magentoimport-success">
												'.implode('<br />',$messages).'
												</div>';							
						
					} // end if

					return $content;

				}

				/**
				 * Get some help text
				 * @return string
				 */				
				function getHelp() {
					$content.= $this->ll('text_help');
					
					return $content;
				}
								
				/**
				 * Truncates the temporary export table
				 * @param	string		$table: the table which should get truncated
				 * @return void
				 */
				function truncateExportTable($table) {
					if ($table) {
						$query = 'truncate table '.$table;
						$GLOBALS['TYPO3_DB']->sql_query($query);
					}
				}
					
				/**
		  	 * Just a shorter localization
		  	 *
		  	 * @param	string		$val: the id of the localized item
		  	 * @return	string		localized value
		  	 */
				function ll($val) {
			    return $GLOBALS['LANG']->getLL($val);
			  }

				/**
		  	 * Make a string bold
		  	 *
		  	 * @param	string		$val: the string
		  	 * @return	string		the bolded string
		  	 */			  
			  function strong($val) {
					return '<strong>'.$val.'</strong>';
				}
				
			}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/magentoimport/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/magentoimport/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_magentoimport_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>
