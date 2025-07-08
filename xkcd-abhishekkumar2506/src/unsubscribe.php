<?php
require 'functions.php';
$message = '';

if (isset($_POST['send_code']) && !empty($_POST['unsubscribe_email'])) {
    $email = trim($_POST['unsubscribe_email']);
    $code = generateVerificationCode();

    if (!is_dir('codes')) {
        mkdir('codes', 0755, true);
    }

    file_put_contents("codes/unsub_{$email}.txt", $code);
    sendUnsubscribeCode($email, $code);
    $message = "Unsubscribe verification code sent to your email.";
}

if (isset($_POST['verify_code']) && !empty($_POST['verification_code'])) {
    $email = trim($_POST['unsubscribe_email']);
    $inputCode = trim($_POST['verification_code']);
    $file = "codes/unsub_{$email}.txt";

    if (file_exists($file)) {
        $storedCode = trim(file_get_contents($file));
        if ($inputCode === $storedCode) {
            unsubscribeEmail($email);
            unlink($file);
            $message = "You have been unsubscribed successfully.";
        } else {
            $message = "Invalid verification code.";
        }
    } else {
        $message = "No unsubscribe code found. Please request again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unsubscribe from XKCD Comics</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(120deg, #fbc2eb, #a6c1ee);
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
            background: #fefefe;
            border: 2px dashed #ccc;
            border-radius: 15px;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #cc0000;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
            color: #333;
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
            border-color: #cc0000;
            box-shadow: 0 0 6px rgba(204, 0, 0, 0.3);
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background-color: #cc0000;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background-color: #a10000;
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
                <h2>Unsubscribe from XKCD Comics</h2>

                <?php if ($message): ?>
                    <div class="message"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <!-- Unsubscribe form -->
                <form method="POST">
                    <label>Email:</label>
                    <input type="email" name="unsubscribe_email" required>
                    <button type="submit" name="send_code" id="submit-unsubscribe">Unsubscribe</button>
                </form>

                <hr>

                <!-- Verification form -->
                <form method="POST">
                    <label>Verification Code:</label>
                    <input type="text" name="verification_code" maxlength="6" required>
                    <input type="hidden" name="unsubscribe_email" value="<?= isset($_POST['unsubscribe_email']) ? htmlspecialchars($_POST['unsubscribe_email']) : '' ?>">
                    <button type="submit" name="verify_code" id="submit-verification">Verify</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
