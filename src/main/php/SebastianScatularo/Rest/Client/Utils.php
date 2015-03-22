<?php

namespace SebastianScatularo\Rest\Client;

/**
 * Utils
 *
 * @author sebastianscatularo@gmail.com
 */
class Utils
{

    public static function hasCurlSupport()
    {
        if (in_array('curl', get_loaded_extensions())) {
            return true;
        } else {
            return false;
        }
    }

}
