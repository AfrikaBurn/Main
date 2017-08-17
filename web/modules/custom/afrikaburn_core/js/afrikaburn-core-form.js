/**
 * @file
 * AfrikaBurn core behaviors.
 */

(function ($) {

  'use strict';

  Drupal.behaviors.afrikaburnCoreForm = {
    attach: function (context, settings) {

      // Form validation
      $('.form-text, .form-autocomplete, .form-checkbox, .form-select, .form-textarea, .form-file', context).blur(
        function(){
          $(this).valid();
        }
      );

      // You're a wizard Harry
      $('.js-wizard', context).each(
        function(){
          new Wizard(this);
        }
      );

    }
  };

  class Wizard {

    constructor(element){

      this.root = $(element);
      this.tabs = $('.vertical-tabs__menu', this.root).children();
      this.panels = $('.field-group-tab .details-wrapper', this.root);

      this.panels.append('<div class="wizard-actions"></div>');
      this.panels.not(':first').find('.wizard-actions').append('<input type="button" value="Previous" class="js-previous button" />');
      this.panels.not(':last').find('.wizard-actions').append('<input type="button" value="Next" class="js-next button" />');
      this.root.parents('form').find('.form-actions').appendTo(this.panels.last());

      $('.js-next', this.root).click(
        () => {
          $(this.tabs.filter('.is-selected')).next().find('a').click();
          $('html, body').animate({ scrollTop: this.root.offset().top - 200 }, 500);
        }
      );

      $('.js-previous', this.root).click(
        () => {
          $(this.tabs.filter('.is-selected')).prev().find('a').click();
          $('html, body').animate({ scrollTop: this.root.offset().top - 200 }, 500);
        }
      );

    }

  }

})(jQuery);

