"use strict";

// Class definition
var CreateEmployee = (function () {
    // Base elements
    var _wizardEl;
    var _formEl;
    var _wizardObj;
    var _validations = [];
    var payment_method_input = $('select[name="PartTime"]');
    var hourly_wage_input = $('input[name="EhChf"]');
    var monthly_wage_input = $('input[name="rroga"]');

    // Private functions
    var _initWizard = function () {
        // Initialize form wizard
        _wizardObj = new KTWizard(_wizardEl, {
            startStep: 1, // initial active step number
            clickableSteps: false, // to make steps clickable this set value true and add data-wizard-clickable="true" in HTML for class="wizard" element
        });

        // Validation before going to next page
        _wizardObj.on("change", function (wizard) {
            if (wizard.getStep() > wizard.getNewStep()) {
                return; // Skip if stepped back
            }

            if (wizard.currentStep == 3) {
                ReviewEmployee.init();
            }

            // Validate form before change wizard step
            var validator = _validations[wizard.getStep() - 1]; // get validator for currnt step

            if (validator) {
                validator.validate().then(function (status) {
                    if (status == "Valid") {
                        wizard.goTo(wizard.getNewStep());

                        KTUtil.scrollTop();
                    } else {
                        Swal.fire({
                            text: lang.get("script.some_errors"),
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: Lang.get("script.confirmButton"),
                            customClass: {
                                confirmButton: "btn font-weight-bold btn-light",
                            },
                        }).then(function () {
                            KTUtil.scrollTop();
                        });
                    }
                });
            }

            return false; // Do not change wizard step, further action will be handled by he validator
        });

        // Change event
        _wizardObj.on("changed", function (wizard) {
            KTUtil.scrollTop();
        });

        // Submit event
        _wizardObj.on("submit", function (wizard) {
            _formEl.submit();
        });
    };

    var _initValidation = function () {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        // Step 1
        _validations.push(
            FormValidation.formValidation(_formEl, {
                fields: {
                    name: {
                        validators: {
                            notEmpty: {
                                message: Lang.get("script.validations.name"),
                            },
                        },
                    },
                    email: {
                        validators: {
                            notEmpty: {
                                message: Lang.get("script.validations.email_required"),
                            },
                        },
                    },
                    gender: {
                        validators: {
                            notEmpty: {
                                message: Lang.get("script.validations.gender"),
                            },
                        },
                    },
                    maried: {
                        validators: {
                            notEmpty: {
                                message: Lang.get("script.validations.married"),
                            },
                        },
                    },
                    ORT: {
                        validators: {
                            notEmpty: {
                                message: Lang.get("script.validations.canton"),
                            },
                        },
                    },
                    pin: {
                        validators: {
                            notEmpty: {
                                message: Lang.get("script.validations.pin"),
                            },
                            between: {
                                min: 1000,
                                max: 9999,
                                message: Lang.get(
                                    "script.validations.pin_between"
                                ),
                            },
                        },
                    },
                    additional_income_toggle: {
                        validators: {
                            notEmpty: {
                                message: Lang.get(
                                    "script.validations.additional_income_toggle"
                                ),
                            },
                        },
                    },
                    married_since: {
                        validators: {
                            notEmpty: {
                                message: Lang.get(
                                    "script.validations.married_since"
                                ),
                            },
                        },
                    },
                    religion: {
                        validators: {
                            notEmpty: {
                                message: Lang.get(
                                    "script.validations.religion"
                                ),
                            },
                        },
                    },
                    children: {
                        validators: {
                            notEmpty: {
                                message: Lang.get(
                                    "script.validations.children"
                                ),
                            },
                        },
                    },
                    work_permit: {
                        validators: {
                            notEmpty: {
                                message: Lang.get(
                                    "script.validations.work_permit"
                                ),
                            },
                        },
                    },
                    work_permit_expiry: {
                        validators: {
                            notEmpty: {
                                message: Lang.get(
                                    "script.validations.work_permit_expiry"
                                ),
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    // Bootstrap Framework Integration
                    bootstrap: new FormValidation.plugins.Bootstrap({
                        //eleInvalidClass: '',
                        eleValidClass: "",
                    }),
                },
            })
        );

        // Step 2
        _validations.push(
            FormValidation.formValidation(_formEl, {
                fields: {
                    sage_number: {
                        validators: {
                            notEmpty: {
                                message: Lang.get("script.validations.sage_id"),
                            },
                        },
                    },
                    roli: {
                        validators: {
                            notEmpty: {
                                message: Lang.get(
                                    "script.validations.access_management"
                                ),
                            },
                        },
                    },
                    PartTime: {
                        validators: {
                            notEmpty: {
                                message: Lang.get(
                                    "script.validations.payment_method"
                                ),
                            },
                        },
                    },
                    PartTime: {
                        validators: {
                            notEmpty: {
                                message: Lang.get(
                                    "script.validations.payment_method"
                                ),
                            },
                        },
                    },
                    "locations[]": {
                        validators: {
                            choice: {
                                min: 1,
                                message: Lang.get(
                                    "script.validations.location"
                                ), // Add a proper message here
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    // Bootstrap Framework Integration
                    bootstrap: new FormValidation.plugins.Bootstrap({
                        //eleInvalidClass: '',
                        eleValidClass: "",
                    }),
                },
            })
        );

        // Step 3
        _validations.push(
            FormValidation.formValidation(_formEl, {
                fields: {
                    rroga: {
                        validators: {
                            callback: {
                                message: Lang.get(
                                    "script.validations.monthly_wage"
                                ),
                                callback: function (ele) {
                                    if (
                                        payment_method_input.val() == 0 &&
                                        ele.value == ""
                                    ) {
                                        return false;
                                    }
                                    return true;
                                },
                            },
                        },
                    },
                    child_allowance: {
                        validators: {
                            notEmpty: {
                                message: Lang.get(
                                    "script.validations.child_allowance"
                                ),
                            },
                        },
                    },
                    decki200: {
                        validators: {
                            callback: {
                                message: Lang.get(
                                    "script.validations.child_allowance_200"
                                ),
                                callback: function (input) {
                                    var childAllowanceValue = $(
                                        'select[name="child_allowance"]'
                                    ).val();
                                    var decki200Value = input.value;

                                    if (childAllowanceValue === "yes") {
                                        return decki200Value !== ""; // Required if Yes
                                    }

                                    return true; // Not required otherwise
                                },
                            },
                        },
                    },
                    decki250: {
                        validators: {
                            callback: {
                                message: Lang.get(
                                    "script.validations.education_allowance_250"
                                ),
                                callback: function (input) {
                                    var childAllowanceValue = $(
                                        'select[name="child_allowance"]'
                                    ).val();
                                    var decki250Value = input.value;

                                    if (childAllowanceValue === "yes") {
                                        return decki250Value !== ""; // Required if Yes
                                    }

                                    return true; // Not required otherwise
                                },
                            },
                        },
                    },

                    BVG: {
                        validators: {
                            notEmpty: {
                                message: Lang.get("script.validations.bvg"),
                            },
                        },
                    },
                    Perqind1: {
                        validators: {
                            notEmpty: {
                                message: Lang.get(
                                    "script.validations.perqind1"
                                ),
                            },
                        },
                    },
                    Perqind2: {
                        validators: {
                            notEmpty: {
                                message: Lang.get(
                                    "script.validations.perqind2"
                                ),
                            },
                        },
                    },
                    Perqind3: {
                        validators: {
                            notEmpty: {
                                message: Lang.get(
                                    "script.validations.perqind3"
                                ),
                            },
                        },
                    },
                    // start: {
                    // 	validators: {
                    // 		notEmpty: {
                    // 			message: Lang.get('script.validations.start')
                    // 		},
                    // 	}
                    // }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    // Bootstrap Framework Integration
                    bootstrap: new FormValidation.plugins.Bootstrap({
                        //eleInvalidClass: '',
                        eleValidClass: "",
                    }),
                    startEndDate: new FormValidation.plugins.StartEndDate({
                        format: "YYYY-MM-DD",
                        startDate: {
                            field: "start",
                            message: Lang.get("script.validations.start_max"),
                        },
                        endDate: {
                            field: "end",
                            message: Lang.get("script.validations.end_min"),
                        },
                    }),
                },
            })
        );
    };

    var _initLogic = function () {
        function the_logic() {
            if (payment_method_input.val() == 0) {
                //monthly
                hourly_wage_input.parent(".form-group").addClass("hidden");
                hourly_wage_input.val("");
                monthly_wage_input.parent(".form-group").removeClass("hidden");
            } else {
                //hourly
                hourly_wage_input.parent(".form-group").removeClass("hidden");
                monthly_wage_input.parent(".form-group").addClass("hidden");
                monthly_wage_input.val("");
            }
        }

        the_logic();

        payment_method_input.on("change", function (e) {
            the_logic();
        });
    };

    return {
        // public functions
        init: function () {
            _wizardEl = KTUtil.getById("kt_wizard");
            _formEl = KTUtil.getById("createUser");

            _initWizard();
            _initValidation();
            _initLogic();
        },
    };
})();

var ReviewEmployee = (function () {
    // Elements
    var openTag = "<div>";
    var closeTag = "</div>";
    var personal, status, deduction;

    //personal
    var name,
        surname,
        phone,
        email,
        DOB,
        gender,
        maried,
        strasse,
        PLZ,
        ORT1,
        ORT,
        TAX,
        AHV,
        bankname,
        IBAN,
        PIN,
        additional_income_toggle,
        CARD;

    //personal
    var _function, role, PartTime, noqnaSmena, locations;

    //deduction
    var EhChf,
        decki250,
        decki200,
        BVG,
        Perqind1,
        Perqind2,
        Perqind3,
        start,
        end,
        oldSaldoF,
        oldSaldo13,
        work_percetage;

    // Private functions
    var _setValues = function () {
        personal = $("#review-personal");
        status = $("#review-status");
        deduction = $("#review-deduction");

        //personal
        name = $('input[name="name"]').val();
        surname = $('input[name="surname"]').val();
        phone = $('input[name="phone"]').val();
        email = $('input[name="email"]').val();
        DOB = $('input[name="DOB"]').val();
        gender = $('select[name="gender"] option:selected').text();
        maried = $('select[name="maried"] option:selected').text();
        strasse = $('input[name="strasse"]').val();
        PLZ = $('input[name="PLZ"]').val();
        ORT1 = $('input[name="ORT1"]').val();
        ORT = $('select[name="ORT"] option:selected').text();
        TAX = $('input[name="TAX"]').val();
        AHV = $('input[name="AHV"]').val();
        bankname = $('input[name="bankname"]').val();
        IBAN = $('input[name="IBAN"]').val();
        PIN = $('input[name="pin"]').val();
        CARD = $('input[name="card"]').val();
        additional_income_toggle = $(
            'input[name="additional_income_toggle"]'
        ).val();

        //personal
        _function = $('select[name="function"] option:selected').text();
        role = $('select[name="role"] option:selected').text();
        PartTime = $('select[name="PartTime"] option:selected').text();
        noqnaSmena = $('input[name="noqnaSmena"]').is(":checked")
            ? Lang.get("script.validations.yes")
            : Lang.get("script.validations.no");
        locations = $.map($('input[name="locations[]"]'), function (e) {
            return $(e).is(":checked")
                ? $(e).closest("label").find("b").text()
                : null;
        });

        //deduction
        EhChf = $('input[name="EhChf"]').val();
        decki250 = $('input[name="decki250"]').val();
        decki200 = $('input[name="decki200"]').val();
        BVG = $('input[name="BVG"]').val();
        Perqind1 = $('input[name="Perqind1"]').val();
        Perqind2 = $('input[name="Perqind2"]').val();
        Perqind3 = $('input[name="Perqind3"]').val();
        start = $('input[name="start"]').val();
        end = $('input[name="end"]').val();
        oldSaldoF = $('input[name="oldSaldoF"]').val();
        oldSaldo13 = $('input[name="oldSaldo13"]').val();
        work_percetage = $('input[name="work_percetage"]').val();
    };

    var _initReview = function () {
        var text = "";
        text +=
            openTag + Lang.get("script.first_name") + ": " + name + closeTag;
        text +=
            openTag + Lang.get("script.last_name") + ": " + surname + closeTag;
        text += openTag + Lang.get("script.phone") + ": " + phone + closeTag;
        text += openTag + Lang.get("script.email") + ": " + phone + closeTag;
        text += openTag + Lang.get("script.dob") + ": " + DOB + closeTag;
        text += openTag + Lang.get("script.gender") + ": " + gender + closeTag;
        text += openTag + Lang.get("script.maried") + ": " + maried + closeTag;
        text += openTag + Lang.get("script.street") + ": " + strasse + closeTag;
        text += openTag + Lang.get("script.plz") + ": " + PLZ + closeTag;
        text += openTag + Lang.get("script.ort") + ": " + ORT1 + closeTag;
        text += openTag + Lang.get("script.canton") + ": " + ORT + closeTag;
        text +=
            openTag +
            Lang.get("script.withholding_tax") +
            ": " +
            TAX +
            closeTag;
        text += openTag + Lang.get("script.ahv") + ": " + AHV + closeTag;
        text += openTag + Lang.get("script.bank") + ": " + bankname + closeTag;
        text += openTag + Lang.get("script.iban") + ": " + IBAN + closeTag;
        text += openTag + Lang.get("script.pin") + ": " + PIN + closeTag;
        text += openTag + Lang.get("script.card") + ": " + CARD + closeTag;
        personal.html(text);

        var text = "";
        text +=
            openTag + Lang.get("script.function") + ": " + _function + closeTag;
        text +=
            openTag +
            Lang.get("script.access_management") +
            ": " +
            role +
            closeTag;
        text +=
            openTag +
            Lang.get("script.payment_method") +
            ": " +
            PartTime +
            closeTag;
        text +=
            openTag +
            Lang.get("script.nightshift") +
            ": " +
            noqnaSmena +
            closeTag;
        text +=
            openTag +
            Lang.get("script.hotels") +
            ": " +
            locations.join(", ") +
            closeTag;
        status.html(text);

        var text = "";
        text +=
            openTag + Lang.get("script.hourly_rate") + ": " + EhChf + closeTag;
        text +=
            openTag +
            Lang.get("script.education_allowance_250") +
            ": " +
            decki250 +
            closeTag;
        text +=
            openTag +
            Lang.get("script.child_allowance_200") +
            ": " +
            decki200 +
            closeTag;
        text += openTag + Lang.get("script.bvg") + ": " + BVG + closeTag;
        text +=
            openTag +
            Lang.get("script.holiday_compensation_1") +
            ": " +
            Perqind1 +
            closeTag;
        text +=
            openTag +
            Lang.get("script.holiday_compensation_2") +
            ": " +
            Perqind2 +
            closeTag;
        text +=
            openTag +
            Lang.get("script.13th_salary") +
            ": " +
            Perqind3 +
            closeTag;
        text +=
            openTag + Lang.get("script.start_date") + ": " + start + closeTag;
        text += openTag + Lang.get("script.end_date") + ": " + end + closeTag;
        text +=
            openTag +
            Lang.get("script.old_holiday_balance") +
            ": " +
            oldSaldoF +
            closeTag;
        text +=
            openTag +
            Lang.get("script.old_balance_13") +
            ": " +
            oldSaldo13 +
            closeTag;
        text +=
            openTag +
            Lang.get("script.work_percetage") +
            ": " +
            work_percetage +
            closeTag;
        deduction.html(text);
    };

    return {
        // public functions
        init: function () {
            _setValues();
            _initReview();
        },
    };
})();

jQuery(document).ready(function () {
    CreateEmployee.init();
});

document.addEventListener("DOMContentLoaded", function () {
    const submitBtn = document.getElementById("finalSubmit");
    const form = document.getElementById("createUser");

    submitBtn.addEventListener("click", function () {
        submitBtn.disabled = true; // Disable the button
        submitBtn.textContent = "Submitting..."; // Optional: Change button text
        form.submit(); // Submit the form
    });
});

$(document).ready(function () {
    // Initialize Select2 for nationality dropdown
    $(".select2-nationality").select2({
        allowClear: true,
        templateResult: formatCountry,
        templateSelection: formatCountrySelection,
        width: "100%",
    });

    // Format the displayed option
    function formatCountry(country) {
        if (!country.id) {
            return country.text;
        }
        var $country = $("<span>" + country.text + "</span>");
        return $country;
    }

    // Format the selected option
    function formatCountrySelection(country) {
        return country.text;
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const taxToggle = document.getElementById("taxToggle");

    if (taxToggle) {
        // Handle change event
        taxToggle.addEventListener("change", function () {
            const container = document.getElementById("taxInputContainer");
            const taxInput = container.querySelector('input[name="TAX"]');

            if (this.value === "yes") {
                container.style.display = "block";
            } else {
                container.style.display = "none";
                taxInput.value = "";
            }
        });

        // Initialize on page load
        taxToggle.dispatchEvent(new Event("change"));
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const incomeToggle = document.getElementById("additionalIncomeToggle");
    const incomeAmountContainer = document.getElementById(
        "additionalIncomeAmountContainer"
    );

    if (incomeToggle) {
        incomeToggle.addEventListener("change", function () {
            if (this.value === "yes") {
                incomeAmountContainer.style.display = "";
            } else {
                incomeAmountContainer.style.display = "none";
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const childAllowanceSelect = document.getElementById("child_allowance");
    const allowanceFields = document.getElementById("allowance_fields");
    const decki250Input = document.getElementById("decki250");
    const decki200Input = document.getElementById("decki200");

    // Function to toggle fields visibility and clear values
    function toggleAllowanceFields() {
        if (childAllowanceSelect.value === "yes") {
            allowanceFields.style.display = "flex";
        } else {
            // Clear the input values when hiding
            decki250Input.value = "";
            decki200Input.value = "";
            allowanceFields.style.display = "none";
        }
    }

    // Initial check
    toggleAllowanceFields();

    // Add event listener
    childAllowanceSelect.addEventListener("change", toggleAllowanceFields);
});
