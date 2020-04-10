<?php

function create_column($value)
{
	return '<td>' . htmlspecialchars($value) . '</td>';
}

// first check whether we have an incoming request to modify some of our data
$funcs = ['create_event', 'edit_event', 'delete_event'];
$requested_func = rex_get('func');

$msg = '';

if (in_array($requested_func, $funcs)) {
	switch ($requested_func) {
		case 'create_event':
			break;
		case 'edit_event':
			break;
		case 'delete_event':
			$event_id = rex_get('event_id');
			if (!$event_id) {
				break;
			}
			$delete_query = <<< EOSQL
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
}


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
			e.event_location
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
			e.event_location
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


$table = <<<EOHTML
	<table class="table">
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Ortsgruppe</th>
				<th>Startdatum</th>
				<th>Enddatum</th>
				<th>Ort</th>
				<th colspan="2">Bearbeiten</th>
			</tr>
		</thead>
		<tbody>
EOHTML;

foreach ($events as $event) {
	$table .= '<tr>';
	$table .= create_column($event['event_id']);
	$table .= create_column($event['event_name']);
	$table .= create_column($event['group_name']);
	$table .= create_column($event['event_start']);
	$table .= create_column($event['event_end']);
	$table .= create_column($event['event_location']);
	$table .= '
		<td class="rex-table-action">
			<a href="' . rex_url::currentBackendPage(['func' => 'edit_event', 'event_id' => urlencode($event['event_id'])]) . '">
				<i class="rex-icon rex-icon-edit"></i> editieren
			</a>
		</td>';
	$table .= '
		<td class="rex-table-action">
			<a href="' . rex_url::currentBackendPage(['func' => 'delete_event', 'event_id' => urlencode($event['event_id'])]) . '">
				<i class="rex-icon rex-icon-delete"></i> löschen
			</a>
		</td>';
	$table .= '</tr>';
}
$table .= '</tbody> </table>';

$fragment->setVar('content', $msg . $table, false);
echo $fragment->parse('core/page/section.php');
