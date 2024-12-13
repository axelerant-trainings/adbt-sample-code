(function (Drupal, $) {

  // Gathering Article title.
  var title = $('.title.page-title').text().trim();
  
  // Generating some random integer value for comment count placeholder.
  // var randomNumber = Math.floor(Math.random() * 1000) + 1;
  // var commentCount = randomNumber;

  // Get the comment count from drupalSettings.
  var commentCount = drupalSettings.fitness_challenge.comment_count;


  // alert("Article " + title + " could be an interesting one, as people interacted over comments " + commentCount + " times.");

})(Drupal, jQuery);

