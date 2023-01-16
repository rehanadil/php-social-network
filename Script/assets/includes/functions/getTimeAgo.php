<?php
function getTimeAgo($unix, $details=false)
{
    global $time;
    
    if ($details == true)
    {
        if (date('Y', $unix) == date('Y'))
        {
            if (date('dM', $unix) == date('dM'))
            {
                return date('h:i A', $unix);
            }
            else
            {
                return date('d M - h:i A', $unix);
            }
        }
        else
        {
            return date('d M Y - h:i A', $unix);
        }
    }
    else
    {
        $interval = 'Just now';
        
        if ($unix > $time)
        {
            $diff = $unix - $time;
            $prefix = 'after';
            $math = 'round';
        }
        else
        {
            $diff = $time - $unix;
            $prefix = 'before';
            $math = 'floor';
        }
        
        if ($diff >= 120)
        {
            $reminder = $math($diff / 60);
            $suffix = 'min';
            
            if ($diff >= (60 * 60))
            {
                $reminder = $math($diff / (60 * 60));
                $suffix = 'hr';
                
                if ($diff >= (60 * 60 * 24))
                {
                    $reminder = $math($diff / (60 * 60 * 24));
                    $suffix = 'day';
                    
                    if ($diff >= (60 * 60 * 24 * 7))
                    {
                        $reminder = $math($diff / (60 * 60 * 24 * 7));
                        $suffix = 'week';
                        
                        if ($diff > (60 * 60 * 24 * 31))
                        {
                            $reminder = $math($diff / (60 * 60 * 24 * 31));
                            $suffix = 'month';
                            
                            if ($diff > (60 * 60 * 24 * 30 * 12))
                            {
                                $reminder = $math($diff / (60 * 60 * 24 * 30 * 12));
                                $suffix = 'yr';
                            }
                        }
                    }
                }
            }
            
            $interval = $reminder . ' ' . $suffix;
            
            if ($reminder != 1)
            {
                $interval .= 's';
            }
            
            if ($prefix == "after")
            {
                $interval = 'after ' . $interval;
            }
        }
        
        return $interval;
    }
}
