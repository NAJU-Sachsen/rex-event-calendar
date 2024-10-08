<?php

// upgrade to v0.0.3
if (rex_version::compare($this->getVersion(), '0.0.3', '<')) {
    rex_sql_table::get('naju_event')
        ->ensureColumn(new rex_sql_column('event_type', "enum ('camp', 'workshop', 'work_assignment', 'group_meeting')"))
        ->alter();
}

// upgrade to v0.0.4
if (rex_version::compare($this->getVersion(), '0.0.4', '<')) {
    rex_sql_table::get('naju_event')
        ->ensureColumn(new rex_sql_column('event_type', "enum ('camp', 'workshop', 'work_assignment', 'group_meeting', 'other')"))
        ->alter();
}


// upgrade to v0.1.0
if (rex_version::compare($this->getVersion(), '0.1.0', '<')) {
    rex_sql_table::get('naju_event')
        ->ensureColumn(new rex_sql_column('event_start_time', 'time', true))
        ->ensureColumn(new rex_sql_column('event_start_time', 'time', true))
        ->ensureColumn(new rex_sql_column('event_type', "enum ('camp', 'workshop', 'work_assignment', 'group_meeting', 'excursion', 'other')"))
        ->alter();
}


// upgrade to v0.4.0
if (rex_version::compare($this->getVersion(), '0.4.0', '<')) {
    rex_sql_table::get('naju_event')
        ->ensureColumn(new rex_sql_column('event_booked_out', 'bool', false, false))
        ->alter();
}


// upgrade to v0.5.0
if (rex_version::compare($this->getVersion(), '0.5.0', '<')) {
    rex_sql_table::get('naju_event')
        ->ensureColumn(new rex_sql_column('event_type',"enum ('camp', 'workshop', 'work_assignment', 'group_meeting', 'excursion', 'other', 'holiday_event')"))
        ->alter();
}


// upgrade to v0.6.0
if (rex_version::compare($this->getVersion(), '0.6.0', '<')) {
    rex_sql_table::get('naju_event')
        ->ensureColumn(new rex_sql_column('event_tags', 'text', false, ''))
        ->alter();

    rex_sql_table::get('naju_event_tags')
        ->addColumn(new rex_sql_column('tag_name', 'varchar(75)', false, null, 'unique'))
        ->create();
}
