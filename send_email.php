<?php
/**
 * Script d'envoi d'emails pour NAP Consulting
 * Envoie un email à l'agence et un email de confirmation au client
 */

// Configuration des emails
$admin_email = "contact@nap.com";
$admin_name = "NAP Consulting";
$site_name = "NAP Consulting";

// Vérification que le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html#contact");
    exit;
}

// Récupération et nettoyage des données
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$visa_type = isset($_POST['visa_type']) ? trim(strip_tags($_POST['visa_type'])) : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';
$consent = isset($_POST['consent']) ? true : false;

// Validation des champs obligatoires
$errors = [];
if (empty($name)) $errors[] = "Nom complet";
if (empty($email)) $errors[] = "Email";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
if (empty($visa_type)) $errors[] = "Type de demande";
if (empty($message)) $errors[] = "Message";
if (!$consent) $errors[] = "Acceptation des conditions";

if (!empty($errors)) {
    // Redirection avec erreur
    header("Location: index.html#contact?status=error");
    exit;
}

// Préparation des données pour l'email
$date_demande = date('d/m/Y à H:i:s');
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// ========== EMAIL À L'ADMINISTRATION (NAP Consulting) ==========
$admin_subject = "🔔 NOUVELLE DEMANDE - $visa_type - $name";

$admin_message = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Nouvelle demande NAP Consulting</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px; }
        .header { background: linear-gradient(135deg, #EF4444, #3B82F6); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; background: #f9f9f9; }
        .info-row { margin-bottom: 15px; padding: 10px; background: white; border-radius: 8px; border-left: 4px solid #EF4444; }
        .label { font-weight: bold; color: #EF4444; display: inline-block; width: 140px; }
        .footer { text-align: center; padding: 15px; font-size: 12px; color: #888; background: #f0f0f0; border-radius: 0 0 10px 10px; }
        .badge { display: inline-block; background: #3B82F6; color: white; padding: 5px 12px; border-radius: 20px; font-size: 12px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>📋 NOUVELLE DEMANDE CLIENT</h2>
            <p>NAP Consulting - Accompagnement Visa</p>
        </div>
        <div class='content'>
            <div style='text-align: center; margin-bottom: 20px;'>
                <span class='badge'>Demande reçue le $date_demande</span>
            </div>
            
            <div class='info-row'>
                <span class='label'>👤 Nom complet :</span> $name
            </div>
            <div class='info-row'>
                <span class='label'>📧 Email :</span> <a href='mailto:$email'>$email</a>
            </div>
            <div class='info-row'>
                <span class='label'>📞 Téléphone :</span> " . (!empty($phone) ? $phone : "Non renseigné") . "
            </div>
            <div class='info-row'>
                <span class='label'>🌍 Type de demande :</span> <strong style='color:#3B82F6;'>$visa_type</strong>
            </div>
            <div class='info-row'>
                <span class='label'>💬 Message :</span><br>
                <div style='margin-top: 8px; padding: 12px; background: #fff; border-radius: 8px; border: 1px solid #e0e0e0;'>
                    " . nl2br($message) . "
                </div>
            </div>
            <div class='info-row'>
                <span class='label'>🔒 Consentement RGPD :</span> Oui
            </div>
            <hr style='margin: 20px 0; border: none; border-top: 1px solid #e0e0e0;'>
            <div style='font-size: 12px; color: #666;'>
                <strong>Informations techniques :</strong><br>
                IP: $ip_address<br>
                User Agent: $user_agent
            </div>
        </div>
        <div class='footer'>
            <p>© " . date('Y') . " NAP Consulting - Cet email est généré automatiquement.</p>
            <p>📌 Une réponse doit être apportée au client dans les 24h.</p>
        </div>
    </div>
</body>
</html>
";

// ========== EMAIL DE CONFIRMATION AU CLIENT ==========
$client_subject = "✨ NAP Consulting - Votre demande a bien été reçue !";

$client_message = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Confirmation de votre demande - NAP Consulting</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 550px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px; }
        .header { background: linear-gradient(135deg, #EF4444, #3B82F6); color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 25px; background: #ffffff; }
        .highlight { background: #f0f9ff; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #3B82F6; }
        .cta-button { display: inline-block; background: #EF4444; color: white; padding: 12px 25px; text-decoration: none; border-radius: 30px; margin: 15px 0; font-weight: bold; }
        .footer { text-align: center; padding: 15px; font-size: 12px; color: #888; background: #f9f9f9; border-radius: 0 0 10px 10px; }
        .contact-info { margin-top: 20px; padding-top: 15px; border-top: 1px solid #e0e0e0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>✨ Bienvenue chez NAP Consulting ✨</h2>
            <p>Votre projet de mobilité internationale commence ici</p>
        </div>
        <div class='content'>
            <p>Bonjour <strong>" . htmlspecialchars($name) . "</strong>,</p>
            
            <p>Nous vous remercions d'avoir confié votre projet à <strong>NAP Consulting</strong>. Votre demande concernant <strong>" . htmlspecialchars($visa_type) . "</strong> a bien été reçue et est en cours de traitement.</p>
            
            <div class='highlight'>
                <p><strong>📌 Ce que nous allons faire :</strong></p>
                <ul style='margin: 5px 0 0 20px;'>
                    <li>Analyser votre situation dans les plus brefs délais</li>
                    <li>Un expert dédié vous contactera sous 24h ouvrables</li>
                    <li>Nous préparerons ensemble la meilleure stratégie pour votre dossier</li>
                </ul>
            </div>
            
            <p><strong>📋 Récapitulatif de votre demande :</strong></p>
            <ul>
                <li><strong>Nom :</strong> $name</li>
                <li><strong>Type de demande :</strong> $visa_type</li>
                <li><strong>Date de réception :</strong> $date_demande</li>
            </ul>
            
            <div style='text-align: center;'>
                <a href='tel:+33123456789' class='cta-button' style='background: #EF4444; color: white; padding: 12px 25px; text-decoration: none; border-radius: 30px; display: inline-block; margin: 10px 0;'>
                    📞 Nous contacter d'urgence
                </a>
            </div>
            
            <p>En attendant le retour de notre équipe, n'hésitez pas à consulter notre site pour découvrir les témoignages de nos anciens clients ou préparer les documents nécessaires à votre dossier.</p>
            
            <div class='contact-info'>
                <p><strong>📧 Une question ?</strong> Répondez directement à cet email ou contactez-nous :</p>
                <p>📞 France : +33 1 23 45 67 89<br>
                💬 WhatsApp Canada : +1 514 999 8888<br>
                🌐 Site web : www.napconsulting.fr</p>
            </div>
        </div>
        <div class='footer'>
            <p>© " . date('Y') . " NAP Consulting - Experts en immigration Canada & France</p>
            <p>Cet email est une confirmation automatique. Merci de ne pas y répondre directement.</p>
        </div>
    </div>
</body>
</html>
";

// En-têtes pour les emails HTML
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: NAP Consulting <$admin_email>\r\n";
$headers .= "Reply-To: $admin_email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// En-têtes pour l'email client (avec Reply-To vers l'agence)
$client_headers = "MIME-Version: 1.0\r\n";
$client_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$client_headers .= "From: NAP Consulting <$admin_email>\r\n";
$client_headers .= "Reply-To: $admin_email\r\n";

// Envoi des emails
$admin_sent = mail($admin_email, $admin_subject, $admin_message, $headers);
$client_sent = mail($email, $client_subject, $client_message, $client_headers);

// Redirection avec statut
if ($admin_sent && $client_sent) {
    header("Location: index.html#contact?status=success");
} else {
    // Journalisation de l'erreur (optionnel)
    error_log("Erreur envoi email NAP Consulting - " . date('Y-m-d H:i:s') . " - Destinataire: $email");
    header("Location: index.html#contact?status=error");
}
exit;
?>
