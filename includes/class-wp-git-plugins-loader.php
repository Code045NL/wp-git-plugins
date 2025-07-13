<?php
class WP_Git_Plugins_Loader {
    /**
     * Create custom tables for settings and repositories
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $settings_table = $wpdb->prefix . 'wpgp_settings';
        $repos_table = $wpdb->prefix . 'wpgp_repos';

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql_settings = "CREATE TABLE $settings_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            setting_key varchar(191) NOT NULL,
            setting_value longtext NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY setting_key (setting_key)
        ) $charset_collate;";

        $sql_repos = "CREATE TABLE $repos_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            repo_url varchar(255) NOT NULL,
            branch varchar(100) NOT NULL,
            added_at datetime NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY repo_url (repo_url)
        ) $charset_collate;";

        dbDelta($sql_settings);
        dbDelta($sql_repos);
    }
    protected $actions;
    protected $filters;

    public function __construct() {
        $this->actions = [];
        $this->filters = [];
    }

    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {
        $hooks[] = [
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        ];

        return $hooks;
    }

    public function run() {
        foreach ($this->filters as $hook) {
            add_filter(
                $hook['hook'],
                [$hook['component'], $hook['callback']],
                $hook['priority'],
                $hook['accepted_args']
            );
        }

        foreach ($this->actions as $hook) {
            add_action(
                $hook['hook'],
                [$hook['component'], $hook['callback']],
                $hook['priority'],
                $hook['accepted_args']
            );
        }
    }
}
