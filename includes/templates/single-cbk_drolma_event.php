<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $post;
$categories = get_the_terms( $post->ID, 'cbk_drolma_event_category' );
$location = get_post_meta( $post->ID, '_cbk_drolma_event_location', true );
$external_url = get_post_meta( $post->ID, '_cbk_drolma_event_external_url', true );
$button_text = get_post_meta( $post->ID, '_cbk_drolma_event_button_text', true );
$start_date = get_post_meta( $post->ID, '_cbk_drolma_event_start_date', true );
$start_time = get_post_meta( $post->ID, '_cbk_drolma_event_start_time', true );
$end_date = get_post_meta( $post->ID, '_cbk_drolma_event_end_date', true );
$end_time = get_post_meta( $post->ID, '_cbk_drolma_event_end_time', true );
$image_id = get_post_meta( $post->ID, '_cbk_drolma_event_image_id', true );
$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '';
$program_items = get_post_meta( $post->ID, '_cbk_drolma_event_program_items', true );
$prices = get_post_meta( $post->ID, '_cbk_drolma_event_prices', true );

$host_ids = get_post_meta( $post->ID, '_cbk_drolma_event_hosts', true );

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
            echo 'le '.esc_html($start_date_str).'<br/> de '.esc_html($start_time_str).' à '.esc_html($end_time_str);
        }
        else{
            echo 'le '.esc_html($start_date_str);
        }
    }
    else{
        if($start_time && $end_time &&$start_time!=$end_time)
        {
            echo 'du '.esc_html($start_date_str).' à '.esc_html($start_time_str). '<br/> au '.esc_html($end_date_str).' à '.esc_html($end_time_str);
        }
        else{
            echo 'du '.esc_html($start_date_str). ' au '.esc_html($end_date_str);
        }
    }
}

get_header();
?>
<style>
.cbk_event{
    max-width:1090px;
    margin: 0px auto;
    display: grid;
    gap:20px;
    align-content: center;
    align-items: center;
    grid-template-columns: 60% 40%;
    grid-template-areas:
        "cbk_event_category cbk_event_category"
        "cbk_event_title cbk_event_title"
        "cbk_event_date cbk_event_date"
        "cbk_event_date_separator_location cbk_event_date_separator_location"
        "cbk_event_location cbk_event_location"
        "cbk_event_image cbk_event_host_list"
        "cbk_event_text cbk_event_host_list"
        "cbk_event_program cbk_event_tarif"
        ;
}
.cbk_event div{
    text-align: center;
}

.cbk_event .cbk_event_category{
    grid-area: cbk_event_category;
    font-family: "Montserrat", sans-serif;
    font-style: normal;
    font-weight: 700;
    border-color: rgb(58, 58, 58);
    color: rgb(58, 58, 58);
    font-size: 39px;
    line-height: 39px;
    outline-color: rgb(58, 58, 58);
}
.cbk_event .cbk_event_title{
    grid-area: cbk_event_title;
    font-family: "Montserrat", sans-serif;
    font-style: normal;
    font-weight: 700;
    border-color: rgb(110, 181, 178);
    color: rgb(110, 181, 178);
    font-size: 32px;
    line-height: 32px;
    outline-color: rgb(110, 181, 178);
}
.cbk_event .cbk_event_date{
    grid-area: cbk_event_date;
    font-family: "Montserrat", sans-serif;
    font-style: normal;
    font-weight: 700;
    border-bottom-color: rgb(58, 58, 58);
    color: rgb(58, 58, 58);
    font-size: 24px;
    letter-spacing: 1.5px;
    line-height: 28.8px;
    outline-color: rgb(58, 58, 58);
    text-transform: uppercase;
}
.cbk_event .cbk_event_date_separator_location{
    grid-area: cbk_event_date_separator_location;
    margin:0px auto;
}
.cbk_event .cbk_event_location{
    grid-area: cbk_event_location;
    color: rgb(76, 88, 99);
    font-family: "Noto Sans", sans-serif;
    font-size: 20px;
    font-style: italic;
    font-weight: 400;
    line-height: 23.8333px;
}
.cbk_event .cbk_event_image{
    grid-area:cbk_event_image;
    width: 75%;
    margin:0px auto;
}
.cbk_event .cbk_event_text{
    font-family: "Noto Sans", sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 400;
    line-height: 23.8333px;
    margin: 0px;
}
.cbk_event .cbk_event_text p{
    text-align: justify;
}
.cbk_event .cbk_event_host_list{
    grid-area:cbk_event_host_list;
}
.cbk_event .cbk_event_host{
    grid-area:cbk_event_host;
    box-shadow: rgba(0, 0, 0, 0.5) 0px 0px 10px 0px;
    box-sizing: border-box;
    color: rgb(34, 34, 34);
    font-family: "Noto Sans", sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 400;
    line-height: 23.8333px;
    margin: 0px 60px;
    outline-color: rgb(34, 34, 34);
    outline-style: none;
    outline-width: 0px;
    overflow-wrap: break-word;
    padding: 10px 35px;
    vertical-align: baseline
}


.cbk_event_host_image{
}
.cbk_event_host_name{
    color: rgb(110, 181, 178);
    font-family: "Montserrat", sans-serif;
    font-size: 20px;
    font-style: normal;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.cbk_event_host_description{
    box-sizing: border-box;
    color: rgb(76, 88, 99);
    font-family: "Noto Sans", sans-serif;
    font-size: 16px;
    font-style: italic;
    font-weight: 400;
}

.cbk_event .cbk_event_program{
    grid-area:cbk_event_program;
    padding:10px;
    text-align: justify;
}

.cbk_event .cbk_event_program .cbk_event_program_title{
  font-family: "Montserrat", sans-serif;
  font-size: 20px;
  font-style: normal;
  font-weight: 700;
  letter-spacing: 1.5px;
  line-height: 20px
}

.cbk_event .cbk_event_program .cbk_event_program_item_text{
  font-family: "Montserrat", sans-serif;
  font-size: 20px;
  font-style: normal;
  font-weight: 700;
  letter-spacing: 1.5px;
  line-height: 20px
}

.cbk_event .cbk_event_tarif{
    grid-area:cbk_event_tarif;
    background-color:green;
    padding:10px;
}

.cbk_drolma_event_separator{
    width: 100px;
    height: 3px;
    background-color: rgb(228, 197, 6);
}

.cbk_drolma_event_separator2{
    width: 100px;
    height: 3px;
    background-color: #6EB5B2;
}
</style>
<div class="cbk_event">
    <div class="cbk_event_category"><span><?php echo $categories[0]->name; ?></span></div>
    <div class="cbk_event_title"><span><?php the_title(); ?></span></div>
    <div class="cbk_event_date"><span><?php echo render_date($post); ?></span></div>
    <div class="cbk_drolma_event_separator cbk_event_date_separator_location"></div>
    <div class="cbk_event_location"><span><?php echo esc_html($location); ?></span></div>
    <div class="cbk_event_image">
        <img
        fetchpriority="high"
        decoding="async"
        width="1024"
        height="512"
        src="<?php echo esc_url($image_url); ?>"
        class="attachment-large size-large wp-image-9928"
        alt=""
        />
    </div>
    <div class="cbk_event_text">
        <?php the_content(); ?>
    </div>
    <div class="cbk_event_host_list">
      <?php foreach ($host_ids as $host_id) :
          $host = get_post($host_id);
          if (!$host) continue;
          $photo = get_the_post_thumbnail($host_id, 'thumbnail', ['class'=>'cbk_event_host_image']);
          ?>
          <div class="cbk_event_host">
              <?php if ($photo) echo $photo; ?>
              <div class="cbk_event_host_name"> avec <?php echo esc_html($host->post_title); ?></div>
              <div class="cbk_event_host_description"><?php echo apply_filters('the_content', $host->post_content); ?></div>
          </div>
      <?php endforeach; ?>
    </div>
    <div class="cbk_event_program">
      <h4 class="cbk_event_program_title">PROGRAMME</h4>
      <div class="cbk_drolma_event_separator2"></div>
      <?php foreach ($program_items as $program_item) :
          ?>
          <div class="cbk_event_program_item">
            <i aria-hidden="true" class="fas <?php echo $program_item['icon']; ?>"></i>
            <span class="cbk_event_program_item_text"><?php echo $program_item['text']; ?></span>
          </div>
      <?php endforeach; ?>
    </div>
    <div class="cbk_event_tarif">
      <h4 class="cbk_event_tarif_title">Tarifs</h4>
      <span class="cbk_event_tarif_content">
        <?php echo nl2br(esc_html($prices)); ?>
      </span>
      <?php if (!empty($external_url) && !empty($button_text)): ?>
        <div style="margin-top:20px;">
          <i aria-hidden="true" class="far fa-calendar-check"></i>
          <a href="<?php echo esc_url($external_url); ?>" target="_blank" class="cbk_event_tarif_button" style="display:inline-block;padding:10px 30px;background:#6EB5B2;color:#fff;font-family:'Montserrat',sans-serif;font-size:18px;font-weight:700;text-decoration:none;border-radius:6px;"> 
            <?php echo esc_html($button_text); ?>
          </a>
        </div>
      <?php endif; ?>
    </div>
</div>



<?php get_footer(); ?>
