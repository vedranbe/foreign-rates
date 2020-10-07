<?php

function fr_select_function( $atts = array(), $content = null ) {
  
  extract(shortcode_atts(array(
    'base' => 'base',
    'currencies' => 'currencies',
   ), $atts));
  switch ($base) {
    case "EUR":
    $currency = "Euro";
    break;
    case "USD":
    $currency = "US Dollar";
    break;
    case "CHF":
    $currency = "Swiss Franc";
    break;
    case "GBP":
    $currency = "British Pound";
    break;
    case "CAD":
    $currency = "Canadian Dollar";
    break;
    default:
}    
return '<form action="">
  <input
    name="amount"
    id="amount"
    type="number"
    value="1"
    autocomplete="off"
    oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
    maxlength="10"
    min="1"
  />
  <div class="currencies">
    <div class="selectbox">
      <div class="select" id="selectFrom">
        <div class="content-select">
          <div class="content-option">
            <img src="'. plugins_url() .'/' . ER_PLUGIN_NAME .'/assets/img/flags/'.strtolower($base).'.svg" alt="" />
            <div class="text">
              <h1 class="title" data-rate="1">'.strtoupper($base).'</h1>
              <p class="description">'.$currency.'</p>
            </div>
          </div>
        </div>
        <i class="fas fa-angle-down"></i>
      </div>

      <div class="options" id="optionsFrom">
        <a href="#" class="option">
          <div class="content-option">
            <img src="'. plugins_url() .'/' . ER_PLUGIN_NAME .'/assets/img/flags/'.strtolower($base).'.svg" alt="" />
            <div class="text">
              <h1 class="title" data-rate="1">'.strtoupper($base).'</h1>
              <p class="description">'.$currency.'</p>
            </div>
          </div>
        </a>
      </div>
    </div>
    <div class="switch">
      <svg
        aria-hidden="true"
        data-id="icon-exchange"
        viewBox="0 0 50 47"
        height="32px"
        width="30px"
      >
        <path
          fill="currentColor"
          fill-rule="evenodd"
          d="M49.897 35.977L26.597 25v7.874H7.144v6.207h19.455v7.874zM.103 11.642l23.3 10.977v-7.874h19.454V8.538H23.402V.664z"
        ></path>
      </svg>
    </div>
    <div class="selectbox">
      <div class="select" id="selectTo">
        <div class="content-select">
          <div class="content-option">
            <img src="'. plugins_url() .'/' . ER_PLUGIN_NAME .'/assets/img/flags/'.strtolower($base).'.svg" alt="" />
            <div class="text">
              <h1 class="title" data-rate="1">'.strtoupper($base).'</h1>
              <p class="description">'.$currency.'</p>
            </div>
          </div>
        </div>
        <i class="fas fa-angle-down"></i>
      </div>

      <div class="options" id="optionsTo">
      <a href="#" class="option">
          <div class="content-option">
            <img src="'. plugins_url() .'/' . ER_PLUGIN_NAME .'/assets/img/flags/'.strtolower($base).'.svg" alt="" />
            <div class="text">
              <h1 class="title" data-rate="1">'.strtoupper($base).'</h1>
              <p class="description">'.$currency.'</p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>
  <input
    type="hidden"
    name="currencyFrom"
    id="hiddenInputFrom"
    value="'.strtoupper($base).'"
    data-rate="1"
  />
  <input
    type="hidden"
    name="currencyTo"
    id="hiddenInputTo"
    value="'.strtoupper($base).'"
    data-rate="1"
  />
</form>
<div class="result">
  <div id="valueFrom"></div>
  <div class="currencyFrom"></div>
  <div>=</div>
  <div id="valueTo"></div>
  <div class="currencyTo"></div>
</div>
<div class="rates">
  <div id="rateFrom"></div>
  <div class="currencyFrom"></div>
  <div>=</div>
  <div id="rateTo"></div>
  <div class="currencyTo"></div>
</div>
<div class="rates">
  <div id="rateFromReverse"></div>
  <div class="currencyTo"></div>
  <div>=</div>
  <div id="rateToReverse"></div>
  <div class="currencyFrom"></div>
</div>
</div>
<script>
(function ($, root, undefined) {
    $(function () {
      "use strict";
      var currency;
      $.ajax({
        type : "GET",
        dataType: "json",
        async: false,
        url: "https://api.exchangeratesapi.io/latest?base='.$base.'&symbols='.$currencies.'",
        success: function (data) {

          $.each(data.rates, function (key, value) {
            switch (key) {
              case "EUR":
                currency = "Euro";
                break;
              case "USD":
                currency = "US Dollar";
                break;
              case "CHF":
                currency = "Swiss Franc";
                break;
              case "GBP":
                currency = "British Pound";
                break;
              case "CAD":
                currency = "Canadian Dollar";
                break;
              default:
            }
            $("<a href=\"#\" class=\"option\"><div class=\"content-option\"><img src=\"'. plugins_url() .'/' . ER_PLUGIN_NAME .'/assets/img/flags/" + key.toLowerCase() + ".svg\" alt=\"\" /><div class=\"text\"><h1 class=\"title\" data-rate=\"" + value + "\">" + key + "</h1><p class=\"description\">" + currency + "</p></div></div></a>").appendTo(".options");
          });
        },
      });
    });
})(jQuery, this);
</script>';
}

unset($base);
unset($symbol);
unset($currency);
unset($json_a);
unset($value);
unset($rate);

add_shortcode('foreign_rates', 'fr_select_function');
