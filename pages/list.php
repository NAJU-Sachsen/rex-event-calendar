<?php

// first check whether we have an incoming request to modify some of our data
$form_funcs = ['create_event', 'edit_event', 'delete_event'];
$status_funcs = ['event_offline', 'event_online'];
$requested_func = rex_get('func');

$msg = '';

if (in_array($requested_func, $status_funcs)) {
	$event_id = rex_get('event_id');
	$query = 'UPDATE naju_event SET event_active = IF (event_active, 0, 1) WHERE event_id = :id';
	$sql = rex_sql::factory();
	$sql->setQuery($query, ['id' => $event_id]);
}

if (in_array($requested_func, $form_funcs)) {
	switch ($requested_func) {
		case 'create_event':
			break;
		case 'edit_event':
			$event_id = rex_get('event_id');
			if (!$event_id) {
				break;
			}

			$fragment = new rex_fragment();
			$fragment->setVar('title', 'Veranstaltung bearbeiten');
			$fragment->setVar('class', 'edit', false);

			$form = naju_event_form::factory('naju_event', 'Veranstaltung bearbeiten', 'event_id = ' . rex_sql::factory()->escape($event_id));
			$form->addParam('event_id', $event_id);

			// event_name
			$field = $form->addTextField('event_name');
			$field->setLabel('Name');

			// event_start
			$field = $form->addInputField('date', 'event_start', null, ['class' => 'form-control']);
			$field->setLabel('Start');

			// event_end
			$field = $form->addInputField('date', 'event_end', null, ['class' => 'form-control']);
			$field->setLabel('Ende');
			$field->setDefaultSaveValue(null);

			// event_description
			$field = $form->addTextAreaField('event_description');
			$field->setLabel('Beschreibung');

			// event_location
			$field = $form->addTextField('event_location');
			$field->setLabel('Ort');

			// event target group type field
			$field = $form->addSelectField('event_target_group_type');
			$field->setLabel('Zielgruppen auswählen');
			$select = $field->getSelect();
			$select->setMultiple();

			// inflate event target group select
			$event = rex_sql::factory()->setQuery(
				'select event_target_group_type from naju_event where event_id = :id', ['id' => $event_id])->getArray()[0];

			$target_groups = ['children' => 'Kinder', 'teens' => 'Jugendliche', 'young_adults' => 'junge Erwachsene', 'families' => 'Familien'];
			$selected_target_groups = explode(',', $event['event_target_group_type']);
			foreach ($target_groups as $tg_key => $tg_val) {
				if (in_array($tg_key, $selected_target_groups)) {
					$select->addOption($tg_val, $tg_key, 0, 0, ['selected' => 'true']);
				} else {
					$select->addOption($tg_val, $tg_key);
				}
			}

			// event_target_group
			$field = $form->addTextField('event_target_group');
			$field->setLabel('Zielgruppe');

			// event type field
			$field = $form->addRadioField('event_type');
			$field->setLabel('Veranstaltungsart:');
			$field->addArrayOptions(['camp' => 'Camp', 'workshop' => 'Workshop',
				'work_assignment' => 'Arbeitseinsatz', 'group_meeting' => 'Aktiventreffen', 'other' => 'sonstiges']);

			// event_price
			$field = $form->addTextField('event_price');
			$field->setLabel('Teilnahmegebühr');

			// event_price_reduced
			$field = $form->addTextField('event_price_reduced');
			$field->setLabel('reduzierte Teilnahmegebühr');

			// event_registration
			$field = $form->addTextField('event_registration');
			$field->setLabel('Anmeldeinfos');

			// event_link
			$field = $form->addLinkmapField('event_link');
			$field->setLabel('Artikel-Link');
			$field->setDefaultSaveValue(null);

			$fragment->setVar('body', $form->get(), false);
			echo $fragment->parse('core/page/section.php');
			break;
		case 'delete_event':
			$event_id = rex_get('event_id');
			if (!$event_id) {
				break;
			}
			$delete_query = <<<EOSQL
				delete from naju_event
				where event_id = :id
				limit 1
EOSQL;

			$sql = rex_sql::factory()->setQuery($delete_query, ['id' => $event_id]);

			// if the delete statement succeeds, one row will be returned, otherwise none
			// therefore to detect success of the statement we may compare the number of returned rows
			$error = $sql->hasError() || 0 == $sql->getRows();
			if ($error) {
				$msg .= '<p class="alert alert-danger">Veranstaltung konnte nicht gelöscht werden</p>';
			} else {
				$msg .= '<p class="alert alert-success">Veranstaltung wurde gelöscht</p>';
			}

			break;
	}
} else {
	// afterwards proceed as usual by displaying the current list of events

	$fragment = new rex_fragment();
	$fragment->setVar('title', 'Veranstaltungen');

	$current_date = date('Y') . '-01-01';

	if (rex::getUser()->isAdmin()) {
		$event_query = <<<EOSQL
			select
				e.event_id,
				e.event_name,
				g.group_name,
				e.event_start,
				e.event_end,
				e.event_location,
				e.event_active
			from
				naju_event e
				join naju_local_group g on e.event_group = g.group_id
			where
				e.event_start >= '$current_date'
			order by e.event_start, e.event_end asc
EOSQL;
		$events = rex_sql::factory()->setQuery($event_query)->getArray();
	} else {
		$user_id = rex::getUser()->getId();
		$event_query = <<<EOSQL
			select
				e.event_id,
				e.event_name,
				g.group_name,
				e.event_start,
				e.event_end,
				e.event_location,
				e.event_active
			from
				naju_event e
				join naju_local_group g on e.event_group = g.group_id
				join naju_group_account ga on e.event_group = ga.group_id
			where
				e.event_start >= '$current_date'
				and ga.account_id = '$user_id'
			order by e.event_start, e.event_end asc
EOSQL;
		$events = rex_sql::factory()->setQuery($event_query)->getArray();
	}

	$list = rex_list::factory($event_query);

	$th_edit = '<th colspan="3">Bearbeiten</th>';
	$td_edit = '###TH_EDIT###';

	$list->removeColumn('event_id');
	$list->removeColumn('event_active');

	$list->setColumnLabel('event_name', 'Name');
	$list->setColumnLabel('group_name', 'Ortsgruppe');
	$list->setColumnLabel('event_start', 'Startdatum');
	$list->setColumnLabel('event_end', 'Enddatum');
	$list->setColumnLabel('event_location', 'Ort');

	$list->addColumn($th_edit, $td_edit, -1, ['###VALUE###', '###VALUE###']);
	$list->setColumnFormat($th_edit, 'custom', function ($params) {
		$list = $params['list'];
		$content = '';

		$event_id = $list->getValue('event_id');
		$event_active = $list->getValue('event_active');

		$content .= '
			<td class="rex-table-action">
				<a href="' . rex_url::currentBackendPage(['func' => 'edit_event', 'event_id' => urlencode($event_id)]) . '">
					<i class="rex-icon rex-icon-edit"></i> editieren
				</a>
			</td>';

		$content .=  '
			<td class="rex-table-action">
				<a href="' . rex_url::currentBackendPage(['func' => 'delete_event', 'event_id' => urlencode($event_id)]) . '">
					<i class="rex-icon rex-icon-delete"></i> löschen
				</a>
			</td>';

		if ($event_active) {
			$content .= '
				<td class="rex-table-action">
					<a class="rex-online" href="' . rex_url::currentBackendPage(['func' => 'event_offline', 'event_id' => urlencode($event_id)]) . '">
						<i class="rex-icon rex-icon-online"></i> online
					</a>
				</td>';
		} else {
			$content .= '
				<td class="rex-table-action">
					<a class="rex-offline" href="' . rex_url::currentBackendPage(['func' => 'event_online', 'event_id' => urlencode($event_id)]) . '">
						<i class="rex-icon rex-icon-offline"></i> offline
					</a>
				</td>';
		}

		return $content;
	});

	$fragment->setVar('content', $msg . $list->get(), false);
	echo $fragment->parse('core/page/section.php');
}
