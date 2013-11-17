<?php if(!defined('IS_CMS')) die();

/***************************************************************
*
* Plugin fuer moziloCMS, welches mit Hilfe von PHP QR Code (http://phpqrcode.sourceforge.net/) einen QRCode erzeugt
* by black-night - Daniel Neef
* 
***************************************************************/

class QR_Code extends Plugin {
    

    /***************************************************************
    * 
    * Gibt den HTML-Code zurueck, mit dem die Plugin-Variable ersetzt 
    * wird.
    * 
    ***************************************************************/	
	
	private $lang_admin;
	private $lang_cms;
	
    function getContent($value) {               
        require_once($this->PLUGIN_SELF_DIR.'/lib/qrlib.php');
        global $specialchars;
        $filename = $this->PLUGIN_SELF_DIR.'cache/'.str_replace('/','_',$specialchars->replaceSpecialChars($value,true,true)).'.png';
        $filename_src = $this->PLUGIN_SELF_URL.'cache/'.$specialchars->replaceSpecialChars(str_replace('/','_',$specialchars->replaceSpecialChars($value,true,true)),true,true).'.png';
        
        $this->checkForSettingChange();
        
        if (!file_exists($filename)) {
            QRcode::png($value, $filename, $this->getCorrectionLevel(), $this->getSize(), 2);
        }
                
        $result = '<img src="'.$filename_src.'" class="qrcode" alt="'.$value.'"/>';
        
        return $result;

    } // function getContent
    
    
    
    /***************************************************************
    * 
    * Gibt die Konfigurationsoptionen als Array zurueck.
    * 
    ***************************************************************/
    function getConfig() {

        $config = array();
        $config['correction_level'] = array(
        		"type" => "select",
        		"description" => $this->lang_admin->getLanguageValue("config_correction_level"),
                "descriptions" => array(
                        "L" => "niedrig",
                        "M" => "mittel",
                        "Q" => "viertel",
                        "H" => "hoch"
                        )                
        );        
        $config['size'] = array(
                "type" => "text",
                "description" => $this->lang_admin->getLanguageValue("config_size"),
                "maxlength" => "2",
                "regex" => "/^[1-9][0-9]?/",
                "regex_error" => $this->lang_admin->getLanguageValue("config_number_regex_error")
        );        
        return $config;            
    } // function getConfig
    
    
    
    /***************************************************************
    * 
    * Gibt die Plugin-Infos als Array zurueck. 
    * 
    ***************************************************************/
    function getInfo() {
        global $ADMIN_CONF;
        $dir = $this->PLUGIN_SELF_DIR;
        $language = $ADMIN_CONF->get("language");
        $this->lang_admin = new Language($dir."sprachen/admin_language_".$language.".txt");        
        $info = array(
            // Plugin-Name
            "<b>".$this->lang_admin->getLanguageValue("config_plugin_name")."</b> \$Revision: 1 $",
            // CMS-Version
            "2.0",
            // Kurzbeschreibung
            $this->lang_admin->getLanguageValue("config_plugin_desc"),
            // Name des Autors
           "black-night",
            // Download-URL
            array("http://software.black-night.org","Software by black-night"),
            # Platzhalter => Kurzbeschreibung
            array('{QR_Code|...}' => $this->lang_admin->getLanguageValue("config_plugin_name")
                 )
            );
            return $info;        
    } // function getInfo
    
    /***************************************************************
    *
    * Interne Funktionen
    *
    ***************************************************************/
    
    private function getCorrectionLevel() {
        if (!$this->settings->get("correction_level")) {
            return 'L';
        }else {
            return $this->settings->get("correction_level");
        }
    }
    
    private function getSize() {
        if (!$this->settings->get("size")) {
            return 4;
        }else {
            return $this->getInteger($this->settings->get("size"));
        }
    }    
    
    private function checkForSettingChange() {        
        if (!file_exists($this->PLUGIN_SELF_DIR.'cache/c_cl_'.$this->getCorrectionLevel().'.tmp') or 
            !file_exists($this->PLUGIN_SELF_DIR.'cache/c_s_'.$this->getSize().'.tmp')) {
            $this->clearCache();            
        }
    }
    
    private function clearCache() {
        $dir = $this->PLUGIN_SELF_DIR.'cache/';
        $verz = opendir($dir);
        while ($file = readdir ($verz))
        {
            if($file != "." && $file != "..")
            {
                unlink($dir.$file);
            }
        }
        closedir($verz);
        touch($dir.'c_cl_'.$this->getCorrectionLevel().'.tmp');
        touch($dir.'c_s_'.$this->getSize().'.tmp');
    }

    private function getInteger($value) {
    	if (is_numeric($value) and ($value > 0)) {
    		return $value;
    	} else {
    		return 1;    	
    	}
    } //function getInteger
           
    
} // class QR_Code

?>