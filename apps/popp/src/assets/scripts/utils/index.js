import 'slick-carousel';
import "slick-carousel/slick/slick.css";

export default (function () {
  // ------------------------------------------------------
  // @Window Resize
  // ------------------------------------------------------

  /**
   * NOTE: Register resize event for Masonry layout
   */
  const EVENT = document.createEvent('UIEvents');
  window.EVENT = EVENT;
  EVENT.initUIEvent('resize', true, false, window, 0);


  window.addEventListener('load', () => {
    /**
     * Trigger window resize event after page load
     * for recalculation of masonry layout.
     */
    window.dispatchEvent(EVENT);
  });

  // ------------------------------------------------------
  // @External Links
  // ------------------------------------------------------

  // Open external links in new window
  $('a')
    .filter('[href^="http"], [href^="//"]')
    .not(`[href*="${window.location.host}"]`)
    .attr('rel', 'noopener noreferrer')
    .attr('target', '_blank');

  // ------------------------------------------------------
  // @Resize Trigger
  // ------------------------------------------------------

  // Trigger resize on any element click
  document.addEventListener('click', () => {
    window.dispatchEvent(window.EVENT);
  });

  $('[data-toggle="tooltip"]').tooltip();
  
     
  $('#carouselLogo').slick({
    slidesToShow: 7,
    slidesToScroll: 3,
    autoplay: true,
    autoplaySpeed: 2000,
    dots: false,
    centerMode: true,
    arrows: false,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 5,
          slidesToScroll: 3,
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1
        }
      }
      // You can unslick at a given breakpoint now by adding:
      // settings: "unslick"
      // instead of a settings object
    ]
  });


}());
