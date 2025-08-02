    <?php
    include_once 'includes/functions/registration.php';

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
        rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

        <!--<link rel="stylesheet" href="assets/css/styles.css">-->
        <link rel="stylesheet" href="assets/css/custom_steps.css">
        <title>Running Event Registration</title>
        
    </head>
    <body class="bg-light">
            <?php require_once 'includes/countdown.php'; ?>

        <div class="container mb-5">
                <div class="p-4 p-md-5">
                    <!-- Progress Bar -->
                    <div class="d-flex justify-content-between position-relative mb-5">
                        <div class="progress position-absolute w-100" style="height: 2px; top: 50%; transform: translateY(-50%); background-color: #333 ;">
                            <div class="progress-bar" role="progressbar" style="width: <?= (($currentStep-1)/4)*100 ?>%"></div>
                        </div>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="progress-step <?= $i < $currentStep ? 'bg-success' : ($i == $currentStep ? 'bg-primary' : 'bg-secondary') ?> text-white" data-step="<?= $i ?>">
                                <?= $i ?>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <!-- Step 1: Select Distance -->
                    <div class="registration-step <?= $currentStep == 1 ? 'active' : '' ?>" id="step-1">
                        <img src="assets/img/background.jpg" alt="" style="width: 250px; height: auto;" id="logo_event" class="img-fluid">
                        <h1 class="text-left mb-4">Select Distance</h1>
                        <form method="post" class="text-center">
                            <input type="hidden" name="step" value="1">
                            <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                                <button type="submit" name="distance" value="3Km" class="distance-btn btn btn-lg <?= $_SESSION['registration_data']['distance'] == '3Km' ? 'btn-primary' : 'btn-outline-primary' ?>">3Km</button>
                                <button type="submit" name="distance" value="6Km" class="distance-btn btn btn-lg <?= $_SESSION['registration_data']['distance'] == '6Km' ? 'btn-primary' : 'btn-outline-primary' ?>">6Km</button>
                                <button type="submit" name="distance" value="12Km" class="distance-btn btn btn-lg <?= $_SESSION['registration_data']['distance'] == '12Km' ? 'btn-primary' : 'btn-outline-primary' ?>">12Km</button>
                            </div>
                        </form>
                    </div>

                    <!-- Step 2: Registration -->
                    <div class="registration-step <?= $currentStep == 2 ? 'active' : '' ?>" id="step-2">
                        <h1 class="text-left mb-4">Registration</h1>
                        <form method="post">
                            <input type="hidden" name="step" value="2">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Full name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($_SESSION['registration_data']['full_name']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Select</option>
                                        <option value="Male" <?= $_SESSION['registration_data']['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= $_SESSION['registration_data']['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                                        <option value="Other" <?= $_SESSION['registration_data']['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="home_address" class="form-label">Home Address</label>
                                <textarea class="form-control" id="home_address" name="home_address" rows="3" required><?= htmlspecialchars($_SESSION['registration_data']['home_address']) ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_SESSION['registration_data']['email']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone_number" name="phone_number" minlength="11" maxlength="11" 
        pattern="\d{11}" 
        required 
        placeholder="Enter 11-digit number" value="<?= htmlspecialchars($_SESSION['registration_data']['phone_number']) ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="emergency_contact" class="form-label">Emergency Contact</label>
                                                <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" minlength="11" maxlength="11" 
                        pattern="\d{11}" 
                        required 
                        placeholder="Enter 11-digit number" value="<?= htmlspecialchars($_SESSION['registration_data']['emergency_contact']) ?>" required>
                                            </div>
                                            <div class="mb-4">
    <label class="form-label">Shirt Size</label>
    <div class="shirt-size-column">
        <?php
        $sizes = [
            'XS - W/19in L/27in'=> '',
            'S - W/20in L/28in' => '',
            'M - W/21in L/29in' => '',
            'L - W/22.50in L/30.50in' => '',
            'XL- W/24in L/32in' => '',
            'XXL - W/25.50in L/33in' => ''
        ];

        foreach ($sizes as $key => $desc): ?>
            <label class="shirt-option">
                <input type="radio" name="shirt_size" value="<?= $key ?>"
                    <?= (isset($_SESSION['registration_data']['shirt_size']) && $_SESSION['registration_data']['shirt_size'] === $key) ? 'checked' : '' ?> required>
                <span class="label-content"><?= $key ?></span>
                <div class="size-description"><?= $desc ?></div>
            </label>
        <?php endforeach; ?>
    </div>
</div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="index.php?step=1" class="btn btn-dark" id="btn">Previous</a>
                                <button type="submit" class="btn btn-dark" id="btn">Next</button>
                            </div>
                        </form>
                    </div>

                    <!-- Step 3: Payment Method -->
                    <div class="registration-step <?= $currentStep == 3 ? 'active' : '' ?>" id="step-3">
                        <h1 class="text-left mb-4">Payment Method</h1>
                        <form method="post">
                            <input type="hidden" name="step" value="3">
                            <div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
                                <?php foreach ($paymentMethods as $method): ?>
                                    <div class="col">
                                        <div class="payment-option h-100 p-3 border rounded <?= $_SESSION['registration_data']['payment_method'] == $method ? 'border-success bg-light' : '' ?>">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_method" id="pay_<?= htmlspecialchars($method) ?>" 
                                                    value="<?= htmlspecialchars($method) ?>" 
                                                    <?= $_SESSION['registration_data']['payment_method'] == $method ? 'checked' : '' ?>
                                                    required>
                                                <label class="form-check-label w-100" for="pay_<?= htmlspecialchars($method) ?>">
                                                    <?= htmlspecialchars($method) ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="index.php?step=2" class="btn btn-dark">Previous</a>
                                <button type="submit" class="btn btn-dark">Next</button>
                            </div>
                        </form>
                    </div>

                    <!-- Step 4: Details Summary -->
                    <div class="registration-step <?= $currentStep == 4 ? 'active' : '' ?>" id="step-4">
                        <h1 class="text-left mb-4">Details Summary</h1>
                        <div class="mb-4">
                            <?php foreach ($_SESSION['registration_data'] as $key => $value): ?>
                                <?php if (!in_array($key, ['agreement1', 'agreement2']) && !empty($value)): ?>
                                    <div class="mb-3">
                                        <strong><?= ucwords(str_replace('_', ' ', $key)) ?>:</strong>
                                        <span class="d-block"><?= htmlspecialchars($value) ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <form method="post">
                            <input type="hidden" name="step" value="4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="agreement1" id="agreement1" <?= $_SESSION['registration_data']['agreement1'] ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="agreement1">
                                    I agree to the terms and conditions
                                </label>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="agreement2" id="agreement2" <?= $_SESSION['registration_data']['agreement2'] ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="agreement2">
                                    I agree to the waiver of liability
                                </label>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="index.php?step=3" class="btn btn-dark">Previous</a>
                                <button type="submit" class="btn btn-success">Confirm Registration</button>
                            </div>
                        </form>
                    </div>

                    <!-- Step 5: Complete -->
    <!-- Step 5: Complete -->
<div class="registration-step <?= $currentStep == 5 ? 'active' : '' ?>" id="step-5">
    <h1 class="text-left mb-4">Registration Complete!</h1>
    <?php if (isset($_SESSION['qr_code']) && file_exists($_SESSION['qr_code'])): ?>
        <div class="card mx-auto mb-4 text-center" style="max-width: 500px;">
            <div class="card-body">
                <!-- Payment Status -->
                <p class="card-text" style="font-size: 1.2rem;">
                    <strong>Status:</strong>
                    <?php if (!empty($_SESSION['payment_status']) && $_SESSION['payment_status'] === 'Paid'): ?>
                        <span class="badge bg-success">Paid</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Unpaid</span>
                    <?php endif; ?>
                </p>

                <!-- Participant Info -->
                <p class="card-text"><strong>Name:</strong> <?= htmlspecialchars($_SESSION['participant_name'] ?? '') ?></p>
                <h3 class="card-text">Transaction #: <?= htmlspecialchars($_SESSION['transaction_number'] ?? 'N/A') ?></h3>

                <!-- QR Code -->
                <?php if (isset($_SESSION['qr_code'])): ?>
                    <img src="<?= $_SESSION['qr_code'] ?>" alt="QR Code" class="img-fluid my-3" style="max-width: 300px;">
                <?php endif; ?>

                <!-- Pay Now Button -->
                <?php if (empty($_SESSION['payment_status']) || $_SESSION['payment_status'] !== 'Paid'): ?>
                    <div class="text-center my-4">
                        <a href="includes/payment/pay-now.php?txn=<?= $_SESSION['transaction_number'] ?>" class="btn btn-success">
                            Pay Now
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    
            <!--<div class="qr-code-container text-center my-4">
                <canvas id="qr-canvas" style="display: none;"></canvas>
                <img src="<?= htmlspecialchars($_SESSION['qr_code']) ?>?t=<?= time() ?>" w
                    alt="Registration QR Code" 
                    class="img-fluid"
                    id="main-qr-code">
            </div>-->

           <!-- <button class="download-btn btn btn-primary d-block mx-auto mb-5" id="download-qr-btn">
                Download QR Code
            </button>-->
            <?php if (empty($_SESSION['payment_status']) || $_SESSION['payment_status'] !== 'Paid'): ?>
            <!--<a href="../includes/payment/payment.php urlencode($_SESSION['transaction_number'] ?? '') ?>" 
            class="btn btn-primary d-block mx-auto mb-5">
                Pay for Registration
            </a>-->
        <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-warning">QR code could not be generated. Please contact support.</div>
        <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <script src="/assets/js/hide_show.js"></script>
    <script src="/assets/js/qr-code.js"></script>
    </body>
    </html>
