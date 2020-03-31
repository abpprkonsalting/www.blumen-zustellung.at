/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer store credit(balance) application
 */
define([
    'ko',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'mage/url',
    'Magento_Checkout/js/model/error-processor',
    'Magedelight_Giftcard/js/model/payment/discount-messages',
    'mage/storage',
    'mage/translate',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/full-screen-loader'
], function (ko, $, quote, mage_url, errorProcessor, messageContainer, storage, $t, getPaymentInformationAction,
    totals, fullScreenLoader
) {
    'use strict';

    return function (mdGiftcardCode, appliedGiftCards) {
        var quoteId = quote.getQuoteId(),
            url = mage_url.build('giftcard/cart/giftcardpost');

        fullScreenLoader.startLoader();

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: {giftcard_code: mdGiftcardCode(), isAjax: true},
        }).done(function (response) {
            fullScreenLoader.stopLoader();
            if(response.status)
            {
                mdGiftcardCode('');
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
