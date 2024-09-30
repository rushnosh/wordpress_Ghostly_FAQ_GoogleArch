<?php 

if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly
    class FrontEndFAQ {

        public function __construct() {
            //Enter in constructor code here
            add_shortcode( 'ggefaqsh', array($this, 'frontendShortCodeOutput')  );
            add_action( 'wp_enqueue_scripts', array($this, 'faq_frontend_scripts') );
        }

        //Registering our front end scripts - JS and CSS - only when the short code is used
        function faq_frontend_scripts() {
            wp_register_style( 'gge-faq-frontend-style', GGE_FAQ_URL_PATH . 'frontend/build/style-index.css', array(), gge_version_id(), 'all' );
            wp_register_script( 'gge-faq-frontend-script', GGE_FAQ_URL_PATH . 'frontend/build/index.js', array(), gge_version_id(), true );
        }

        //Adding a short code to return a php template
        // [bartag foo="foo-value"]
        public function frontendShortCodeOutput( $atts ) {
            $a = shortcode_atts(array(
                'sel_tax_slug' => ''
            ), $atts);
            //Get Taxonomy via slug
            $taxCat = get_term_by('slug',$a['sel_tax_slug'],'faqs');
            $gotTaxology = array();
           

            ob_start();
            //Start of FAQ container div
            ?>
            <div id="faq-tax-id" class="faq-container">
            <?php
            if ($taxCat) {

                $gotTaxology = array(
                    array(
                        'taxonomy' => 'faqs',
                        'field'    => 'slug',
                        'terms'    => $taxCat->slug
                    )
                );
                //taxonomy description
                $desc = $taxCat->description;
                ?>
                    <h2><?php echo $desc?></h2>
                <?php
            }
            //Using the WP_Query object to look into the WP DB to search for the custom post type of faq
            $custquery = new WP_Query(array(
                'posts_per_page' => -1,
                'post_type' => 'faq_post',
                'order_by' => 'post_ID',
                'tax_query' => $gotTaxology,
                'order' => 'ASC'
                )
            );
            $googleFaqArrary = array();

            //Below is the loop which will loop through all of the selected posts/custon post type items
            while ($custquery->have_posts()) {
                $custquery->the_post();

                if (!get_post_meta( get_the_ID(), 'faq_google_arch_disable', true )) {
                    //Gather data for Google FAQ Rich Results
                    array_push($googleFaqArrary, array(
                        'ID' => get_the_ID(),
                        'Question' => $this->stripUnnessaryStringsForGoogleArch(get_the_title()) ,
                        'Answer' => $this->stripUnnessaryStringsForGoogleArch(get_the_content())
                    ));
                };
                ?>

                    <div id="faq-i-<?php the_ID()?>" class="faq-item">
                        <div class="offset" id="faq-i-answer-<?php the_ID()?>"></div>
                        <a href="#faq-i-answer-<?php the_ID()?>" class="btn faq-question" data-clicked="No"><?php the_title(); ?></a>
                        <div class="faq-answer faq-a-card" style="display: none;">
                            <?php the_content(); ?>
                        </div>
                    </div>

                <?php
            }

            //End of Container div
            ?>

                </div>
                <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@type": "FAQPage",
                    "mainEntity": [
                        <?php
                        $numItems = count($googleFaqArrary);
                        $i = 0;
                            foreach ($googleFaqArrary as $faq) {
                                $quest = $faq['Question'];
                                $answ = $faq['Answer'];
                                echo '{';
                                echo '"@type": "Question",';
                                echo '"name": "' . $quest . '",';
                                echo '"acceptedAnswer": {';
                                    echo '"@type": "Answer",';
                                    echo '"text": "' . $answ . '"';
                                    echo '}';
                                if(++$i === $numItems){
                                    echo '}';
                                    break; 
                                }
                                echo '},';
                            }
                        ?>
                    ]
                }
                </script>

            <?php
            //Ensure you reset the post data once you have finished off with the custom query
            wp_reset_postdata();

            //This will enqueue the required front end scripts for the faq
            wp_enqueue_style( 'gge-faq-frontend-style' );
            wp_enqueue_script( 'gge-faq-frontend-script' );
            return ob_get_clean ();

        }


        public function stripUnnessaryStringsForGoogleArch($toUpdateData) {
            $toSendData = '';
            //Intial Strip of data tags
            $toSendData = wp_strip_all_tags($toUpdateData);
            //Remove any quotes
            $toSendData = str_replace('"', '', $toSendData);
            //Remove signle quotes
            $toSendData = str_replace("'","", $toSendData);
            //Update Brackets to HTML CODE
            $toSendData = str_replace("(","&#40;", $toSendData);
            $toSendData = str_replace(")","&#41;", $toSendData);

            //Return refined data
            return $toSendData;
        }

    }

?>