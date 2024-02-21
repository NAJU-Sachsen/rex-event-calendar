<?php

$form = naju_event_form::factory('naju_event', 'Veranstaltung hinzufügen', 'event_id = -1'); // ID = -1 does never exist => new entry
$form->addParam('func', 'add-event');

// event name field
$field = $form->addTextField('event_name');
$field->setLabel('Name');

// event local group field
$field = $form->addSelectField('event_group');
$field->setLabel('Ortsgruppe');
$select = $field->getSelect();

if (rex::getUser()->isAdmin()) {
    $group_query = <<<EOSQL
        select group_id, group_name
        from naju_local_group
EOSQL;
    $local_groups = rex_sql::factory()->setQuery($group_query)->getArray();
} else {
    $user_id = rex::getUser()->getId();

    $permitted_groups_query = <<<EOSQL
        select g.group_id, g.group_name
        from naju_local_group g
            join naju_group_account ga
            on g.group_id = ga.group_id
        where ga.account_id = :id
EOSQL;

    $local_groups = rex_sql::factory()->setQuery($permitted_groups_query, ['id' => $user_id])->getArray();
}

foreach ($local_groups as $group) {
	$select->addOption($group['group_name'], $group['group_id']);
}

// event start date field
$field = $form->addInputField('date', 'event_start', null, ['class' => 'form-control']);
$field->setLabel('Startdatum');

// event end date field
$field = $form->addInputField('date', 'event_end', null, ['class' => 'form-control']);
$field->setLabel('Enddatum (optional)');
$field->setDefaultSaveValue(null);

// event start time
$field = $form->addInputField('time', 'event_start_time', null, ['class' => 'form-control']);
$field->setLabel('Startzeit (optional)');
$field->setDefaultSaveValue(null);

// event end time
$field = $form->addInputField('time', 'event_end_time', null, ['class' => 'form-control']);
$field->setLabel('Endzeit (optional)');
$field->setDefaultSaveValue(null);

// event description field
$field = $form->addTextAreaField('event_description');
$field->setLabel('Beschreibung');

// event location field
$field = $form->addTextField('event_location');
$field->setLabel('Ort (optional)');

// event target group type field
$field = $form->addSelectField('event_target_group_type');
$field->setLabel('Zielgruppen auswählen');
$select = $field->getSelect();
$select->addArrayOptions(['children' => 'Kinder', 'teens' => 'Jugendliche',
    'young_adults' => 'junge Erwachsene', 'families' => 'Familien']);
$select->setMultiple();

// TODO event_target_group_type is a SET column but SET is currently not supported...

// event target group field
$field = $form->addTextField('event_target_group');
$field->setLabel('Zielgruppe beschreiben (optional)');

// event type field
$field = $form->addRadioField('event_type');
$field->setLabel('Veranstaltungsart:');
$field->addArrayOptions(['camp' => 'Camp', 'workshop' => 'Workshop',
    'work_assignment' => 'Arbeitseinsatz', 'group_meeting' => 'Aktiventreffen',
    'excursion' => 'Exkursion', 'holiday_event' => 'Ferienveranstaltung', 'other' => 'sonstiges']);

// event price field
$field = $form->addTextField('event_price');
$field->setLabel('Preis (optional)');

// event reduced price field
$field = $form->addTextField('event_price_reduced');
$field->setLabel('reduzierter Preis (optional)');

// event registration info field
$field = $form->addTextField('event_registration');
$field->setLabel('Anmeldeinfos (optional)');

// event further reading link
$field = $form->addLinkmapField('event_link');
$field->setLabel('Link zum Artikel (optional)');
$field->setDefaultSaveValue(null);

$content = '';
$msg = rex_get('_msg');

if ($msg) {
    $content .= '<div class="alert alert-info">' . rex_escape($msg) . '</div>';
}

$content .= $form->get();

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', 'Veranstaltung erstellen', false);
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;
