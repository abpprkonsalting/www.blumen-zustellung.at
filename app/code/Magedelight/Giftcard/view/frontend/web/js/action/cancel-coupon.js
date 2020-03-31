/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer store credit(balance) application
 */
define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'mage/url',
    'Magento_Checkout/js/model/error-processor',
    'Magedelight_Giftcard/js/model/payment/discount-messages',
    'mage/storage',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'mage/translate',
    'Magento_Checkout/js/model/full-screen-loader'
], function ($, quote, urlManager, errorProcessor, messageContainer, storage, getPaymentInformationAction, totals, $t,
  fullScreenLoader
) {
    'use strict';

    return function (appliedGiftCards) {
        var quoteId = quote.getQuoteId(),
            url = urlManager.build('giftcard/cart/giftcardremove');

        messageContainer.clear();
        fullScreenLoader.startLoader();

        var form = '#md-giftcard-form';
        var formdata = $(form).serialize();
        var formdataJson = $(form).serializeArray();
        formdata += '&isAjax=1';

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: formdata,
        }).done(function (response) {
            fullScreenLoader.stopLoader();
            if(response.status)
            {
                appliedGiftCards(response.data.appliedGiftCards);

                var deferred = $.Deferred();
                totals.isLoading(true);
                getPaymentInformationAction(deferred);
                $.when(deferred).done(function () {
                    fullScreenLoader.stopLoader();
                    totals.isLoading(false);
                });

                messageContainer.addSuccessMessage({
                    'message': response.message
                });
            }
            else
            {
                errorProcessor.process({responseText: '{"message":"'+response.message+'"}'}, messageContainer);
            }
        }).fail(function (response) {
            fullScreenLoader.stopLoader();
            totals.isLoading(false);
            errorProcessor.process({responseText: '{"message":"Oops, something wrong happened"}'}, messageContainer);
        });
    };
});
