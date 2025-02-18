<?php
/**
 * Plugin Name: Simple Redirect Manager
 * Description: Manage custom redirects.
 * Version: 1.0
 * Author: V.Krykun
 */

if (!defined('ABSPATH')) {
    exit;
}

function crm_add_admin_menu() {
    add_menu_page('Simple Redirect Manager', 'Simple Redirect Manager', 'manage_options', 'crm_redirect_manager', 'crm_redirect_manager_page');
}
add_action('admin_menu', 'crm_add_admin_menu');

function crm_register_settings() {
    register_setting('crm_redirect_group', 'crm_redirects', 'crm_sanitize_redirects');
}
add_action('admin_init', 'crm_register_settings');

function crm_sanitize_redirects($input) {
    $sanitized = [];
    if (!empty($input) && is_array($input)) {
        foreach ($input as $redirect) {
            if (!empty($redirect['source']) && !empty($redirect['target'])) {
                $sanitized[] = [
                    'source' => trailingslashit(trim($redirect['source'])),
                    'target' => esc_url_raw(trim($redirect['target'])),
                    'code'   => in_array($redirect['code'], ['301', '302', '307', '308']) ? $redirect['code'] : '301',
                ];
            }
        }
    }
    return $sanitized;
}

function crm_redirect_manager_page() {
    ?>
    <div class="wrap">
        <h1>Simple Redirect Manager</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('crm_redirect_group');
            do_settings_sections('crm_redirect_group');
            $redirects = get_option('crm_redirects', []);
            ?>
            <table class="form-table">
                <thead>
                    <tr>
                        <th>Source URL</th>
                        <th>Target URL</th>
                        <th>Redirect Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="crm-redirects-list">
                    <?php if (!empty($redirects) && is_array($redirects)): ?>
                        <?php foreach ($redirects as $index => $redirect): ?>
                            <tr>
                                <td><input type="text" name="crm_redirects[<?php echo $index; ?>][source]" value="<?php echo esc_attr($redirect['source'] ?? ''); ?>" required /></td>
                                <td><input type="text" name="crm_redirects[<?php echo $index; ?>][target]" value="<?php echo esc_attr($redirect['target'] ?? ''); ?>" required /></td>
                                <td>
                                    <select name="crm_redirects[<?php echo $index; ?>][code]">
                                        <option value="301" <?php selected($redirect['code'] ?? '301', '301'); ?>>301 Permanent</option>
                                        <option value="302" <?php selected($redirect['code'] ?? '302', '302'); ?>>302 Temporary</option>
                                        <option value="307" <?php selected($redirect['code'] ?? '307', '307'); ?>>307 Temporary Redirect</option>
                                        <option value="308" <?php selected($redirect['code'] ?? '308', '308'); ?>>308 Permanent Redirect</option>
                                    </select>
                                </td>
                                <td><button type="button" class="button crm-remove-redirect">Remove</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <button type="button" class="button" id="crm-add-redirect">Add Redirect</button>
            <?php submit_button(__('Save Changes', 'textdomain')); ?>
        </form>
    </div>
    <script>
        document.getElementById('crm-add-redirect').addEventListener('click', function () {
            let index = document.querySelectorAll('#crm-redirects-list tr').length;
            let row = document.createElement('tr');
            row.innerHTML = '<td><input type="text" name="crm_redirects['+index+'][source]" required /></td>' +
                            '<td><input type="text" name="crm_redirects['+index+'][target]" required /></td>' +
                            '<td>' +
                                '<select name="crm_redirects['+index+'][code]">' +
                                    '<option value="301">301 Permanent</option>' +
                                    '<option value="302">302 Temporary</option>' +
                                    '<option value="307">307 Temporary Redirect</option>' +
                                    '<option value="308">308 Permanent Redirect</option>' +
                                '</select>' +
                            '</td>' +
                            '<td><button type="button" class="button crm-remove-redirect">Remove</button></td>';
            document.getElementById('crm-redirects-list').appendChild(row);
        });

        document.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('crm-remove-redirect')) {
                e.target.closest('tr').remove();
            }
        });
    </script>
    <?php
}

function crm_redirect_catcher() {
    if (is_admin()) {
        return;
    }
    global $wp;

    $redirects = get_option('crm_redirects', []);

    if (!empty($redirects) && is_array($redirects)) {
        
        error_log( print_r( $redirects, true ) );

        $current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $current_url = add_query_arg( $wp->query_vars, home_url( $wp->request ) );

        
        foreach ($redirects as $redirect) {
            if (!isset($redirect['source'], $redirect['target'], $redirect['code'])) {
                continue;
            }
            $source_path = trim(parse_url($redirect['source'], PHP_URL_PATH), '/');
             error_log('URL: '. $source_path );
            
            $source_query = parse_url($redirect['source'], PHP_URL_QUERY) ?? '';

            if (strpos($redirect['target'], '?') !== false) {
                $full_target = $redirect['target'];
            } else {
                $full_target = $redirect['target'] . ($source_query ? '?' . $source_query : '');
            }

            if ($current_path === $source_path) {
                $code = (int) $redirect['code'];
                wp_redirect($full_target, $code);
                exit;
            }
        }
    }
}
add_action('template_redirect', 'crm_redirect_catcher');

