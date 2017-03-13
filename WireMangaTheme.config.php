<?php namespace ProcessWire;

class WireMangaThemeConfig extends ModuleConfig {
    public function __construct() {
        $this->add([
			[// Checkbox field: wmt_clean_up
				"name"  => "wmt_clean_up",
				"type"  => "checkbox",
				"label" => $this->_("Clean Up"),
				"description" => $this->_("Remove all templates, fields and pages created by this module when it is uninstalled."),
				"value" => $this->_(0),
			],
			[// Checkbox field: wmt_update_theme
				"name"  => "wmt_update_template_files",
				"type"  => "checkbox",
				"label" => $this->_("Update Template (theme) Files"),
				"description" => $this->_("Replaces the files in /site/templates/ with files from this module that might contain fixes and additional features.\nCheck and click submit."),
				"notes" => $this->_("If you have modified any of the files make sure to back them up before running this."),
				"value" => $this->_(0),
			],
		]);
    }
}