<?php
/**
 * PKTS Karate Mail Helper
 * Handles sending email notifications for customer orders and status updates.
 */

if (!function_exists('sendOrderConfirmationEmail')) {
    /**
     * Send Order Confirmation Email to Customer's Email / Gmail account.
     */
    function sendOrderConfirmationEmail($order_number, $customer_name, $customer_email, $customer_phone, $customer_address, $customer_notes, $payment_method, $total_amount, $cart_items) {
        if (empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
            error_log("Order Email Error: Invalid or missing email address '$customer_email'");
            return false;
        }

        $formatted_total = '₱' . number_format(floatval($total_amount), 2);
        $order_date = date('F j, Y, g:i a');

        // Build HTML Table Rows for Items
        $items_html = '';
        $items_text = '';

        foreach ($cart_items as $item) {
            $name = htmlspecialchars($item['name'] ?? ($item['product_name'] ?? 'Product'));
            $size = htmlspecialchars($item['size'] ?? 'One Size');
            $price_num = floatval(preg_replace('/[^0-9.]/', '', strval($item['price'] ?? 0)));
            $qty = intval($item['qty'] ?? ($item['quantity'] ?? 1));
            $item_total = $price_num * $qty;

            $items_html .= '
            <tr>
                <td style="padding: 12px 14px; border-bottom: 1px solid #282828; color: #ffffff; font-size: 14px;">
                    <strong>' . $name . '</strong>' . ($size !== 'One Size' ? '<br><span style="font-size: 12px; color: #888888;">Size: ' . $size . '</span>' : '') . '
                </td>
                <td style="padding: 12px 14px; border-bottom: 1px solid #282828; color: #bbbbbb; font-size: 14px; text-align: center;">' . $qty . '</td>
                <td style="padding: 12px 14px; border-bottom: 1px solid #282828; color: #bbbbbb; font-size: 14px; text-align: right;">₱' . number_format($price_num, 2) . '</td>
                <td style="padding: 12px 14px; border-bottom: 1px solid #282828; color: #ffffff; font-weight: 600; font-size: 14px; text-align: right;">₱' . number_format($item_total, 2) . '</td>
            </tr>';

            $items_text .= "- {$name} (Size: {$size}) x {$qty} @ ₱" . number_format($price_num, 2) . " = ₱" . number_format($item_total, 2) . "\n";
        }

        $notes_section = '';
        if (!empty($customer_notes)) {
            $notes_section = '
            <tr>
                <td style="padding: 10px 14px; color: #888888; font-weight: 600; font-size: 13px;">Customer Notes:</td>
                <td style="padding: 10px 14px; color: #dddddd; font-size: 13px;">' . htmlspecialchars($customer_notes) . '</td>
            </tr>';
        }

        // HTML Mail Body
        $html_body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - PKTS Karate</title>
</head>
<body style="margin: 0; padding: 20px 0; background-color: #0d0d0d; font-family: \'Inter\', \'Segoe UI\', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #0d0d0d; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; width: 100%; background-color: #171717; border-radius: 14px; overflow: hidden; border: 1px solid #2a2a2a; box-shadow: 0 10px 30px rgba(0,0,0,0.6);">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 35px 20px 25px; background: linear-gradient(135deg, #141414 0%, #290808 100%); border-bottom: 3px solid #e02020;">
                            <h1 style="margin: 0; font-size: 26px; font-weight: 800; color: #ffffff; text-transform: uppercase; letter-spacing: 2px;">
                                PKTS <span style="color: #e02020;">KARATE</span> DOJO
                            </h1>
                            <p style="margin: 6px 0 0 0; font-size: 13px; color: #aaaaaa; text-transform: uppercase; letter-spacing: 1.5px;">Order Receipt & Notification</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px 30px 20px 30px;">
                            <p style="margin: 0 0 10px 0; font-size: 18px; font-weight: 700; color: #ffffff;">Hello ' . htmlspecialchars($customer_name) . ',</p>
                            <p style="margin: 0 0 20px 0; font-size: 14px; color: #cccccc; line-height: 1.6;">
                                Thank you for your order with <strong>PKTS Karate Dojo</strong>! We have successfully received your checkout details and are now processing your items.
                            </p>

                            <!-- Order Status Badge -->
                            <div style="margin-bottom: 25px;">
                                <span style="display: inline-block; background-color: rgba(224, 32, 32, 0.15); border: 1px solid #e02020; color: #ff4d4d; font-size: 13px; font-weight: 700; padding: 8px 16px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    Order #' . htmlspecialchars($order_number) . ' &bull; PENDING
                                </span>
                            </div>

                            <!-- Customer & Shipping Summary Box -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #111111; border-radius: 8px; border: 1px solid #222222; margin-bottom: 25px;">
                                <tr>
                                    <td width="38%" style="padding: 10px 14px; color: #888888; font-weight: 600; font-size: 13px;">Date:</td>
                                    <td style="padding: 10px 14px; color: #ffffff; font-size: 13px;">' . $order_date . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 14px; color: #888888; font-weight: 600; font-size: 13px; border-top: 1px solid #1f1f1f;">Payment Method:</td>
                                    <td style="padding: 10px 14px; color: #ffffff; font-size: 13px; border-top: 1px solid #1f1f1f;">' . htmlspecialchars($payment_method) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 14px; color: #888888; font-weight: 600; font-size: 13px; border-top: 1px solid #1f1f1f;">Phone Contact:</td>
                                    <td style="padding: 10px 14px; color: #ffffff; font-size: 13px; border-top: 1px solid #1f1f1f;">' . htmlspecialchars($customer_phone) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 14px; color: #888888; font-weight: 600; font-size: 13px; border-top: 1px solid #1f1f1f;">Delivery Address:</td>
                                    <td style="padding: 10px 14px; color: #ffffff; font-size: 13px; border-top: 1px solid #1f1f1f;">' . htmlspecialchars($customer_address) . '</td>
                                </tr>
                                ' . $notes_section . '
                            </table>

                            <!-- Items Purchased Table -->
                            <h3 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 700; color: #ffffff; text-transform: uppercase; letter-spacing: 0.5px;">
                                Items Ordered
                            </h3>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #111111; border-radius: 8px; border: 1px solid #222222; margin-bottom: 25px; border-collapse: collapse;">
                                <thead>
                                    <tr style="background-color: #1c1c1c; border-bottom: 2px solid #2d2d2d;">
                                        <th style="padding: 10px 14px; color: #e02020; font-size: 12px; text-transform: uppercase; text-align: left;">Item</th>
                                        <th style="padding: 10px 14px; color: #e02020; font-size: 12px; text-transform: uppercase; text-align: center;">Qty</th>
                                        <th style="padding: 10px 14px; color: #e02020; font-size: 12px; text-transform: uppercase; text-align: right;">Price</th>
                                        <th style="padding: 10px 14px; color: #e02020; font-size: 12px; text-transform: uppercase; text-align: right;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ' . $items_html . '
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #1a1a1a;">
                                        <td colspan="3" style="padding: 14px; color: #ffffff; font-weight: 700; font-size: 15px; text-align: right;">Total Amount:</td>
                                        <td style="padding: 14px; color: #e02020; font-weight: 800; font-size: 18px; text-align: right;">' . $formatted_total . '</td>
                                    </tr>
                                </tfoot>
                            </table>

                            <p style="margin: 0 0 15px 0; font-size: 13px; color: #aaaaaa; line-height: 1.5;">
                                If you have any questions regarding your order status or delivery updates, feel free to contact us at <a href="mailto:info@pktskarate.com" style="color: #e02020; text-decoration: none;">info@pktskarate.com</a> or via phone at <strong>+63 917 123 4567</strong>.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 20px; background-color: #101010; border-top: 1px solid #222222; color: #666666; font-size: 12px;">
                            <p style="margin: 0 0 4px 0; font-weight: 600; color: #888888;">PKTS Karate Dojo Pasig</p>
                            <p style="margin: 0 0 10px 0;">Robinson Metro East, Pasig City, Metro Manila</p>
                            <p style="margin: 0; font-size: 11px; color: #555555;">&copy; ' . date('Y') . ' PKTS Karate Dojo. All rights reserved.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        // Subject
        $subject = "🥋 Order Confirmation #" . $order_number . " - PKTS Karate Dojo";

        // Headers
        $headers  = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: PKTS Karate Dojo <no-reply@pktskarate.com>" . "\r\n";
        $headers .= "Reply-To: PKTS Karate Dojo <info@pktskarate.com>" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // Dispatch email
        $sent = @mail($customer_email, $subject, $html_body, $headers);

        if (!$sent) {
            error_log("Order Email Warning: Native mail() returned false when sending to '$customer_email'.");
        }

        return $sent;
    }
}

if (!function_exists('sendOrderStatusUpdateEmail')) {
    /**
     * Send Order Status Update Email to Customer's Email / Gmail account.
     */
    function sendOrderStatusUpdateEmail($order_number, $customer_name, $customer_email, $new_status) {
        if (empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $status_label = strtoupper(htmlspecialchars($new_status));
        $subject = "📦 Order Update #" . $order_number . " - Status: " . ucfirst($new_status);

        $html_body = '
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="margin: 0; padding: 20px 0; background-color: #0d0d0d; font-family: \'Inter\', sans-serif;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="background: #171717; border-radius: 12px; border: 1px solid #2a2a2a; color: #fff; padding: 30px;">
                    <h2 style="color: #e02020; margin-top: 0; text-transform: uppercase;">PKTS KARATE DOJO</h2>
                    <p style="font-size: 16px;">Hello ' . htmlspecialchars($customer_name) . ',</p>
                    <p>Your order <strong>#' . htmlspecialchars($order_number) . '</strong> status has been updated to:</p>
                    <div style="background: rgba(224,32,32,0.15); border: 1px solid #e02020; color: #ff5252; font-weight: bold; padding: 12px 20px; display: inline-block; border-radius: 8px; font-size: 16px; margin: 10px 0 20px 0;">
                        ' . $status_label . '
                    </div>
                    <p style="color: #aaa; font-size: 14px;">If you have any questions, reply directly to this email or call +63 917 123 4567.</p>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: PKTS Karate Dojo <no-reply@pktskarate.com>\r\n";
        $headers .= "Reply-To: PKTS Karate Dojo <info@pktskarate.com>\r\n";

        return @mail($customer_email, $subject, $html_body, $headers);
    }
}
