<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $post;
$location = get_post_meta( $post->ID, '_cbk_drolma_event_location', true );
$external_url = get_post_meta( $post->ID, '_cbk_drolma_event_external_url', true );
$button_text = get_post_meta( $post->ID, '_cbk_drolma_event_button_text', true );
$categories = get_the_terms( $post->ID, 'cbk_drolma_event_category' );
$host_ids = get_post_meta( $post->ID, '_cbk_drolma_event_hosts', true );
$image_id = get_post_meta( $post->ID, '_cbk_drolma_event_image_id', true );
$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '';
if (!is_array($host_ids)) $host_ids = [];
function cbk_drolma_event_category_hierarchy($cat) {
    $names = [$cat->name];
    while ($cat->parent) {
        $cat = get_term($cat->parent, 'cbk_drolma_event_category');
        if ($cat && !is_wp_error($cat)) {
            array_unshift($names, $cat->name);
        } else {
            break;
        }
    }
    return implode(' &gt; ', $names);
}
function render_date($post){
    $start_date = get_post_meta( $post->ID, '_cbk_drolma_event_start_date', true );
    $start_time = get_post_meta( $post->ID, '_cbk_drolma_event_start_time', true );
    $end_date = get_post_meta( $post->ID, '_cbk_drolma_event_end_date', true );
    $end_time = get_post_meta( $post->ID, '_cbk_drolma_event_end_time', true );
    $start_date_str=esc_html(date_i18n('l d F Y', strtotime($start_date)));
    $end_date_str=esc_html(date_i18n('l d F Y', strtotime($end_date)));
    $start_time_str=esc_html(date_i18n('H\hi', strtotime($start_time)));
    $end_time_str=esc_html(date_i18n('H\hi', strtotime($end_time)));
    if($start_date==$end_date){
        if($start_time && $end_time &&$start_time!=$end_time)
        {
            echo 'le '.esc_html($start_date_str).' de '.esc_html($start_time_str).' à '.esc_html($end_time_str);
        }
        else{
            echo 'le '.esc_html($start_date_str);
        }
    }
    else{
        if($start_time && $end_time &&$start_time!=$end_time)
        {
            echo 'du '.esc_html($start_date_str).' à '.esc_html($start_time_str). ' au '.esc_html($end_date_str).' à '.esc_html($end_time_str);
        }
        else{
            echo 'du '.esc_html($start_date_str). ' au '.esc_html($end_date_str);
        }
    }
    ?>
    <?php
}
get_header();
?>

<main class="cbk-drolma-event-single">
    <?php if ($categories && !is_wp_error($categories)) : ?>
        <h1 class="cbk-drolma-event-category">
            <?php echo $categories[0]->name; ?>
        </h1>
    <?php endif; ?>
    <h2><?php the_title(); ?></h2>
    <h3><?php echo render_date($post); ?></h3>
    <span><?php echo esc_html($location); ?></span>
    <?php if (!empty($host_ids)) : ?>
        <div class="cbk-drolma-event-hosts">
            <strong>Hosts:</strong>
            <ul>
            <?php foreach ($host_ids as $host_id) :
                $host = get_post($host_id);
                if (!$host) continue;
                $photo = get_the_post_thumbnail($host_id, 'thumbnail', ['style'=>'max-width:80px; height:auto;']);
                ?>
                <li style="margin-bottom:1em;">
                    <?php if ($photo) echo $photo; ?>
                    <div><strong><?php echo esc_html($host->post_title); ?></strong></div>
                    <div><?php echo apply_filters('the_content', $host->post_content); ?></div>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if ($image_url): ?>
        <div class="cbk-drolma-event-image">
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title($post)); ?>" style="max-width:100%;height:auto;" />
        </div>
    <?php endif; ?>
    <div class="cbk-drolma-event-content">
        <?php the_content(); ?>
    </div>
    <div class="cbk-drolma-event-meta">
        <?php if ( $external_url ) : ?>
            <p><a href="<?php echo esc_url($external_url); ?>" class="cbk-drolma-event-btn" target="_blank"><?php echo esc_html($button_text ? $button_text : 'Learn More'); ?></a></p>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
