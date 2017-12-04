/**
 * @file
 * AfrikaBurn shared form behaviors.
 */

(function ($) {

  'use strict'

  Drupal.behaviors.afrikaburnSharedForm = {
    attach: function (context, settings) {
      if (!this.behavioursAttached){
        $(document).ready(
          () => {

            function checkAge(){
              var age = Date.now() - Date.parse($('#edit-field-date-of-birth-0-value-date').val())
              if (age >= 883593928000) {
                $('#ui-id-15').show();
              } else {
                $('#ui-id-15').hide();
              }
            }

            $('#edit-field-date-of-birth-0-value-date').change(checkAge)
            checkAge()

          }
        );
        this.behavioursAttached = true
      }
    }
  };


})(jQuery);

