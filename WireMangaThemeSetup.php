<?php namespace ProcessWire;
/*
 * WireMangaThemeSetup 
 *
 * Create theme settings templates, fields and pages
 *
 */

class WireMangaThemeSetup extends Wire {
	/**
	 * Install Module
	 */
	public function install() {
		$this->setArrays();
		$this->createTemplates();
		$this->createPages();
		$this->createFields();
		$this->copyFiles($this->config->paths->siteModules . "WireMangaTheme/TemplateFiles/", $this->config->paths->templates);
		$initFile = $this->config->paths->siteModules . "WireManga/Hooks/init.php";
		if(!file_exists($initFile)) {
			copy($initFile, $this->config->paths->site);
		}

		// Change title field in the repeater_wm_menu template context
		$t = $this->wire("templates")->get("name=repeater_wm_menu");
		$f = $t->fieldgroup->getField('title', true);
		$f->label = "Name";
		$this->wire("fields")->saveFieldgroupContext($f, $t->fieldgroup);

		// Change title field in the repeater_wm_external_sites template context
		$t = $this->wire("templates")->get("name=repeater_wm_sites");
		$f = $t->fieldgroup->getField('title', true);
		$f->label = "Name";
		$this->wire("fields")->saveFieldgroupContext($f, $t->fieldgroup);
	}


	/**
	 * Uninstall Module
	 */
	public function uninstall() {
		$this->setArrays();
		$this->deletePages();
		$this->deleteFields();
		$this->deleteTemplates();
	}

	protected $templates_;
	protected $pages_;
	protected $fields_;

	public function setArrays() {

		$this->pages_ = [
			["title" => "User"          , "name" => "user"    , "template" => "wm_account" , "path" => "/"],
			["title" => "Theme Settings", "name" => "settings", "template" => "wm_settings", "path" => "/"],
			["title" => "Ajax"          , "name" => "ajax"    , "template" => "wm_ajax"    , "path" => "/"],
		];
		
		$this->fields_ = [
			["name" => "wm_adult"            , "type" => "Checkbox", "add_to" => "wm_manga_single", "label" => "Adult", "notes" => "If checked there will be a warning before showing the manga page."],
			["name" => "wm_site_url"         , "type" => "Text"    , "add_to" => null             , "label" => "External Url"],
			["name" => "wm_sites"            , "type" => "Repeater", "add_to" => "wm_manga_single", "label" => "External Sites", "fields" => "title wm_site_url", "notes" => "Sites with more information about the manga."],
			["name" => "wm_seo_title"        , "type" => "Text"    , "add_to" => "wm_manga_single", "label" => "Seo Title", "notes" => "[SEO title](https://moz.com/learn/seo/title-tag), if not set the page title will be used."],
			["name" => "wm_seo_description"  , "type" => "Textarea", "add_to" => "wm_manga_single", "label" => "Seo Description", "notes" => "[SEO description](https://moz.com/learn/seo/meta-description)"],
			["name" => "wm_views"            , "type" => "Integer" , "add_to" => "wm_manga_single", "label" => "Views"],
			["name" => "wm_menu_class"       , "type" => "Text"    , "add_to" => null             , "label" => "Class", "notes" => "Add class to the <li> tag containing the menu item."],
			["name" => "wm_menu_URL"         , "type" => "Text"    , "add_to" => null             , "label" => "URL", "notes" => "**Internal links:** relative paths to the root url\n**External links:** url with or without http/s, must start with //\n**No href:** - (hyphen) removes the href tag completely\nLeave  blank for home page"],
			["name" => "wm_menu_show_to"     , "type" => "Options" , "add_to" => null             , "label" => "Show To", "default" => 1, "setOptionsString" => "All Users\nLogged In Users\nLogged Out Users"],
			["name" => "wm_user_activate"    , "type" => "Checkbox", "add_to" => "wm_settings"    , "label" => "Require Account Activation", "notes" => "If checked users will need to activate their account (through email) before being able to use it."],
			["name" => "wm_site_name"        , "type" => "Text"    , "add_to" => "wm_settings"    , "label" => "Site Name", "notes" => "Used in the title tag and emails."],
			["name" => "wm_site_title_sep"   , "type" => "Text"    , "add_to" => "wm_settings"    , "label" => "Site Title Separator", "notes" => "Used in the title tag."],
			["name" => "wm_site_email"       , "type" => "Email"   , "add_to" => "wm_settings"    , "label" => "Site Email", "notes" => "Emails will be sent from this address."],
			["name" => "wm_menu"             , "type" => "Repeater", "add_to" => "wm_settings"    , "label" => "Menu", "fields" => "title wm_menu_URL wm_menu_class wm_menu_show_to"],
			["name" => "wm_no_chapters"      , "type" => "Text"    , "add_to" => "wm_settings"    , "label" => "No Chapters Available Text", "notes" => "Text displayed when the manga has no chapters."],
			["name" => "wm_before_chapters"  , "type" => "Text"    , "add_to" => "wm_settings"    , "label" => "Before Chapters Text", "notes" => "Text displayed before chapters list on manga page."],
			["name" => "wm_limit_width"      , "type" => "Checkbox", "add_to" => "wm_settings"    , "label" => "Limit Page Width", "notes" => "When checked the page will have a max width of 1200px."],
			["name" => "wm_all_images"       , "type" => "Checkbox", "add_to" => "user"           , "label" => "Display all chapter images", "notes" => "When checked all images of a chapter will be displayed on the same page.\nCan be overriden on the manga edit page."],
			["name" => "wm_adult_warning_off", "type" => "Checkbox", "add_to" => "user"           , "label" => "Disable Adult Warning", "notes" => "If checked the adult warning won't show anymore."],
			["name" => "wm_hide_adult"       , "type" => "Checkbox", "add_to" => "user"           , "label" => "Hide Adult Manga", "notes" => "If checked manga marked as adult will be hidden."],
			["name" => "wm_account_status"   , "type" => "Text"    , "add_to" => "user"           , "label" => "Account Status"],
			["name" => "wm_profile_image"    , "type" => "Image"   , "add_to" => "user"           , "label" => "Profile Image", "defaultValuePage" => true, "ext" => "gif jpg jpeg png", "maxFiles" => 1, "descRows" => 0],
			["name" => "wm_registration_date", "type" => "Datetime", "add_to" => "user"           , "label" => "Registration Date"]
		];

		$this->templates_ = [
			["name" => "wm_account", "urlSegments" => 1],
			["name" => "wm_settings"],
			["name" => "wm_ajax"],
		];
	}

	/**
	 * Recursive copy of template files
	 *
	 * Copies the template files from module directory
	 * to the template directory
	 *
	 * @param string $src Path to source directory
	 * @param string $dest Path to destination directory
	 *
	 */
	public function copyFiles($src, $dest) {
		$files = scandir($src);
		@mkdir($dest);
		foreach($files as $file) {
			if($file !== "." && $file !== ".."){
				if(is_dir("$src/$file")){
					$this->copyFiles("$src/$file", "$dest/$file");
				} else {
					copy("$src/$file", "$dest/$file");
				}
			}
		}
	}

	/** 
	 * Create templates when module is installed
	 */
	protected function createTemplates() {
		// loop the templates array and create the templates
		foreach($this->templates_ as $tpl) {
			if(!$this->wire("templates")->get("name={$tpl["name"]}")->id) {
				$fg = new Fieldgroup();
				$fg->name = $tpl["name"];
				$fg->add("title");
				$fg->save();
				$t = new Template();
				$t->name = $tpl["name"];
				$t->fieldgroup = $fg;
				$t->save();
				if(isset($tpl["urlSegments"])) {
					$t->urlSegments = $tpl["urlSegments"];
				}
				$t->save();
				$tpl_id = $tpl["name"] . "_id";
				$$tpl_id = $t->id;
			}
		}
	}

	/** 
	 * Create fields when module is installed
	 */
	protected function createFields() {
		foreach($this->fields_ as $field) {
			$template_id = $this->wire("templates")->get("name={$field["add_to"]}")->id;

			if(!$this->wire("fields")->get("name={$field["name"]}")->id) {
				if($field["type"] === "Repeater") {
					$this->createRepeater($field["name"], $field["fields"], $field["label"], $template_id, null);
					continue;
				}
				$f = new Field();
				$f->type = $this->modules->get("Fieldtype{$field["type"]}");
				$f->name = $field["name"];
				$f->label = $field["label"];
				$f->save();
				if(isset($field["notes"])) {
					$f->notes = $field["notes"];
				}
				if(isset($field["defaultValuePage"])) {
					$p = $this->wire("pages")->get("path=/settings/");
					$f->defaultValuePage = $p->id;
				}
				if(isset($field["ext"])) {
					$f->extensions = $field["ext"];
					$f->outputFormat = 1;
				}
				if(isset($field["maxFiles"])) {
					$f->maxFiles = $field["maxFiles"];
				}
				if(isset($field["descRows"])) {
					$f->descriptionRows = $field["descRows"];
				}
				if(isset($field["parent"])) {
					$p = $this->wire("pages")->get("template=wm_taxonomy, name={$field["parent"]}");
					$f->parent_id = $p->id;
				}
				if(isset($field["template"])) {
					$f->template_id = $this->wire("templates")->get("name={$field["template"]}")->id;
				}
				if(isset($field["inputfield"])) {
					$f->inputfield = $field["inputfield"];
				}
				if(isset($field["addable"])) {
					$f->addable = $field["addable"];
				}
				if($field["name"] == "wm_description") {
					$f->inputfieldClass = "InputfieldCKEditor";
				}
				if($field["name"] == "wm_views") {
					$f->collapsed = 4;
				}
				if(isset($field["default"])) {
					$f->required = 1;
					$f->defaultValue = $field["default"];
				}
				if(isset($field["setOptionsString"])) {
					$manager = new SelectableOptionManager();
					$manager->setOptionsString($f, $field["setOptionsString"], false);
					$f->save();
				}
				$f->save();
				if($template_id) {
					$t = $this->wire("templates")->get($template_id);
					$t->fieldgroup->add($f);
					$t->fieldgroup->save();
				}
			}

			
		}
	}

	/**
	 * Creates a repeater field with associated fieldgroup, template, and page
	 *
	 * @param string $repeaterName The name of your repeater field
	 * @param string $repeaterFields List of field names to add to the repeater, separated by spaces
	 * @param string $repeaterLabel The label for your repeater
	 * @param string $repeaterTags Tags for the repeater field
	 * @return Returns the new Repeater field
	 *
	 */
	public function createRepeater($repeaterName, $repeaterFields, $repeaterLabel, $template_id, $repeaterTags) {
		$fieldsArray = explode(' ', $repeaterFields);
		
		$f = new Field();
		$f->type = $this->modules->get("FieldtypeRepeater");
		$f->name = $repeaterName;
		$f->label = $repeaterLabel;
		$f->tags = $repeaterTags;
		if($repeaterName == "wm_menu"){
			$f->repeaterDepth = 1;
		}
		$f->repeaterReadyItems = 3;
		
		//Create fieldgroup
		$repeaterFg = new Fieldgroup();
		$repeaterFg->name = "repeater_$repeaterName";
		
		//Add fields to fieldgroup
		foreach($fieldsArray as $field) {
			$repeaterFg->append($this->fields->get($field));
		}
		
		$repeaterFg->save();
		
		//Create template
		$repeaterT = new Template();
		$repeaterT->name = "repeater_$repeaterName";
		$repeaterT->flags = 8;
		$repeaterT->noChildren = 1;
		$repeaterT->noParents = 1;
		$repeaterT->noGlobal = 1;
		$repeaterT->slashUrls = 1;
		$repeaterT->fieldgroup = $repeaterFg;
		
		$repeaterT->save();
		
		//Setup page for the repeater - Very important
		$repeaterPage = "for-field-{$f->id}";
		$f->parent_id = $this->pages->get("name=$repeaterPage")->id;
		$f->template_id = $repeaterT->id;
		$f->repeaterReadyItems = 3;
		
		//Now, add the fields directly to the repeater field
		foreach($fieldsArray as $field) {
			$f->repeaterFields = $this->fields->get($field);
		}
		
		$f->save();
		if($template_id) {
			$t = $this->wire("templates")->get($template_id);
			$t->fieldgroup->add($f);
			$t->fieldgroup->save();
		}
	}

	/**
	 * Create pages when module is installed
	 */
	protected function createPages() {
		foreach($this->pages_ as $page_) {
			$parent_path = $page_["path"];
			$p = $this->wire("pages")->get("name={$page_["name"]}");
			if(!$p->id) {
				$p = new Page();
				$p->template = $page_["template"];
				$p->parent = $this->wire("pages")->get("path={$parent_path}");
				$p->title = $page_["title"];
				$p->name = $page_["name"];
				$p->save();
			}
		}
	}

	/**
	 * Delete pages when module is uninstalled
	 */
	protected function deletePages() {
		foreach($this->pages_ as $page_) {
			$page_path = $page_["path"] . $page_["name"] . "/";
			$p = $this->wire("pages")->get("path={$page_path}");
			if($p->id && $p->name != "admin") {
				$this->wire("pages")->delete($p, true);
			}
		}
	}

	/**
	 * Delete fields when module is uninstalled
	 */
	protected function deleteFields() {
		foreach($this->fields_ as $field) {
			$f = $this->wire("fields")->get($field["name"]);
			if($f->id){
				$fieldgroups = $f->getFieldgroups();
				foreach($fieldgroups as $fg) {
					$fg->remove($f);
					$fg->save();
				}
				if($f->type->name === "FieldtypeRepeater") {
					$repeater_fg = $this->wire("fieldgroups")->get("name=repeater_{$field["name"]}");
					if($repeater_fg->id){
						$repeater_fg->remove($field["fields"]);
						$repeater_fg->save();
					}

					$repeater_tpl = $this->wire("templates")->get("name=repeater_{$field["name"]}");
					if($repeater_tpl->id){
						$repeater_tpl->flags = Template::flagSystemOverride;
						$repeater_tpl->flags = 0;
						$repeater_tpl->save();
						$this->wire("templates")->delete($repeater_tpl);
						$this->wire("fieldgroups")->delete($repeater_fg);
					}
				}
				$this->wire("fields")->delete($f);
			}
		}
	}

	/**
	 * Delete templates when module is uninstalled
	 */
	protected function deleteTemplates() {
		foreach($this->templates_ as $tpl) {
			$t = $this->wire("templates")->get("name={$tpl["name"]}");
			if($t->id && $tpl["name"] !== "user") {
				$this->wire("templates")->delete($t);
				$this->wire("fieldgroups")->delete($t->fieldgroup);
			}
		}
	}
}
