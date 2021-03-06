<?php

/**
 * @file plugins/generic/pln/classes/form/PLNSettingsForm.inc.php
 *
 * Copyright (c) 2013-2017 Simon Fraser University Library
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PLNSettingsForm
 * @ingroup plugins_generic_pln_classes_form
 *
 * @brief Form for journal managers to modify PLN plugin settings
 */
import('lib.pkp.classes.form.Form');

class PLNSettingsForm extends Form {

	/**
	 * @var $_contextId int
	 */
	var $_contextId;

	/**
	 * @var $plugin object
	 */
	var $_plugin;

	/**
	 * Constructor
	 * @param $plugin object
	 * @param $contextId int
	 */
	function __construct($plugin, $contextId) {
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;
		parent::__construct($plugin->getTemplateResource('settings.tpl'));
	}

	/**
	 * Initialize form data.
	 */
	function initData() {
		$contextId = $this->_contextId;
		if (!$this->_plugin->getSetting($contextId, 'terms_of_use')) {
			$this->_plugin->getServiceDocument($contextId);
		}
		$this->setData('terms_of_use', unserialize($this->_plugin->getSetting($contextId, 'terms_of_use')));
		$this->setData('terms_of_use_agreement', unserialize($this->_plugin->getSetting($contextId, 'terms_of_use_agreement')));
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('terms_agreed'));
		
		$terms_agreed = $this->getData('terms_of_use_agreement');
		if ($this->getData('terms_agreed')) {
			foreach (array_keys($this->getData('terms_agreed')) as $term_agreed) {
				$terms_agreed[$term_agreed] = gmdate('c');
			}
			$this->setData('terms_of_use_agreement', $terms_agreed);
		}
	}

	/**
	 * Check for the prerequisites for the plugin, and return a translated
	 * message for each missing requirement.
	 *
	 * @return array
	 */
	function _checkPrerequisites() {
		$messages = array();

		if( ! @include_once('Archive/Tar.php')) {
			$messages[] = __('plugins.generic.pln.notifications.archive_tar_missing');
		}
		if( ! $this->_plugin->curlInstalled()) {
			$messages[] = __('plugins.generic.pln.notifications.curl_missing');
		}
		if( ! $this->_plugin->zipInstalled()) {
			$messages = __('plugins.generic.pln.notifications.zip_missing');
		}
		if( ! $this->_plugin->cronEnabled()) {
			$messages = __('plugins.generic.pln.settings.acron_required');
		}
		return $messages;
	}

	/**
	 * Fetch the form.
	 * @copydoc Form::fetch()
	 */
	function fetch($request, $template = null, $display = false) {
		$context = $request->getContext();
		$issn = '';
		if ($context->getSetting('onlineIssn')) {
			$issn = $context->getSetting('onlineIssn');
		} else if ($context->getSetting('printIssn')) {
			$issn = $context->getSetting('printIssn');
		}
		$hasIssn = false;
		if ($issn != '') {
			$hasIssn = true;
		}
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign(array(
			'pluginName' => $this->_plugin->getName(),
			'hasIssn' => $hasIssn,
			'prerequisitesMissing' => $this->_checkPrerequisites(),
			'journal_uuid' => $this->_plugin->getSetting($this->_contextId, 'journal_uuid'),
			'terms_of_use' => unserialize($this->_plugin->getSetting($this->_contextId, 'terms_of_use')),
			'terms_of_use_agreement' => $this->getData('terms_of_use_agreement'),
		));

		return parent::fetch($request, $template, $display);
	}

	/**
	 * @see Form::execute()
	 */
	function execute() {
		$this->_plugin->updateSetting($this->_contextId, 'terms_of_use_agreement', serialize($this->getData('terms_of_use_agreement')), 'object');

		$pluginSettingsDao = DAORegistry::getDAO('PluginSettingsDAO');
		$pluginSettingsDao->installSettings($this->_contextId, $this->_plugin->getName(), $this->_plugin->getContextSpecificPluginSettingsFile());
	}

}
