jQuery(document).ready(function($) {
    // AJAX add-repository form
    $('#wp-git-plugins-add-repo').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        let repoUrl = $('#repo-url').val().trim();
        // Always use 'main' branch for initial add
        const branch = 'main';

        // Validate repo URL for public repos only
        if (!repoUrl.match(/^https?:\/\/github\.com\/[^\/]+\/[^\/]+(\.git)?\/?$/)) {
            showNotice('error', 'Invalid GitHub repository URL. Format: https://github.com/owner/repo or https://github.com/owner/repo.git<br><strong>URL used:</strong> ' + repoUrl);
            form.find('button[type="submit"]').prop('disabled', false).html('Add Repository');
            return;
        }

        form.find('button[type="submit"]').prop('disabled', true).html('<span class="spinner is-active"></span>');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_git_plugins_add_repo',
                nonce: wpGitPlugins.ajax_nonce,
                repo_url: repoUrl,
                repo_branch: branch
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', response.data.message || 'Repository added successfully.');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showNotice('error', response.data.message || 'Failed to add repository.');
                    form.find('button[type="submit"]').prop('disabled', false).html('Add Repository');
                }
            },
            error: function() {
                showNotice('error', 'An error occurred. Please try again.');
                form.find('button[type="submit"]').prop('disabled', false).html('Add Repository');
            }
        });
    });

    // Handle branch selection and change
    $('.wp-git-plugins-container').on('change', '.branch-select', function(e) {
        const select = $(this);
        const repoUrl = select.data('repo-url');
        const newBranch = select.val();
        select.prop('disabled', true);
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_git_plugins_change_branch',
                nonce: wpGitPlugins.ajax_nonce,
                repo_url: repoUrl,
                branch: newBranch,
                remove_and_clone: 1
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', 'Branch changed to ' + newBranch);
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showNotice('error', response.data.message || 'Failed to change branch');
                    select.prop('disabled', false);
                }
            },
            error: function() {
                showNotice('error', 'An error occurred. Please try again.');
                select.prop('disabled', false);
            }
        });
    });
    // Handle repository actions
    $('.wp-git-plugins-container').on('click', '.install-plugin', function(e) {
        e.preventDefault();
        const button = $(this);
        const repoUrl = button.data('repo');
        const isPrivate = $('#is-private').is(':checked') ? 1 : 0;
        
        button.prop('disabled', true).html('<span class="spinner is-active"></span>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_git_plugins_install',
                nonce: wpGitPlugins.ajax_nonce,
                repo_url: repoUrl
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', response.data.message);
                is_private: isPrivate
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showNotice('error', response.data.message);
                    button.prop('disabled', false).html('<span class="dashicons dashicons-download"></span>');
                }
            },
            error: function() {
                showNotice('error', 'An error occurred. Please try again.');
                button.prop('disabled', false).html('<span class="dashicons dashicons-download"></span>');
            }
        });
    });

    // Activate plugin
    $('.wp-git-plugins-container').on('click', '.activate-plugin', function(e) {
        e.preventDefault();
        const button = $(this);
        const pluginSlug = button.data('plugin');
        
        button.prop('disabled', true).html('<span class="spinner is-active"></span>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_git_plugins_activate',
                nonce: wpGitPlugins.ajax_nonce,
                plugin_slug: pluginSlug
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', response.data.message);
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showNotice('error', response.data.message);
                    button.prop('disabled', false).html('<span class="dashicons dashicons-controls-play"></span>');
                }
            },
            error: function() {
                showNotice('error', 'An error occurred. Please try again.');
                button.prop('disabled', false).html('<span class="dashicons dashicons-controls-play"></span>');
            }
        });
    });

    // Deactivate plugin
    $('.wp-git-plugins-container').on('click', '.deactivate-plugin', function(e) {
        e.preventDefault();
        const button = $(this);
        const pluginSlug = button.data('plugin');
        
        button.prop('disabled', true).html('<span class="spinner is-active"></span>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_git_plugins_deactivate',
                nonce: wpGitPlugins.ajax_nonce,
                plugin_slug: pluginSlug
            },
            success: function() {
                showNotice('success', 'Plugin deactivated successfully');
                setTimeout(() => window.location.reload(), 1500);
            },
            error: function() {
                showNotice('error', 'An error occurred. Please try again.');
                button.prop('disabled', false).html('<span class="dashicons dashicons-controls-pause"></span>');
            }
        });
    });

    // Delete repository
    $('.wp-git-plugins-container').on('click', '.delete-repo, .delete-plugin', function(e) {
        e.preventDefault();
        
        if (!confirm(wpGitPlugins.i18n.confirm_delete)) {
            return;
        }
        
        const button = $(this);
        const repoUrl = button.data('repo');
        
        button.prop('disabled', true).html('<span class="spinner is-active"></span>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_git_plugins_delete',
                nonce: wpGitPlugins.ajax_nonce,
                repo_url: repoUrl
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', response.data.message);
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showNotice('error', response.data.message);
                    button.prop('disabled', false).html('<span class="dashicons dashicons-trash"></span>');
                }
            },
            error: function() {
                showNotice('error', 'An error occurred. Please try again.');
                button.prop('disabled', false).html('<span class="dashicons dashicons-trash"></span>');
            }
        });
    });

    // Check for updates
    $('#wp-git-plugins-check-updates').on('click', function(e) {
        e.preventDefault();
        const button = $(this);
        
        button.prop('disabled', true).html('<span class="spinner is-active"></span> Checking...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wp_git_plugins_check_updates',
                nonce: wpGitPlugins.ajax_nonce
            },
            success: function(response) {
                if (response.success) {
                    if (response.data.updates && response.data.updates.length > 0) {
                        showNotice('success', 'Updates are available for ' + response.data.updates.length + ' plugins');
                    } else {
                        showNotice('info', 'All plugins are up to date');
                    }
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showNotice('error', response.data.message || 'An error occurred while checking for updates');
                }
            },
            error: function() {
                showNotice('error', 'An error occurred while checking for updates');
            },
            complete: function() {
                button.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Check for Updates');
            }
        });
    });

    // Show notice function
    function showNotice(type, message) {
        const notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.wrap h1').after(notice);
        
        // Auto-remove notice after 5 seconds
        setTimeout(() => {
            notice.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Handle tab switching
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        const target = $(this).data('target');
        $('.tab-content').hide();
        $(target).show();
    });

    // Initialize first tab as active
    $('.nav-tab:first').addClass('nav-tab-active');
    $('.tab-content').hide().first().show();
});
