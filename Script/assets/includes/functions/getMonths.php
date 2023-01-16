<?php
/* Get months */
function getMonths()
{
    global $lang;

    $months[1] = array('january', $lang['january']);
    $months[2] = array('february', $lang['february']);
    $months[3] = array('march', $lang['march']);
    $months[4] = array('april', $lang['april']);
    $months[5] = array('may', $lang['may']);
    $months[6] = array('june', $lang['june']);
    $months[7] = array('july', $lang['july']);
    $months[8] = array('august', $lang['august']);
    $months[9] = array('september', $lang['september']);
    $months[10] = array('october', $lang['october']);
    $months[11] = array('november', $lang['november']);
    $months[12] = array('december', $lang['december']);

    return $months;
}