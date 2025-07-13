# WP Git Plugins

![Banner](assets/banner-772x250.png)

A WordPress plugin to manage other plugins directly from Git repositories with auto-update capabilities.

## Features

- **Git Repository Management**: Add, remove, and update plugins directly from Git repositories
- **Version Control**: Always know which version of each plugin is installed
- **Repo Plugins Overview**: A clean list of your installed repo plugins.
- **Debug Log Overview**: Clear logging of plugin actions in the dashboard.
- **Private Repoitories**: Compatible with Wordpress plugins in private repos.

## Installation

1. Download the plugin as a ZIP file
2. Go to WordPress admin > Plugins > Add New > Upload Plugin
3. Upload the ZIP file and activate the plugin
4. Navigate to Plugins > Git Plugins to start adding repositories

### GitHub Authentication

For private repositories or to avoid GitHub API rate limits, you'll need to set up a GitHub Personal Access Token:

1. Go to [GitHub Settings > Developer settings > Personal access tokens](https://github.com/settings/tokens)
2. Click "Generate new token"
3. Give it a descriptive name (e.g., "WP Git Plugins")
4. Select the `repo` scope (full control of private repositories)
5. Generate the token and copy it
6. In WordPress, go to Plugins > Git Plugins > Settings
7. Paste your token in the "GitHub Access Token" field and save
8. Save your Github Username to connect to your private repositories 

## Usage

### Managing Repositories

1. **Adding a Repository**
   - Go to Plugins > Git Plugins
   - Enter the GitHub repository URL (e.g., `https://github.com/username/repository` or `git@github.com:username/repository.git`)
   - Click "Add Repository"


2. **Updating a Plugin**
   - Find the repository in the list
   - Click the "Update" button to pull the latest changes

3. **Removing a Repository**
   - Find the repository in the list
   - Click the "Remove" button to remove it from the list
   - Note: This does not uninstall the plugin, it only removes it from the Git Plugins management

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Git (for command-line operations)
- PHP ZIP extension (for handling ZIP files)

## Security

- Only users with `manage_options` capability can access the plugin settings
- All form submissions are secured with nonces
- Inputs are properly sanitized and validated

## Data Storage

- Settings will be stored in the database under the wpgp_settings table
- Repositories added to the list are stored under the wpgp_repos table

## License

GPL v2 or later

## Support

For support, please open an issue on the [GitHub repository](https://github.com/WeAreCode045/wp-git-plugins/issues).
