$(function () {
  if (window.isDev && $("form").not("[data-mailform-config]").length > 0)
    console.info(
      'WF: found a form which is not managed by WF. Add > data-mailform-config="<config>" < to the form in order to make WF manage forms.'
    );

  var FORMS = $("form[data-mailform-config]"); // selector for forms that shall be handles by the FW

  /*
   * prepare forms...
   */
  FORMS.off(); // remove "submit" handler from all forms that should be handled by the FW
  FORMS.each(function () {
    $(window).trigger("mailform-before-preparation", $(this));

    if ($(this).attr("data-mailform-mark-required") !== undefined) {
      $(this)
        .find("input, textarea, select")
        .each(function () {
          if ($(this).attr("required") !== undefined)
            $(this).attr("placeholder", $(this).attr("placeholder") + " *");
        });
    }

    if (
      $(this).attr("action") === undefined ||
      $(this).attr("action").trim() === ""
    ) {
      if (window.isDev)
        console.warn(
          'WF: <form> is missing "action" attribute:',
          $(this),
          "Using /mail as default"
        );
      $(this).attr("action", "/mail"); // default mail target if not set
    }

    if (!$(this).find("[data-mailform-submit]").length) {
      if (window.isDev)
        console.error(
          'WF: Form is missing button that is marked as "submit button". Please add "data-mailform-submit" to the form submit button'
        );
      return;
    } else {
      var form = $(this);
      form
        .find("[data-mailform-submit]:not([data-preserve-events])")
        .off()
        .click(function (e) {
          e.preventDefault();
          form.submit(); // this will trigger the validation plugin to run
        });
    }

    $('<div class="form-result"></div>').insertAfter($(this));

    /*
     * actual form validation
     */
    const custom_validation_config = $(this).data("validation-rules")
      ? window[$(this).data("validation-rules")]
      : {}; // use e.g. data-validation-rules="foobar" ... @ script window.foobar={RULES HERE}
    $(this).validate(
      Object.assign(
        {},
        {
          submitHandler: function (form) {
            var $form = $(form);

            let hasFiles = false;

            $form
              .find("[data-mailform-submit]")
              .prop("disabled", true)
              .addClass("mf-processing");

            /*
             * find required fields and add them to post data so also PHP can check if all required fields are set
             */
            var req_inputs = [];
            $form.find("[required]").each(function () {
              if (
                $(this).is(":visible") ||
                $(this).hasClass("req-even-if-hidden")
              )
                req_inputs.push($(this).attr("name"));
            });

            var data = $(form).serializeArray(); // the actual form fields & their values
            data.push({ name: "reqs", value: req_inputs }); // the additional "required" data
            data.push({ name: "config", value: $form.data("mailform-config") }); // the config that shall be used for mailing

            /*
             * this allows us to get straight / linear values for checkboxes
             * adding "prevent-boolify" as class will make this feature ignore the checkbox with this class
             * adding data-boolify-<on|off> allows you to define the concrete values passed through post when the checkbox is ticket/unticked
             */
            $form
              .find('input[type="checkbox"]:not(.prevent-boolify)')
              .each(function () {
                var boolify_on = $(this).data("boolify-on") || "Ja";
                var boolify_off = $(this).data("boolify-off") || "Nein";
                data.push({
                  name: $(this).attr("name"),
                  value: $(this).is(":checked") ? boolify_on : boolify_off,
                });
              });

            let success_callback = null;
            if (
              $form.data("after-success") &&
              typeof window[$form.data("after-success")] === "function"
            )
              success_callback = window[$form.data("after-success")]; // so you need to bind your function to the window object (window.myFunction = function() { ... })

            let filesInputs = [];

            // convert data to formdata so we can submit files
            var fdata = new FormData();
            $.each(data, function (key, el) {
              fdata.append(el.name, el.value);
            });

            // add files to formdata
            $form.find('input[type="file"]').each(function (index, el) {
              filesInputs.push(this);
              hasFiles = true;
              $.each(el.files, function (key, file) {
                let fileName = el.name;
                if (el.name.indexOf("[]") === -1 && el.files.length > 1)
                  // without doing this the file would be overwritten by the next one when the input has no "[]" @ the name attribute
                  fileName += "[" + key + "]";
                fdata.append(fileName, file);
              });
            });

            if (hasFiles)
              fdata.append(
                "_file_input_names",
                filesInputs
                  .map(function (el) {
                    return $(el).attr("name");
                  })
                  .join(",")
              );

            function doSubmit() {
              /*
               * just submit the data to the mailhandler
               */
              $.ajax(
                Object.assign(
                  {},
                  {
                    type: "POST",
                    url: $form.attr("action"),
                    data: fdata,
                    dataType: "json", // 'The type of data that you're expecting back from the server'
                    cache: false,
                    processData: false, // mandatory when using FormData()
                    contentType: false, // ... same here
                    success: function (res) {
                      $form.next(".form-result").html(res.status.html);
                      if (!window.isDev && res.status.success) {
                        $form
                          .find("[data-mailform-submit]")
                          .off()
                          .prop("disabled", true); // disable submit btn and remove handler
                        if (!success_callback) $form.slideUp();
                      }
                      if (res.status.success && success_callback)
                        success_callback($form, data, req_inputs, res);
                      if (res.status.success) {
                        $(window).trigger("mailform-successfully-sent", $form);
                        //                      ^
                        //                      |
                        // hook anywhere with: $(window).on('mailform-successfully-sent', function (event, form) { // ... your code })
                      }
                    },
                  },
                  hasFiles
                    ? {
                        // .. special attribs for forms with files
                      }
                    : {
                        // .. special attribs for non-file forms
                      }
                )
              );
            } // -- doSubmit

            if (
              window.grecaptcha &&
              typeof window.grecaptcha.execute === "function" &&
              window.grecaptcha_version === 3
            ) {
              if (typeof window.grecaptcha_site_key === "undefined")
                console.error(
                  "WF: reCAPTCHA v3 is enabled but no site key is set. Please set the site key @ Config"
                );

              grecaptcha
                .execute(window.grecaptcha_site_key, { action: "submit" })
                .then(function (token) {
                  fdata.append("g-recaptcha-response", token);
                  doSubmit();
                });
            } else doSubmit();
          },
        },
        custom_validation_config
      )
    );

    /*
     * fill forms on dev
     */
    if (window.isDev) {
      $(this)
        .find("input, select, textarea")
        .each(function () {
          if ($(this).data("dev-val")) {
            if ($(this).is("select"))
              $(this)
                .find("option")
                .eq($(this).data("dev-val"))
                .prop("selected", true);
            else if ($(this).is("input, textarea"))
              $(this).val($(this).data("dev-val"));
            $(this).focus().blur(); // trigger label effect (move label up above the input)
          }
        });
      //$('.sm_default-form-legals').find('input[type="checkbox"]').prop('checked', true);
      $(".sm_checkbox-grp")
        .find('input[type="checkbox"]')
        .each(function () {
          // randomly check some boxes within a group
          $(this).prop("checked", Math.round(Math.random()));
        });
      $(".sm_radio-grp")
        .find('input[type="radio"]')
        .each(function () {
          // just select the last radio button in a radio grp
          $(this).prop("selected", true);
        });
    }

    $(window).trigger("mailform-after-preparation", $(this));
  });
});
