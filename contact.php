<?php
require_once 'header.php';

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_inquiry'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_msg = "Please complete all fields in the inquiry form.";
    } elseif (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO inquiries (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':subject' => $subject,
                ':message' => $message
            ]);
            $success_msg = "Thank you for reaching out to Velvet Vogue! Founder John Finlo and our support team will respond to your inquiry shortly.";
        } catch (PDOException $e) {
            $error_msg = "Failed to submit inquiry: " . $e->getMessage();
        }
    }
}
?>

<div class="container" style="margin: 40px auto;">
    <h1 class="brand-font" style="text-align: center; color: var(--primary-color); margin-bottom: 10px;">Customer Support & Inquiries</h1>
    <p style="text-align: center; color: var(--text-muted); margin-bottom: 40px;">Have a question about sizing, custom orders, or delivery? We are here to help!</p>

    <?php if ($success_msg): ?>
        <div class="alert alert-success" style="max-width: 800px; margin: 0 auto 30px auto;"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger" style="max-width: 800px; margin: 0 auto 30px auto;"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 50px;">
        <!-- Inquiry Form -->
        <div style="background: white; padding: 35px; border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
            <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--primary-color); padding-bottom: 8px;">Send Us an Inquiry</h3>
            
            <form action="contact.php" method="POST" class="needs-validation">
                <div class="form-group">
                    <label for="name">Your Name *</label>
                    <input type="text" name="name" id="name" required class="form-control" placeholder="e.g. Alex Perera">
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" name="email" id="email" required class="form-control" placeholder="alex@example.com">
                </div>

                <div class="form-group">
                    <label for="subject">Subject / Topic *</label>
                    <input type="text" name="subject" id="subject" required class="form-control" placeholder="e.g. Sizing Advice for Evening Blazer">
                </div>

                <div class="form-group">
                    <label for="message">Your Message *</label>
                    <textarea name="message" id="message" rows="5" required class="form-control" placeholder="Type your inquiry or feedback here..."></textarea>
                </div>

                <button type="submit" name="send_inquiry" class="btn btn-primary" style="width: 100%; padding: 12px;">
                    Send Inquiry <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>

        <!-- Contact Information & Store Location -->
        <div>
            <div style="background: white; padding: 35px; border-radius: var(--radius-md); border: 1px solid var(--border-color); margin-bottom: 25px;">
                <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--accent-gold); padding-bottom: 8px;">Store Headquarters</h3>
                
                <div style="display: flex; flex-direction: column; gap: 15px; font-size: 1rem;">
                    <div>
                        <i class="fa-solid fa-location-dot" style="color: var(--primary-color); width: 25px;"></i>
                        <strong>Address:</strong> Velvet Vogue Headquarters, No. 45 Galle Road, Colombo 03, Sri Lanka
                    </div>
                    <div>
                        <i class="fa-solid fa-phone" style="color: var(--primary-color); width: 25px;"></i>
                        <strong>Phone Hotline:</strong> +94 11 234 5678 / +94 77 123 4567
                    </div>
                    <div>
                        <i class="fa-solid fa-envelope" style="color: var(--primary-color); width: 25px;"></i>
                        <strong>Inquiry Email:</strong> support@velvetvogue.com
                    </div>
                    <div>
                        <i class="fa-solid fa-clock" style="color: var(--primary-color); width: 25px;"></i>
                        <strong>Opening Hours:</strong> Monday - Saturday (9:00 AM - 8:00 PM)
                    </div>
                </div>
            </div>

            <div style="background: var(--dark-color); color: white; padding: 25px; border-radius: var(--radius-md);">
                <h4 style="color: var(--accent-gold); margin-bottom: 10px;"><i class="fa-solid fa-comments"></i> Direct Founder Support</h4>
                <p style="font-size: 0.9rem; opacity: 0.9;">
                    "At Velvet Vogue, we value every customer's personal style journey. If you need bespoke tailoring or corporate formalwear inquiries, our team will personally assist you within 24 hours."
                    <br><em style="display: block; margin-top: 10px; color: var(--accent-gold);">- John Finlo (Founder)</em>
                </p>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div style="background: white; padding: 35px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
        <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">Frequently Asked Questions (FAQ)</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <h4 style="color: var(--primary-color); margin-bottom: 5px;">What is your exchange policy?</h4>
                <p style="font-size: 0.9rem; color: var(--text-muted);">We offer a 14-day hassle-free exchange policy for unwashed items with tags intact.</p>
            </div>

            <div>
                <h4 style="color: var(--primary-color); margin-bottom: 5px;">How long does islandwide delivery take?</h4>
                <p style="font-size: 0.9rem; color: var(--text-muted);">Standard delivery takes 2 to 3 business days across Sri Lanka.</p>
            </div>

            <div>
                <h4 style="color: var(--primary-color); margin-bottom: 5px;">How do I know my correct clothing size?</h4>
                <p style="font-size: 0.9rem; color: var(--text-muted);">Each product page lists available sizes (S, M, L, XL). You can also send us an inquiry for custom measurements.</p>
            </div>

            <div>
                <h4 style="color: var(--primary-color); margin-bottom: 5px;">Can I add new items if I am an Admin?</h4>
                <p style="font-size: 0.9rem; color: var(--text-muted);">Yes! Admins can log in and use the Admin Panel to instantly publish new items to the store.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
