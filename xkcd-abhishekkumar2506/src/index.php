<?php
require_once 'functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_code']) && !empty($_POST['email'])) {
        $email = trim($_POST['email']);
        $code = generateVerificationCode();
        if (!is_dir('codes')) {
            mkdir('codes', 0755, true);
        }
        file_put_contents("codes/{$email}.txt", $code);
        sendVerificationEmail($email, $code);
        $message = "Verification code sent to your email.";
    }

    if (isset($_POST['verify_code']) && !empty($_POST['verification_code'])) {
        $email = trim($_POST['email'] ?? '');
        $inputCode = trim($_POST['verification_code']);
        $codeFile = "codes/{$email}.txt";

        if (file_exists($codeFile)) {
            $storedCode = trim(file_get_contents($codeFile));
            if ($inputCode === $storedCode) {
                registerEmail($email);
                unlink($codeFile);
                $message = "Email verified and registered successfully!";
            } else {
                $message = "Invalid code. Please try again.";
            }
        } else {
            $message = "No code found for that email. Please request a new one.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>XKCD Subscription</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(120deg,rgba(209, 13, 235, 0.34),rgb(14, 158, 224));
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .outer-box {
            padding: 20px;
            background: #ffffff33;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .middle-box {
            background: #ffffff;
            border-radius: 20px;
            padding: 20px;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .inner-box {
            background: #f9f9f9;
            border: 2px dashed #ddd;
            border-radius: 15px;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="email"],
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 14px;
            box-sizing: border-box;
        }

        input:focus {
            outline: none;
            border-color: #7bb3ff;
            box-shadow: 0 0 6px rgba(123, 179, 255, 0.5);
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background-color: #6c63ff;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background-color: #5a52e6;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
            color: green;
        }

        hr {
            margin: 30px 0;
            border: none;
            border-top: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <div class="outer-box">
        <div class="middle-box">
            <div class="inner-box">
                <h2>Subscribe to XKCD Comics</h2>

                <?php if ($message): ?>
                    <div class="message"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <!-- Form 1: Request verification -->
                <form method="post">
                    <label for="email">Email Address:</label>
                    <input id="email" type="email" name="email" required>
                    <button type="submit" name="send_code">Send Verification Code</button>
                </form>

                <hr>

                <!-- Form 2: Verify -->
                <form method="post">
                    <label for="verification_code">Verification Code:</label>
                    <input id="verification_code" type="text" name="verification_code" maxlength="6" required>
                    <input type="hidden" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    <button type="submit" name="verify_code">Verify</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
