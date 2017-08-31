jQuery(window).scroll(function() {
    var scrollHeight = jQuery(window).scrollTop();
    if(scrollHeight  > 0) {
        jQuery("a.back-to-top-button").addClass("show")
    }
	else {
	 	jQuery("a.back-to-top-button").removeClass("show")
	}
});

/*----------------------------------------------------
ADD CLASS "HIGHLIGHTED" TO THE ACTIVE PARENT MENU ITEM
----------------------------------------------------*/

jQuery("ul.nav-child").hover(parentColour, parentColour);
	function parentColour(){
	jQuery(this).parent().toggleClass("highlighted");
}

function supportsPlaceholder() {
  return 'placeholder' in document.createElement('input');
}

function radioCheckBoxWrapper() {
	jQuery("input:radio, input:checkbox")
	jQuery("input:radio, input:checkbox").not(".radio-checkbox-wrapper input:radio, .radio-checkbox-wrapper input:checkbox").wrap("<div class='radio-checkbox-wrapper'></div>").after("<div class='radio-checkbox-dummy'></div>");
}

function inputTypeFile() {
	jQuery("input:file").not(".input-file-wrapper input:file").wrap("<div class='input-file-wrapper'>Select file</div>");
	jQuery(".input-file-wrapper").after("<div class='clr'></div><p class='selected-file'>Selected file: None selected</p>");
}

function rotateImages() {
  var oCurrentPhoto = jQuery("div.slide.current");
  var oNextPhoto = oCurrentPhoto.next("div.slide");
  if (oNextPhoto.length == 0)
    oNextPhoto = jQuery("div.slide:first");

  oCurrentPhoto.removeClass("current").addClass("previous");
  oNextPhoto.css({opacity: 0.0}).addClass("current")
    .animate({opacity: 1.0}, 2000,
      function() {
        oCurrentPhoto.removeClass("previous");
      });
}

function toggleContactForm(clickedElement) {
	var contactForm = clickedElement.next();
	if (contactForm.hasClass('hidden')) {
		contactForm.slideDown( "slow", function() {
		 	contactForm.toggleClass('hidden');
		 	var scrollBarPosition = clickedElement.offset().top - 15;
        	jQuery('html,body').animate({scrollTop: scrollBarPosition}, 300);
		});
	} else {
		contactForm.slideUp( "slow", function() {
			 contactForm.toggleClass('hidden');
		});
	}
}


jQuery(document).ready(function() {
  	radioCheckBoxWrapper();
  	inputTypeFile();
  	jQuery("a.back-to-top-button").attr("href","javascript: void(0)");
  	jQuery("input:file").change(function() {
		var selectedFile = jQuery(this).val().split('\\').pop();;
	  	jQuery(this).parent().siblings('p.selected-file').html("Selected file: <span class='file-name'>" + selectedFile + "</span>");
	  	jQuery(this).parent().addClass('file-selected');
	});
	jQuery("a.back-to-top-button").click(function(event) {
		event.preventDefault();
		jQuery("html, body").animate({"scrollTop": "0px"}, 100);
	});
	jQuery(function() {
	  setInterval("rotateImages()", 5000);
	});

	jQuery(".user-menu-icon").click(function(e) {
		jQuery(".user-menu").toggleClass("show");
		jQuery(this).toggleClass("active");
		e.stopPropagation();
	});

	jQuery(".user-menu ul li a").each(function(e) {
		var href = jQuery(this).attr('href').split('/');
		var slug = href[href.length-1];
	    jQuery(this).addClass(slug);
	})


	jQuery(document).click(function(e) {
	    jQuery(".user-menu").removeClass("show");
	    jQuery(this).removeClass("active");
	});

	jQuery(".user-menu").click(function(e) {
	    e.stopPropagation();
	});

	jQuery("div.messages").prepend("<span class='icon'></span>");

    //Initialise jQuery UI accordion on the accordion field group.
  if (jQuery(".field-group-accordion-wrapper")[0]) {
    jQuery( ".field-group-accordion-wrapper" ).accordion({
      collapsible: true,
      active: false,
      animate: 200,
      activate: function(event, ui) {
        if (jQuery(ui.newHeader).offset()) {
          var scrollBarPosition = jQuery(ui.newHeader).offset().top - 15;
            jQuery('html,body').animate({scrollTop: scrollBarPosition}, 100);
          }
        }
    });
  }

	// jQuery('.page-community .views-field-field-creative-lead > .field-content,' +
	// 	'.page-community .views-field-field-new-members-email > .field-content').addClass('hidden');

 //  jQuery('.page-community .views-field-field-creative-lead label.views-label-field-creative-lead,' +
 //  	'.page-community .views-field-field-new-members-email label.views-label-field-new-members-email').click(function(e) {
 //  		var clickedElement = jQuery(this);
 //  		toggleContactForm(clickedElement);
 //  });

    //jQuery('div.field-type-multifield.field-name-field-creative-lead').after("<div class='description'>we encourage you to have a team, but under stand some small vehicles may not need the support.</div><button id='copy-contact-person'>*Use above details</button>");

    jQuery('input#edit-field-solo-project-und').change(function() {
    	if (this.checked) {
    		var firstName = jQuery('body.page-node-add-mutant-vehicles fieldset#edit-field-contact-person-und-0 div.field-name-field-first-name input').val();
    		var lastName = jQuery('body.page-node-add-mutant-vehicles fieldset#edit-field-contact-person-und-0 div.field-name-field-last-name input').val();
    		var emailAddress = jQuery('body.page-node-add-mutant-vehicles fieldset#edit-field-contact-person-und-0 div.field-name-field-email-address input').val();
    		var phoneNumber = jQuery('body.page-node-add-mutant-vehicles fieldset#edit-field-contact-person-und-0 div.field-name-field-phone-number input').val();
    		jQuery('body.page-node-add-mutant-vehicles fieldset#edit-field-creative-lead-und-0 div.field-name-field-first-name input').val(firstName);
    		jQuery('body.page-node-add-mutant-vehicles fieldset#edit-field-creative-lead-und-0 div.field-name-field-last-name input').val(lastName);
    		jQuery('body.page-node-add-mutant-vehicles fieldset#edit-field-creative-lead-und-0 div.field-name-field-email-address input').val(emailAddress);
    		jQuery('body.page-node-add-mutant-vehicles fieldset#edit-field-creative-lead-und-0 div.field-name-field-phone-number input').val(phoneNumber);
    	}
    });

    jQuery('body.page-node-add-mutant-vehicles fieldset#edit-field-contact-person-und-0 input,' +
    	'body.page-node-add-mutant-vehicles fieldset#edit-field-creative-lead-und-0 input').focus(function(e) {
    	console.log(jQuery(this).attr('name'));
    });

    jQuery('input.form-text.form-autocomplete').wrap("<div class='autocomplete-wrapper'></div>");
    //jQuery(".field-group-div").not(":has(.field)").remove();

    jQuery('body.page-user-login form#user-login').after("<a href='/user/password?'>Forgot Password</a>");

jQuery(".js input.form-autocomplete").attr('placeholder', 'Type to search' );

});




