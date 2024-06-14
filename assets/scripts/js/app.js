/////////////////////////////////////
// Foundation Abide Validation
/////////////////////////////////////

function checkboxValidator(
  $el,      /* jQuery element to validate */
  required, /* is the element required according to the `[required]` attribute */
  parent    /* parent of the jQuery element `$el` */
) {
  const group = parent.closest('.checkbox-group');
  const min = group.attr('data-abide-validator-min');
  const checked = group.find(':checked').length;
  if (checked >= min) {
    // clear label highlight
    group.find('label').each(function() {
     $(this).removeClass('is-invalid-label');
    });
    // clear checkbox error
    group.find(':checkbox').each(function() {
     $(this).removeClass('is-invalid-input').removeAttr('data-invalid');
    });
    group.find('.form-error').hide();
    return true;
  } else {
    group.find('.form-error').css({
      display: 'block'
    });
    return false;
  }
}

Foundation.Abide.defaults.validators['require-checkbox'] = checkboxValidator;
Foundation.Abide.defaults.patterns['minlength'] = /^(.){6,}$/;
Foundation.Abide.defaults.patterns['phone-number'] = /^[0-9\-\+\(\)\s]*$/;

$(document).foundation();



/////////////////////////////////////
// Add Flex Video to YouTube, Vimeo
/////////////////////////////////////
$('iframe[src*="youtube.com"], iframe[src*="vimeo.com"]').each(function() {
  if ( jQuery(this).innerWidth() / jQuery(this).innerHeight() > 1.5 ) {
    $(this).wrap("<div class='widescreen responsive-embed'/>");
  } else {
    $(this).wrap("<div class='responsive-embed'/>");
  }
});

$('iframe[src*="google.com"]').each(function() {
  $(this).wrap("<div class='responsive-embed'/>");
});



/////////////////////////////////////
// Venue Select Menu
/////////////////////////////////////
  
$('#venue-list').on('change', function() {
  const url = $(this).val();
  if (url != "") {
    window.location.href = url;
  }
})



/////////////////////////////////////
// QR Code
/////////////////////////////////////

if ($('#qrcode').length > 0) {
  $('#qrcode').append(qrcode);
}



/////////////////////////////////////
// Slideshows
/////////////////////////////////////

if ($('.slick').length > 0) {
  $('.slick-featured').slick({
    adaptiveHeight: false,
    autoplay: true
  });

  $('.slick-ads').slick({
    autoplay: true,
    arrows: false,
    dots: true
  });

  $('.slick-gallery').slick({
    autoplay: true
  });
}



/////////////////////////////////////
// Partner genre filter
/////////////////////////////////////

$('#partner-genre').on('change', function() {
  const genre = $(this).val();
  $('.partner-grid .partner-filter').removeClass('disabled');

  $('.partner-grid .partner-filter').each(function( index ) {
    if ( genre != "" && !$(this).hasClass(genre) ) {
      $(this).addClass('disabled');
    }
  });

})



/////////////////////////////////////
//	Form Value Check
/////////////////////////////////////

const options = {
  url: "/account/schools",
  list: {
    maxNumberOfElements: 10,
    match: {
      enabled: true
    }
  }
};

$("input#field-school").easyAutocomplete(options);

/*
if ($('#form-account-update').length > 0 ) {
  const date = $('#field-birthdate').val();
  selectDate(date);
}
*/
$('#field-email').on('blur', checkEmail);
$('#birthdate-select select').on('change', checkBirthdate);
$('input#field-language-other').on('change', checkLanguage);
$('input[name="fields[userDemoGender][]"]').on('change', checkGender);
$('.edit-pass input[name="fields[passUser]"]').on('change', setPassStatus);

function selectDate(date) {
  const arr = date.split('/');
  $('select[name=birth-month]').val(arr[0]).change();
  $('select[name=birth-day]').val(arr[1]).change();
  $('select[name=birth-year]').val(arr[2]).change();
}

function checkBirthdate() {
  let formattedDate;
  const birthMonth = $('#birthdate-select select[name="birth-month"]').val();
  const birthDay = $('#birthdate-select select[name="birth-day"]').val();
  const birthYear = $('#birthdate-select select[name="birth-year"]').val();

  if (birthMonth.length > 0 && birthDay.length > 0 && birthYear.length > 0) {
    formattedDate = birthMonth + '/' + birthDay + '/' + birthYear;
    $('#field-birthdate').val(formattedDate);
    console.log(formattedDate);
  }
}

function checkEmail() {
  $.ajax({
    type: 'GET',
    dataType: 'text',
    url: '/account/emailcheck/'+$(this).val(),
    success: function(data){
    const value = $.trim(data);
      if (value == "1") {
        $("#register-email .duplicate-error").removeClass('hide');
      } else {
        $("#register-email .duplicate-error").addClass('hide');
      }
      if ($('#account_username').hasClass('error')) {
        $("label.username_return").html("");
      }
      if ($('#account_username').val() == "") {
        $("label.username_return").html("");
      }
      if ($('#account_username').val() == $('#account_username').data('current')) {
        $("label.username_return").html("");
      }
    }
  })
  return false;
}

function checkLanguage() {
  if ($(this).prop('checked')) {
    $('#field-language-custom-input').addClass('show');
  } else {
    $('#field-language-custom-input').removeClass('show');
  }
}

function checkGender() {
  if ($(this).val() == "custom") {
    $('#field-gender-custom-input').addClass('show');
  } else {
    $('#field-gender-custom-input').removeClass('show');
  }
}

function setPassStatus() {
  if($(this).val() == "") {
    console.log('available');
  } else {
    console.log('assigned');
  }
}



/////////////////////////////////////
//	Monthly Calendar
/////////////////////////////////////

if ($('#calFilters').length > 0) {
  const urlParams= new URLSearchParams(window.location.search);

  const genre = urlParams.has("genre")? urlParams.get("genre") : "";
  const cat = urlParams.has("cat")? urlParams.get("cat") : "";
  const partner = urlParams.has("partner")? urlParams.get("partner") : "";
  const venue = urlParams.has("venue")? urlParams.get("venue") : "";

  if (genre) {
    $('#calFilters select[name=genre]').val(genre).change().addClass('selected');
  }
  if (cat) {
    $('#calFilters select[name=cat]').val(cat).change().addClass('selected');
  }
  if (partner) {
    $('#calFilters select[name=partner]').val(partner).change().addClass('selected');
  }
  if (venue) {
    $('#calFilters select[name=venue]').val(venue).change().addClass('selected');
  }
}

$('#calFilters select, #calFilters input').on('change', function() {
  const value = $(this).val();

  if(value) {
    $(this).addClass('selected');
  } else {
    $(this).removeClass('selected');
  }
  $('#calEventsListings').addClass('loading');
  $('#calFilters').submit();
})

$('#calPicker td.calendar-pad').on('click', function() {
  $('#calPicker td').removeClass('active');
  $(this).addClass('active');

  const site = $(this).data('site');
  const date = $(this).data('date');
  const dateLabel = $(this).data('date-label');
  const eventCount = $(this).data('event-count');

  const query = window.location.href.indexOf('?');
  const hashes = window.location.href.slice(window.location.href.indexOf('?') + 1);
  const urlParams= new URLSearchParams(window.location.search);

  const isFree = urlParams.has("isFree")? "true" : "";
  const genre = urlParams.has("genre")? urlParams.get("genre") : "";
  const cat = urlParams.has("cat")? urlParams.get("cat") : "";
  const partner = urlParams.has("partner")? urlParams.get("partner") : "";
  const venue = urlParams.has("venue")? urlParams.get("venue") : "";

  let filter;
  filter = isFree + genre + cat + partner + venue;

  let url;
  if (filter == "") {
    url = "/api/events-date.json?site=" + site +"&date=" + date;
  }
  else if (query > 0) {
    url = "/api/events-filtered.json?site=" + site +"&date=" + date + '&' + hashes;
  } else {
    url = "/api/events-date.json?site=" + site +"&date=" + date;
  }
  
  console.log(url);

  $('#calEvents #calEventsDate').text(dateLabel);
  $('#calEvents #calEventsCount').text(eventCount + ' events');
  $('#calEventsListings').addClass('loading');

  $.getJSON( url, function( data ) {
    const events = data.data;
    const totalEvents = data.meta.pagination.total;
    let allDay = [];
    let notAllDay = [];

    $.each( events, function( key, val ) {
      const isAllDay = events[key].allDay;

      if (isAllDay == 1) {
        allDay.push(events[key]);
      } else {
        notAllDay.push(events[key]);
      }
    });

    allDay.sort(SortByTitle);

    let listings = [];

    $.each(notAllDay, function( key, val) {
      const enabled = notAllDay[key].enabled;
      const id = notAllDay[key].id;
      const title = notAllDay[key].title;
      const start = notAllDay[key].start;
      const startDate = notAllDay[key].startDate;
      const startTime = notAllDay[key].startTime;
      const slug = notAllDay[key].slug;
      let venue = "";
      let isFree = "";

      if (notAllDay[key].venue.length) {
        venue = notAllDay[key].venue[0].title;
      }

      if (notAllDay[key].category.length) {
        isFree = notAllDay[key].category.some(e => e.id === 63);
      }

      if ( date === startDate && enabled === true ) {
        let string ='<p class="calendar-item"><strong>' + startTime + '</strong> ';
        if (isFree) {
          string += '<a href="/calendar?cat=63" class="category button small">Free</a> ';
        }
        string += '<strong><a href="/calendar/event/' + slug + '">' + title + '</a></strong> ';
        string += '<span>' + venue + '</span></p>';

        listings.push( string );
      }

    });
    $.each(allDay, function( key, val) {
      const enabled = allDay[key].enabled;
      const id = allDay[key].id;
      const title = allDay[key].title;
      const start = allDay[key].start;
      const slug = allDay[key].slug;
      let venue = "";
      let isFree = "";

      if (allDay[key].venue.length) {
        venue = allDay[key].venue[0].title;
      }

      if (allDay[key].category.length) {
        isFree = allDay[key].category.some(e => e.id === 63);
      }

      if ( enabled === true ) {
        let string ='<p class="calendar-item"><strong>All Day</strong> ';
        if (isFree) {
          string += '<a href="/calendar?cat=63" class="category button small">Free</a> ';
        }
        string += '<strong><a href="/calendar/event/' + slug + '">' + title + '</a></strong> '
        string += '<span>' + venue + '</span></p>';

        listings.push( string );
      }

    });
    
    console.log(listings);

    $('#calList').html(listings.join(""))
    $('#calEventsListings').removeClass('loading');

  });
} )

function SortByTitle(x,y) {
  return ((x.title == y.title) ? 0 : ((x.title > y.title) ? 1 : -1 ));
}



/////////////////////////////////////
// Event Check-In
/////////////////////////////////////

$("#form-checkin input:radio[name='showtimes']").on('change', function() {

  const showtime = $(this).val();

  const backingField = $("#field-showtime");
  const unlistedField = $("#field-showtimes-unlisted-options");
  const unlistedBackingField = $("#field-showtime-unlisted");
  const isUnlisted = showtime === "unlisted";

  backingField.val(isUnlisted? unlistedField.val() : showtime);
  unlistedBackingField.val(isUnlisted? "1" : "0");

  unlistedField.toggleClass("disabled", !isUnlisted);
  unlistedField.prop("disabled", !isUnlisted);
  unlistedField.prop("required", isUnlisted);
  unlistedField.toggleClass("hide", !isUnlisted);

  unlistedField.toggle(isUnlisted);

})

$('#form-checkin #field-showtimes-unlisted-options').on('change', function() {
  $("#field-showtime").val($(this).val());
})

$('#form-checkin #field-guest-count').on('change', function() {
  const guestCount = $(this).val();
  const guestTypes = $("#checkin-guest-types");
  const guestTypesList = $("#checkin-guest-types .checkbox-group")
  guestTypes.toggle(guestCount > 0);
  guestTypesList.attr("data-abide-validator-min", guestCount > 0? "1" : "0");
})