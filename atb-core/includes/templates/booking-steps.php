<?php global $chauffeur_data; ?>

<div class="step-wrapper clearfix">	
	<div class="step-icon-wrapper">
	
		<?php if($step == '1') { ?>
			<div class="step-icon step-icon-current"><?php esc_html_e('1','chauffeur'); ?></div>
		<?php } else { ?>
			<div class="step-icon"><?php esc_html_e('1','chauffeur'); ?></div>
		<?php } ?>
		
	</div>
	<div class="step-title"><?php esc_html_e('Trip Details','chauffeur'); ?></div>
	<div class="step-image"><img src="<?php echo ATT_URL;?>/assets/images/trip-details-icon.jpg'"></div>
</div>

<div class="step-wrapper clearfix">
	<div class="step-icon-wrapper">
		
		<?php if($step == '2') { ?>
			<div class="step-icon step-icon-current"><?php esc_html_e('2.','chauffeur'); ?></div>
		<?php } else { ?>
			<div class="step-icon"><?php esc_html_e('2','chauffeur'); ?></div>
		<?php } ?>
		
	</div>
	<div class="step-title"><?php esc_html_e('Select Vehicle','chauffeur'); ?></div>
	<div class="step-image"><img src="<?php echo ATT_URL;?>/assets/images/select-vehicle-icon.jpg'"></div>
</div>

<div class="step-wrapper clearfix">
	<div class="step-icon-wrapper">
		
		<?php if($step == '3') { ?>
			<div class="step-icon step-icon-current"><?php esc_html_e('3.','chauffeur'); ?></div>
		<?php } else { ?>
			<div class="step-icon"><?php esc_html_e('3','chauffeur'); ?></div>
		<?php } ?>
		
	</div>
	<?php if( $chauffeur_data['hide-pricing'] != '1' ) { ?>
		<div class="step-title"><?php esc_html_e('Enter Payment Details','chauffeur'); ?></div>
		<div class="step-image"><img src="<?php echo ATT_URL;?>/assets/images/payment-details-icon.jpg'"></div>
	<?php } else { ?>
		<div class="step-title"><?php esc_html_e('Review Details','chauffeur'); ?></div>
	<?php } ?>
</div>

<div class="step-wrapper qns-last clearfix">
	<div class="step-icon-wrapper">
		
		<?php if($step == '4') { ?>
			<div class="step-icon step-icon-current"><?php esc_html_e('4.','chauffeur'); ?></div>
		<?php } else { ?>
			<div class="step-icon"><?php esc_html_e('4','chauffeur'); ?></div>
		<?php } ?>
		
	</div>
	<div class="step-title"><?php esc_html_e('Confirmation','chauffeur'); ?></div>
	<div class="step-image"><img src="<?php echo ATT_URL;?>/assets/images/confirmation-icon.jpg'"></div>
</div>

<div class="step-line"></div>