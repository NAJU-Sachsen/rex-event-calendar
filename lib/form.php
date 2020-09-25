<?php

class naju_event_form extends rex_form
{

    public function preSave($fieldsetName, $fieldName, $fieldValue, rex_sql $saveSql)
    {
        if ($fieldName == 'event_target_group_type') {
            return naju_form::multivalues2SQL($fieldValue);
        }

        return $fieldValue;
    }

}

