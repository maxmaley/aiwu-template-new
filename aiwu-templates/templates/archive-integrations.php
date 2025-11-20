<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php do_action('astra_header'); ?>

<?php
// Get search parameter
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;

// Get integrations with search
$args = [
    'taxonomy' => 'template_integration',
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC',
    'number' => 12,
    'offset' => ($paged - 1) * 12,
];

if ($search) {
    $args['name__like'] = $search;
    $args['search'] = $search;
}

$integrations = get_terms($args);

// Get total count for pagination
$total_args = $args;
unset($total_args['number']);
unset($total_args['offset']);
$all_integrations = get_terms($total_args);
$total_found = is_array($all_integrations) ? count($all_integrations) : 0;
$total_pages = ceil($total_found / 12);
?>

<div class="aiwu-templates-container aiwu-integrations-archive">
    <div class="aiwu-templates-inner">
        <div class="aiwu-templates-header">
            <h1 class="aiwu-templates-title">Available <span>Integrations</span></h1>
            <p class="aiwu-templates-subtitle">Connect your favorite tools and services with AIWU workflows.</p>
        </div>

        <!-- Search Bar -->
        <form method="get" action="" class="aiwu-integrations-search-bar">
            <div class="aiwu-search-wrapper">
                <span class="aiwu-search-icon">🔍</span>
                <input type="text" name="s" class="aiwu-search-input" placeholder="Search integrations..." value="<?php echo esc_attr($search); ?>">
            </div>
            <button type="submit" class="aiwu-clear-btn aiwu-apply-btn">Search</button>
            <?php if ($search): ?>
                <a href="<?php echo home_url('/integrations/'); ?>" class="aiwu-clear-btn">Clear</a>
            <?php endif; ?>
        </form>

        <div class="aiwu-results-count">
            Showing <?php echo count($integrations); ?> of <?php echo $total_found; ?> integration<?php echo $total_found !== 1 ? 's' : ''; ?>
        </div>

        <?php if (!empty($integrations) && !is_wp_error($integrations)): ?>
            <div class="aiwu-integrations-grid">
                <?php foreach ($integrations as $integration):
                    $icon_id = get_term_meta($integration->term_id, 'icon_image_id', true);
                    $icon_url = $icon_id ? wp_get_attachment_url($icon_id) : '';
                    $term_link = get_term_link($integration);

                    // Count templates using this integration
                    $template_count = $integration->count;
                ?>
                    <a href="<?php echo esc_url($term_link); ?>" class="aiwu-integration-card">
                        <div class="aiwu-integration-card-icon">
                            <?php if ($icon_url): ?>
                                <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($integration->name); ?> icon" loading="lazy">
                            <?php else:
                                $initials = aiwu_get_integration_initials($integration->name);
                            ?>
                                <div class="aiwu-integration-card-initials">
                                    <span><?php echo esc_html($initials); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="aiwu-integration-card-content">
                            <h2 class="aiwu-integration-card-name"><?php echo esc_html($integration->name); ?></h2>
                            <?php if ($template_count > 0): ?>
                                <div class="aiwu-integration-card-count">
                                    <?php echo $template_count; ?> template<?php echo $template_count !== 1 ? 's' : ''; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="aiwu-pagination">
                    <?php
                    $base_url = home_url('/integrations/');
                    if ($search) {
                        $base_url = add_query_arg('s', $search, $base_url);
                    }

                    // Previous button
                    if ($paged > 1):
                        $prev_url = add_query_arg('paged', $paged - 1, $base_url);
                        echo '<a href="' . esc_url($prev_url) . '" class="aiwu-page-btn">←</a>';
                    else:
                        echo '<span class="aiwu-page-btn" disabled>←</span>';
                    endif;

                    // Page numbers
                    if ($paged > 2):
                        $first_url = $base_url;
                        echo '<a href="' . esc_url($first_url) . '" class="aiwu-page-btn">1</a>';
                        if ($paged > 3):
                            echo '<span class="aiwu-page-dots">...</span>';
                        endif;
                    endif;

                    for ($i = max(1, $paged - 1); $i <= min($total_pages, $paged + 1); $i++):
                        $page_url = ($i === 1) ? $base_url : add_query_arg('paged', $i, $base_url);
                        $active_class = $i === $paged ? ' active' : '';
                        echo '<a href="' . esc_url($page_url) . '" class="aiwu-page-btn' . $active_class . '">' . $i . '</a>';
                    endfor;

                    if ($paged < $total_pages - 1):
                        if ($paged < $total_pages - 2):
                            echo '<span class="aiwu-page-dots">...</span>';
                        endif;
                        $last_url = add_query_arg('paged', $total_pages, $base_url);
                        echo '<a href="' . esc_url($last_url) . '" class="aiwu-page-btn">' . $total_pages . '</a>';
                    endif;

                    // Next button
                    if ($paged < $total_pages):
                        $next_url = add_query_arg('paged', $paged + 1, $base_url);
                        echo '<a href="' . esc_url($next_url) . '" class="aiwu-page-btn">→</a>';
                    else:
                        echo '<span class="aiwu-page-btn" disabled>→</span>';
                    endif;
                    ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="aiwu-no-results show">
                <div class="aiwu-no-results-icon">🔍</div>
                <div class="aiwu-no-results-text">No integrations found</div>
                <?php if ($search): ?>
                    <div class="aiwu-no-results-hint">Try a different search term</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- CTA Section -->
        <div class="aiwu-cta-section">
            <div class="aiwu-cta-content">
                <h2 class="aiwu-cta-title">Connect All Your Tools with AIWU</h2>
                <p class="aiwu-cta-description">
                    Build powerful automations connecting all your favorite services with AIWU Workflow Builder.
                    Free visual automation tool that runs directly on your WordPress site.
                </p>
                <div class="aiwu-cta-features">
                    <div class="aiwu-cta-feature">
                        <div class="aiwu-cta-feature-icon">∞</div>
                        <div class="aiwu-cta-feature-text">
                            <div class="aiwu-cta-feature-title">Free Forever</div>
                            <div class="aiwu-cta-feature-desc">No monthly fees, unlimited workflows</div>
                        </div>
                    </div>
                    <div class="aiwu-cta-feature">
                        <div class="aiwu-cta-feature-icon">⚡</div>
                        <div class="aiwu-cta-feature-text">
                            <div class="aiwu-cta-feature-title">Visual Builder</div>
                            <div class="aiwu-cta-feature-desc">Drag-and-drop, no code needed</div>
                        </div>
                    </div>
                    <div class="aiwu-cta-feature">
                        <div class="aiwu-cta-feature-icon">🔑</div>
                        <div class="aiwu-cta-feature-text">
                            <div class="aiwu-cta-feature-title">Your API Keys</div>
                            <div class="aiwu-cta-feature-desc">Use OpenAI, Claude, or any provider</div>
                        </div>
                    </div>
                </div>
                <a href="https://aiwuplugin.com/ai-workflow-automation-builder-for-wordpress/" class="aiwu-cta-button" target="_blank" rel="noopener">
                    Explore Workflow Builder
                    <span class="aiwu-cta-arrow">→</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php do_action('astra_footer'); ?>

<?php wp_footer(); ?>
</body>
</html>
