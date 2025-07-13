<?php
class WP_Git_Plugins_Settings {
    private $plugin_name;
    private $version;
    private $options;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->options = $this->get_all_settings();
    }

    // No longer register settings with WordPress options API
    // Settings are now stored in wpgp_settings table
    public function register_settings() {
        // No-op: settings handled via custom table
    }
    /**
     * Get a setting value by key
     */
    public function get_setting($key) {
        global $wpdb;
        $table = $wpdb->prefix . 'wpgp_settings';
        $row = $wpdb->get_row($wpdb->prepare("SELECT setting_value FROM $table WHERE setting_key = %s", $key));
        return $row ? maybe_unserialize($row->setting_value) : null;
    }

    /**
     * Set a setting value by key
     */
    public function set_setting($key, $value) {
        global $wpdb;
        $table = $wpdb->prefix . 'wpgp_settings';
        $serialized = maybe_serialize($value);
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE setting_key = %s", $key));
        if ($exists) {
            $wpdb->update($table, ['setting_value' => $serialized], ['setting_key' => $key]);
        } else {
            $wpdb->insert($table, ['setting_key' => $key, 'setting_value' => $serialized]);
        }
        return true;
    }

    /**
     * Get all settings as associative array
     */
    public function get_all_settings() {
        global $wpdb;
        $table = $wpdb->prefix . 'wpgp_settings';
        $rows = $wpdb->get_results("SELECT setting_key, setting_value FROM $table");
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row->setting_key] = maybe_unserialize($row->setting_value);
        }
        return $settings;
    }

    public function github_username_callback() {
        // Removed GitHub username field (private repo support removed)
    }

    /**
     * Get GitHub username
     */
    public function get_github_username() {
        return $this->get_setting('github_username');
    }

    /**
     * Set GitHub username
     */
    public function set_github_username($username) {
        return $this->set_setting('github_username', $username);
    }

    /**
     * Get GitHub personal access token
     */
    public function get_github_token() {
        return $this->get_setting('github_token');
    }

    /**
     * Set GitHub personal access token
     */
    public function set_github_token($token) {
        return $this->set_setting('github_token', $token);
    }

    public function get_repositories_option() {
        return $this->repositories_option;
    }
    public function set_repositories_option($option_name) {
        if (empty($option_name)) {
            return new WP_Error('invalid_option_name', __('Option name cannot be empty.', 'wp-git-plugins'));
        }
        $this->repositories_option = $option_name;
        update_option('wp_git_plugins_repositories_option', $option_name);
        $this->log('Repositories option set successfully');
        return true;
    }
    public function get_debug_log_enabled() {
        return defined('WP_DEBUG') && WP_DEBUG === true;
    }
    public function enable_debug_log() {
        if (!defined('WP_DEBUG') || WP_DEBUG !== true) {
            return new WP_Error('debug_disabled', __('Debug mode is not enabled in WordPress.', 'wp-git-plugins'));
        }
        $this->log('Debug logging enabled');
        return true;
    }
    

    public function get_debug_log() {
        return $this->debug_log;
    }
    
    public function clear_debug_log() {
        $this->debug_log = [];
        return true;
    }

    public function github_settings_section_callback() {
        echo '<p>' . esc_html__('Configure your GitHub settings below.', 'wp-git-plugins') . '</p>';
    }

    public function github_access_token_callback() {
        $token = $this->options['github_access_token'] ?? '';
        ?>
        <input type="password" id="github_access_token" name="wp_git_plugins_options[github_access_token]" 
               value="<?php echo esc_attr($token); ?>" class="regular-text" />
        <p class="description">
            <?php esc_html_e('Enter your GitHub Personal Access Token with repo scope for private repositories.', 'wp-git-plugins'); ?>
            <a href="https://github.com/settings/tokens" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e('Generate token', 'wp-git-plugins'); ?>
            </a>
        </p>
        <?php
    }

    public function check_updates_interval_callback() {
        $interval = $this->options['check_updates_interval'] ?? 'twicedaily';
        $intervals = [
            'hourly' => __('Hourly', 'wp-git-plugins'),
            'twicedaily' => __('Twice Daily', 'wp-git-plugins'),
            'daily' => __('Daily', 'wp-git-plugins'),
            'weekly' => __('Weekly', 'wp-git-plugins')
        ];
        ?>
        <select id="check_updates_interval" name="wp_git_plugins_options[check_updates_interval]">
            <?php foreach ($intervals as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($interval, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description">
            <?php esc_html_e('How often to check for plugin updates.', 'wp-git-plugins'); ?>
        </p>
        <?php
    }

    public function sanitize_options($input) {
        $sanitized_input = [];

        if (isset($input['github_username'])) {
            $sanitized_input['github_username'] = sanitize_text_field($input['github_username']);
        }

        if (isset($input['github_access_token'])) {
            $sanitized_input['github_access_token'] = sanitize_text_field($input['github_access_token']);
        }

        if (isset($input['check_updates_interval'])) {
            $valid_intervals = ['hourly', 'twicedaily', 'daily', 'weekly'];
            $sanitized_input['check_updates_interval'] = in_array($input['check_updates_interval'], $valid_intervals)
                ? $input['check_updates_interval']
                : 'twicedaily';
        }

        return $sanitized_input;
    }
}
