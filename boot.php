
<?php

if (rex::isBackend() && rex_plugin::get('structure', 'content')->isAvailable()) {
    rex_view::addCssFile($this->getAssetsUrl('style.css'));
}
