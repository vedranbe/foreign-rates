(function ($, root, undefined) {
  $(function () {
    "use strict";

    $(".result, .rates").hide();
    setTimeout(function () {
      var selectFrom = $("#selectFrom");
      var optionsFrom = $("#optionsFrom");
      var selectedFrom = $("#selectFrom .content-select");
      var hiddenInputFrom = $("#hiddenInputFrom");

      var selectTo = $("#selectTo");
      var optionsTo = $("#optionsTo");
      var selectedTo = $("#selectTo .content-select");
      var hiddenInputTo = $("#hiddenInputTo");

      function delimitNumbers(str) {
        return (str + "").replace(/\b(\d+)((\.\d+)*)\b/g, function (a, b, c) {
          return (
            (b.charAt(0) > 0 && !(c || ".").lastIndexOf(".")
              ? b.replace(/(\d)(?=(\d{3})+$)/g, "$1,")
              : b) + c
          );
        });
      }

      function changeValue() {
        var value =
          ($("#amount").val() * $("#hiddenInputTo").attr("data-rate")) /
          $("#hiddenInputFrom").attr("data-rate");
        var rate =
          $("#hiddenInputTo").attr("data-rate") /
          $("#hiddenInputFrom").attr("data-rate");
        var rateReverse =
          $("#hiddenInputFrom").attr("data-rate") /
          $("#hiddenInputTo").attr("data-rate");
        $(".result > div:nth-child(3)").show();
        $(".result #valueFrom").text(delimitNumbers($("#amount").val()));
        $(".result #valueTo").text(delimitNumbers(value.toFixed(4)));
        $(".rates #rateFrom").text("1");
        $(".rates #rateTo").text(rate.toFixed(6));
        $(".rates #rateFromReverse").text("1");
        $(".rates #rateToReverse").text(rateReverse.toFixed(6));
        $(".currencyFrom").text(hiddenInputFrom.val());
        $(".currencyTo").text(hiddenInputTo.val());
        $(".rates, .result").fadeTo(500, 1);
      }

      $.fn.swapWith = function (swap_with_selector) {
        var el1 = this;
        var el2 = $(swap_with_selector);

        if (el1.length === 0 || el2.length === 0) return;

        var el2_content = el2.html();
        el2.html(el1.html());
        el1.html(el2_content);
      };

      $(".switch").click(function (e) {
        e.preventDefault();
        $(selectedFrom).swapWith($(selectedTo));
        hiddenInputFrom.val(selectedFrom.find(".title").text());
        var rateFrom = selectedFrom.find(".title").data("rate");
        hiddenInputFrom.attr("data-rate", rateFrom);
        hiddenInputTo.val(selectedTo.find(".title").text());
        var rateTo = selectedTo.find(".title").data("rate");
        hiddenInputTo.attr("data-rate", rateTo);
        changeValue();
      });

      $("#optionsFrom > .option").on("click", function (e) {
        e.preventDefault();
        selectedFrom.html("");
        $(this).clone().appendTo(selectedFrom);
        selectFrom.toggleClass("active");
        optionsFrom.toggleClass("active");
        hiddenInputFrom.val(selectedFrom.find(".title").text());
        var rateFrom = selectedFrom.find(".title").data("rate");
        hiddenInputFrom.attr("data-rate", rateFrom);
        changeValue();
      });

      $("#optionsTo > .option").on("click", function (e) {
        e.preventDefault();
        selectedTo.html("");
        $(this).clone().appendTo(selectedTo);
        selectTo.toggleClass("active");
        optionsTo.toggleClass("active");
        hiddenInputTo.val(selectedTo.find(".title").text());
        var rateTo = selectedTo.find(".title").data("rate");
        hiddenInputTo.attr("data-rate", rateTo);
        changeValue();
      });
      $(selectFrom).on("click", function (e) {
        e.preventDefault();
        selectFrom.toggleClass("active");
        optionsFrom.toggleClass("active");
        selectTo.removeClass("active");
        optionsTo.removeClass("active");
      });

      $(selectTo).on("click", function (e) {
        e.preventDefault();
        selectFrom.removeClass("active");
        optionsFrom.removeClass("active");
        selectTo.toggleClass("active");
        optionsTo.toggleClass("active");
      });
      $("#amount").on("input", function () {
        changeValue();
      });
    }, 500);
  });
})(jQuery, this);
