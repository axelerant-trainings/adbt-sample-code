/**
 * @file
 * Attaches custom JS for evaluating number in the URL.
 */

(function ($, Drupal) {
  var pathName = window.location.pathname;
  var number_val = pathName.match(/adbt-debugging\/(.*)$/i)[1];

  var type = 'Odd';
  if (number_val % 2 === 0) {
    type = 'Even';
  }

 console.log(type);

})(jQuery, Drupal);
