
<?php

// upgrade to v0.0.3
if (rex_version::compare($this->getVersion(), '0.0.3', '<')) {
    rex_sql_table::get(rex::getTable('naju_event'))
        ->ensureColumn(new rex_sql_column('event_type', "enum ('camp', 'workshop', 'work_assignment', 'group_meeting')"))
        ->alter();
}
