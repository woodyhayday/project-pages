/*!
 * Project Pages
 * https://projectpages.io
 * V2.0
 *
 * Date: 05/08/24
 */
jQuery(document).ready(function($) {
    $(document).on('click', '.notice.is-dismissible', function() {

        var toDismiss = jQuery(this).attr('data-pp-dismiss');
        console.log('Dismissing ', toDismiss );

        $.post(adminNotification.ajax_url, {
            action: adminNotification.dismiss_target,
            to_dismiss: toDismiss,
            dismiss: 'true',
            _ajax_nonce: adminNotification.nonce
        });
    });
});