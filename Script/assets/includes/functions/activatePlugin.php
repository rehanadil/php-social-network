<?php
function activate_plugin ($P)
{
    global $PLUGINS;

    if ( isset($PLUGINS[$P]) )
    {
        $PLUGINS[$P] = true;
    }
}