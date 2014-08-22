<?php

/**
 * values and methods for the site management
 */

new Aoloe\Debug();
use function Aoloe\debug as debug;

class Site {
    private $path_relative = null;
    private $cdn = null;
    private $online = null;

    private $css = array();
    private $js = array();
    private $font = array();

    /**
     * get a relative path to the root of the server
     * TODO: accept a baseroot which is not DOCUMENT_ROOT
     */
    public function get_path_relative($path = null) {
        if (!isset($this->path_relative)) {
            $this->path_relative = str_repeat('../', substr_count($_SERVER['REQUEST_URI'], '/', 1));
        }
        return $this->path_relative.(isset($path) ? $path : '');
    }

    public function is_online() {
        if (!isset($this->online)) {
            $this->online =  gethostbyname('ideale.ch') !== 'ideale.ch';
            if (!isset($this->cdn)) {
                $this->cdn = $this->online;
            }
        }
        return $this->online;
    }

    private function is_cdn() {
        if (!isset($this->cdn)) {
            $this->is_online();
        }
        return $this->cdn;
    }

    public function add_css($local, $web = null) {
        $this->css[] = $this->get_http_ressource_path($local, $web);
    }
    // TODO: remove duplicates
    public function get_css() {
        return array_unique($this->css);
    }

    public function add_js($local, $web = null) {
        $this->js[] = $this->get_http_ressource_path($local, $web);
    }
    // TODO: remove duplicates
    public function get_js() {
        return array_unique($this->js);
    }

    public function add_font($local, $web = null) {
        $this->font[] = $this->get_http_ressource_path($local, $web);
    }
    // TODO: remove duplicates
    public function get_font() {
        return array_unique($this->font);
    }

    /**
     * return the optional cdn url only if the site is connected to the internet and using the CDN
     * has not been disabled.
     * add the relative path to local urls.
     */
    private function get_http_ressource_path($local = null, $cdn = null) {
        // debug('host', );
        if (!preg_match('/^http[s]?:\/\//', $local)) {
            $local = $this->get_path_relative().$local;
        }
        if (isset($cdn)) {
            return  $this->is_cdn() ? $cdn : $local;
        } else {
            return $local;
        }
    }
}
