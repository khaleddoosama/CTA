/*

Template:  Webmin - Bootstrap 4 & Angular 5 Admin Dashboard Template
Author: potenzaglobalsolutions.com
Design and Developed by: potenzaglobalsolutions.com

NOTE: 

*/

(function ($) {
  "use strict";



  $(document).ready(function () {
    $("#signupForm").validate({
      rules: {
        name: "required",
        description: {
          required: true,
          minlength: 2
        },
        selling_price: {
          required: true,
          digits: true,
        },
        discount_price: {
          digits: true,
        },
        quantity: {
          required: true,
          digits: true,
        },
        image: "required",
      },
      messages: {
        name: "الرجاء ادخال اسم المنتج",
        description: {
          required: "الرجاء ادخال وصف المنتج",
          minlength: "الرجاء ادخال وصف المنتج بشكل صحيح"
        },
        selling_price: {
          required: "الرجاء ادخال سعر البيع",
          digits: "الرجاء ادخال سعر البيع بشكل صحيح",
        },
        discount_price: {
          digits: "الرجاء ادخال سعر الخصم بشكل صحيح",
        },
        quantity: {
          required: "الرجاء ادخال الكمية",
          digits: "الرجاء ادخال الكمية بشكل صحيح",
        },
        image: "الرجاء ادخال صورة المنتج",
      },
      errorElement: "em",
      errorPlacement: function (error, element) {
        // Add the `help-block` class to the error element
        error.addClass("help-block");

        if (element.prop("type") === "checkbox") {
          error.insertAfter(element.parent("label"));
        } else {
          error.insertAfter(element);
        }
      },
      highlight: function (element, errorClass, validClass) {
        $(element).parents(".col-sm-5").addClass("has-error").removeClass("has-success");
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).parents(".col-sm-5").addClass("has-success").removeClass("has-error");
      }
    });
    $("#signupForm2").validate({
      rules: {
        name: "required",
        description: {
          required: true,
          minlength: 2
        },
        selling_price: {
          required: true,
          digits: true,
        },
        discount_price: {
          digits: true,
        },
        quantity: {
          required: true,
          digits: true,
        },
      },
      messages: {
        name: "الرجاء ادخال اسم المنتج",
        description: {
          required: "الرجاء ادخال وصف المنتج",
          minlength: "الرجاء ادخال وصف المنتج بشكل صحيح"
        },
        selling_price: {
          required: "الرجاء ادخال سعر البيع",
          digits: "الرجاء ادخال سعر البيع بشكل صحيح",
        },
        discount_price: {
          digits: "الرجاء ادخال سعر الخصم بشكل صحيح",
        },
        quantity: {
          required: "الرجاء ادخال الكمية",
          digits: "الرجاء ادخال الكمية بشكل صحيح",
        },
      },
      errorElement: "em",
      errorPlacement: function (error, element) {
        // Add the `help-block` class to the error element
        error.addClass("help-block");

        if (element.prop("type") === "checkbox") {
          error.insertAfter(element.parent("label"));
        } else {
          error.insertAfter(element);
        }
      },
      highlight: function (element, errorClass, validClass) {
        $(element).parents(".col-sm-5").addClass("has-error").removeClass("has-success");
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).parents(".col-sm-5").addClass("has-success").removeClass("has-error");
      }
    });

    $("#sliderForm1").validate({
      rules: {
        title: "required",
        description: {
          required: true,
          minlength: 2
        },
        image: "required",
      },
      messages: {
        title: "الرجاء ادخال اسم المنتج",
        description: {
          required: "الرجاء ادخال وصف المنتج",
          minlength: "الرجاء ادخال وصف المنتج بشكل صحيح"
        },
        image: "الرجاء ادخال صورة المنتج",
      },
      errorElement: "em",
      errorPlacement: function (error, element) {
        // Add the `help-block` class to the error element
        error.addClass("help-block");

        if (element.prop("type") === "checkbox") {
          error.insertAfter(element.parent("label"));
        } else {
          error.insertAfter(element);
        }
      },
      highlight: function (element, errorClass, validClass) {
        $(element).parents(".col-sm-5").addClass("has-error").removeClass("has-success");
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).parents(".col-sm-5").addClass("has-success").removeClass("has-error");
      }
    });
    $("#sliderForm2").validate({
      rules: {
        title: "required",
        description: {
          required: true,
          minlength: 2
        },
      },
      messages: {
        title: "الرجاء ادخال اسم المنتج",
        description: {
          required: "الرجاء ادخال وصف المنتج",
          minlength: "الرجاء ادخال وصف المنتج بشكل صحيح"
        },
      },
      errorElement: "em",
      errorPlacement: function (error, element) {
        // Add the `help-block` class to the error element
        error.addClass("help-block");

        if (element.prop("type") === "checkbox") {
          error.insertAfter(element.parent("label"));
        } else {
          error.insertAfter(element);
        }
      },
      highlight: function (element, errorClass, validClass) {
        $(element).parents(".col-sm-5").addClass("has-error").removeClass("has-success");
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).parents(".col-sm-5").addClass("has-success").removeClass("has-error");
      }
    });


  });

})(jQuery);