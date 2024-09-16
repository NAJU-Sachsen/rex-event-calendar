<?php

$func = rex_get('func', 'string');
$msg = '';

$list = rex_list::factory('SELECT tag_name FROM naju_event_tags ORDER BY tag_name', 30, 'tags');
$list->setColumnLabel('tag_name', 'Tag');

$form = rex_form::factory('naju_event_tags', 'Neuer Tag', 'FALSE');
$form->addParam('func', 'add-tag');
$form->addErrorMessage(REX_FORM_ERROR_VIOLATE_UNIQUE_KEY, 'Tag existiert bereits.');
$field = $form->addTextField('tag_name');
$field->setLabel('Tag');
$field->getValidator()->add('notMatch', 'Zeichen % und , sind nicht erlaubt', '/,|%/');

$list_frag = new rex_fragment();
$list_frag->setVar('title', 'Tags');
$list_frag->setVar('body', $list->get(), false);

$form_frag = new rex_fragment();
$form_frag->setVar('class', 'edit', false);
$form_frag->setVar('title', 'Tag erstellen');
$form_frag->setVar('body', $form->get(), false);

echo $list_frag->parse('core/page/section.php');
echo $form_frag->parse('core/page/section.php');
