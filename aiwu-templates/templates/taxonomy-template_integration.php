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
$term = get_queried_object();
$icon_id = get_term_meta($term->term_id, 'icon_image_id', true);
$icon_url = $icon_id ? wp_get_attachment_url($icon_id) : '';

// Get node fields
$triggers = get_term_meta($term->term_id, 'integration_triggers', true);
$actions = get_term_meta($term->term_id, 'integration_actions', true);
$logic_blocks = get_term_meta($term->term_id, 'integration_logic_blocks', true);

// Parse node data (array of arrays with title and description)
$triggers_data = !empty($triggers) ? $triggers : [];
$actions_data = !empty($actions) ? $actions : [];
$logic_blocks_data = !empty($logic_blocks) ? $logic_blocks : [];
?>

<div class="aiwu-template-detail aiwu-integration-page">
  <div class="aiwu-template-container">

    <!-- Breadcrumbs for SEO -->
    <nav class="aiwu-breadcrumbs" aria-label="Breadcrumb">
      <ol itemscope itemtype="https://schema.org/BreadcrumbList">
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" href="<?php echo home_url(); ?>">
            <span itemprop="name">Home</span>
          </a>
          <meta itemprop="position" content="1" />
        </li>
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" href="<?php echo home_url('/integrations/'); ?>">
            <span itemprop="name">Integrations</span>
          </a>
          <meta itemprop="position" content="2" />
        </li>
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <span itemprop="name"><?php echo esc_html($term->name); ?></span>
          <meta itemprop="position" content="3" />
        </li>
      </ol>
    </nav>

    <!-- Hero Section -->
    <div class="aiwu-template-hero aiwu-integration-hero">
      <div class="aiwu-integration-hero-content">
        <div class="aiwu-integration-hero-text">
          <h1 class="aiwu-template-title"><?php echo esc_html($term->name); ?> Integration</h1>
          <?php if (!empty($term->description)): ?>
            <div class="aiwu-template-description">
              <?php echo wpautop(wp_kses_post($term->description)); ?>
            </div>
          <?php endif; ?>
        </div>

        <?php if ($icon_url): ?>
          <div class="aiwu-integration-hero-icon">
            <div class="aiwu-integration-hero-icon-wrapper">
              <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($term->name); ?> icon" loading="lazy">
            </div>
          </div>
        <?php else:
          $initials = aiwu_get_integration_initials($term->name);
        ?>
          <div class="aiwu-integration-hero-icon">
            <div class="aiwu-integration-hero-icon-wrapper aiwu-integration-hero-icon-initials">
              <span><?php echo esc_html($initials); ?></span>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Available Nodes Section -->
    <?php if (!empty($triggers_data) || !empty($actions_data) || !empty($logic_blocks_data)): ?>
    <div class="aiwu-integration-nodes">
      <h2 class="aiwu-section-title">⚡ Available Nodes</h2>

      <div class="aiwu-nodes-grid">
        <?php if (!empty($triggers_data) && is_array($triggers_data)): ?>
        <div class="aiwu-node-category">
          <div class="aiwu-node-category-header">
            <div class="aiwu-node-category-icon">🎯</div>
            <h3 class="aiwu-node-category-title">Triggers</h3>
          </div>
          <div class="aiwu-node-items">
            <?php foreach ($triggers_data as $trigger):
              if (empty($trigger['title'])) continue;
            ?>
              <div class="aiwu-node-item">
                <h4 class="aiwu-node-item-title"><?php echo esc_html($trigger['title']); ?></h4>
                <?php if (!empty($trigger['description'])): ?>
                  <p class="aiwu-node-item-desc"><?php echo esc_html($trigger['description']); ?></p>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($actions_data) && is_array($actions_data)): ?>
        <div class="aiwu-node-category">
          <div class="aiwu-node-category-header">
            <div class="aiwu-node-category-icon">⚙️</div>
            <h3 class="aiwu-node-category-title">Actions</h3>
          </div>
          <div class="aiwu-node-items">
            <?php foreach ($actions_data as $action):
              if (empty($action['title'])) continue;
            ?>
              <div class="aiwu-node-item">
                <h4 class="aiwu-node-item-title"><?php echo esc_html($action['title']); ?></h4>
                <?php if (!empty($action['description'])): ?>
                  <p class="aiwu-node-item-desc"><?php echo esc_html($action['description']); ?></p>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($logic_blocks_data) && is_array($logic_blocks_data)): ?>
        <div class="aiwu-node-category">
          <div class="aiwu-node-category-header">
            <div class="aiwu-node-category-icon">🧩</div>
            <h3 class="aiwu-node-category-title">Logic Blocks</h3>
          </div>
          <div class="aiwu-node-items">
            <?php foreach ($logic_blocks_data as $block):
              if (empty($block['title'])) continue;
            ?>
              <div class="aiwu-node-item">
                <h4 class="aiwu-node-item-title"><?php echo esc_html($block['title']); ?></h4>
                <?php if (!empty($block['description'])): ?>
                  <p class="aiwu-node-item-desc"><?php echo esc_html($block['description']); ?></p>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Templates Using This Integration -->
    <?php
    $templates_query = new WP_Query([
      'post_type' => 'workflow_template',
      'posts_per_page' => 4,
      'post_status' => 'publish',
      'tax_query' => [
        [
          'taxonomy' => 'template_integration',
          'field' => 'term_id',
          'terms' => $term->term_id,
        ],
      ],
    ]);
    ?>

    <?php if ($templates_query->have_posts()): ?>
    <div class="aiwu-integration-templates">
      <div class="aiwu-section-header">
        <h2 class="aiwu-section-title">📋 Templates Using <?php echo esc_html($term->name); ?></h2>
        <?php if ($templates_query->found_posts > 4): ?>
          <a href="<?php echo add_query_arg('integration', $term->slug, get_post_type_archive_link('workflow_template')); ?>"
             class="aiwu-explore-more-link">
            View all <?php echo $templates_query->found_posts; ?> templates →
          </a>
        <?php endif; ?>
      </div>

      <div class="aiwu-templates-grid">
        <?php while ($templates_query->have_posts()): $templates_query->the_post();
          $categories = wp_get_post_terms(get_the_ID(), 'template_category');
          $difficulties = wp_get_post_terms(get_the_ID(), 'template_difficulty');
          $integrations = wp_get_post_terms(get_the_ID(), 'template_integration', ['orderby' => 'name']);
          $desc = get_post_meta(get_the_ID(), '_template_description', true);
        ?>
          <a href="<?php the_permalink(); ?>" class="aiwu-template-card">
            <?php if (!empty($integrations)): ?>
              <div class="aiwu-integrations-preview">
                <?php
                $visible = array_slice($integrations, 0, 3);
                $remaining = count($integrations) - 3;

                foreach ($visible as $integration):
                    $icon_id = get_term_meta($integration->term_id, 'icon_image_id', true);
                    $icon_url = $icon_id ? wp_get_attachment_url($icon_id) : '';
                ?>
                    <div class="aiwu-integration-icon" title="<?php echo esc_attr($integration->name); ?>">
                      <?php if ($icon_url): ?>
                        <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($integration->name); ?>">
                      <?php else:
                        $initials = aiwu_get_integration_initials($integration->name);
                      ?>
                        <span class="aiwu-integration-initials"><?php echo esc_html($initials); ?></span>
                      <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php if ($remaining > 0): ?>
                  <div class="aiwu-integration-more">+<?php echo $remaining; ?></div>
                <?php endif; ?>
              </div>
            <?php endif; ?>
            <div class="aiwu-template-content">
              <h2 class="aiwu-template-name"><?php the_title(); ?></h2>
              <?php if ($desc): ?>
                <p class="aiwu-template-desc"><?php echo esc_html($desc); ?></p>
              <?php endif; ?>
              <div class="aiwu-template-labels">
                <?php if (!empty($categories)): ?>
                  <?php foreach ($categories as $cat): ?>
                    <span class="aiwu-template-category"><?php echo esc_html($cat->name); ?></span>
                  <?php endforeach; ?>
                <?php endif; ?>
                <?php if (!empty($difficulties)): ?>
                  <?php foreach ($difficulties as $diff): ?>
                    <span class="aiwu-difficulty-badge aiwu-difficulty-<?php echo esc_attr(strtolower($diff->slug)); ?>">
                      <?php echo esc_html($diff->name); ?>
                    </span>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </a>
        <?php endwhile; ?>
      </div>

      <?php if ($templates_query->found_posts > 4): ?>
        <div class="aiwu-explore-more-wrapper">
          <a href="<?php echo add_query_arg('integration', $term->slug, get_post_type_archive_link('workflow_template')); ?>"
             class="aiwu-explore-more-btn">
            Explore More Templates
            <span class="aiwu-cta-arrow">→</span>
          </a>
        </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>

    <!-- CTA Section -->
    <div class="aiwu-cta-section aiwu-cta-single">
        <div class="aiwu-cta-content">
            <h2 class="aiwu-cta-title">Ready to Build Workflows with <?php echo esc_html($term->name); ?>?</h2>
            <p class="aiwu-cta-description">
                AIWU Workflow Builder lets you create powerful automations with <?php echo esc_html($term->name); ?> and other integrations.
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
