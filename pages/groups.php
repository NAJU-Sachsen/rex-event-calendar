<?php

$fragment = new rex_fragment();
$fragment->setVar('title', 'Ortsgruppen');

$local_groups = rex_list::factory('select group_id, group_name from naju_local_group');
$local_groups->setColumnLabel('group_id', 'ID');
$local_groups->setColumnLabel('group_name', 'Ortsgruppe');

$fragment->setVar('content', $local_groups->get(), false);
echo $fragment->parse('core/page/section.php');
