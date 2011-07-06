<?php
		require_once 'kibu/core/class/Database.php';
		require_once 'kibu/core/class/Utility.php';
		require_once 'kibu/core/class/Url.php';
		require_once 'kibu/core/class/Date.php';
		require_once 'kibu/core/class/Content.php';
		require_once 'kibu/core/class/GlobalContent.php';
		require_once 'kibu/core/class/Template.php';
		require_once 'kibu/core/class/Cookie.php';
		require_once 'kibu/core/class/Authentication.php';
		require_once 'kibu/core/class/Module.php';
		require_once 'kibu/core/class/Navigation.php';
		require_once 'kibu/core/includes/global_routines.php';
		require_once 'kibu/core/class/PageSettings.php';
		require_once 'kibu/core/class/EditorToolbar.php';

		class Kibu {

				protected $_template;
				protected $_year;
				protected $_contentArray;
				protected $_constants;
				protected $_additionalHead;
				protected $_additionalFoot;
				protected $globalNav;
				protected $currentNav;
				protected $_content;
				protected $_pageContent;
				protected $_globalContent;
				protected $_contentType;
				protected $_pageTitle;
				protected $_editorToolbar;
				protected $_bodyExtra;

				protected $db;
				protected $url;
				protected $util;
				public $siteConfig;
				public static $auth;
				protected $date;
				protected $content;
				public $module;
				public $navigation;
				public $additionalHead;
				public $additionalFoot;
				protected $tpl;

				public function __construct($db, $url) {
						$this->db = $db; // instantiate Database class, connect to database
						$this->url = $url; // instantiate URL class
						$this->date = new Date();
				}

				public function testConfig() {
						if($this->db->connected && $this->url->configured) {
								$query = "SELECT contentRecords.contentID FROM contentRecords, siteConfig WHERE contentRecords.siteConfigID = siteConfig.siteConfigID";
								$this->db->setQuery($query);
								if($this->db->getNumRows() > 0 ) {
										return true;
								}
								else {
										return false;
								}
						}
						else {
								return false;
						}

				}

				public function setCore() {
						$this->auth = new Authentication(); // instantiate Authentication class
						$this->tpl = new Template('./kibu/templates/'); // instantiate Template class
						$this->siteConfig = $this->url->siteConfig; // instantiate Constants class
						$this->content = new Content($this->siteConfig); // instantiate Content class
						$this->_contentArray = $this->content->getContentRecord(); // generate array of content record values based on Content class
						$this->_contentType = $this->content->getContentType(); // generate array of content type values based on Content class
						$this->_globalContent = new GlobalContent($this->_contentArray);

						if($this->content->getAuthLevel() > $this->auth->getUserLevel()) {
								$this->_bodyExtra = "onLoad=\"Modalbox.show('/modal.php?class=LoginLogout&amp;mode=login&amp;restrictedcontent=true&amp;curPage=".$this->url->getCurPage() ."', {title: 'Login', width: 700, overlayClose: false, overlayOpacity: 90}); return false;\"";
						}
						else {
								$this->_pageContent = $this->content->getContentBody();
						}

						$this->module = new Module();
						$this->setNavigation();

						$this->_year = $this->date->__get('fullyear');
						$this->_editorToolbar = new EditorToolbar($this->content->getContentRecord());
						$this->additionalPageHead($this->_editorToolbar->outputJSLink());

						$this->_pageTitle = $this->content->pageTitle($this->siteConfig['siteAddress'], $this->siteConfig['siteTagLine']);

						$kibuCredit = new Template('./kibu/templates/');
						$this->_additionalFoot .= $kibuCredit->fetch('kibu_credit.tpl.php');
				}

				private function setNavigation() {
						$this->navigation = new Navigation($this->siteConfig, $this->content->getContentRecordValue('sectionID')); // instantiate Navigation class
						$this->globalNav = $this->navigation->getGlobalNav(); // set globalNav variable
						$this->currentNav = $this->navigation->getCurrentNav(); // set currentNav variable
				}

				public function override($var, $val) {
						$this->$var = $val;
				}

				public function additionalPageHead($item) {
						$this->_additionalHead .= $item;
				}

				public function getAdditionalPageHead() {
						return $this->_additionalHead;
				}

				public function additionalPageFoot($item) {
						$this->_additionalFoot .= $item;
				}

				public function getAdditionalPageFoot() {
						return $this->_additionalFoot;
				}

				public function outputPage() {
						$this->tpl->set_vars($this->_contentArray, true); // set contentArray Template class variables
						$this->tpl->set_vars($this->siteConfig); // set constants to Template class variables
						$this->tpl->set('year', $this->_year); // get this year from Date class
						$this->tpl->set('additionalHead', $this->_additionalHead);
						$this->tpl->set('additionalFoot', $this->_additionalFoot);
						$this->tpl->set('globalNav', $this->globalNav);
						$this->tpl->set('currentNav', $this->currentNav);
						$this->tpl->set('pageContent', $this->_pageContent);
						$this->tpl->set('globalContent', $this->_globalContent->getGlobalAssets());
						$this->tpl->set('pageTitle', $this->_pageTitle);
						$this->tpl->set('editorToolbar', $this->_editorToolbar->outputToolbar());
						$this->tpl->set('welcomeMessage', $this->auth->welcomeMessage());
						$this->tpl->set('bodyExtra', $this->_bodyExtra);
						echo $this->tpl->fetch($this->_contentType['templateLink'] . '.tpl.php'); // echo the results to output the assembled page, using the content template link from first query as master template.
				}
		}

?>
