<?php

$form = rex_form::factory('naju_local_group', 'Ortsgruppe erstellen', 'group_id = -1');	// ID = -1 does (never) exist => new entry
$form->setEditMode(false);
$form->addParam('func', 'add-group');

$field = $form->addTextField('group_name');
$field->setLabel('Name');

// $fragment = new rex_fragment();
// $fragment->setVar('title', 'Ortsgruppe hinzufügen');

// $fragment->setVar('content', $form->get(), false);
// echo $fragment->parse('core/page/section.php');

$form->show();
