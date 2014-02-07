<?php

/**
 * PhileTags
 *
 * Originally a pico plugin by DanReeves: https://github.com/DanReeves/picotags
 * Adds page tagging functionality to Phile.
 *
 * @author Philipp Schmitt
 * @link lxl.io
 * @license http://mit-license.org/
 */
class PhileTags extends \Phile\Plugin\AbstractPlugin implements \Phile\EventObserverInterface {

    private $is_tag;
    private $current_tag;
    private $tag_separator;

    private $config;

    public function __construct() {
        \Phile\Event::registerEvent('config_loaded', $this);
        \Phile\Event::registerEvent('before_render_template', $this);
        \Phile\Event::registerEvent('after_read_file_meta', $this);
        \Phile\Event::registerEvent('request_uri', $this);
        $this->config = \Phile\Registry::get('Phile_Settings');
    }

    public function on($eventKey, $data = null) {
        if ($eventKey == 'config_loaded') {
            $this->config_loaded();
        } elseif ($eventKey == 'request_uri') {
            $this->request_uri($data['uri']);
        } elseif ($eventKey == 'before_render_template') {
            $this->export_twig_vars();
        } elseif ($eventKey == 'after_read_file_meta') {
            if (isset($data['meta']['tags'])) {
                $data['meta']['tags'] = $this->tags_convert($data['meta']['tags']);
            }
            if ($this->is_tag) {
                // TODO User configurable template name
                $data['meta']['template'] = "tag";
            }
        }
        // TODO call getPages() somewhere
    }

    private function tags_convert($tags) {
        if (!isset($tags)) return null;
        $tags = explode($this->tag_separator, $tags);
        asort($tags);
        return $tags;
    }

    private function config_loaded() {
        // merge the arrays to bind the settings to the view
        // Note: this->config takes precedence
        $this->config = array_merge($this->settings, $this->config);
        if (isset ($this->config['tag_separator'])) {
            $this->tag_separator = $this->config['tag_separator'];
        } else {
            $this->tag_separator = ',';
        }
    }

    private function request_uri(&$uri) {
        // Set is_tag to true if the first four characters of the URL are 'tag/'
        $this->is_tag = (substr($uri, 1, 4) === 'tag/');
        error_log("URI: " . $uri . ' ' . ($this->is_tag ? "TAG PAGE" : "not a tag/ page"), 0);
        error_log("Substr: " . substr($uri, 1, 4), 0);
        // If the URL does start with 'tag/', grab the rest of the URL
        if ($this->is_tag) $this->current_tag = substr($uri, 5);
    }

    private function export_twig_vars() {
        if ($this->is_tag) {
            if (\Phile\Registry::isRegistered('templateVars')) {
                $twig_vars = \Phile\Registry::get('templateVars');
            } else {
                $twig_vars = array();
            }

            // Override 404 header
            header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
            // Set page title to #TAG
            $twig_vars['meta']['title'] = "#" . $this->current_tag;
            // Return current tag and list of all tags as Twig vars
            $twig_vars['current_tag'] = $this->current_tag; /* {{ current_tag }} is a string*/
            // TODO remove following line?
            $twig_vars['tag_list'] = $this->tag_list; /* {{ tag_list }} in an array*/
            \Phile\Registry::set('templateVars', $twig_vars);
        }
    }

}
