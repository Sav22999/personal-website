<?php

function render_email($type, $data)
{
    $is_copy = ($type === 'copy');

    $subject = $is_copy
        ? 'Your message has been received'
        : 'New message from ' . htmlspecialchars($data['name']);

    $heading = $is_copy
        ? 'Message received'
        : 'New contact form message';

    $intro = $is_copy
        ? 'Thank you for reaching out! Your message has been received and you will get a reply as soon as possible. Below is a copy of your message.'
        : 'You have received a new message from the contact form on saveriomorelli.com.';

    $fields = [];
    $fields[] = ['label' => 'Name', 'value' => htmlspecialchars($data['name'])];
    $fields[] = ['label' => 'Reason', 'value' => htmlspecialchars($data['reason'])];
    $fields[] = ['label' => 'Email', 'value' => htmlspecialchars($data['email'])];

    if (!empty($data['project'])) {
        $fields[] = ['label' => 'Project', 'value' => htmlspecialchars($data['project'])];
    }
    if (!empty($data['project_version'])) {
        $fields[] = ['label' => 'Project version', 'value' => htmlspecialchars($data['project_version'])];
    }
    if (!empty($data['os'])) {
        $fields[] = ['label' => 'OS', 'value' => htmlspecialchars($data['os'])];
    }
    if (!empty($data['browser'])) {
        $fields[] = ['label' => 'Browser', 'value' => htmlspecialchars($data['browser'])];
    }
    if (!empty($data['language'])) {
        $fields[] = ['label' => 'Language', 'value' => htmlspecialchars($data['language'])];
    }

    $rows = '';
    foreach ($fields as $field) {
        $rows .= '
                        <tr>
                            <td style="padding: 10px 16px; font-size: 13px; color: #888888; white-space: nowrap; vertical-align: top; border-bottom: 1px solid #f0f0f0;">' . $field['label'] . '</td>
                            <td style="padding: 10px 16px; font-size: 14px; color: #333333; border-bottom: 1px solid #f0f0f0;">' . $field['value'] . '</td>
                        </tr>';
    }

    $message_html = nl2br(htmlspecialchars($data['message']));

    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 16px;">
                <table role="presentation" width="560" cellspacing="0" cellpadding="0" style="max-width: 560px; width: 100%;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 24px 32px; background-color: #66CBFF; border-radius: 12px 12px 0 0;">
                            <!--[if mso]><span style="font-size: 18px; font-weight: 400; color: #222222; letter-spacing: -0.02em;">Saverio Morelli</span><![endif]-->
                            <!--[if !mso]><!--><span style="font-family: \'Stack Sans Notch\', -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; color: #222222; letter-spacing: -0.02em;">Saverio Morelli</span><!--<![endif]-->
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding: 32px; background-color: #ffffff;">
                            <h1 style="margin: 0 0 8px; font-size: 20px; font-weight: 600; color: #222222;">' . $heading . '</h1>
                            <p style="margin: 0 0 28px; font-size: 14px; color: #666666; line-height: 1.6;">' . $intro . '</p>

                            <!-- Fields table -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border: 1px solid #e8e8e8; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                ' . $rows . '
                            </table>

                            <!-- Message -->
                            <div style="margin-bottom: 8px; font-size: 13px; font-weight: 600; color: #888888; text-transform: uppercase; letter-spacing: 0.05em;">Message</div>
                            <div style="padding: 16px; background-color: #f9f9f9; border-radius: 8px; border: 1px solid #e8e8e8; font-size: 14px; color: #333333; line-height: 1.7;">' . $message_html . '</div>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 32px; background-color: #fafafa; border-top: 1px solid #e8e8e8; border-radius: 0 0 12px 12px;">
                            <p style="margin: 0; font-size: 12px; color: #aaaaaa; line-height: 1.5;">' . ($is_copy ? 'This is an automated confirmation. Please do not reply to this email.' : 'Sent from saveriomorelli.com contact form.') . '</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

    return ['subject' => $subject, 'html' => $html];
}
