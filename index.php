<!DOCTYPE html>
<html lang="en">
<head>
    <title>PHP Auth System</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Simple PHP authentication system with OTP">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<header class="topbar">
    <div class="container">
        <div class="topbar-content">
            <div>
                <h1>PHP Auth System</h1>
                <p>Registration, OTP login and password management</p>
            </div>
            <span class="project-tag">Core PHP + MySQL</span>
        </div>
    </div>
</header>

<main class="container main-wrapper">
    <div class="row g-4">
        <div class="col-lg-4">
            <aside class="side-card">
                <h2>Auth APIs</h2>
                <p class="side-text">Select an action and test the API from the panel.</p>

                <div class="menu-list">
                    <button class="menu-btn active" data-box="registerBox">
                        <span class="menu-count">01</span>
                        <span>
                            <strong>Register User</strong>
                            <small>Create user and generate OTP</small>
                        </span>
                    </button>

                    <button class="menu-btn" data-box="generateBox">
                        <span class="menu-count">02</span>
                        <span>
                            <strong>Generate OTP</strong>
                            <small>Regenerate email/mobile OTP</small>
                        </span>
                    </button>

                    <button class="menu-btn" data-box="verifyBox">
                        <span class="menu-count">03</span>
                        <span>
                            <strong>Verify OTP</strong>
                            <small>Login and create token</small>
                        </span>
                    </button>

                    <button class="menu-btn" data-box="changeBox">
                        <span class="menu-count">04</span>
                        <span>
                            <strong>Change Password</strong>
                            <small>Protected by JWT token</small>
                        </span>
                    </button>

                    <button class="menu-btn" data-box="passwordBox">
                        <span class="menu-count">05</span>
                        <span>
                            <strong>Verify Password</strong>
                            <small>Check password hash</small>
                        </span>
                    </button>
                </div>

                <div class="note-box">
                    <strong>Note</strong>
                    <p>OTP is valid for 5 minutes. Time is stored in Asia/Kolkata timezone.</p>
                </div>
            </aside>
        </div>

        <div class="col-lg-8">
            <section class="content-card">
                <div id="registerBox" class="form-box active">
                    <div class="box-head">
                        <h3>Register User</h3>
                        <p>Enter user details. Profile image will be saved as Base64.</p>
                    </div>

                    <form id="registerForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Mobile Number</label>
                                <input class="form-control" name="mobile" placeholder="9876543210">
                            </div>

                            <div class="col-md-6">
                                <label>Email Address</label>
                                <input class="form-control" name="email" placeholder="name@example.com">
                            </div>

                            <div class="col-md-6">
                                <label>Password</label>
                                <input class="form-control" name="password" type="password" placeholder="Minimum 6 characters">
                            </div>

                            <div class="col-md-6">
                                <label>Profile Image</label>
                                <input class="form-control" name="profile_image" type="file" accept="image/*">
                            </div>
                        </div>

                        <button class="btn-main mt-4">Register User</button>
                    </form>
                </div>

                <div id="generateBox" class="form-box">
                    <div class="box-head">
                        <h3>Generate OTP Again</h3>
                        <p>Send either email or mobile, one at a time.</p>
                    </div>

                    <label>Email or Mobile</label>
                    <input id="generateInput" class="form-control" placeholder="name@example.com or 9876543210">

                    <button id="generateBtn" class="btn-main mt-4">Generate OTP</button>
                </div>

                <div id="verifyBox" class="form-box">
                    <div class="box-head">
                        <h3>Verify OTP & Login</h3>
                        <p>Enter OTP received/generated for email or mobile.</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label>Email or Mobile</label>
                            <input id="verifyInput" class="form-control" placeholder="name@example.com or 9876543210">
                        </div>

                        <div class="col-md-5">
                            <label>OTP</label>
                            <input id="otpInput" class="form-control otp-field" placeholder="6 digit OTP" maxlength="6">
                        </div>
                    </div>

                    <button id="verifyBtn" class="btn-main mt-4">Verify OTP</button>
                </div>

                <div id="changeBox" class="form-box">
                    <div class="box-head">
                        <h3>Change Password</h3>
                        <p>Login token is automatically saved after OTP verification.</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label>Old Password</label>
                            <input id="oldPass" class="form-control" type="password" placeholder="Old password">
                        </div>

                        <div class="col-md-6">
                            <label>New Password</label>
                            <input id="newPass" class="form-control" type="password" placeholder="New password">
                        </div>

                        <div class="col-md-6">
                            <label>Confirm Password</label>
                            <input id="confirmPass" class="form-control" type="password" placeholder="Confirm password">
                        </div>
                    </div>

                    <button id="changeBtn" class="btn-main mt-4">Change Password</button>
                </div>

                <div id="passwordBox" class="form-box">
                    <div class="box-head">
                        <h3>Verify Password</h3>
                        <p>Check whether entered password matches bcrypt hash.</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Email</label>
                            <input id="passEmail" class="form-control" placeholder="name@example.com">
                        </div>

                        <div class="col-md-6">
                            <label>Password</label>
                            <input id="passValue" class="form-control" type="password" placeholder="Password">
                        </div>
                    </div>

                    <button id="passBtn" class="btn-main mt-4">Verify Password</button>
                </div>

                <div class="response-card">
                    <div class="response-title">
                        <h4>API Response</h4>
                        <button id="clearResponse" type="button">Clear</button>
                    </div>
                    <pre id="response">{}</pre>
                </div>
            </section>
        </div>
    </div>
</main>

<div id="messageBox" class="message-box"></div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
