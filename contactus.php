<?php
$msg = "";
$msgClass = "";
if (filter_has_var(INPUT_POST, 'submit')) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    if (!empty($email) && !empty($name) && !empty($message)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $msg = 'Please use a valid email';
            $msgClass = 'alert-danger';
        } else {
            $toEmail = 'your_email@example.com'; // Replace with your email
            $subject = 'Contact Form Submission from ' . $name;
            $body = "
                <h2>Contact Form Submission</h2>
                <h4>Name</h4><p>" . $name . "</p>
                <h4>Email</h4><p>" . $email . "</p>
                <h4>Message</h4><p>" . $message . "</p>
            ";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-Type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: " . $name . "<" . $email . ">" . "\r\n";

            if (mail($toEmail, $subject, $body, $headers)) {
                $msg = 'Your email has been sent';
                $msgClass = 'alert-success';
            } else {
                $msg = 'There was an error sending the email';
                $msgClass = 'alert-danger';
            }
        }
    } else {
        $msg = 'Please fill in all fields';
        $msgClass = 'alert-danger';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/contactus.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar" style="background-color: var(--primary-color);">
        <div class="logo">
            <a href="index.html" class="home-link">
                <img src="assets/img/logo.png" alt="Alumni Network Logo">
                <span>Alumni Network</span>
            </a>
        </div>
        <div class="nav-links">
            <a href="index.html" class="home-btn">Home</a>
        </div>
    </nav>

    <div class="container">
        <h1 class="mb-4">You are successfully registered to the System</h1>
        <p class="lead">Stay tuned for emails which will be brought up to you. If you have any queries, don't hesitate to contact us.</p>

        <?php if ($msg != ''): ?>
            <div class="alert <?php echo $msgClass; ?> alert-dismissible fade show" role="alert">
                <?php echo $msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" name="message" rows="5" required><?php echo isset($_POST['message']) ? $_POST['message'] : ''; ?></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        gsap.from(".container h1", {opacity: 0, duration: 1, delay: 0.5, y: 50});
        gsap.from(".container p", {opacity: 0, duration: 1, delay: 0.75, y: 50});
        gsap.from(".form-label", {opacity: 0, duration: 0.75, delay: 1, stagger: 0.2, x: -20});
        gsap.from(".form-control", {opacity: 0, duration: 0.75, delay: 1, stagger: 0.2, x: 20});
        gsap.from(".btn-primary", {opacity: 0, duration: 1, delay: 1.5, scale: 0.8});
    </script>
</body>
</html>
