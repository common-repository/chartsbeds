<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Include the pagination class
include 'pagination.class.php';

/// Function ADD Reviews to the page
function cbeds_review_add_shortcode($cbh) {
    $cbh = shortcode_atts( array(
        'limit' => esc_attr($cbh->limit),
		'key' => esc_attr($cbh->key),
    ), $atts );

    if(empty($cbh['limit'])){
        $cbh['limit'] = 200;
    }
    
    $Context = stream_context_create(array('http' => array('timeout' => '2',)));
	
	if(empty($cbh['key'])){
		$thekey = htmlspecialchars_decode (get_option("charts_key"));
		$json = file_get_contents('http://dashboard.chartspms.com/REVIEWS.json.php?apiKey='.$thekey.''.'&limit='.esc_attr($cbh['limit']).'', true, $Context);
	}else{
		$ekey = 'http://dashboard.chartspms.com/REVIEWS.json.php?apiKey='.$cbh['key'].'&limit='.esc_attr($cbh['limit']).'';
		$json = file_get_contents($ekey, true, $Context);
	}

    $obj = json_decode($json, true); 

$revPage = '<script>
                jQuery(document).ready(function() {
                    jQuery(".charts-widg-p").shorten({
                        "showChars": 100,
                        "moreText": " +",
                        "lessText": " -",
                    });
                    jQuery(".cb-rev-clients").shorten({
                        "showChars": 100,
                        "moreText": " +",
                        "lessText": " -",
                    });
                    jQuery(".morecontent a").addClass("btn btn-default btn-xs");
                    jQuery(".morelink").click(function() {
                        if (jQuery(this).closest(".rcustomers").hasClass("col-md-10")) {
                            jQuery(this).closest(".rcustomers").removeClass("col-md-10")
                        } else {
                            jQuery(this).closest(".rcustomers").addClass("col-md-10")
                        };
                    });
                });
            </script>';

    $revPage .= '<div class="row tinliner">
    <div class="cb-thanks">
    <a href="http://www.chartsbeds.com/" target="_blank"><img src="'.plugin_dir_url( __FILE__ ).'/img/chartsbeds-web-logo.png" width="100px" /></a>
    </div>';

    $all_reviews = $obj['reviews'];
    // If we have an array with items
    if (count($all_reviews)) {

        if(!get_option('rev_per_page')){
            $per_page = get_option('rev_per_page');
        }else{
            $per_page = 10;
        }

        // Create the pagination object
        $pagination = new pagination($all_reviews, (!empty(get_query_var('page')) ? get_query_var('page') : 1), $per_page );
        // Decide if the first and last links should show
        $pagination->setShowFirstAndLast(false);
        // You can overwrite the default seperator
        $pagination->setMainSeperator('');
        // Parse through the pagination class
        $reviewsPages = $pagination->getResults();
        // If we have items
        if (count($reviewsPages) != 0) {

        // Loop through all the items in the array
        $counter = 1;
        foreach ($reviewsPages as $reviewsArray) {
            $g_rates = $reviewsArray['guest_rating']*.7; 



            $revPage .= '<div class="col-md-6  rcustomers">
                    <div class="testimonials">
                        <div class="active item">
                            <blockquote style="margin:0;">
                                <p class="cb-rev-clients">'.$reviewsArray['review'];
                                    if($reviewsArray['answer']){
                                        $revPage .=  "<br><i class='fa fa-comments revanswer' aria-hidden='true'></i>".$obj['property']." answered: ".$reviewsArray['answer'];
                                    } 
            $revPage .=         '</p>
                            </blockquote>
                            <div class="testimonials-rate col-md-4">'.__( 'Rating' , 'cbrevpage' ).' :
                                '.$reviewsArray['guest_rating'].'
                                <div class="star-ratings">
                                    <div class="star-ratings-top" style="width:'.$g_rates.'px">
                                        <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
                                    <div class="star-ratings-bottom">
                                        <span>☆</span><span>☆</span><span>☆</span><span>☆</span><span>☆</span></div>
                                </div>
                            </div>
                            <div class="carousel-info">
                                <img alt="" src="'.$reviewsArray['gravatar'].'" class="pull-left">
                                <div class="pull-left">
                                    <span class="testimonials-name">'.$reviewsArray['name'].'</span>
                                    <span class="testimonials-time">'.$reviewsArray['country'].'</span>
                                    <span class="testimonials-post">'.$reviewsArray['timestamp'].'</span>';

                            if($reviewsArray['recommends']){
                                $revPage .= '<span class="testimonials-post"><i class="fa fa-heart recommends" aria-hidden="true"></i> '.$reviewsArray['name'].'&nbsp;'.__( 'recommends this hotel' , 'cbrevpage' ).'</span>';
                            }

             $revPage .=       '</div>
                            </div>
                        </div>
                    </div>
                </div>';

            $counter++;
            }
            // print out the page numbers beneath the results
            $revPage .= '<ul class="charts-pagination">'.$pagination->getLinks($_GET).'</ul>';
        }
    }
    return $revPage;
}
add_shortcode('chartsbeds-review-page', 'cbeds_review_add_shortcode');