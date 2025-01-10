<?php
// verify-otp.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    $enteredOtp = $_POST['otp'];

    // Check if the OTP is correct
    if ($enteredOtp == $_SESSION['otp']) {
        // OTP is valid, show registration form
        header("Location: registration-form.php");
    } else {
        $error = "Invalid OTP.";
    }
}
?>

<!-- OTP verification form -->
<form method="POST">
    <input type="text" name="otp" placeholder="Enter OTP" required>
    <button type="submit" class="btn btn-primary">Verify OTP</button>
</form>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
