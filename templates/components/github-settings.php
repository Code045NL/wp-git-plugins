<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['github_username'], $_POST['github_token'])) {
    // Basic nonce check can be added for security
    $username = sanitize_text_field($_POST['github_username']);
    $token = sanitize_text_field($_POST['github_token']);
    if (class_exists('WP_Git_Plugins_Settings')) {
        $settings = new WP_Git_Plugins_Settings('wp-git-plugins', '1.0.0');
        $settings->set_github_username($username);
        $settings->set_github_token($token);
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('GitHub settings saved.', 'wp-git-plugins') . '</p></div>';
    }
}
// Load settings from custom table
$settings = class_exists('WP_Git_Plugins_Settings') ? (new WP_Git_Plugins_Settings('wp-git-plugins', '1.0.0'))->get_all_settings() : [];
$username = $settings['github_username'] ?? '';
$token = $settings['github_token'] ?? '';
?>
<div class="wp-git-plugins-card">
    <h2><?php esc_html_e('GitHub Settings', 'wp-git-plugins'); ?></h2>
    <form method="post" action="">
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="github_username"><?php esc_html_e('GitHub Username', 'wp-git-plugins'); ?></label>
                </th>
                <td>
                    <input type="text" id="github_username" name="github_username" value="<?php echo esc_attr($username); ?>" class="regular-text" />
                    <p class="description"><?php esc_html_e('Enter your GitHub username.', 'wp-git-plugins'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="github_token"><?php esc_html_e('GitHub Personal Access Token', 'wp-git-plugins'); ?></label>
                </th>
                <td>
                    <input type="password" id="github_token" name="github_token" value="<?php echo esc_attr($token); ?>" class="regular-text" />
                    <p class="description">
                        <?php esc_html_e('Enter your GitHub Personal Access Token.', 'wp-git-plugins'); ?>
                        <a href="https://github.com/settings/tokens" target="_blank" rel="noopener noreferrer">
                            <?php esc_html_e('Generate token', 'wp-git-plugins'); ?>
                        </a>
                    </p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary">
                <?php esc_attr_e('Save Settings', 'wp-git-plugins'); ?>
            </button>
        </p>
    </form>
    <div class="github-token-help">
        <h3><?php esc_html_e('How to create a GitHub Personal Access Token', 'wp-git-plugins'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go to GitHub and log in to your account', 'wp-git-plugins'); ?></li>
            <li><?php esc_html_e('Click on your profile picture in the top right corner and select "Settings"', 'wp-git-plugins'); ?></li>
            <li><?php esc_html_e('In the left sidebar, click on "Developer settings"', 'wp-git-plugins'); ?></li>
            <li><?php esc_html_e('Click on "Personal access tokens" and then "Tokens (classic)"', 'wp-git-plugins'); ?></li>
            <li><?php esc_html_e('Click on "Generate new token" and then "Generate new token (classic)"', 'wp-git-plugins'); ?></li>
            <li><?php esc_html_e('Give your token a descriptive name', 'wp-git-plugins'); ?></li>
            <li><?php esc_html_e('Select the "repo" scope (for private repositories)', 'wp-git-plugins'); ?></li>
            <li><?php esc_html_e('Click "Generate token"', 'wp-git-plugins'); ?></li>
            <li><?php esc_html_e('Copy the generated token and paste it into the field above', 'wp-git-plugins'); ?></li>
        </ol>
    </div>
</div>
