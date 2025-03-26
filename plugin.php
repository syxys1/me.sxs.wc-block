<?php
/**
 * Plugin Name: Custom Product Collection Block
 * Description: A block to display products by category.
 * Version: 1.0
 * Author: Sylvain
 * License: GPL-3.0-or-later
 * Text Domain: custom-product-collection
 */

defined('ABSPATH') || exit;

// Register and enqueue block assets.
function custom_product_collection_register_block() {
    // Fetch product categories on the backend.
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true, // Set to false if you want to include empty categories.
    ));

    // Format categories into an array for JavaScript.
    $formatted_categories = array();
    if (!is_wp_error($categories)) {
        foreach ($categories as $category) {
            $formatted_categories[] = array(
                'id' => $category->term_id,
                'name' => $category->name,
                'slug' => $category->slug,
            );
        }
    }

    // Register the editor script.
    wp_register_script(
        'custom-product-collection-editor',
        plugins_url('build/index.js', __FILE__),
        array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor'),
        filemtime(plugin_dir_path(__FILE__) . 'build/index.js')
    );

    // Pass the categories to the script using wp_localize_script.
    wp_localize_script('custom-product-collection-editor', 'customProductCollectionData', array(
        'categories' => $formatted_categories,
    ));

    wp_register_style(
        'custom-product-collection-editor',
        plugins_url('src/editor.css', __FILE__),
        array('wp-edit-blocks'),
        filemtime(plugin_dir_path(__FILE__) . 'src/editor.css')
    );

    wp_register_style(
        'custom-product-collection-style',
        plugins_url('src/style.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'src/style.css')
    );

    // Register the block.
    register_block_type('custom-product-collection/block', array(
        'editor_script' => 'custom-product-collection-editor',
        'editor_style'  => 'custom-product-collection-editor',
        'style'         => 'custom-product-collection-style',
        'render_callback' => 'custom_product_collection_render_block',
        'attributes' => array(
            'excludeCategories' => array(
                'type' => 'array',
                'default' => array(),
            ),
            'orderBy' => array(
                'type' => 'string',
                'default' => 'date',
            ),
            'order' => array(
                'type' => 'string',
                'default' => 'DESC',
            ),
            'columns' => array(
                'type' => 'number',
                'default' => 4,
            ),
            'title' => array(
                'type' => 'string',
                'default' => 'Product Collection',
            ),
        ),
    ));
}

add_action('init', 'custom_product_collection_register_block');

function custom_product_collection_render_block($attributes) {
    $exclude_categories = $attributes['excludeCategories'];
    $order = $attributes['order'];
    $show_subcategories = $attributes['showSubcategories'];
    $title_font_size = $attributes['titleFontSize'];
    $title_font_color = $attributes['titleFontColor'];
    $separator_color = $attributes['separatorColor'];
    $separator_thickness = $attributes['separatorThickness'];
    $show_price = $attributes['showPrice'];
    $show_add_to_cart = $attributes['showAddToCart'];
    $product_font_size = $attributes['productFontSize'];
    $product_margin = $attributes['productMargin'];
    $product_border_color = $attributes['productBorderColor'];
    $product_border_style = $attributes['productBorderStyle'];
    $accordion_title_font_size = $attributes['accordionTitleFontSize'];
    $accordion_title_font_color = $attributes['accordionTitleFontColor'];
    $accordion_caret_color = $attributes['accordionCaretColor'];
    $accordion_caret_image = $attributes['accordionCaretImage'];


    // Fetch categories in hierarchical order
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'exclude' => $exclude_categories,
        'orderby' => 'name',
        'order' => $order,
    ));

    ob_start();
    ?>
    <div class="custom-product-collection-block">
        <style>
            .category-title h3 {
                font-size: <?php echo esc_attr($title_font_size); ?>px;
                color: <?php echo esc_attr($title_font_color); ?>;
            }
            .category-separator hr {
                border-color: <?php echo esc_attr($separator_color); ?>;
                border-width: <?php echo esc_attr($separator_thickness); ?>px;
            }
            .product-item {
                font-size: <?php echo esc_attr($product_font_size); ?>px;
                margin: <?php echo esc_attr($product_margin); ?>px;
                border: <?php echo esc_attr($product_border_style); ?> solid <?php echo esc_attr($product_border_color); ?>;
            }
            .accordion-title {
                font-size: 2em; /* Correspond à la taille d'un H2 */
                color: <?php echo esc_attr($accordion_title_font_color); ?>;
                display: flex;
                justify-content: flex-start;
                align-items: center;
                cursor: pointer;
                padding: 10px 15px;
                background-color: transparent; /* Fond transparent */
                border: none;
                border-radius: 0;
                font-weight: bold; /* Titre en gras */
                border-bottom: 1px solid <?php echo esc_attr($separator_color); ?>; /* Trait sous la catégorie */
            }
            .accordion-caret {
                color: <?php echo esc_attr($accordion_caret_color); ?>;
                margin-right: 10px;
                <?php if ($accordion_caret_image) : ?>
                    background-image: url('<?php echo esc_url($accordion_caret_image); ?>');
                    background-size: contain;
                    background-repeat: no-repeat;
                    width: 20px;
                    height: 20px;
                    transition: transform 0.3s ease;
                <?php endif; ?>
            }
            .accordion-content {
                display: none; /* Hidden by default */
                padding: 15px;
                border: 1px solid #ddd;
                border-top: none;
            }
            .accordion-content.open {
                display: block; /* Shown when open */
            }
        </style>

        <?php foreach ($categories as $category) : ?>
            <!-- Accordion View -->
            <div class="accordion-title" data-accordion-target="<?php echo esc_attr($category->term_id); ?>">
                <span class="accordion-caret"></span>
                <span><?php echo esc_html($category->name); ?></span>
            </div>
            
            <div class="accordion-content" id="accordion-content-<?php echo esc_attr($category->term_id); ?>">
                <div class="category-title">
                    <?php if ($show_subcategories) : ?>
                        <h3><?php echo esc_html($category->name); ?></h3>
                    <?php endif; ?>
                </div>
                <div class="category-separator">
                    <hr />
                </div>

                <!-- Products directly in the category -->
                <div class="product-grid" style="grid-template-columns: repeat(<?php echo esc_attr($attributes['columns']); ?>, 1fr);">
                    <?php
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'orderby' => $attributes['orderBy'],
                        'order' => $order,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'term_id',
                                'terms' => $category->term_id,
                            ),
                        ),
                    );

                    $products = new WP_Query($args);

                    if ($products->have_posts()) :
                        while ($products->have_posts()) :
                            $products->the_post();
                            global $product; ?>
                            <div class="product-item">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                    <h5><?php the_title(); ?></h5>
                                    <?php if ($show_price) : ?>
                                        <p><?php echo $product->get_price_html(); ?></p>
                                    <?php endif; ?>
                                    <?php if ($show_add_to_cart) : ?>
                                        <button><?php _e('Add to Cart', 'custom-product-collection'); ?></button>
                                    <?php endif; ?>
                                </a>
                            </div>
                        <?php endwhile;
                        else : ?>
                            <p><?php _e('No products found in this category.', 'custom-product-collection'); ?></p>
                        <?php endif; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        document.querySelectorAll('.accordion-title').forEach((title) => {
            title.addEventListener('click', () => {
                const targetId = title.getAttribute('data-accordion-target');
                const content = document.getElementById(`accordion-content-${targetId}`);
                const caret = title.querySelector('.accordion-caret');

                if (content.classList.contains('open')) {
                    content.classList.remove('open');
                    caret.style.transform = 'rotate(0deg)';
                } else {
                    content.classList.add('open');
                    caret.style.transform = 'rotate(90deg)';
                }
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
