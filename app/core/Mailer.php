<?php
// app/core/Mailer.php
// Uses PHPMailer — place 3 files in app/libraries/PHPMailer/:
// PHPMailer.php, SMTP.php, Exception.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once ROOT_PATH . '/vendor/autoload.php';

class Mailer {

    private array $settings;

    public function __construct(array $settings = []) {
        $this->settings = $settings;
    }

    /**
     * Core send method — uses PHPMailer to send emails via SMTP
     */
    public function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool {
        $s = $this->settings;

        $mail = new PHPMailer(true);

        try {
            // ── Server settings ────────────────────────────────
            $mail->isSMTP();
            $mail->Host       = $s['smtp_host']      ?? SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = $s['smtp_user']      ?? SMTP_USER;
            $mail->Password   = $s['smtp_pass']      ?? SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int)($s['smtp_port'] ?? SMTP_PORT);

            $smtpUser  = trim((string)($s['smtp_user'] ?? SMTP_USER));
            $fromName  = trim((string)($s['smtp_from_name'] ?? SMTP_FROM_NAME)) ?: SMTP_FROM_NAME;

            $noReplyEmail = $this->buildNoReplyAddress($s);

            $mail->setFrom($smtpUser, $fromName);

            // Sender = envelope sender; must equal the SMTP auth account.
            $mail->Sender = $smtpUser;

            $mail->addReplyTo($noReplyEmail, $fromName);

            // ── To ─────────────────────────────────────────────
            $mail->addAddress($toEmail, $toName);

            // ── Content ────────────────────────────────────────
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody); // plain text fallback

            $mail->send();

            error_log("[Mailer] ✓ Sent to: {$toEmail} | Subject: {$subject}");
            return true;

        } catch (Exception $e) {
            error_log("[Mailer] ✗ Failed to: {$toEmail} | Error: {$mail->ErrorInfo}");
            return false;
        }
    }


    private function buildNoReplyAddress(array $s): string {
        // Candidates: configured from-email first, then SMTP user
        $candidates = [
            trim((string)($s['smtp_from_email'] ?? SMTP_FROM_EMAIL)),
            trim((string)($s['smtp_user']       ?? SMTP_USER)),
        ];

        foreach ($candidates as $addr) {
            if (!$addr || !filter_var($addr, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            [$local, $domain] = explode('@', $addr, 2);

            // If the address is already a noreply address, use it directly.
            if (strcasecmp($local, 'noreply') === 0 || strcasecmp($local, 'no-reply') === 0) {
                return $addr;
            }

            // Build noreply@<domain> from this address's domain.
            return 'noreply@' . $domain;
        }

        // Last resort
        return SMTP_FROM_EMAIL;
    }

    /** Send email to admin about a new agent approval request */
    public function sendAgentRequestToAdmin(
        string $adminEmail,
        string $adminName,
        array  $agent,
        string $approveUrl,
        string $rejectUrl
    ): bool {
        $subject = "New Agent Approval Request — {$agent['name']}";
        $body    = $this->agentRequestTemplate($adminName, $agent, $approveUrl, $rejectUrl);
        return $this->send($adminEmail, $adminName, $subject, $body);
    }

    /** Send email to agent: account created, awaiting approval */
    public function sendAgentAccountCreated(string $agentEmail, string $agentName): bool {
        $subject = "Account Created — Waiting for Approval";
        $body    = $this->agentAccountCreatedTemplate($agentName);
        return $this->send($agentEmail, $agentName, $subject, $body);
    }

    /** Send email to agent: account has been approved */
    public function sendAgentApproved(string $agentEmail, string $agentName): bool {
        $subject = "Your Agent Account Has Been Approved!";
        $body    = $this->agentApprovedTemplate($agentName);
        return $this->send($agentEmail, $agentName, $subject, $body);
    }

    /** Send email to agent: account has been rejected */
    public function sendAgentRejected(string $agentEmail, string $agentName, string $note = ''): bool {
        $subject = "Update on Your Agent Account Application";
        $body    = $this->agentRejectedTemplate($agentName, $note);
        return $this->send($agentEmail, $agentName, $subject, $body);
    }

    // ── HTML Templates ────────────────────────────────────────

    private function agentRequestTemplate(string $adminName, array $agent, string $approveUrl, string $rejectUrl): string {
        $name     = htmlspecialchars($agent['name']);
        $email    = htmlspecialchars($agent['email']);
        $phone    = htmlspecialchars($agent['phone']    ?? 'N/A');
        $headline = htmlspecialchars($agent['headline'] ?? 'N/A');
        $bio      = htmlspecialchars($agent['bio']      ?? 'N/A');
        $location = htmlspecialchars($agent['location'] ?? 'N/A');
        $website  = htmlspecialchars($agent['website']  ?? 'N/A');
        $aName    = htmlspecialchars($adminName);

        $year = date('Y');
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f3f2ef;margin:0;padding:30px 0;">
  <div style="max-width:580px;margin:0 auto;background:#fff;border-radius:8px;border:1px solid #e0e0e0;overflow:hidden;">
    <div style="background:#0a66c2;padding:24px 30px;">
      <span style="color:#fff;font-size:18px;font-weight:700;">LinkedIn Admin</span>
    </div>
    <div style="padding:30px;">
      <p style="font-size:15px;color:#333;margin:0 0 6px;">Hello <strong>{$aName}</strong>,</p>
      <p style="font-size:14px;color:#555;margin:0 0 20px;">A new agent has registered and completed their profile. Please review and take action.</p>
      <div style="background:#f8f8f8;border:1px solid #e8e8e8;border-radius:6px;padding:18px;margin-bottom:20px;">
        <h3 style="font-size:14px;font-weight:700;color:#191919;margin:0 0 12px;">Agent Details</h3>
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
          <tr><td style="color:#888;padding:5px 0;width:110px;">Name</td><td style="color:#333;font-weight:600;">{$name}</td></tr>
          <tr><td style="color:#888;padding:5px 0;">Email</td><td style="color:#333;">{$email}</td></tr>
          <tr><td style="color:#888;padding:5px 0;">Phone</td><td style="color:#333;">{$phone}</td></tr>
          <tr><td style="color:#888;padding:5px 0;">Headline</td><td style="color:#333;">{$headline}</td></tr>
          <tr><td style="color:#888;padding:5px 0;">Location</td><td style="color:#333;">{$location}</td></tr>
          <tr><td style="color:#888;padding:5px 0;">Website</td><td style="color:#333;">{$website}</td></tr>
          <tr><td style="color:#888;padding:5px 0;vertical-align:top;">Bio</td><td style="color:#333;">{$bio}</td></tr>
        </table>
      </div>
      <div style="display:flex;gap:12px;">
        <a href="{$approveUrl}" style="display:inline-block;padding:11px 24px;background:#057642;color:#fff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:600;">✓ Approve Agent</a>
        <a href="{$rejectUrl}"  style="display:inline-block;padding:11px 24px;background:#cc1016;color:#fff;text-decoration:none;border-radius:6px;font-size:14px;font-weight:600;">✗ Reject Agent</a>
      </div>
    </div>
    <div style="background:#f8f8f8;border-top:1px solid #e0e0e0;padding:16px 30px;font-size:11px;color:#aaa;text-align:center;">
      &copy; {$year} LinkedIn Admin
    </div>
  </div>
</body>
</html>
HTML;
    }

    private function agentAccountCreatedTemplate(string $agentName): string {
        $name   = htmlspecialchars($agentName);
        $appUrl = APP_URL;
        $year = date('Y');
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f3f2ef;margin:0;padding:30px 0;">
  <div style="max-width:580px;margin:0 auto;background:#fff;border-radius:8px;border:1px solid #e0e0e0;overflow:hidden;">
    <div style="background:#0a66c2;padding:24px 30px;">
      <span style="color:#fff;font-size:18px;font-weight:700;">LinkedIn</span>
    </div>
    <div style="padding:30px;">
      <div style="text-align:center;margin-bottom:24px;">
        <div style="width:64px;height:64px;background:#e8f0fb;border-radius:50%;margin:0 auto 12px;line-height:64px;font-size:32px;text-align:center;">⏳</div>
        <h2 style="font-size:20px;font-weight:700;color:#191919;margin:0 0 6px;">Account Created!</h2>
        <p style="font-size:14px;color:#777;margin:0;">Your profile is under review.</p>
      </div>
      <p style="font-size:14px;color:#444;margin:0 0 16px;">Hi <strong>{$name}</strong>,</p>
      <p style="font-size:14px;color:#555;line-height:1.6;margin:0 0 20px;">
        Thank you for registering as an agent on <strong>LinkedIn</strong>. Your profile has been submitted and is currently being reviewed by our admin team.
      </p>
      <div style="background:#e8f0fb;border:1px solid #c0d4f0;border-radius:6px;padding:14px 18px;margin-bottom:24px;">
        <p style="font-size:13px;color:#0a66c2;margin:0;font-weight:500;">⏳ You will receive another email once your account is approved or rejected. This usually takes 1-2 business days.</p>
      </div>
    </div>
    <div style="background:#f8f8f8;border-top:1px solid #e0e0e0;padding:16px 30px;font-size:11px;color:#aaa;text-align:center;">
      &copy; {$year} LinkedIn Admin
    </div>
  </div>
</body>
</html>
HTML;
    }

    private function agentApprovedTemplate(string $agentName): string {
        $name   = htmlspecialchars($agentName);
        $appUrl = APP_URL;
        $year = date('Y');
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f3f2ef;margin:0;padding:30px 0;">
  <div style="max-width:580px;margin:0 auto;background:#fff;border-radius:8px;border:1px solid #e0e0e0;overflow:hidden;">
    <div style="background:#0a66c2;padding:24px 30px;">
      <span style="color:#fff;font-size:18px;font-weight:700;">LinkedIn</span>
    </div>
    <div style="padding:30px;">
      <div style="text-align:center;margin-bottom:24px;">
        <div style="width:64px;height:64px;background:#e8f5e9;border-radius:50%;margin:0 auto 12px;line-height:64px;font-size:32px;text-align:center;">✓</div>
        <h2 style="font-size:20px;font-weight:700;color:#191919;margin:0 0 6px;">Account Approved!</h2>
        <p style="font-size:14px;color:#777;margin:0;">Your agent account is now active.</p>
      </div>
      <p style="font-size:14px;color:#444;margin:0 0 16px;">Hi <strong>{$name}</strong>,</p>
      <p style="font-size:14px;color:#555;line-height:1.6;margin:0 0 20px;">
        Your agent account on <strong>LinkedIn</strong> has been <strong style="color:#057642;">approved</strong>. You can now log in and start using all agent features.
      </p>
      <a href="{$appUrl}/signin" style="display:inline-block;padding:12px 28px;background:#0a66c2;color:#fff;text-decoration:none;border-radius:24px;font-size:14px;font-weight:600;">Sign In to Your Account</a>
    </div>
    <div style="background:#f8f8f8;border-top:1px solid #e0e0e0;padding:16px 30px;font-size:11px;color:#aaa;text-align:center;">
      &copy; {$year} LinkedIn Admin
    </div>
  </div>
</body>
</html>
HTML;
    }

    private function agentRejectedTemplate(string $agentName, string $note = ''): string {
        $name     = htmlspecialchars($agentName);
        $noteHtml = $note
            ? "<div style='background:#fff8e1;border:1px solid #ffe082;border-radius:6px;padding:14px 18px;margin-bottom:20px;'><p style='font-size:13px;color:#7a6010;margin:0;'><strong>Reason:</strong> " . htmlspecialchars($note) . "</p></div>"
            : '';
        $year = date('Y');
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f3f2ef;margin:0;padding:30px 0;">
  <div style="max-width:580px;margin:0 auto;background:#fff;border-radius:8px;border:1px solid #e0e0e0;overflow:hidden;">
    <div style="background:#0a66c2;padding:24px 30px;">
      <span style="color:#fff;font-size:18px;font-weight:700;">LinkedIn</span>
    </div>
    <div style="padding:30px;">
      <div style="text-align:center;margin-bottom:24px;">
        <div style="width:64px;height:64px;background:#fde8e8;border-radius:50%;margin:0 auto 12px;line-height:64px;font-size:32px;text-align:center;">✗</div>
        <h2 style="font-size:20px;font-weight:700;color:#191919;margin:0 0 6px;">Application Not Approved</h2>
      </div>
      <p style="font-size:14px;color:#444;margin:0 0 16px;">Hi <strong>{$name}</strong>,</p>
      <p style="font-size:14px;color:#555;line-height:1.6;margin:0 0 20px;">
        After reviewing your profile, our admin team was unable to approve your application at this time.
      </p>
      {$noteHtml}
      <p style="font-size:14px;color:#555;line-height:1.6;">You may update your profile and reapply, or contact support for help.</p>
    </div>
    <div style="background:#f8f8f8;border-top:1px solid #e0e0e0;padding:16px 30px;font-size:11px;color:#aaa;text-align:center;">
      &copy; {$year} LinkedIn Admin
    </div>
  </div>
</body>
</html>
HTML;
    }
}