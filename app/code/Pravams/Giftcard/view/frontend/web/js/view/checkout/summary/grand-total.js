/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals',
    'jquery'
], function (Component, quote, priceUtils, totals) {
    'use strict';

    return Component.extend({
        defaults: {
            isFullTaxSummaryDisplayed: window.checkoutConfig.isFullTaxSummaryDisplayed || false,
            template: 'Magento_Tax/checkout/summary/grand-total'
        },
        totals: quote.getTotals(),
        isTaxDisplayedInGrandTotal: window.checkoutConfig.includeTaxInGrandTotal || false,

        /**
         * @return {*}
         */
        isDisplayed: function () {
            return this.isFullMode();
        },

        /**
         * @return {*|String}
         */
        getValue: function () {
            this.showGiftcardDiscount();
            var price = 0;

            if (this.totals()) {
                price = totals.getSegment('grand_total').value;
            }

            return this.getFormattedPrice(price);
        },

        /**
         * @return {*|String}
         */
        getBaseValue: function () {
            var price = 0;

            if (this.totals()) {
                price = this.totals()['base_grand_total'];
            }

            return priceUtils.formatPrice(price, quote.getBasePriceFormat());
        },

        /**
         * @return {*}
         */
        getGrandTotalExclTax: function () {
            var total = this.totals();

            if (!total) {
                return 0;
            }

            return this.getFormattedPrice(total['grand_total']);
        },

        /**
         * @return {Boolean}
         */
        isBaseGrandTotalDisplayNeeded: function () {
            var total = this.totals();

            if (!total) {
                return false;
            }

            return total['base_currency_code'] != total['quote_currency_code']; //eslint-disable-line eqeqeq
        },

        /*
        * @return {Boolean}
        * */
        showGiftcardDiscount: function(){
            var gcText = '<tr class="totals-giftcard"><th class="mark" colspan="1" scope="row">Giftcard Discount</th><td class="amount"><span class="price">';
            var currency = jQuery('#giftcard-store-currency').val();
            gcText = gcText+"-"+currency+jQuery('#giftcard-value-only').val();
            gcText = gcText+'</span></td></tr>';
            jQuery('.totals-giftcard').remove();
            if(jQuery('#giftcard-value-only').val()>0){
                jQuery(gcText).insertBefore(jQuery(('.grand')));
            }else{
                jQuery('.totals-giftcard').remove();
            }
        }
    });
});
