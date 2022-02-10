/**
 * @file
 * Scripts for the color palette.
 */

(function ($, Drupal, drupalSettings) {
    var node;
    jQuery.ajax({
        url: 'http://webservices.ddev.site/jsonapi/node/page',
        method: 'GET',
        data: JSON.stringify(node),
        success: function (node) {
            $.each(node.data, function (index, item) {
                // Print node titles.
                alert(index + 1 + '. ' + item.attributes.title);
            });
        }
    });

})(jQuery, Drupal, drupalSettings);
