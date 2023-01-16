<?php
/* Validate username */
function validateUsername($u)
{
    if (strlen($u) > 3 && ! is_numeric($u) && preg_match('/^[A-Za-z0-9_]+$/', $u))
    {
        return true;
    }
}
