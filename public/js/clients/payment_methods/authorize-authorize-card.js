/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**************************************************************************!*\
  !*** ./resources/js/clients/payment_methods/authorize-authorize-card.js ***!
  \**************************************************************************/
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

/**
 * Invoice Ninja (https://invoiceninja.com)
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2021. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license 
 */
var AuthorizeAuthorizeCard = /*#__PURE__*/function () {
  function AuthorizeAuthorizeCard(publicKey, loginId) {
    _classCallCheck(this, AuthorizeAuthorizeCard);

    this.publicKey = publicKey;
    this.loginId = loginId;
    this.cardHolderName = document.getElementById("cardholder_name");
    this.cardButton = document.getElementById("card_button");
  }

  _createClass(AuthorizeAuthorizeCard, [{
    key: "handleAuthorization",
    value: function handleAuthorization() {
      var myCard = $('#my-card');
      var authData = {};
      authData.clientKey = this.publicKey;
      authData.apiLoginID = this.loginId;
      var cardData = {};
      cardData.cardNumber = myCard.CardJs('cardNumber').replace(/[^\d]/g, '');
      cardData.month = myCard.CardJs('expiryMonth').replace(/[^\d]/g, '');
      cardData.year = myCard.CardJs('expiryYear').replace(/[^\d]/g, '');
      cardData.cardCode = document.getElementById("cvv").value.replace(/[^\d]/g, '');
      ;
      var secureData = {};
      secureData.authData = authData;
      secureData.cardData = cardData;
      document.getElementById('card_button').disabled = true;
      document.querySelector('#card_button > svg').classList.remove('hidden');
      document.querySelector('#card_button > span').classList.add('hidden');
      Accept.dispatchData(secureData, this.responseHandler);
      return false;
    }
  }, {
    key: "responseHandler",
    value: function responseHandler(response) {
      if (response.messages.resultCode === "Error") {
        var i = 0;
        var $errors = $('#errors'); // get the reference of the div

        $errors.show().html("<p>" + response.messages.message[i].code + ": " + response.messages.message[i].text + "</p>");
        document.getElementById('card_button').disabled = false;
        document.querySelector('#card_button > svg').classList.add('hidden');
        document.querySelector('#card_button > span').classList.remove('hidden');
      } else if (response.messages.resultCode === "Ok") {
        document.getElementById("dataDescriptor").value = response.opaqueData.dataDescriptor;
        document.getElementById("dataValue").value = response.opaqueData.dataValue;
        document.getElementById("server_response").submit();
      }

      return false;
    }
  }, {
    key: "handle",
    value: function handle() {
      var _this = this;

      this.cardButton.addEventListener("click", function () {
        _this.cardButton.disabled = !_this.cardButton.disabled;

        _this.handleAuthorization();
      });
      return this;
    }
  }]);

  return AuthorizeAuthorizeCard;
}();

var publicKey = document.querySelector('meta[name="authorize-public-key"]').content;
var loginId = document.querySelector('meta[name="authorize-login-id"]').content;
/** @handle */

new AuthorizeAuthorizeCard(publicKey, loginId).handle();
/******/ })()
;