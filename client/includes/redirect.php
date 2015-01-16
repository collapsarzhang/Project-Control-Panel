<?php

function safe_redirect($url, $exit=true) {
 
    // Only use the header redirection if headers are not already sent
    if (!headers_sent()){
 
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url);
 
        // Optional workaround for an IE bug (thanks Olav)
        header("Connection: close");
    }
 
    // HTML/JS Fallback:
    // If the header redirection did not work, try to use various methods other methods
 
    print '<html>';
    print '<head><title>Redirecting you...</title>';
    print '<meta http-equiv="Refresh" content="0;url='.$url.'" />';
    print '</head>';
    print '<body onload="location.replace(\''.$url.'\')">';
 
    // If the javascript and meta redirect did not work, 
    // the user can still click this link
    print 'You should be redirected to this URL:<br />';
    print "<a href=".$url.">".$url."</a><br /><br />";
 
    print 'If you are not, please click on the link above.<br />';    
 
    print '</body>';
    print '</html>';
 
    // Stop the script here (optional)
    if ($exit) exit;
}

?>