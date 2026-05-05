function notify(message, type = "success") {
    const box = $(`<div class="message ${type}">${message}</div>`);
    $("#messageBox").append(box);

    setTimeout(function () {
        box.fadeOut(200, function () {
            $(this).remove();
        });
    }, 2600);
}

function showResponse(data) {
    $("#response").text(JSON.stringify(data, null, 2));

    if (data.data && data.data.token) {
        localStorage.setItem("token", data.data.token);
    }

    if (data.status === true) {
        notify(data.message || "Success", "success");
    } else if (data.message) {
        notify(data.message, "error");
    }
}

function handleError(xhr) {
    showResponse(xhr.responseJSON || {
        status: false,
        message: "Request failed",
        error: xhr.responseText
    });
}

function isEmail(value) {
    return value.includes("@");
}

$(".menu-btn").click(function () {
    $(".menu-btn").removeClass("active");
    $(this).addClass("active");

    $(".form-box").removeClass("active");
    $("#" + $(this).data("box")).addClass("active");
});

$("#clearResponse").click(function () {
    $("#response").text("{}");
});

$("#registerForm").submit(function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: "api/login/register.php",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: showResponse,
        error: handleError
    });
});

$("#generateBtn").click(function () {
    let value = $("#generateInput").val().trim();

    if (!value) {
        notify("Email ya mobile enter karo", "error");
        return;
    }

    let body = isEmail(value) ? {email: value} : {mobile: value};

    $.ajax({
        url: "api/login/generateOTP.php",
        method: "POST",
        data: JSON.stringify(body),
        contentType: "application/json",
        success: showResponse,
        error: handleError
    });
});

$("#verifyBtn").click(function () {
    let value = $("#verifyInput").val().trim();
    let otp = $("#otpInput").val().trim();

    if (!value || !otp) {
        notify("Email/mobile aur OTP enter karo", "error");
        return;
    }

    let body = isEmail(value) ? {email: value, otp: otp} : {mobile: value, otp: otp};

    $.ajax({
        url: "api/login/verifyOTP.php",
        method: "POST",
        data: JSON.stringify(body),
        contentType: "application/json",
        success: showResponse,
        error: handleError
    });
});

$("#changeBtn").click(function () {
    let token = localStorage.getItem("token");

    if (!token) {
        notify("Pehle OTP verify/login karo", "error");
        return;
    }

    $.ajax({
        url: "api/password/change-password.php",
        method: "POST",
        headers: {
            Authorization: "Bearer " + token
        },
        data: JSON.stringify({
            old_password: $("#oldPass").val(),
            new_password: $("#newPass").val(),
            confirm_password: $("#confirmPass").val()
        }),
        contentType: "application/json",
        success: showResponse,
        error: handleError
    });
});

$("#passBtn").click(function () {
    $.ajax({
        url: "api/password/verify-password.php",
        method: "POST",
        data: JSON.stringify({
            email: $("#passEmail").val().trim(),
            password: $("#passValue").val()
        }),
        contentType: "application/json",
        success: showResponse,
        error: handleError
    });
});

$("#otpInput").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "").slice(0, 6);
});
