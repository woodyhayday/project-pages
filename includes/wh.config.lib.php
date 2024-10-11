<?php 
/*!
 * Project Pages
 * https://projectpages.io
 * V2.0
 *
 * Date: 28/04/24
 */

	class WH_WP_ConfigLib {
		
		#} Main settings storage
		private $settings;
		private $settingsIndex = false;
		private $settingsKey = false;
		private $settingsVer = false;
		private $settingsDefault = false;	
		private $settingsPlugin = false;
		private $settingsPluginVer = false;
		private $settingsPluginDBVer = false;
		
		#} DMZ Settings
		private $settingsDMZRegister;
		private $settingsDMZKey = false;
		private $settingsDMZ;

		#} :)
		private $whlibVer = '2.0';	

		#} added "protected" list of setting keys that don't get reset when resetting to default
		private $settingsProtected = false;
		
		#} Constructor
		function __construct($config=array()) {

			#} localise any passed config
			if (is_array($config)){

				if (isset($config['setting_index'])) 		$this->settingsIndex = $config['setting_index'];
				if (isset($config['conf_key'])) 			$this->settingsKey = $config['conf_key'];
				if (isset($config['conf_ver'])) 			$this->settingsVer = $config['conf_ver'];
				if (isset($config['conf_defaults'])) 		$this->settingsDefault = $config['conf_defaults'];
				if (isset($config['conf_plugin'])) 			$this->settingsPlugin = $config['conf_plugin'];
				if (isset($config['conf_pluginver'])) 		$this->settingsPluginVer = $config['conf_pluginver'];
				if (isset($config['conf_plugindbver'])) 	$this->settingsPluginDBVer = $config['conf_plugindbver'];
				if (isset($config['conf_dmzkey'])) 			$this->settingsDMZKey = $config['conf_dmzkey'];
				if (isset($config['conf_protected'])) 		$this->settingsProtected = $config['conf_protected'];

			} else exit('WHConfigLib initiated incorrectly.');

			#} define dmz settings key
			#} Set by passed config now $this->settingsDMZKey = $this->settingsKey . '_dmzregister';
			
			#} Load direct
			$this->loadFromDB(); $this->loadDMZFromDB();
			
			#} Fill any missing vars
			$this->validateAndUpdate();
			
			#} If empty it's first run so init from defaults
			if (empty($this->settings)) $this->initCreate();
			
		}
		
		#} Checks through defaults + existing and adds defaults where unset
		function validateAndUpdate(){
			$defaultsAdded = 0;
			foreach ($this->settingsDefault as $key => $val) 
				if (!isset($this->settings[$key])) {
					$this->settings[$key] = $val;
					$defaultsAdded++;
				}
			
			if ($defaultsAdded > 0) $this->saveToDB();

		}
		
		#} Initial Create
		function initCreate(){

			#} If properly initialised!
			if ($settingsKey !== false && $settingsVer !== false && $settingsDefault !== false && $settingsPlugin !== false && $settingsPluginVer !== false){
			
				#} Create + save initial from default
				#} Following have to be set out of props
				$defaultOptions = $this->settingsDefault;
				$defaultOptions['settingsID'] = $this->settingsVer; 
				$defaultOptions['plugin'] = $this->settingsPlugin;
				$defaultOptions['version'] = $this->settingsPluginVer; 
				$defaultOptions['db_version'] = $this->settingsPluginDBVer; 

				#} Pass back to settings, and save
				$this->settings = $defaultOptions;
				$this->saveToDB();

			#} else brutal exit!
			} else exit('WHConfigLib initiated incorrectly.');
			
		}

		#} Reset to defaults
		function resetToDefaults(){
			
			#} reset to default opts
			#} NOW with added protection :) any protected field keys wont get re-written

			#} Copy any protected keys over the new reset settings (if is set)
			$existingSettings = $this->settings;
			$newSettings = $this->settingsDefault;
			if (isset($this->settingsProtected) && is_array($this->settingsProtected)) foreach ($this->settingsProtected as $protectedKey){
				
				#} If isset
				if (isset($existingSettings[$protectedKey])){

					#} Pass it along
					$newSettings[$protectedKey] = $existingSettings[$protectedKey];

				}
			}

			#} Save em down
			$this->settings = $newSettings;
			$this->saveToDB();

		}
		
		#} Get all options as object
		function getAll(){
			
			return $this->settings;
			
		}

		// get index of setting descriptors
		function getIndex( $grouped_by_category = false  ){

			if ( $grouped_by_category ){

				return $this->groupIndexByCategory();

			}

			return $this->settingsIndex;

		}

		// get particular index value
		function getIndexValue( $key ){

			if ( isset( $this->settingsIndex[ $key ] ) ){

				return $this->settingsIndex[ $key ];

			}

			return false;

		}

		// set particular index value
		function setIndexValue( $key, $value ){

			if ( isset( $this->settingsIndex[ $key ] ) ){

				$this->settingsIndex[ $key ] = $value;
				return true;

			}

			return false;

		}

		// get index + values of all settings
		function getValuesIndex( $grouped_by_category = false,  $disassociate = false){

			// cycle through and value each index item
			$valuedIndex = array();

			foreach ( $this->settingsIndex as $key => $index_item ){

				$valuedIndex[$key] = $index_item;
				$valuedIndex[$key]['value'] = $this->get($key);

				// for checkboxes we Bool the value
				if ( $valuedIndex[$key]['type'] == 'checkbox' ){

					if ( $valuedIndex[$key]['value'] == 1 ){
						$valuedIndex[$key]['value'] = true;
					} else {
						$valuedIndex[$key]['value'] = false;
					}

				}

			}

			if ( $grouped_by_category ){

				return $this->groupIndexByCategory( $valuedIndex, $disassociate );

			}

			return $valuedIndex;

		}

		// orders index into a category-grouped list
		function groupIndexByCategory( $index = false, $disassociate = false  ){

			// load default if not passed
			if ( !$index ){

				$index = $this->settingsIndex;

			}

			$groupedIndex = array();

			if ( is_array( $index) && count( $index ) > 0 ){
			
				foreach ( $index as $k => $v ){

					$cat = ( isset( $v['category'] ) && !empty( $v['category'] ) ) ? $v['category'] : 'General Settings';
					$cat_slug = sanitize_title( $cat );

					// exists in stack?
					if ( !isset( $groupedIndex[$cat_slug] ) ) {

						$groupedIndex[$cat_slug] = array(

							'name' => $cat,
							'key' => $cat_slug,
							'settings' => array()

						);

					}


					// disassociate?
					if ( $disassociate ){

						// keyless
						$x = $v;
						$x['key'] = $k;
						$groupedIndex[$cat_slug]['settings'][] = $x;

					} else {

						// keyed
						$groupedIndex[$cat_slug]['settings'][$k] = $v;

					}



				}

			}

			// if disassociated, we need to cull keys at category level
			if ( $disassociate ){

				$deKeyed = array();
				foreach ( $groupedIndex as $k => $v ){
					$deKeyed[] = $v;
				}

				return $deKeyed;

			}

			return $groupedIndex;

		}

		
		#} Get single option
		function get($key){
			
			if (empty($key) === true) return false;
			
			if (isset($this->settings[$key]))
				return $this->settings[$key];
			else
				return false;
			
		}
		
		#} Add/Update *brutally
		function update($key,$val=''){
			
			if (empty($key) === true) return false;
			
			#} Don't even check existence as I guess it doesn't matter?
			$this->settings[$key] = $val;		
			
			#} Save down
			$this->saveToDB();
		}		
		
		#} Delete option
		function delete($key){
			
			if (empty($key) === true) return false;
			
			$newSettings = array();
			foreach($this->settings as $k => $v)
				if ($k != $key) $newSettings[$k] = $v;
				
			#} Brutal
			$this->settings = $newSettings;	
			
			#} Save down
			$this->saveToDB();
						
		}


		#} ==================================
		#} DMZ Config additions
		#} 2 layers:
		#} DMZConfig = whole object
		#} DMZConfigValue = object.value
		#} ==================================

		#} Get single option
		function dmzGet($dmzKey,$confKey){
			
			if (empty($dmzKey) === true || empty($confKey) === true) return false;
			
			#} Assumes it's loaded!?
			if (isset($this->settingsDMZ[$dmzKey])){

				if (isset($this->settingsDMZ[$dmzKey][$confKey])) {

					return $this->settingsDMZ[$dmzKey][$confKey];

				}

			} 
			
			return false;
			
		}		
		
		#} Delete option
		function dmzDelete($dmzKey,$confKey){
			
			if (empty($dmzKey) === true || empty($confKey) === true) return false;
			
			$existingSettings = $this->dmzGetConfig($dmzKey);
			$newSettings = array();
			if (isset($existingSettings) && is_array($existingSettings)) { foreach($existingSettings as $k => $v) {
					if ($k != $confKey) $newSettings[$k] = $v;
				}
			}
				
			#} Brutal
			$this->settingsDMZ[$dmzKey] = $newSettings;	
			
			#} Save down
			$this->saveToDB();
						
		}
		
		#} Add/Update *brutally
		function dmzUpdate($dmzKey,$confKey,$val=''){
			
			if (empty($dmzKey) === true || empty($confKey) === true) return false;
			
			#} if not set, create
			if (!isset($this->settingsDMZ[$dmzKey])){

				#} add to register
				$this->settingsDMZRegister[$dmzKey] = $dmzKey;

				#} Create as arr
				$this->settingsDMZ[$dmzKey] = array();

			}

			#} Don't even check existence as I guess it doesn't matter?
			$this->settingsDMZ[$dmzKey][$confKey] = $val;		
			
			#} Save down
			$this->saveToDB();
		}
		
		#} Get alls option
		function dmzGetConfig($dmzKey){
			
			if (empty($dmzKey) === true) return false;
			
			#} Assumes it's loaded!?
			if (isset($this->settingsDMZ[$dmzKey])){

				return $this->settingsDMZ[$dmzKey];

			} 
			
			return false;
			
		}	
		
		#} Delete Config
		function dmzDeleteConfig($dmzKey){
			
			if (empty($dmzKey) === true) return false;
				
			#} Brutal
			unset($this->settingsDMZ[$dmzKey]);
			unset($this->settingsDMZRegister[$dmzKey]);
			
			#} Save down
			$this->saveToDB();
						
		}	
		
		#} Add/Update Config *brutally
		function dmzUpdateConfig($dmzKey,$config){
			
			if (empty($dmzKey) === true || empty($config) === true) return false;
			
			#} if not set, create
			if (!isset($this->settingsDMZ[$dmzKey])){

				#} add to register
				$this->settingsDMZRegister[$dmzKey] = $dmzKey;

			}

			#} Just brutally override
			$this->settingsDMZ[$dmzKey] = $config;		
			
			#} Save down
			$this->saveToDB();
		}	
		
		#} Load/Reload DMZ options from db 
		function loadDMZFromDB(){
			
			#} Load the register
			$this->settingsDMZRegister = get_option($this->settingsDMZKey);

			#} Load anything logged in register
			if (is_array($this->settingsDMZRegister) && count($this->settingsDMZRegister) > 0) { foreach ($this->settingsDMZRegister as $regEntry){

					#} Load it
					$this->settingsDMZ[$regEntry] = get_option($this->settingsDMZKey.'_'.$regEntry);

				}
			}
			return $this->settingsDMZ;
			
		}

		#} / DMZ Fields


		
		#} Save back to db
		function saveToDB(){
		
			$u = array();
			$u[] = update_option($this->settingsKey, $this->settings);				

			#} Also any DMZ's!

				#} save register
				update_option($this->settingsDMZKey,$this->settingsDMZRegister);
				if (isset($this->settingsDMZRegister) && is_array($this->settingsDMZRegister)) foreach ($this->settingsDMZRegister as $dmzKey){ # => $dmzVal

					$u[] = update_option($this->settingsDMZKey.'_'.$dmzKey, $this->settingsDMZ[$dmzKey]);	

				}

			return $u;
			
		}
		
		#} Load/Reload from db 
		function loadFromDB(){
			
			$this->settings = get_option($this->settingsKey);
			return $this->settings;
			
		}		
		
		#} Uninstall func - effectively creates a bk then removes its main setting
		function uninstall(){
			
			#} Set uninstall flag
			$this->settings['uninstall'] = time();
			
			#} Backup
			$this->createBackup('Pre-UnInstall Backup');
			
			#} Blank it out
			$this->settings = NULL;
			
			#} Return the delete
			return delete_option($this->settingsKey);
			
		}
		
		#} Backup existing settings obj (ripped from sgv2.0)
		function createBackup($backupLabel=''){
			
			$existingBK = get_option($this->settingsKey.'_bk'); if (!is_array($existingBK)) $existingBK = array();
			$existingBK[time()] = array(
				'main' => $this->settings,
				'dmzreg' => $this->settingsDMZRegister,
				'dmz' => $this->settingsDMZ
			);
			if (!empty($backupLabel)) $existingBK[time()]['backupLabel'] = sanitize_text_field($backupLabel); #} For named settings bk
			update_option($this->settingsKey.'_bk',$existingBK);
			return $existingBK[time()];
			
		}
		
		#} Kills all bks
		function killBackups(){
		
			return delete_option($this->settingsKey.'_bk');
			
		}
		
		#} Retrieve BKs
		function getBKs(){
			
			$x = get_option($this->settingsKey.'_bk');
			
			if (is_array($x)) return $x; else return array();
			
		}
		
		#} Reload from BK (bkkey will be a timestamp, use getBKs to list these keys)
		function reloadFromBK($bkkey){
		
			$backups = get_option($this->settingsKey.'_bk');
			
			if (isset($backups[$bkkey])) if (is_array($backups[$bkkey])) {
				
				#} kill existing settings and use backed up ones
				$this->settings = $backups[$bkkey];
				
				#} Save 
				$this->saveToDB();
			
				return true;	
				
			} 
			
			return false;
				
			
		}
		
		
		
	}


	#} This is a wrapper/factory class which simplifies using DMZ fields for extension plugins
	class WHWPConfigExtensionsLib {

		#} key holder
		private $extperma = false;
		private $settingsObj = false;
		private $existingSettings = false;

		#} Constructor
		function __construct($extperma='',$defaultConfig=array()) {

			if (!empty($extperma)){

				#} store 
				$this->extperma = 'ext_'.$extperma;

				#} initiate settings obj as a dmz set 
				global $zeroBSCRM_Settings;
				$existingSettings = $zeroBSCRM_Settings->dmzGetConfig($this->extperma);

				#} Create if not existing
				if (!is_array($existingSettings)){

					#} init
					$zeroBSCRM_Settings->dmzUpdateConfig($this->extperma,$defaultConfig);

				}

			} else exit('WHConfigLib initiated incorrectly.');

		}

		#} passthrough funcs

		function get($key){

			global $zeroBSCRM_Settings;
			return $zeroBSCRM_Settings->dmzGet($this->extperma,$key);

		}

		function delete($key){

			global $zeroBSCRM_Settings;
			return $zeroBSCRM_Settings->dmzDelete($this->extperma,$key);


		}

		function update($key,$val=''){

			global $zeroBSCRM_Settings;
			return $zeroBSCRM_Settings->dmzUpdate($this->extperma,$key,$val);


		}

		function getAll(){

			global $zeroBSCRM_Settings;
			return $zeroBSCRM_Settings->dmzGetConfig($this->extperma);

		}

	}


	
	
?>