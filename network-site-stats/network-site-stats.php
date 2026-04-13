<?php
/**
 * Plugin Name: Network Site Stats
 * Description: Hiển thị thống kê các site con trong mạng lưới WordPress Multisite
 * Version: 1.0.0
 * Author: Bac XJE
 * Network: true
 * Text Domain: network-site-stats
 */

if (!defined('ABSPATH')) exit;

class Network_Site_Stats {
    
    public function __construct() {
        add_action('network_admin_menu', array($this, 'add_network_menu'));
    }
    
    public function add_network_menu() {
        add_menu_page(
            'Network Site Stats',
            'Site Stats',
            'manage_network',
            'network-site-stats',
            array($this, 'display_stats_page'),
            'dashicons-chart-bar',
            30
        );
    }
    
    public function display_stats_page() {
        if (!current_user_can('manage_network')) {
            wp_die('Bạn không có quyền truy cập trang này.');
        }
        
        $sites = get_sites(array('number' => 100));
        ?>
        <div class="wrap">
            <h1>
                <span class="dashicons dashicons-chart-bar" style="font-size: 32px; margin-right: 10px;"></span>
                Thống Kê Mạng Lưới Website
            </h1>
            <p>Tổng số site: <strong><?php echo count($sites); ?></strong></p>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th width="8%">ID</th>
                        <th width="25%">Tên Site</th>
                        <th width="30%">URL</th>
                        <th width="12%">Số Bài Viết</th>
                        <th width="25%">Bài Mới Nhất</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sites as $site): ?>
                        <?php
                        switch_to_blog($site->blog_id);
                        
                        $post_count = wp_count_posts('post')->publish;
                        $latest_post = wp_get_recent_posts(array('numberposts' => 1));
                        $latest_date = !empty($latest_post) ? 
                            get_the_date('d/m/Y H:i', $latest_post[0]['ID']) : 
                            'Chưa có bài viết';
                        
                        restore_current_blog();
                        ?>
                        <tr>
                            <td><strong><?php echo $site->blog_id; ?></strong></td>
                            <td>
                                <strong><?php echo get_blog_details($site->blog_id)->blogname; ?></strong>
                            </td>
                            <td>
                                <a href="<?php echo get_site_url($site->blog_id); ?>" target="_blank">
                                    <?php echo get_site_url($site->blog_id); ?>
                                </a>
                            </td>
                            <td><?php echo $post_count; ?></td>
                            <td><?php echo $latest_date; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <style>
                .wrap h1 { display: flex; align-items: center; }
                .wp-list-table th { background: #f0f0f1; font-weight: 600; }
                .wp-list-table td { vertical-align: middle; }
            </style>
        </div>
        <?php
    }
}

if (is_multisite()) {
    new Network_Site_Stats();
}
