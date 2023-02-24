<div class="manage-booking-page-atb-wrap">
    <?php if( $loop->have_posts()): ?>
        <table style="width:100%" class="atb-table">
            <tr>
                <th>Booking ID</th>
                <th>Name, Date and Time</th> 
                <th>Details</th>
            </tr>
            <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                <?php global $post; ?>
                <tr>
                    <td><?php echo "#".$post->ID;?></td>
                    
                    <td><strong><?php the_title() ?></strong></td>

                    <td><a class="details-btn-atb" href="<?php the_permalink() ?>" title="<?php echo esc_attr( get_the_title() ) ?>"><i class="fa fa-eye" aria-hidden="true"></i> View Details</a></td>
                </tr>
                
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <div class="">
            <em><?php _e("You do not have any Bookings yet.", "chauffeur") ?></em>
        </div>
    <?php endif; ?>
        <?php wp_reset_query(); ?>
</div>