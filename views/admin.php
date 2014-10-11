<!-- This file is used to markup the administration form of the widget. -->

<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">

<label for="<?php echo $this->get_field_id( 'zipcode' ); ?>"><?php _e( 'Zipcode:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'zipcode' ); ?>" name="<?php echo $this->get_field_name( 'zipcode' ); ?>" type="text" value="<?php echo esc_attr( $zipcode ); ?>">

<label for="<?php echo $this->get_field_id( 'radius' ); ?>"><?php _e( 'Radius (mi):' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'radius' ); ?>" name="<?php echo $this->get_field_name( 'radius' ); ?>" type="text" value="<?php echo esc_attr( $radius ); ?>">

<label for="<?php echo $this->get_field_id( 'max_bikes' ); ?>"><?php _e( 'Maximum Bikes to Show (maximum 20):' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'max_bikes' ); ?>" name="<?php echo $this->get_field_name( 'max_bikes' ); ?>" type="text" value="<?php echo esc_attr( $max_bikes ); ?>">