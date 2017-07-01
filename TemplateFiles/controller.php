<?php namespace ProcessWire;

if(file_exists("./_overrides/{$page->template->name}.php")) {
    include("./_overrides/{$page->template->name}.php");
} else {
    include("./{$page->template->name}.php");
}