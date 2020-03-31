/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magedelight_Giftcard/js/action/set-coupon-code',
    'Magedelight_Giftcard/js/action/cancel-coupon'
], function ($, ko, Component, setCouponCodeAction, cancelCouponAction) {
    'use strict';

    var mdGiftcardCode = ko.observable(null),
        appliedGiftCards = ko.observableArray(window.checkoutConfig.appliedGiftcards);

    return Component.extend({
        defaults: {
            template: 'Magedelight_Giftcard/payment/discount'
        },
        mdGiftcardCode: mdGiftcardCode,
        appliedGiftCards: appliedGiftCards,
        isBlockDisplayed: window.checkoutConfig.isGiftcardBlockDisplayed,

        /**
         * Check  if gift card block can be displayed
         */
        isDisplayed: function () {
            return this.isBlockDisplayed;
        },

        /**
         * Coupon code application procedure
         */
        apply: function () {
            if (this.validate()) {
                setCouponCodeAction(mdGiftcardCode, appliedGiftCards);
            }
        },

        /**
         * Cancel using coupon
         */
        cancel: function () {
            cancelCouponAction(appliedGiftCards);
        },

        /**
         * Coupon form validation
         *
         * @returns {Boolean}
         */
        validate: function () {
            var form = '#md-giftcard-form';

            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
