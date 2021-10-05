<?php
/**
 * Header Redirect
 *
 * Header redirect in two flavors
 * For very fine grained control over headers, you could use the Output
 * Library's set_header() function.
 *
 * @access    public
 *
 * @param string    the URL
 * @param string    the method: location or redirect
 *
 * @return    string
 */
if (!function_exists('redirect')) {
    function redirect($uri = '', $method = 'location', $http_response_code = 302)
    {
        if (!preg_match('#^https?://#i', $uri)) {
            $uri = site_url($uri);
        }
        
        switch ($method) {
            case 'refresh'    :
                header("Refresh:0;url=" . $uri);
                break;
            default            :
                get_instance()->hooks->_call_hook('post_controller');
                get_instance()->hooks->_call_hook('post_system');
                header("Location: " . $uri, true, $http_response_code);
                break;
        }
        exit;
    }
}

include_once APPPATH . '../system/helpers/url_helper.php';